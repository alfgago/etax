<?php

namespace App\Jobs;

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

class ProcessInvoice implements ShouldQueue
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
            $invoiceUtils = new InvoiceUtils();
            Log::info('send job invoice id: '.$this->invoiceId);
            $client = new Client();
            $invoice = Invoice::find($this->invoiceId);
            $company = Company::find($this->companyId);
            if ( $company->atv_validation ) {
                if ($invoice->hacienda_status == '01' && $invoice->document_type == '01') {
                    $requestDetails = $this->setDetails43($invoice->items);
                    $requestData = $this->setInvoiceData43($invoice, $requestDetails);
                    $apiHacienda = new BridgeHaciendaApi();
                    $tokenApi = $apiHacienda->login(false);
                    if ($requestData !== false) {
                        Log::info('Enviando Request  API HACIENDA -->>' . $this->invoiceId);
                        $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/invoice43/create', [
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
                        Log::info('Response Api Hacienda '. json_encode($response));
                        if (isset($response['status']) && $response['status'] == 200) {
                            Log::info('API HACIENDA 200 :'. $invoice->document_number);
                            $date = Carbon::now();
                            $invoice->hacienda_status = '03';
                            $invoice->save();
                            $path = 'empresa-' . $company->id_number . "/facturas_ventas/$date->year/$date->month/$invoice->document_key.xml";
                            $save = Storage::put(
                                $path,
                                ltrim($response['data']['xmlFirmado'], '\n')
                            );
                            if ($save) {
                                $xml = new XmlHacienda();
                                $xml->invoice_id = $invoice->id;
                                $xml->bill_id = 0;
                                $xml->xml = $path;
                                $xml->save();
                                
                                $xmlExtract = ltrim($response['data']['response'], '\n');
                                $file = $invoiceUtils->sendInvoiceNotificationEmail( $invoice, $company, $xmlExtract );

                            }
                            Log::info('Factura enviada y XML guardado.');
                        } else if (isset($response['status']) && $response['status'] == 400 &&
                            strpos($response['message'], 'ya fue recibido anteriormente') <> false) {
                            Log::info('Consecutive repeated -->' . $invoice->document_number);
                            $invoice->hacienda_status = '04';
                            $invoice->save();


                        }
                        Log::info('Proceso de facturación finalizado con éxito.');
                    }
                }
            }else {
                Log::warning('El job Invoices no se procesó, porque la empresa no tiene un certificado válido.'.$company->id_number);
            }
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
                'receptor_nombre' => $data['client_first_name'].' '.$data['client_last_name'],
                'receptor_ubicacion_provincia' => substr($receptorPostalCode,0,1),
                'receptor_ubicacion_canton' => substr($receptorPostalCode,1,2),
                'receptor_ubicacion_distrito' => substr($receptorPostalCode,3),
                'receptor_ubicacion_otras_senas' => $data['client_address'] ?? '',
                'receptor_email' => $data['client_email'] ?? '',
                'receptor_cedula_numero' => $data['client_id_number'] ?? '',
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
                'emisor_cedula' => $company->id_number ?? '',
                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
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
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error);
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

    private function setInvoiceData43(Invoice $data, $details) {
        try {
            $company = $data->company;
            $ref = $data->reference_number;
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
                'fecha_emision' => $data['generated_date'] ?? '',
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

}
