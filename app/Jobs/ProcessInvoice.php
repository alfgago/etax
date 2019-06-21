<?php

namespace App\Jobs;

use App\Company;
use App\Invoice;
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
            Log::info('send job invoice id: '.$this->invoiceId);
            $client = new Client();
            $invoice = Invoice::find($this->invoiceId);
            $company = Company::find($this->companyId);
            $requestDetails = $this->setDetails($invoice->items);
            $requestData = $this->setInvoiceData($invoice, $requestDetails);
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
            if ($requestData !== false) {
                Log::info('Enviando Request  API HACIENDA -->>'.$this->invoiceId);
                $result = $client->request('POST', config('etax.api_hacienda_url') . '/index.php/invoice/create', [
                    'headers' => [
                        'Auth-Key'  => config('etax.api_hacienda_key'),
                        'Client-Service' => config('etax.api_hacienda_client'),
                        'Authorization' => $tokenApi,
                        'User-ID' => config('etax.api_hacienda_user_id'),
                        'Connection' => 'Close'
                    ],
                    'multipart' => $requestData,
                    'verify' => false,
                    'connect_timeout' => 20
                ]);

                $response = json_decode($result->getBody()->getContents(), true);
                if (isset($response['status']) && $response['status'] == 200) {
                    Log::info('API HACIENDA 200 -->>'.$result->getBody()->getContents());
                    $date = Carbon::now();
                    $invoice->hacienda_status = 3;
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
                        Mail::to($invoice->client_email)->send(new \App\Mail\InvoiceNotification(['xml' => $path,
                            'data_invoice' => $invoice, 'data_company' => $company,
                            'xml' => ltrim($response['data']['response'], '\n')]));
                    }
                }
                Log::error('ERROR Enviando parametros  API HACIENDA Invoice: '.$this->invoiceId.'-->>'.$result->getBody()->getContents());
            }
        } catch (\Exception $e) {
            Log::error('ERROR Enviando parametros  API HACIENDA Invoice: '.$this->invoiceId.'-->>'.$e);
        }
    }

    private function setInvoiceData(Invoice $data, $details) {
        try {
            $company = $data->company;
            $ref = getInvoiceReference($company->last_invoice_ref_number);
            $data->reference_number = $ref;
            $data->save();
            $receptorPostalCode = $data['client_zip'];
            $invoiceData = null;
            $request = null;
            $invoiceData = [
                'consecutivo' => $ref ?? '',
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
            Log:info('Error al crear data para request en API HACIENDA -->>'. $error);
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
}
