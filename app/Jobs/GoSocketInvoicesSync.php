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
use App\Jobs\GSProcessXMLFile;
use Illuminate\Support\Facades\Cache;

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
        $cachekey = "avoid-duplicate-GS-$this->companyId-$this->queryDates";
        if ( Cache::has($cachekey) ) {
            Log::debug( "Esta volviendo a entrar en los mismos 15mins");
            return false;
        }
        Cache::put($cachekey, true, 900);
        
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
                        GSProcessXMLFile::dispatch($factura, $token, $companyId, 'I')->onQueue('gosocket');
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
                //foreach ($tiposFacturas as $tipoFactura) {
                    $tipoFactura = $tiposFacturas[0];
                    $facturas = $apiGoSocket->getReceivedDocuments($token, $integracion->company_token, $tipoFactura, $queryDates);
                    foreach ($facturas as $factura) {
                        GSProcessXMLFile::dispatch($factura, $token, $companyId, 'B')->onQueue('gosocket');
                    }
                //}
            }
        }catch( \Exception $ex ) {
            Log::error("Error en sincronizar bills gosocket ".$ex);
        }catch( \Throwable $ex ) {
            Log::error("Error en sincronizar bills gosocket ".$ex);
        }
    }
}
