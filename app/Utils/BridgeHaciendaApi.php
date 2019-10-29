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
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Log;


class BridgeHaciendaApi
{
    public function login($cache = true) {
        try {
            if ($cache === false) {
                return $this->requestLogin();
            }
            $value = Cache::remember('token-api-'.currentCompany(), '60000', function () {
                return $this->requestLogin();
            });
            return $value;
        } catch (ClientException $error) {
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
            return false;
        }
    }

    public function createInvoice(Invoice $invoice, $token) {
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
                    $path = 'empresa-'.$company->id_number.
                        "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml";
                    $save = Storage::put(
                        $path,
                        ltrim($response['data']['xmlFirmado'], '\n'));
                    
                    if ($save) {
                        $xml = new XmlHacienda();
                        $xml->invoice_id = $invoice->id;
                        $xml->bill_id = 0;
                        $xml->xml = $path;
                        $xml->save();
                        Log::info('XML Guardado -->> ' . 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml");

                        $file = $invoiceUtils->sendInvoiceEmail($invoice, $company, $path);
                        
                        ProcessInvoice::dispatch($invoice->id, $company->id, $token)
                            ->onConnection(config('etax.queue_connections'))->onQueue('invoicing');
                        return $invoice;
                    }
                }else{
                    Log::warning('Error en respuesta de firma -->>'. json_encode($response) );
                }
                return $invoice;
            }
        } catch (ClientException $error) {
            Log::error('Error al crear factura en API HACIENDA -->>'. $error->getMessage() );
            return $invoice;
        }
    }

    public function createCreditNote(Invoice $invoice, $token) {
        try {

            $company = $invoice->company;
            //Send to queue invoice
            ProcessCreditNote::dispatch($invoice->id, $company->id, $token)
                ->onConnection(config('etax.queue_connections'))->onQueue('invoicing');
            return $invoice;

        } catch (ClientException $error) {
            Log::error('Error al crear factura en API HACIENDA -->>'. $error->getMessage() );
            return $invoice;
        }
    }

    public function acceptInvoice(Bill $bill, $token) {
        try {
            $provider = $bill->provider;
            $ref = currentCompanyModel()->last_rec_ref_number;
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
            Log::error('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
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
            Log::error('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
            return false;
        } catch ( \Throwable $error ) {
            Log::error('Error en facturacion -->>'. $error->getMessage() );
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

    public function queryHacienda($invoice, $token, $company) {
        try {
            $client = new Client();
            $file = null;
            Log::info('Consultando Mensaje Hacienda XML  API HACIENDA -->>' . $invoice->id);
            $query = $this->setInvoiceInfo($invoice->document_key, $company);
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
            Log::info('Response Api Hacienda '. json_encode($response));
            if (isset($response['status']) && $response['status'] == 200) {
                if ($invoice->document_type == ('01' || '08' || '09' || '04')) {
                    $pathMH = 'empresa-' . $company->id_number . "/facturas_ventas/$invoice->year/$invoice->month/MH-$invoice->document_key.xml";
                    $saveMH = Storage::put(
                        $pathMH,
                        ltrim($response['data']['mensajeHacienda'], '\n')
                    );
                } else {
                    $pathMH = 'empresa-' . $company->id_number . "/notas_credito_ventas/$invoice->year/$invoice->month/MH-$invoice->document_key.xml";
                    $saveMH = Storage::put(
                        $pathMH,
                        ltrim($response['data']['mensajeHacienda'], '\n')
                    );
                }
                if ($saveMH) {
                    XmlHacienda::where('invoice_id', $invoice->id)->update(['xml_message' => $pathMH]);
                    $file = Storage::get($pathMH);
                }
                if (strpos($response['data']['response'],"ESTADO=rechazado") !== false) {
                    $invoice->hacienda_status = '04';
                    $invoice->save();
                } else if (strpos($response['data']['response'],"ESTADO=aceptado") !== false) {
                    $invoice->hacienda_status = '03';
                    $invoice->save();
                } else if (strpos($response['data']['response'],"ESTADO=procesando") !== false) {
                    $invoice->hacienda_status = '05';
                    $invoice->save();
                }
            } else {
                return false;
            }
            return $file;
        } catch (\Exception $e) {
            Log::info('Validate User Atv Response -->>' . $e);
            return redirect('Invoice/index')->withErrors('Error al  consulta en Hacienda');
        }
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
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
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
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
            return false;
        }
    }
}
