<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
use App\Bill;
use App\XmlHacienda;
use Carbon\Carbon;
use App\GoSocketData;
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
use Illuminate\Support\Facades\Cache;

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
            
            $gsData = GoSocketData::where( "company_id", $companyId )->where('document_id',$factura['DocumentId'])->first();
            if( !isset($gsData) ){
                $gsResponse = GoSocketData::create([
                  'company_id' => $companyId,  
                  'document_id' => $factura['DocumentId']
                ]);
            }
            
            if( !isset($gsResponse->bill_id) && !isset($gsResponse->invoice_id) ){
            
                if( !isset($gsResponse->gs_xml) ){
                    $gsResponse = $apiGoSocket->getXML($token, $factura['DocumentId']);
                    $gsData->gs_response = $gsResponse;
                    $gsData->save();
                }else{
                    $gsResponse = $gsResponse->gs_xml;
                }
                
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
                        $invoice = Invoice::select('company_id, id, document_key')->where('company_id', $companyId)->where('document_key', $clave)->first();
                        if(!$invoice){
                            $invoice = Invoice::saveInvoiceXML( $arr, 'GS' );
                            if( $invoice ) {
                                $gsData->invoice_id = $invoice->id;
                                $gsData->save();
                                Invoice::storeXML( $invoice, $xml );
                            }
                        }else{
                            $gsData->invoice_id = $invoice->id;
                            $gsData->save();
                        }
                    }else{
                        Log::warning("GS factura no calza: $identificacionEmisor, company->id_number, ".$factura['DocumentId']);
                    }
                }else{
                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionReceptor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3
                        $bill = Bill::select('company_id, id, document_key')->where('company_id', $companyId)->where('document_key', $clave)->first();
                        if(!$bill){
                            $bill = Bill::saveBillXML( $arr, 'GS' );
                            if( $bill ) {
                                $gsData->bill_id = $bill->id;
                                $gsData->save();
                                Bill::storeXML( $bill, $xml );
                            }
                        }else{
                            $gsData->bill_id = $bill->id;
                            $gsData->save();
                        }
                    }else{
                        Log::warning("GS factura no calza: $identificacionReceptor, company->id_number, ".$factura['DocumentId']);
                    }
                }
                $company->save();
            }
        }catch(\Exception $e){
            Log::error('Error en GS XML: ' . $e->getMessage());
        }
    }

}
