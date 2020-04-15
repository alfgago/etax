<?php

namespace App\Jobs;

use App\Bill;
use App\Company;
use App\Invoice;
use Illuminate\Support\Facades\Log;
use App\Utils\BridgeGoSocketApi;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GoSocketInvoicesSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user = '';
    private $companyId = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user = '', $companyId = '') {
        $this->user = $user;
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getInvoices($this->user, $this->companyId);
        $this->getBills($this->user, $this->companyId);
    }

     private function getInvoices($user, $companyId) {

        Log::info("getInvoices ");
        Log::info($user);
        Log::info($companyId);
        try{
            $token = $user->session_token;
            $apiGoSocket = new BridgeGoSocketApi();
            $tipos_facturas = $apiGoSocket->getDocumentTypes($token);
            foreach ($tipos_facturas as $tipo_factura) {
                $facturas = $apiGoSocket->getSentDocuments($token, $user->company_token, $tipo_factura);
                foreach ($facturas as $factura) {
                    $APIStatus = $apiGoSocket->getXML($token, $factura['DocumentId']);
                    $company = Company::find($companyId);
                    $xml  = base64_decode($APIStatus);
                    $xml = simplexml_load_string( $xml);
                    $json = json_encode( $xml );
                    $arr = json_decode( $json, TRUE );
                    try {
                        $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0 ;
                    } catch(\Exception $e) {
                        $identificacionReceptor = 0;
                    };

                    $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                    $consecutivoComprobante = $arr['NumeroConsecutivo'];

                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionEmisor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3.
                        Invoice::saveInvoiceXML( $arr, 'GS' );
                    }
                    $company->save();
                }
            }
        }catch( \Exception $ex ) {
            Log::error("Error en sincronizar invoices gosocket ".$ex);
        }catch( \Throwable $ex ) {
            Log::error("Error en sincronizar invoices gosocket ".$ex);
        }
    }


    private function getBills($user, $companyId) {

        Log::info("getbills ");
        try{
            $token = $user->session_token;
            $apiGoSocket = new BridgeGoSocketApi();
            $tipos_facturas = $apiGoSocket->getDocumentTypes($token);
            foreach ($tipos_facturas as $tipo_factura) {
                $facturas = $apiGoSocket->getReceivedDocuments($token, $user->company_token, $tipo_factura);

                foreach ($facturas as $factura) {
                    $APIStatus = $apiGoSocket->getXML($token, $factura['DocumentId']);
                    $company = Company::find($companyId);
                    $xml  = base64_decode($APIStatus);
                    $xml = simplexml_load_string( $xml);
                    $json = json_encode( $xml );
                    $arr = json_decode( $json, TRUE );
                    $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0;
                    $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                    $consecutivoComprobante = $arr['NumeroConsecutivo'];
                    $clave = $arr['Clave'];
                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionReceptor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3
                        Bill::saveBillXML( $arr, 'GS' );
                    }
                    $company->save();
                }
            }
        }catch( \Exception $ex ) {
            Log::error("Error en sincronizar bills gosocket ".$ex);
        }catch( \Throwable $ex ) {
            Log::error("Error en sincronizar bills gosocket ".$ex);
        }
    }
}
