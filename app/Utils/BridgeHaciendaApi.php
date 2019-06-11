<?php
/**
 * Created by PhpStorm.
 * User: xavierp
 * Date: 2019-05-31
 * Time: 23:28
 */

namespace App\Utils;

use App\Invoice;
use App\InvoiceItem;
use App\XmlHacienda;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Log;


class BridgeHaciendaApi
{
    public function login() {
        try {
            $value = Cache::remember('token-api-'.currentCompany(), '60000', function () {
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
            });
            return $value;
        } catch (ClientException $error) {
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

    public function createInvoice(Invoice $invoice, $token) {
        try {
            $requestDetails = $this->setDetails($invoice->items);
            $company = currentCompanyModel();
            $requestData = $this->setInvoiceData($invoice, $requestDetails);
            if ($requestData !== false) {
                $client = new Client();
                Log::info('Enviando parametros  API HACIENDA -->>');
                $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/invoice/create', [
                    'headers' => [
                        'Auth-Key'  => config('etax.api_hacienda_key'),
                        'Client-Service' => config('etax.api_hacienda_client'),
                        'Authorization' => $token,
                        'User-ID' => config('etax.api_hacienda_user_id'),
                        'Connection' => 'Close'
                    ],
                    'multipart' => $requestData,
                    'verify' => false,
                ]);
                $response = json_decode($result->getBody()->getContents(), true);
                if (isset($response['status']) && $response['status'] == 200) {
                    $date = Carbon::now();
                    $invoice->hacienda_status = 03;
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
                        Mail::to($invoice->client_email)->send(new \App\Mail\Invoice(['xml' => $path,
                            'data_invoice' => $invoice, 'data_company' =>$company]));
                        return $invoice;
                    }
                }
                return false;
            }
        } catch (ClientException $error) {
            Log:info('Error al crear factura en API HACIENDA -->>'. $error);
            return false;
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
                    'impuesto' => $value['iva_amount'] ?? ''
                );
            }
            return json_encode($details, true);
        } catch (ClientException $error) {
            Log:info('Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

    private function setInvoiceData(Invoice $data, $details) {
        try {
            $company = currentCompanyModel();
            $ref = getInvoiceReference($company->last_invoice_ref_number) + 1;
            $data->reference_number = $ref;
            $data->save();
            $receptorPostalCode = $data['client_zip'];
            $invoiceData = null;
            $request = null;
            $invoiceData = array(
                'consecutivo' => $ref ?? '',
                'fecha_emision' => $data['generated_date']->toDateTimeString() ?? '',
                'receptor_nombre' => $data['client_first_name'].' '.$data['client_last_name'],
                'receptor_ubicacion_provincia' => substr($receptorPostalCode,0,1),
                'receptor_ubicacion_canton' => substr($receptorPostalCode,1,2),
                'receptor_ubicacion_distrito' => substr($receptorPostalCode,3),
                'receptor_ubicacion_otras_senas' => $data['client_address'] ?? '',
                'receptor_email' => $data['client_email'] ?? '',
                'receptor_cedula_numero' => $data['client_id_number'] ?? '',
                'receptor_postal_code' => $receptorPostalCode ?? '',
                'codigo_moneda' => $data['currency'] ?? '',
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
                'emisor_phone' => $company->phone ?? '',
                'emisor_cedula' => $company->id_number ?? '',
                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
                'atvcertFile' => file_get_contents(Storage::url($company->atv->key_url)),
                'detalle' => '{"1": {"cantidad":"1","unidadMedida":"Servicios","detalle":"Honorarios por hora de programacion","precioUnitario":"1130","montoTotal":"1130","subtotal":"1130","montoTotalLinea":"1130", "descuento":0,"impuesto":0}}'
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
            Log:info('Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

}