<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Bill;
use App\Company;
use App\Invoice;
use App\Mail\CreditNoteNotificacion;
use App\Mail\ReceptionNotification;
use App\Provider;
use App\Utils\BridgeHaciendaApi;
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

class ProcessReception implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $billId = '';
    private $providerId = '';
    private $token = '';
    private $ref = '';


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($billId, $providerId, $token, $ref)
    {
        $this->billId = $billId;
        $this->providerId = $providerId;
        $this->token = $token;
        $this->ref = $ref;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ( app()->environment('production') ) {
                Log::info('send job reception id: '.$this->billId);
                $client = new Client();
                $bill = Bill::find($this->billId);
                $company = Company::find($bill->company_id);
                if ( $company->atv_validation ) {
                    if ($bill->hacienda_status == '01' && $bill->document_type == '01') {
                        if($bill->xml_schema == 42) {
                            $requestData = $this->setReceptionData($bill, $this->ref);
                        } else {
                            $requestData = $this->setReceptionData43($bill, $this->ref);
                        }
                        Log::info('Request data'. json_encode($requestData));
                        $apiHacienda = new BridgeHaciendaApi();
                        $tokenApi = $apiHacienda->login(false);
                        if ($requestData !== false) {
                            $endpoint = $bill->xml_schema == 42 ? 'invoice' : 'invoice43';
                            sleep(15);
                            Log::info('Enviando Request Reception  API HACIENDA -->>' . $this->billId);
                            $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/'.$endpoint.'/aceptacionxml', [
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
                            Log::info('Response Reception Api Hacienda '. json_encode($response));
                            ApiResponse::create(['bill_id' => $bill->id, 'document_key' => $bill->document_key,
                                'doc_type' => $bill->document_type,
                                'json_response' => json_encode($response)
                            ]);
                            if (isset($response['status']) && $response['status'] == 200) {
                                Log::info('API HACIENDA 200 -->>' . $result->getBody()->getContents());
                                $date = Carbon::now();
                                $bill->hacienda_status = '03';
                                $bill->save();
                                $xml = XmlHacienda::where('bill_id',$bill->id)->whereNotNull('xml')->first();
                                $path = 'empresa-' . $company->id_number .
                                    "/aceptaciones/$date->year/$date->month/$bill->document_key.xml";
                                $pathFE = $xml->xml;
                                $save = Storage::put(
                                    $path,
                                    ltrim($response['data']['xmlFirmado'], '\n'));
                                if ($save) {
                                    $xml->xml_reception = $path;
                                    $xml->save();
                                    $xmlExtract = ltrim($response['data']['response'], '\n');
                                    Mail::to($bill->provider_email)->cc($company->email)->send(new ReceptionNotification([
                                        'xml' => $path, 'xmlFE' => $pathFE,  'data_invoice' => $bill,
                                        'data_company' => $company, 'response' => $xmlExtract
                                    ]));
                                }
                                Log::info('Reception enviada y XML guardado.');
                            } else if (isset($response['status']) && $response['status'] == 400 &&
                                strpos($response['message'], 'ya fue recibido anteriormente') <> false) {
                                //$bill->accept_status = $bill->accept_status == 2 ? 2 : 0;
                                //$bill->save();
                                Log::info('Failed Job');
                            } else if (isset($response['status']) && $response['status'] == 400 &&
                                strpos($response['message'], 'archivo XML ya existe en nuestras bases de datos') <> false) {
                                //$bill->accept_status = $bill->accept_status == 2 ? 2 : 0;
                                //$bill->save();
                                Log::info('Failed Job');
                            } else {
                                //$bill->accept_status = $bill->accept_status == 2 ? 2 : 0;
                                //$bill->save();
                                Log::error('ERROR Enviando parametros API HACIENDA Reception Empresa '.$company->business_name.' Bill: '.$this->billId);
                            }
                            Log::info('Proceso de Reception finalizado con éxito. Empresa '.$company->business_name.' Bill: '.$this->billId);
                        }
                    } else {
                        $bill->accept_status = 1;
                        $bill->hacienda_status = '03';
                        $bill->save();
                        Log::info('Proceso de Reception Factura ya habia sido enviada. Empresa '.$company->business_name.' Bill: '.$this->billId);
                    }
                }else {
                    Log::warning('El job receptions no se procesó, porque la empresa no tiene un certificado válido: Empresa '.$company->business_name.' Bill: '.$this->billId.'-->>');
                    if( !$company->use_invoicing ){
                        $bill->accept_status = 1;
                        $bill->hacienda_status = '03';
                        $bill->save();
                    }
                }
            }
        } catch ( \Exception $e) {
            Log::error('ERROR Enviando parametros  API HACIENDA Reception: '.$this->billId.'-->>'.$e);
        }
    }

    private function setReceptionData(Bill $data, $ref) {
        try {
            $company = $data->company;
            $provider = $data->provider;
            $invoiceData = null;
            $request = null;
            $invoiceData = [
                'clave' => $data['document_key'],
                'cedula_emisor' => !empty($data['provider_id_number']) ? trim($data['provider_id_number']) : $provider->id_number,
                'fecha_emision' => $data['generated_date'] ?? '',
                'cod_mensaje' => $data['accept_status'] ?? 1,
                'detalle' => 'Detalle',
                'total' => $data['total'],
                'cedula_receptor' => trim($company->id_number),
                'consecutivo' => getDocReference('05', $ref),
                'tipo_documento' => '05',
                'usuarioAtv' => $company->atv->user ? trim($company->atv->user) : '',
                'passwordAtv' => $company->atv->password ? trim($company->atv->password) : '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ? trim($company->atv->pin) : '',
                'atvcertFile' => Storage::get($company->atv->key_url),
            ];

            foreach ($invoiceData as $key => $values) {
                if ($key == 'atvcertFile') {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values,
                        'filename' => $invoiceData['cedula_emisor'].'.p12'
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
            Log::info('Error al crear data para request en Credit Note API HACIENDA -->>'. $error);
            return false;
        }
    }

    private function setReceptionData43(Bill $data, $ref) {
        try {
            $company = $data->company;
            $provider = $data->provider;
            $invoiceData = null;
            $request = null;
            $invoiceData = [
                'clave' => $data['document_key'],
                'cedula_emisor' => !empty($data['provider_id_number']) ? trim($data['provider_id_number']) : trim($provider->id_number),
                'fecha_emision' => $data['generated_date'] ?? '',
                'cod_mensaje' => $data['accept_status'] ?? 1,
                'detalle' => 'Detalle',
                'total' => $data['accept_total_factura'],
                'cedula_receptor' => trim($company->id_number),
                'consecutivo' => getDocReference('05', $ref),
                'tipo_documento' => '05',
                'total_impuesto' => $data['accept_iva_total'],
                'cod_actividad' => $data['commercial_activity'],
                'cond_impuesto' => empty($data['accept_iva_condition']) ? '02' : $data['accept_iva_condition'],
                'total_imp_acredit' => $data['accept_iva_acreditable'],
                'total_gastos' => $data['accept_iva_gasto'],
                'usuarioAtv' => $company->atv->user ? trim($company->atv->user) : '',
                'passwordAtv' => $company->atv->password ? trim($company->atv->password) : '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ? trim($company->atv->pin) : '',
               // 'atvcertFile' => Storage::get($company->atv->key_url),
            ];
            Log::info("Request Data from invoices id: $data->id  --> ".json_encode($invoiceData));
            $invoiceData['atvcertFile'] = Storage::get($company->atv->key_url);

            foreach ($invoiceData as $key => $values) {
                if ($key == 'atvcertFile') {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values,
                        'filename' => $invoiceData['cedula_emisor'].'.p12'
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
            Log::info('Error al crear data para request en Credit Note API HACIENDA -->>'. $error);
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
                    'detalle' => $value['name'] ?? '',
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
            Log::info('Error al crear parametros en Credit Note API HACIENDA -->>'. $error);
            return false;
        }
    }
}
