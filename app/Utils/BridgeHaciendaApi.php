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
use App\Jobs\ProcessCreditNote;
use App\Jobs\ProcessInvoice;
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
            $requestDetails = $this->setDetails43($invoice->items);
            $company = $invoice->company;
            $requestData = $this->setInvoiceData43($invoice, $requestDetails);
            if ($requestData !== false) {
                $client = new Client();
                Log::info('Enviando parametros  API HACIENDA -->>' . $invoice->id);
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
                        Log::info('XML Guardado -->>' . 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml");

                        $invoiceUtils = new InvoiceUtils();
                        $file = $invoiceUtils->sendInvoiceEmail( $invoice, $company, $path );
                        //Send to queue invoice
                        ProcessInvoice::dispatch($invoice->id, $company->id, $token)
                            ->onConnection(config('etax.queue_connections'))->onQueue('invoices');
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
                ->onConnection(config('etax.queue_connections'))->onQueue('invoices');
            return $invoice;

        } catch (ClientException $error) {
            Log::error('Error al crear factura en API HACIENDA -->>'. $error->getMessage() );
            return $invoice;
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

    private function setDetails43($data) {
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
                    'impuesto_codigo' => '01',
                    'impuesto_codigo_tarifa' => Variables::getCodigoTarifaVentas($value['tipo_iva']),
                    'impuesto_tarifa' => $value['iva_percentage'] ?? '',
                    'impuesto_factor_IVA' => $value['iva_percentage'] / 100,
                    'impuesto_monto' => $value['iva_amount'] ?? '',
                    'exoneracion_tipo_documento' => $value['exoneration_document_type'] ?? '',
                    'exoneracion_numero_documento' => $value['exoneration_document_number'] ?? '',
                    'exoneracion_fecha_emision' => $value['exoneration_date'] ?? '',
                    'exoneracion_porcentaje' => $value['exoneration_porcent'] ?? '',
                    'exoneracion_monto' => $value['exoneration_amount'] ?? '',
                    'exoneracion_company' => $value['exoneration_company_name'] ?? '',
                    'impuesto_neto' => $value['impuesto_neto'] ?? '',
                    'base_imponible' => 0,
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
                'emisor_phone' => $company->phone ?? '',
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

    private function setInvoiceData43(Invoice $data, $details) {
        try {
            $company = $data->company;
            $ref = getInvoiceReference($company->last_invoice_ref_number) + 1;
            $data->reference_number = $ref;
            $data->save();
            $receptorPostalCode = $data['client_zip'];
            $invoiceData = null;
            $request = null;
            $totalServiciosGravados = 0;
            $totalServiciosExentos = 0;
            $totalMercaderiasGravadas = 0;
            $totalMercaderiasExentas = 0;
            $totalDescuentos = 0;
            $totalImpuestos = 0;
            $itemDetails = json_decode($details);
            foreach ($itemDetails as $detail){
                if($detail->unidadMedida == 'Sp' && $detail->impuesto_monto == 0){
                    $totalServiciosExentos += $detail->montoTotal;
                }
                if($detail->unidadMedida == 'Sp' && $detail->impuesto_monto > 0){
                    $totalServiciosGravados += $detail->montoTotal;
                }
                if($detail->unidadMedida != 'Sp' && $detail->impuesto_monto == 0){
                    $totalMercaderiasExentas += $detail->montoTotal;
                }
                if($detail->unidadMedida != 'Sp' && $detail->impuesto_monto > 0){
                    $totalMercaderiasGravadas += $detail->montoTotal;
                }
                $totalDescuentos += $detail->descuento;
                $totalImpuestos += $detail->impuesto_monto;
            }
            $totalGravado = $totalServiciosGravados + $totalMercaderiasGravadas;
            $totalExento = $totalServiciosExentos + $totalMercaderiasExentas;
            $totalVenta = $totalGravado + $totalExento;
            $invoiceData = array(
                'consecutivo' => $ref ?? '',
                'fecha_emision' => $data['generated_date']->toDateTimeString() ?? '',
                'codigo_actividad' => str_pad($data['commercial_activity'], 6, '0', STR_PAD_LEFT),
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
                'emisor_phone' => $company->phone ?? '',
                'emisor_cedula' => $company->id_number ? preg_replace("/[^0-9]/", "", $company->id_number) : '',
                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
                'atvcertFile' => Storage::get($company->atv->key_url),
                'servgravados' => $totalServiciosGravados,
                'servexentos' => $totalServiciosExentos,
                'mercgravados' => $totalMercaderiasGravadas,
                'mercexentos' => $totalMercaderiasExentas,
                'totgravado' => $totalGravado,
                'totexento' => $totalExento,
                'totventa' => $totalVenta,
                'totdescuentos' => $totalDescuentos,
                'totventaneta' => $totalVenta - $totalDescuentos,
                'totimpuestos' => $totalImpuestos,
                'totcomprobante' => $totalVenta + $totalImpuestos,
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
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
            return false;
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
}
