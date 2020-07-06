<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
use App\Bill;
use App\XmlHacienda;
use Carbon\Carbon;
use App\Utils\BridgeGoSocketApi;
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

class GSProcessXMLFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $factura = null;
    private $token = null;
    private $companyId = null;
    private $type = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($factura, $token, $companyId, $type)
    {
        $this->factura = $factura;
        $this->token = $token;
        $this->companyId = $companyId;
        $this->type = $type;
    }

    /**
     * Este job se encarga de procesar las facturas de GS, una por una, usando el XML que sale de GS. Es llamado desde otro Job de GoSocketInvoicesSync.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $factura = $this->factura;
            $token = $this->token;
            $companyId = $this->companyId;
            $type = $this->type;
            
            $apiGoSocket = new BridgeGoSocketApi();
            $gsResponse = $apiGoSocket->getXML($token, $factura['DocumentId']);
            $company = Company::find($companyId);
            $xml  = base64_decode($gsResponse);
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
            $clave = $arr['Clave'];
            
            //Log::debug('GS XML: ' . $json);
            if($type == "I"){
                //Compara la cedula de Emisor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionEmisor ) ) {
                    //Registra el XML. Si todo sale bien, lo guarda en S3.
                    Invoice::saveInvoiceXML( $arr, 'GS' );
                }
            }else{
                //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionReceptor ) ) {
                    //Registra el XML. Si todo sale bien, lo guarda en S3
                    Bill::saveBillXML( $arr, 'GS' );
                }
            }
            $company->save();
        }catch(\Exception $e){
            Log::error('Error en GS XML: ' . $e->getMessage());
        }
    }

}
