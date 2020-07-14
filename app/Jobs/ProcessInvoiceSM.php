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
                $invoice = Invoice::find($this->invoiceId);
                $company = Company::find($this->companyId);
                //if (strpos($invoice->generation_method, 'bulk') !== FALSE) {  // Procese el bulk de SM Seguros aqui
                    Log::info('send job invoice id: '.$this->invoiceId);
                    $invoice->in_queue = false;
                    if( !$invoice->client_zip || $invoice->client_zip == "0" ){
                        try{
                          $invoice->client_zip = "10101";
                          $invoice->client_country = 'CR';
                          $invoice->client_state = $data['zip'][0];
                          $invoice->client_city = $data['zip'][1] . $data['zip'][2];
                          $invoice->client_district = $data['zip'];
                        }catch( \Throwable $e ){ }
                    }
                    $invoice->save();
                    if ($company->atv_validation ) {
                        sleep(7);
                        if ($invoice->hacienda_status == '01' && ($invoice->document_type == ('01' || '04' || '08' || '09')) && $invoice->resend_attempts < 6) {
                            if ($invoice->xml_schema == 43) {
                                $requestDetails = $invoiceUtils->setDetails43($invoice->items);
                                $requestData = $invoiceUtils->setInvoiceData43($invoice, $requestDetails);
                            } else {
                                $requestDetails = $this->setDetails($invoice->items);
                                $requestData = $this->setInvoiceData($invoice, $requestDetails);
                            }
                            
                            $this->sendXML($invoice, $requestData);
                            
                        }else{
                            Log::error('No se pudo enviar la factura: '. json_encode($invoice));
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
    
    private function signXML($invoice, $requestData){
        $company = $invoice->company;
        $token = $this->token;
                
        if ($requestData !== false) {
            $client = new Client();
            Log::info("Enviando parametros  API HACIENDA -->> InvoiceID: $invoice->id, CompanyID: $company->id, CompanyName: $company->business_name" );
            $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/invoice43/signxml', [
                'headers' => [
                    'Auth-Key'  => config('etax.api_hacienda_key'),
                    'Client-Service' => config('etax.api_hacienda_client'),
                    'Authorization' => $token,
                    'User-ID' => config('etax.api_hacienda_user_id'),
                    'Connection' => 'Close'
                ],
                'multipart' => $requestData,
                'verify' => false,
                'http_errors' => false
            ]);
            Log::info('Factura firmada -->>' . $invoice->id);
            $response = json_decode($result->getBody()->getContents(), true);
            if (isset($response['status']) && $response['status'] == 200) {
                $date = Carbon::now();
                $invoice->hacienda_status = '01';
                $invoice->save();
                $path = 'empresa-'.$company->id_number . "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml";
                $save = Storage::put( $path, ltrim($response['data']['xmlFirmado'], '\n'));
                
                if ($save) {
                    $xmlHacienda = XMLHacienda::firstOrNew(
                        [
                          'invoice_id' => $invoice->id
                        ]
                    );
                    $xml->bill_id = 0;
                    $xml->xml = $path;
                    $xml->save();
                    Log::info('SM: XML Guardado --> ' . $path);
                }
            }else{
                Log::warning('Error en respuesta de firma -->>'. json_encode($response) );
            }
            return $invoice;
        }
    }
    
    private function sendXML($invoice, $requestData){
        $invoiceUtils = new InvoiceUtils();
        $company = $invoice->company;
        $client = new Client();
        $apiHacienda = new BridgeHaciendaApi();
        if ($requestData !== false) {
            $tokenApi = $apiHacienda->login(false);
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
            ApiResponse::create([
                'invoice_id' => $invoice->id, 
                'company_id' => $company->id,
                'document_key' => $invoice->document_key,
                'doc_type' => $invoice->document_type,
                'json_response' => json_encode($response)
            ]);
            
            
            if( isset($response['status']) ){
                try{
                    //Intenta guardar el original firmado siempre
                    $save = 0;
                    $path = null;
                    if(isset($response['data']['xmlFirmado'])){
                        $path = 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml";
                        $save = Storage::put( $path, ltrim($response['data']['xmlFirmado'], '\n') );
                    }
                }catch(\Exception $e){}
                
                try{ //Intenta guardar la respuesta siempre
                    if(isset($response['data']['mensajeHacienda'])){
                        $saveMH = 0;
                        $pathMH = null;
                        if ( ! (strpos($response['data']['response'],"ESTADO=procesando") !== false) ) {
                            $pathMH = 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/MH-$invoice->document_key.xml";
                            $saveMH = Storage::put( $pathMH, ltrim($response['data']['mensajeHacienda'], '\n') );
                        }
                    }
                }catch(\Exception $e){}
            }
                        
            if ($save) {
                $xml = XMLHacienda::updateOrCreate(
                    [
                      'invoice_id' => $invoice->id
                    ],
                    [
                        'bill_id' => 0,
                        'xml' => $path,
                        'xml_message' => $pathMH
                    ]
                );
                Log::info('XML guardado.');
            }
                        
            
            if (isset($response['status']) && $response['status'] == 200) {
                Log::info('API HACIENDA 200 :'. $invoice->document_number);
                if (strpos($response['data']['response'],"ESTADO=procesando") !== false) {
                    $invoice->hacienda_status = '05';
                } else {
                    $invoice->hacienda_status = '03';
                }
                $invoice->save();
                if ($save && $saveMH) {
                    $sendPdf = true;
                    $file = $invoiceUtils->sendInvoiceNotificationEmail( $invoice, $company, $path, $pathMH, true);
                    Log::info('Factura enviada.');
                }
            }else if (isset($response['status']) && $response['status'] == 400) {
                Log::warning("API Hacienda. Empresa: $company->id, Response: ". json_encode($response));
                sleep(1);
                $invoice->hacienda_status = '05';
                $invoice->save();
                if ( (strpos(json_encode($response),"no se puede facturar") !== false) ) {
                    $invoice->hacienda_status = '01';
                    $invoice->save();
                }
            }
            Log::info('Proceso de facturación finalizado con éxito.');
        }else {
            Log::error('Hacienda request data viene vacio. '.$invoice->id);
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
                'receptor_email' => $data['client_email'] ? replaceAccents($data['client_email']) : '',
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
