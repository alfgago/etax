<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
use App\Utils\BridgeHaciendaApi;
use App\Utils\InvoiceUtils;
use App\Variables;
use App\XmlHacienda;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessInvoiceSM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoiceId = '';
    private $companyId = '';
    private $token = '';


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invoiceId, $companyId, $token)
    {
        $this->invoiceId = $invoiceId;
        $this->companyId = $companyId;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        try {
            //if ( app()->environment('production') ) {
                $invoiceUtils = new InvoiceUtils();
                $client = new Client();
                $invoice = Invoice::find($this->invoiceId);
                $company = Company::find($this->companyId);
                //if (strpos($invoice->generation_method, 'bulk') !== FALSE) {  // Procese el bulk de SM Seguros aqui
                    Log::info('send job invoice id: '.$this->invoiceId);
                    $invoice->in_queue = false;
                    $invoice->save();
                    if ($company->atv_validation ) {
                        sleep(10);
                        if ($invoice->hacienda_status == '01' && ($invoice->document_type == ('01' || '04' || '08' || '09')) && $invoice->resend_attempts < 6) {
                            if ($invoice->xml_schema == 43) {
                                $requestDetails = $invoiceUtils->setDetails43($invoice->items);
                                $requestData = $invoiceUtils->setInvoiceData43($invoice, $requestDetails);
                            } else {
                                $requestDetails = $this->setDetails($invoice->items);
                                $requestData = $this->setInvoiceData($invoice, $requestDetails);
                            }
                            
                            $apiHacienda = new BridgeHaciendaApi();
                            $tokenApi = $apiHacienda->login(false);
                            if ($requestData !== false) {
                                $endpoint = $invoice->xml_schema == 42 ? 'invoice' : 'invoice43';
                                Log::info('Enviando Request  API HACIENDA -->>' . $this->invoiceId);
                                $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/'.$endpoint.'/create', [
                                    'headers' => [
                                        'Auth-Key' => config('etax.api_hacienda_key'),
                                        'Client-Service' => config('etax.api_hacienda_client'),
                                        'Authorization' => $tokenApi,
                                        'User-ID' => config('etax.api_hacienda_user_id'),
                                        'Connection' => 'Close'
                                    ],
                                    'multipart' => $requestData,
                                    'verify' => false,
                                    'http_errors' => false,
                                    'connect_timeout' => 20
                                ]);
                                $response = json_decode($result->getBody()->getContents(), true);
                                $date = Carbon::now();
                                Log::info("API Hacienda. Empresa: $company->id, Response: ". json_encode($response));
                                ApiResponse::create(['invoice_id' => $invoice->id, 'document_key' => $invoice->document_key,
                                    'doc_type' => $invoice->document_type,
                                    'json_response' => json_encode($response)
                                ]);
                                if (isset($response['status']) && $response['status'] == 200) {
                                    Log::info('API HACIENDA 200 :'. $invoice->document_number);
                                    $invoice->hacienda_status = '03';
                                    $invoice->save();
                                    $path = 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml";
                                    $save = Storage::put(
                                        $path,
                                        ltrim($response['data']['xmlFirmado'], '\n')
                                    );
                                    $pathMH = 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/MH-$invoice->document_key.xml";
                                    $saveMH = Storage::put(
                                        $pathMH,
                                        ltrim($response['data']['mensajeHacienda'], '\n')
                                    );
                                    if ($save) {
                                        $xml = new XmlHacienda();
                                        $xml->invoice_id = $invoice->id;
                                        $xml->bill_id = 0;
                                        $xml->xml = $path;
                                        $xml->xml_message = $pathMH;
                                        $xml->save();
                                        
                                        $sendPdf = $invoice->generation_method == "etax-bulk";
                                        $file = $invoiceUtils->sendInvoiceNotificationEmail( $invoice, $company, $path, $pathMH, true);
                                    }
                                    Log::info('Factura enviada y XML guardado.');
                                } else if (isset($response['status']) && $response['status'] == 400 &&
                                    strpos($response['message'], 'ya fue recibido anteriormente') <> false) {
                                    Log::info('Consecutive repeated -->' . $invoice->document_number);
                                    $invoice->hacienda_status = '04';
                                    $invoice->save();
                                } else if (isset($response['status']) && $response['status'] == 400 &&
                                    strpos($response['message'], 'XML ya existe en nuestras bases de datos') <> false) {
                                    Log::info('Consecutive repeated -->' . $invoice->document_number);
                                    $invoice->hacienda_status = '30';
                                    $invoice->save();
                                }else if (isset($response['status']) && $response['status'] == 400 &&
                                    strpos($response['message'], 'archivo XML ya existe en nuestras bases de datos') <> false) {
                                    Log::info('Consecutive repeated -->' . $invoice->document_number);
                                    $invoice->hacienda_status = '04';
                                    $invoice->save();
                                }
                                Log::info('Proceso de facturación finalizado con éxito.');
                            }else {
                                Log::error('Hacienda request data viene vacio. '.$invoice->id);
                            }
                        }else{
                            Log::error('No se pudo enviar la factura:  '. var_dump($invoice));
                        }
                    }else {
                        Log::warning('El job Invoices no se procesó, porque la empresa no tiene un certificado válido.'.$company->id_number);
                    }
                //}
            //}
        } catch ( \Exception $e) {
            Log::error('ERROR Enviando parametros  API HACIENDA Invoice: '.$this->invoiceId.'-->>'.$e);
        }
    }

    private function setInvoiceData(Invoice $data, $details) {
        try {
            $company = $data->company;
            $receptorPostalCode = $data['client_zip'];
            $invoiceData = null;
            $request = null;
            $invoiceData = [
                'consecutivo' => $data['reference_number'] ?? '',
                'fecha_emision' => $data['generated_date'] ?? '',
                'receptor_nombre' => trim($data['client_first_name'].' '.$data['client_last_name']),
                'receptor_ubicacion_provincia' => substr($receptorPostalCode,0,1),
                'receptor_ubicacion_canton' => substr($receptorPostalCode,1,2),
                'receptor_ubicacion_distrito' => substr($receptorPostalCode,3),
                'receptor_ubicacion_otras_senas' => $data['client_address'] ?? '',
                'receptor_email' => $data['client_email'] ? trim($data['client_email']) : '',
                'receptor_cedula_numero' => $data['client_id_number'] ? trim($data['client_id_number']) : '',
                'receptor_postal_code' => $receptorPostalCode ?? '',
                'codigo_moneda' => $data['currency'] ?? '',
                'tipocambio' => $data['currency_rate'] ?? '',
                'tipo_documento' => $data['document_type'] ?? '',
                'sucursal_nro' => '001',
                'terminal_nro' => '00001',
                'emisor_name' => $company->business_name ? trim($company->business_name) : '',
                'emisor_email' => $company->email ? trim($company->email) : '',
                'emisor_company' => $company->business_name ? trim($company->business_name) : '',
                'emisor_city' => $company->city ?? '',
                'emisor_state' => $company->state ?? '',
                'emisor_postal_code' => $company->zip ?? '',
                'emisor_country' => $company->country ?? '',
                'emisor_address' => $company->address ? trim($company->address) : '',
                'emisor_phone' => $company->phone ? trim($company->phone) :  '',
                'emisor_cedula' => $company->id_number ? trim($company->id_number) : '',
                'usuarioAtv' => $company->atv->user ? trim($company->atv->user) : '',
                'passwordAtv' => $company->atv->password ? trim($company->atv->password) : '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ? trim($company->atv->pin) : '',
                'atvcertFile' => Storage::get($company->atv->key_url),
                'detalle' => $details
            ];
            foreach ($invoiceData as $key => $values) {
                if ($key == 'atvcertFile') {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values,
                        'filename' => $invoiceData['emisor_cedula'].'.p12'
                    );
                } else {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values
                    );
                }
            }
            return $request;
        } catch (ClientException $error) {
            Log::info('Error al crear data para request en API HACIENDA -->>'. $error);
            return false;
        }
    }

    private function setDetails($data) {
        try {
            $details = [];
            foreach ($data as $key => $value) {
                $details[$key] = array(
                    'cantidad' => $value['item_count'] ?? '',
                    'unidadMedida' => $value['measure_unit'] ?? '',
                    'detalle' => $value['name'] ? trim($value['name']) : '',
                    'precioUnitario' => $value['unit_price'] ?? '',
                    'subtotal' => $value['subtotal'] ?? '',
                    'montoTotal' => $value['item_count'] * $value['unit_price'] ?? '',
                    'montoTotalLinea' => $value['subtotal'] + $value['iva_amount'] ?? '',
                    'descuento' => $value['discount'] ?? '',
                    'impuesto' => 0 // @todo 4.3
                );
            }
            return json_encode($details, true);
        } catch (ClientException $error) {
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }


}
