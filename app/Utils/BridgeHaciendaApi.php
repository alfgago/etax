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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;


class BridgeHaciendaApi
{
    public function login() {
        try {
            $value = Cache::remember('token-api-'.currentCompany(), '60000', function () {
                $client = new Client();
                $result = $client->request('POST', env('API_HACIENDA_URL') . '/index.php/auth/login', [
                    'headers' => [
                        'Auth-Key'  => env('API_HACIENDA_KEY'),
                        'Client-Service' => env('API_HACIENDA_CLIENT'),
                        'Connection' => 'Close'
                    ],
                    'json' => ["username" => env('API_HACIENDA_USERNAME'),
                        "password" => env('API_HACIENDA_PASSWORD')
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
            Log:info('Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

    public function createInvoice(Invoice $invoice, $token) {
        try {
            $datails = $this->setDetails($invoice->items);
            $data = $this->setInvoiceData($invoice);
            $client = new Client();
            $result = $client->request('POST', env('API_HACIENDA_URL') . '/index.php/invoice/create',
                [
                'headers' => [
                    'Auth-Key'  => env('API_HACIENDA_KEY'),
                    'Client-Service' => env('API_HACIENDA_CLIENT'),
                    'Authorization' => $token,
                    'User-ID' => env('API_HACIENDA_USER_ID'),
                    'Connection' => 'Close'
                ],
                'json' => ["username" =>env('API_HACIENDA_USERNAME'),
                    "password" => env('API_HACIENDA_PASSWORD')], 'verify' => false,
            ]);
            $tokenApi = json_decode($result->getBody()->getContents(), true);
            if (isset($tokenApi['status']) && $tokenApi['status'] == 200) {
                return $tokenApi['data']['token'];
            }
            return false;
        } catch (ClientException $error) {
            Log:info('Error al iniciar session en API HACIENDA -->>'. $error);
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

    private function setInvoiceData($data) {
        try {
            //@TODO Mapeando parametros para enviar al API
            $company = currentCompanyModel();
            $ref = getInvoiceReference($company->last_invoice_ref_number) + 1;
            dd($company, $data, $data['generated_date']->toDateTimeString());
            $invoiceData = null;
            $invoiceData = array(
                'consecutivo' => $ref ?? '',
                'fecha_emision' => $data['generated_date']->toDateTimeString() ?? '',
                'receptor_nombre' => $data['client_first_name'].' '.$data['client_last_name'],
                'receptor_ubicacion_provincia' => $value['unit_price'] ?? '',
                'receptor_ubicacion_canton' => $value['subtotal'] ?? '',
                'receptor_ubicacion_distrito' => $value['item_count'] * $value['unit_price'] ?? '',
                'receptor_ubicacion_otras_senas' => $value['subtotal'] + $value['iva_amount'] ?? '',
                'receptor_email' => $value['discount'] ?? '',
                'receptor_cedula_numero' => $value['iva_amount'] ?? '',
                'receptor_postal_code' => $value['iva_amount'] ?? '',
                'codigo_moneda' => $value['iva_amount'] ?? '',
                'tipo_documento' => $value['iva_amount'] ?? '',
                'sucursal_nro' => $value['iva_amount'] ?? '',
                'terminal_nro' => $value['iva_amount'] ?? '',
                'emisor_name' => $value['iva_amount'] ?? '',
                'emisor_email' => $value['iva_amount'] ?? '',
                'emisor_company' => $value['iva_amount'] ?? '',
                'emisor_city' => $value['iva_amount'] ?? '',
                'emisor_state' => $value['iva_amount'] ?? '',
                'emisor_postal_code' => $value['iva_amount'] ?? '',
                'emisor_country' => $value['iva_amount'] ?? '',
                'emisor_phone' => $value['iva_amount'] ?? '',
                'emisor_cedula' => $value['iva_amount'] ?? '',
                'usuarioAtv' => $value['iva_amount'] ?? '',
                'passwordAtv' => $value['iva_amount'] ?? '',
                'tipoAmbiente' => $value['iva_amount'] ?? '',
                'atvcertPin' => $value['iva_amount'] ?? '',
                'atvcertFile' => $value['iva_amount'] ?? ''
            );
            return $invoiceData;
        } catch (ClientException $error) {
            Log:info('Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

}