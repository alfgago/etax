<?php

namespace App\Jobs;

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
            Log::info('send job credit note id: '.$this->billId);
            $client = new Client();
            $bill = Bill::find($this->billId);
            $company = Company::find($bill->company_id);
            if ( $company->atv_validation ) {
                if ($bill->hacienda_status == '01' && $bill->document_type == '01') {
                    if($bill->xml_schema == 42) {
                        $requestData = $this->setReceptionData($bill, $this->ref);
                    } else {
                        $this->setReceptionData43($bill, $this->ref);
                        return false;
                    }
                    Log::info('Request data'. json_encode($requestData));
                    $apiHacienda = new BridgeHaciendaApi();
                    $tokenApi = $apiHacienda->login(false);
                    if ($requestData !== false) {
                        Log::info('Enviando Request Nota Credito  API HACIENDA -->>' . $this->billId);
                        $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/invoice/aceptacionxml', [
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
                        Log::info('Response Credit Note Api Hacienda '. json_encode($response));
                        if (isset($response['status']) && $response['status'] == 200) {
                            Log::info('API HACIENDA 200 -->>' . $result->getBody()->getContents());
                            $date = Carbon::now();
                            $bill->hacienda_status = 3;
                            $bill->save();
                            $path = 'empresa-' . $company->id_number .
                                "/aceptaciones/$date->year/$date->month/$bill->document_key.xml";
                            $pathFE = 'empresa-' . $company->id_number .
                                "/facturas_compras/$bill->provider_id_number-$bill->document_number.xml";
                            $save = Storage::put(
                                $path,
                                ltrim($response['data']['xmlFirmado'], '\n'));
                            if ($save) {
                                $xml = new XmlHacienda();
                                $xml->invoice_id = 0;
                                $xml->bill_id = $bill->id;
                                $xml->xml = $path;
                                $xml->save();

                                Mail::to($bill->provider_email)->cc($company->email)->send(new ReceptionNotification([
                                    'xml' => $path, 'xmlFE' => $pathFE,  'data_invoice' => $bill,
                                    'data_company' => $company
                                ]));
                            }
                            Log::info('Factura enviada y XML guardado.');
                        } else if (isset($response['status']) && $response['status'] == 400 &&
                            strpos($response['message'], 'ya fue recibido anteriormente') <> false) {
                            $bill->accept_status = 0;
                            $bill->save();
                            Log::info('Failed Job');


                        }
                        Log::info('Proceso de nota de credito finalizado con éxito.');
                    }
                }
            }else {
                Log::warning('El job no se procesó, porque la empresa no tiene un certificado válido: '.$this->billId.'-->>');
            }
        } catch ( \Exception $e) {
            Log::error('ERROR Enviando parametros  API HACIENDA Nota de credito: '.$this->billId.'-->>'.$e);
        }
    }

    private function setReceptionData(Bill $data, $ref) {
        try {
            $company = $data->company;
            $invoiceData = null;
            $request = null;
            $invoiceData = [
                'clave' => $data['document_key'],
                'cedula_emisor' => $data['provider_id_number'],
                'fecha_emision' => $data['generated_date'] ?? '',
                'cod_mensaje' => $data['accept_status'] ?? 1,
                'detalle' => 'Detalle',
                'total' => $data['total'],
                'cedula_receptor' => $company->id_number,
                'consecutivo' => getDocReference('05', $ref),
                'tipo_documento' => '05',

                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
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
            $invoiceData = null;
            $request = null;
            $invoiceData = [
                'clave' => $data['document_key'],
                'cedula_emisor' => $data['provider_id_number'],
                'fecha_emision' => $data['generated_date'] ?? '',
                'cod_mensaje' => $data['accept_status'] ?? 1,
                'detalle' => 'Detalle',
                'total' => $data['total'],
                'cedula_receptor' => $company->id_number,
                'consecutivo' => getDocReference('05', $ref),
                'tipo_documento' => '05',

                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
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
            Log::info('Error al crear parametros en Credit Note API HACIENDA -->>'. $error);
            return false;
        }
    }
}
