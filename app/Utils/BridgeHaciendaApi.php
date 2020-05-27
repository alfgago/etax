<?php
/**
 * Created by PhpStorm.
 * User: xavierp
 * Date: 2019-05-31
 * Time: 23:28
 */

namespace App\Utils;

use App\Bill;
use App\Invoice;
use App\InvoiceItem;
use App\Jobs\ProcessCreditNote;
use App\Jobs\ProcessInvoice;
use App\Jobs\ProcessReception;
use App\Variables;
use App\XmlHacienda;
use App\Utils\InvoiceUtils;
use App\ApiResponse;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class BridgeHaciendaApi
{
    public function login($cache = true, $companyId = false) {
        try {
            if ($cache === false) {
                return $this->requestLogin();
            }
            if( !$companyId ){
                $companyId = currentCompany();
            }
                $value = Cache::remember('token-api-'.$companyId, '60000', function () {
                return $this->requestLogin();
            });
            return $value;
        } catch (ClientException $error) {
            Log::info('Func Login: Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

    public function createInvoice(Invoice $invoice, $token, $sendEmail = true, $singNote = false) {
        try {
            $invoiceUtils = new InvoiceUtils();
            $requestDetails = $invoiceUtils->setDetails43($invoice->items);
            $requestOtherCharges = $invoiceUtils->setOtherCharges($invoice->otherCharges);
            $requestData = $invoiceUtils->setInvoiceData43($invoice, $requestDetails, $requestOtherCharges);
            $company = $invoice->company;

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
                        $xml = new XmlHacienda();
                        $xml->invoice_id = $invoice->id;
                        $xml->bill_id = 0;
                        $xml->xml = $path;
                        $xml->save();
                        Log::info('XML Guardado -->> ' . 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml");

                        if($sendEmail){ //Define si se quiere mandar el correo inicial, o esperar a que se apruebe con hacienda. True lo manda siempre
                            $file = $invoiceUtils->sendInvoiceEmail($invoice, $company, $path);
                        }
                        if ($singNote == false) {
                            ProcessInvoice::dispatch($invoice->id, $company->id, $token)->onQueue('invoicing');
                            return $invoice;
                        }
                    }
                }else{
                    Log::warning('Error en respuesta de firma -->>'. json_encode($response) );
                }
                return $invoice;
            }
        } catch ( \Exception $error) {
            Log::error('Error al crear factura en API HACIENDA -->>'. $error);
            return $invoice;
        }
    }

    public function createCreditNote($invoice, $token) {
        try {
            $company = $invoice->company;
            //Send to queue invoice
            Log::debug( 'debug: ' . json_encode($invoice) );
            ProcessCreditNote::dispatch($invoice->id, $company->id, $token)->onQueue('invoicing');
            return $invoice;

        }catch( \Exception $e ){
            Log::error("Error en NC: " . $e);
        }
    }

    public function acceptInvoice(Bill $bill, $token) {
        try {
            $provider = $bill->provider;
            $ref = $bill->company->last_rec_ref_number;
            Log::debug("Se recibe para aceptacion la factura $bill->document_key.");
            ProcessReception::dispatch($bill->id, $provider->id, $token, $ref)
                ->onConnection(config('etax.queue_connections'))->onQueue('receptions');
            return $bill;
        } catch (ClientException $error) {
            Log::error('Error al crear factura en API HACIENDA -->>'. $error->getMessage() );
            return $bill;
        }
    }

    private function setDetails($data) {
        try {
            $details = null;
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
            Log::error('Func setDetails: Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

    private function setInvoiceData(Invoice $data, $details) {
        try {
            $company = $data->company;
            $ref = getInvoiceReference($company->last_invoice_ref_number) + 1;
            $data->reference_number = $ref;
            $data->save();
            $receptorPostalCode = $data['client_zip'];
            $invoiceData = null;
            $request = null;
            $invoiceData = array(
                'consecutivo' => $ref ?? '',
                'fecha_emision' => $data['generated_date']->toDateTimeString() ?? '',
                'codigo_actividad' => $data['commercial_activity'],
                'receptor_nombre' => $data['client_first_name'].' '.$data['client_last_name'],
                'receptor_ubicacion_provincia' => substr($receptorPostalCode,0,1),
                'receptor_ubicacion_canton' => substr($receptorPostalCode,1,2),
                'receptor_ubicacion_distrito' => substr($receptorPostalCode,3),
                'receptor_ubicacion_otras_senas' => $data['client_address'] ?? '',
                'receptor_otras_senas_extranjero' => $data['foreign_address'] ?? '',
                'receptor_email' => $data['client_email'] ?? '',
                'receptor_cedula_numero' => $data['client_id_number'] ? preg_replace("/[^0-9]/", "", $data['client_id_number']) : '',
                'receptor_postal_code' => $receptorPostalCode ?? '',
                'codigo_moneda' => $data['currency'] ?? '',
                'tipocambio' => $data['currency_rate'] ?? '',
                'tipo_documento' => $data['document_type'] ?? '',
                'sucursal_nro' => '001',
                'terminal_nro' => '00001',
                'emisor_name' => $company->business_name ?? '',
                'emisor_email' => $company->email ?? '',
                'emisor_company' => $company->business_name ?? '',
                'emisor_city' => $company->city ?? '',
                'emisor_state' => $company->state ?? '',
                'emisor_postal_code' => $company->zip ?? '',
                'emisor_country' => $company->country ?? '',
                'emisor_address' => $company->address ?? '',
                'emisor_phone' => $company->phone ? preg_replace('/[^0-9]/', '', $company->phone) : '',
                'emisor_cedula' => $company->id_number ? preg_replace("/[^0-9]/", "", $company->id_number) : '',
                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? '01',
                'atvcertPin' => $company->atv->pin ?? '',
                'atvcertFile' => Storage::get($company->atv->key_url),
                'detalle' => $details
            );

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
            Log::error('Func InvoiceData: Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        } catch ( \Throwable $error ) {
            Log::error('Error en facturacion -->>'. $error);
        }
    }

    private function requestLogin() {
        $client = new Client();
        $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/auth/login', [
            'headers' => [
                'Auth-Key'  => config('etax.api_hacienda_key'),
                'Client-Service' => config('etax.api_hacienda_client'),
                'Connection' => 'Close'
            ],
            'json' => ["username" => config('etax.api_hacienda_username'),
                "password" => config('etax.api_hacienda_password')
            ],
            'verify' => false,
        ]);
        $tokenApi = json_decode($result->getBody()->getContents(), true);
        if (isset($tokenApi['status']) && $tokenApi['status'] == 200) {
            return $tokenApi['data']['token'];
        }
        return false;
    }

    public function validateAtv($token, $company) {
        try {
            $client = new Client();
            Log::info('Validate User ATV  API HACIENDA -->>' . $company->id);
            $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/invoice/validateatv', [
                'headers' => [
                    'Auth-Key'  => config('etax.api_hacienda_key'),
                    'Client-Service' => config('etax.api_hacienda_client'),
                    'Authorization' => $token,
                    'User-ID' => config('etax.api_hacienda_user_id'),
                    'Connection' => 'Close'
                ],
                'multipart' => $this->setHaciendaInfo($company),
                'verify' => false,
                'http_errors' => false
            ]);
            Log::info('Validate User Atv Response -->>' . $company->id);
            $response = json_decode($result->getBody()->getContents(), true);
            return $response;
        } catch (\Exception $e) {
            Log::info('Validate User Atv Response -->>' . $company->id);
        }
    }

    public function queryHacienda($invoice, $token, $company, $findKey = true) {
        try {
            //Se usa para saber si debe enviar notificacion al cliente o no.
            $initialStatus = $invoice->hacienda_status;
            
            $invoice->in_queue = false;
            
            $invoiceUtils = new InvoiceUtils();
            if($findKey){
                //Si encuentra un XML firmmado, devuelve el invoice actualizado
                $invoice = $invoiceUtils->setRealDocumentKey($invoice);
            }
            $key = $invoice->document_key;
            Log::info("Consultando Mensaje Hacienda XML API HACIENDA -->> Empresa: $company->id / Factura: $invoice->id");

            $client = new Client();
            $file = null;
            $query = $this->setInvoiceInfo($key, $company);
            $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/invoice43/consult', [
                'headers' => [
                    'Auth-Key'  => config('etax.api_hacienda_key'),
                    'Client-Service' => config('etax.api_hacienda_client'),
                    'Authorization' => $token,
                    'User-ID' => config('etax.api_hacienda_user_id'),
                    'Connection' => 'Close'
                ],
                'multipart' => $query,
                'verify' => false,
                'http_errors' => false
            ]);

            $response = json_decode($result->getBody()->getContents(), true);
            Log::info('QUERY HACIENDA RESPONSE: '. json_encode($response));
            if (isset($response['status']) && $response['status'] == 200) {
                $fileMH = false;
                if( $invoice->document_type == "03" ){
                    $pathMH = 'empresa-' . $company->id_number . "/notas_credito_ventas/$invoice->year/$invoice->month/MH-$key.xml";
                    $fileMH = Storage::put(
                        $pathMH,
                        ltrim($response['data']['mensajeHacienda'], '\n')
                    );
                } else {
                    $pathMH = 'empresa-' . $company->id_number . "/facturas_ventas/$invoice->year/$invoice->month/MH-$key.xml";
                    $fileMH = Storage::put(
                        $pathMH,
                        ltrim($response['data']['mensajeHacienda'], '\n')
                    );
                }
                
                $xmlHacienda = null;
                if ($fileMH) {
                    $xmlHacienda = XMLHacienda::updateOrCreate(
                        [
                          'invoice_id' => $invoice->id
                        ],
                        [
                            'xml_message' => $pathMH
                        ]
                    );
                    $file = Storage::get($pathMH);
                }
                
                if (strpos($response['data']['response'],"ESTADO=rechazado") !== false) {
                    if($findKey){
                        if($invoice->company_id == '1110'){
                            $retry = $this->retryForSM($invoice, $token, $company);
                        }else{
                            $retry = $this->queryHacienda($this->setTempKey($invoice), $token, $company, false);
                        }
                        if($retry){
                            return $retry;
                        }
                    }
                    $invoice->hacienda_status = '04';
                    $invoice->save();
                } else if (strpos($response['data']['response'],"ESTADO=aceptado") !== false) {
                    $invoice->hacienda_status = '03';
                    $invoice->save();
                    
                    if($initialStatus == '05' && $fileMH && isset($xmlHacienda) ){
                        $path = $invoiceUtils->getXmlPath( $invoice, $company );
                        $invoiceUtils->sendInvoiceNotificationEmail( $invoice, $company, $path, $pathMH, true);
                    }
                } else if (strpos($response['data']['response'],"ESTADO=procesando") !== false) {
                    $invoice->hacienda_status = '05';
                    $invoice->save();
                    return false;
                }
            } else {
                if($findKey){
                    if($invoice->company_id == '1110'){
                        $retry = $this->retryForSM($invoice, $token, $company);
                    }else{
                        $retry = $this->queryHacienda($this->setTempKey($invoice), $token, $company, false);
                    }
                    if($retry){
                        return $retry;
                    }
                }
                return false;
            }
            return $file;
        } catch (\Exception $e) {
            Log::error('Validate User Atv Response -->>' . $e);
            return false;
        }
    }
    
    public function setTempKey($invoice){
        $apiResponse = ApiResponse::select('id','company_id','invoice_id','created_at','document_key')
                                ->where('company_id', $invoice->company_id)
                                ->where('invoice_id', $invoice->id)
                                ->orderBy('created_at','asc')
                                ->first();
        $newKey = $apiResponse->document_key;
        $invoice->document_key = $newKey;
        
        Log::info("QUERY HACIENDA: Generando otra llave $newKey");
        return $invoice;
    }
    
    public function retryForSM($invoice, $token, $company){
        $apiResponses = ApiResponse::select('id','company_id','invoice_id','created_at','document_key')
                ->where('company_id', $invoice->company_id)
                ->where('invoice_id', $invoice->id)
                ->orderBy('created_at','asc')->get();
        //Recorre las veces que ha intentado en SM
        foreach($apiResponses as $apiResponse){
            $responseDate = $apiResponse->created_at;
            $shortDate = str_pad($apiResponseDate->day, 2, "0", STR_PAD_LEFT) . str_pad($apiResponseDate->month, 2, "0", STR_PAD_LEFT);
            $documentKey = $invoice->document_key;
            $newKey = substr_replace($documentKey, $shortDate, 3, 4);
            $invoice->document_key = $newKey;
            //El primero en devolver un archivo, lo devuelve
            $retry = $this->queryHacienda($invoice, $token, $company, false);
            if($retry){
                return $retry;
            }
        }
        return false;
    }

    private function setHaciendaInfo($company) {
        try {
            $companyData = null;
            $request = null;
            $companyData = array(
                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
                'atvcertFile' => Storage::get($company->atv->key_url),
            );
            foreach ($companyData as $key => $values) {
                if ($key == 'atvcertFile') {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values,
                        'filename' => $company->id_number.'.p12'
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
            Log::info('Func setHacienda: Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

    private function setInvoiceInfo($invoice, $company) {
        try {
            $companyData = null;
            $request = null;
            $companyData = array(
                'clave' => $invoice,
                'cedula_emisor' => $company->id_number,
                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
                'atvcertFile' => Storage::get($company->atv->key_url),
            );
            foreach ($companyData as $key => $values) {
                if ($key == 'atvcertFile') {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values,
                        'filename' => $company->id_number.'.p12'
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
            Log::info('Func InvoiceInfo: Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }
}
