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

    private $integracion = '';
    private $companyId = '';
    private $queryDates = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($integracion = '', $companyId = '', $queryDates = '') {
        $this->integracion = $integracion;
        $this->companyId = $companyId;
        $this->queryDates = $queryDates;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getInvoices($this->integracion, $this->companyId, $this->queryDates);
        $this->getBills($this->integracion, $this->companyId, $this->queryDates);
    }

     private function getInvoices($integracion, $companyId, $queryDates) {

        try{
            $token = $integracion->session_token;
            $apiGoSocket = new BridgeGoSocketApi();
            $tiposFacturas = $apiGoSocket->getDocumentTypes($token);
            if (is_array($tiposFacturas)) {
                foreach ($tiposFacturas as $tipoFactura) {
                    $facturas = $apiGoSocket->getSentDocuments($token, $integracion->company_token, $tipoFactura, $queryDates);
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
            }
        }catch( \Exception $ex ) {
            Log::error("Error en sincronizar invoices gosocket ".$ex);
        }catch( \Throwable $ex ) {
            Log::error("Error en sincronizar invoices gosocket ".$ex);
        }
    }


    private function getBills($integracion, $companyId, $queryDates) {

        try{
            $token = $integracion->session_token;
            $apiGoSocket = new BridgeGoSocketApi();
            $tiposFacturas = $apiGoSocket->getDocumentTypes($token);
            if (is_array($tiposFacturas)) {
                foreach ($tiposFacturas as $tipoFactura) {
                    $facturas = $apiGoSocket->getReceivedDocuments($token, $integracion->company_token, $tipoFactura, $queryDates);

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
            }
        }catch( \Exception $ex ) {
            Log::error("Error en sincronizar bills gosocket ".$ex);
        }catch( \Throwable $ex ) {
            Log::error("Error en sincronizar bills gosocket ".$ex);
        }
    }
}
