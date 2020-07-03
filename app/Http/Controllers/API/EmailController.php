<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\LogActivityHandler as Activity;
use \Carbon\Carbon;
use App\Bill;
use App\BillItem;
use App\Invoice;
use App\InvoiceItem;
use App\Company;
use App\Provider;
use App\CalculatedTax;
use App\Http\Controllers\CacheController;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use ZipArchive;

/**
 * @group Controller - Emails
 *
 * Funciones de EmailController. Se encarga de recibir los correos electrónicos y registrarlos ya sea en facturas enviadas o en recibidas.
 */
class EmailController extends Controller
{
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['receiveEmailXML']] );
        $this->middleware('CheckSubscription', ['except' => ['receiveEmailXML']] );
    }

    
    public function receiveEmailXML(Request $request) {
        
        $pdf = null;
        $count = intval($request->attachments);
        $email = "";
        try{
            $emailText = $request->to;
            $emailText = str_replace(array('<','>','"'), '',$emailText);
            $emailArray = explode(" ", $emailText);
            $email = implode( array_unique($emailArray) );
        }catch(\Exception $e){
        }
        
        try{
            $emailTextCC = $request->cc;
            $emailTextCC = str_replace(array('<','>','"'), '',$emailTextCC);
            $emailArrayCC = explode(" ", $emailTextCC);
            $email = $email.",".implode( array_unique($emailArrayCC) );
        }catch(\Exception $e){
        }
        
        //Recorre los archivos buscando el PDF o ZIP
        for ($i = 1; $i <= $count; $i++) {
           try{
               $file = $request->file( "attachment$i" );
               $filename = $file->getClientOriginalName();
               $ext = mb_strtolower(substr($filename, -4));
               if( ".pdf" == $ext ){
                   $pdf = $file;
               }
               if( ".zip" == $ext ){
                    $zipFiles = $this->processZip($file); 
                    foreach($zipFiles as $fname => $zipFile){
                        $ext = mb_strtolower(substr($fname, -4));
                        if( ".pdf" == $ext ){
                           $pdf = $zipFile;
                        }
                    }
                    foreach($zipFiles as $fname => $zipFile){
                        $ext = mb_strtolower(substr($fname, -4));
                        if( ".xml" == $ext ){
                           $this->processAttachmentAsXML( $zipFile, $pdf, $email, true );
                        }
                    }
                    foreach($zipFiles as $fname => $zipFile){
                        $ext = mb_strtolower(substr($fname, -4));
                        if( ".xml" == $ext ){
                           Bill::processMessageXML( $zipFile, true );
                        }
                    }
               }
           }catch(\Throwable $e){
               //Log::error("Error en email recibido: " . $e);
           }
        }
        
        //Recorre los archivos buscando el XML.
        for ($i = 1; $i <= $count; $i++) {
           try{
               $file = $request->file( "attachment$i" );
               $filename = $file->getClientOriginalName();
               $ext = mb_strtolower(substr($filename, -4));
               if( ".xml" == $ext ){
                   $this->processAttachmentAsXML( $file, $pdf, $email );
               }
           }catch(\Throwable $e){}
        }
        
        //Recorre los archivos buscando el XML de aceptacion. Tiene que correr el array otravez, porque no sabe en que posicion va a estar el original.
        for ($i = 1; $i <= $count; $i++) {
           try{ 
               $file = $request->file( "attachment$i" );
               $filename = $file->getClientOriginalName();
               $ext = mb_strtolower(substr($filename, -4));
               if( ".xml" == $ext ){
                   Log::debug($filename);
                   Bill::processMessageXML( $file );
               }
           }catch(\Throwable $e){
               Log::error( 'Error procesando mensaje de respuesta'. $e->getMessage() );
           }
        }
        
        return response()->json([
            'success' => 'Exito'
        ], 200);
        
        
    }
    
    private function processZip($file){
        $zip = new ZipArchive; 
        $zipFiles = array();
        if ($zip->open( $file ) === TRUE) 
        {
            for($i = 0; $i < $zip->numFiles; $i++) 
            {   
                $filename = $zip->getNameIndex($i);
                $fp = $zip->getStream($filename);
                $content = "";
                if(!$fp){}else{
                    while (!feof($fp)) {
                        $content .= fread($fp, 8192);
                    }
                    $zipFiles[$filename] = $content;
                }
                fclose($fp);
            }
        }
        return $zipFiles;
    }
    
    private function processAttachmentAsXML( $file, $pdf = null, $email = null, $isStream = false) {
        try{
            if($isStream){
                $xml = simplexml_load_string( ($file) );
            }else{
                $xml = simplexml_load_string( file_get_contents($file) );
            }
            $json = json_encode( $xml ); // convert the XML string to JSON
            $arr = json_decode( $json, TRUE );
            
            $identificacionReceptor = $arr['Receptor']['Identificacion']['Numero'];
            $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
            $consecutivoComprobante = $arr['NumeroConsecutivo'];
            $clave = $arr['Clave'];
            
            try {
                $bill = Bill::saveBillXML( $arr, 'Email', $email );
                if( $bill ) {
                    Log::info( "CORREO: Se registró la factura de compra $consecutivoComprobante para la empresa $identificacionReceptor, correo $email");
                    Bill::storeXML( $bill, $file );
                    if( isset($pdf) ){
                        Bill::storePDF( $bill, $pdf );
                        Log::info( "CORREO: Se guardó un PDF para la factura de compra $consecutivoComprobante");
                    }
                    //$this->notificar(2, $bill->company_id,  $bill->company_id, 'Recibio una factura de compra por correo', 'Se registró la factura de venta $consecutivoComprobante para la empresa $identificacionReceptor','success','EmailController/processAttachment -> saveInvoice','/facturas_ventas/'.$bill->id);
                }
            }catch( \Throwable $ex ){
                Log::warning( "CORREO: No se pudo guardar la factura de compra via Email. Mensaje: $file->getClientOriginalName()" . $ex->getMessage());
                $company = Company::where('id_number',$identificacionReceptor)->first();
    
                Bill::storeXMLError($identificacionReceptor, $file);
                //$this->notificar(2, $company->id,  $company->id, 'Error recibir factura de venta por correo', 'CORREO: No se pudo guardar la factura de compra via Email. Mensaje: '.$file->getClientOriginalName().'','danger','EmailController/processAttachment -> saveBill','empresa-'.$identificacionReceptor.'/facturas_compras/error/email/'.$file->getClientOriginalName());
            }
           
            try {
                $invoice = Invoice::saveInvoiceXML( $arr, 'Email' );
                if( $invoice ) {
                    Log::info( "CORREO: Se registró la factura de venta $consecutivoComprobante para la empresa $identificacionEmisor");
                    Invoice::storeXML( $invoice, $file );
                    //$this->notificar(2, $invoice->company_id, $invoice->company_id, 'Recibio una factura de venta por correo', 'Se registró la factura de venta '. $consecutivoComprobante.' para la empresa '.$identificacionEmisor.'','success','EmailController/processAttachment -> saveInvoice','/facturas_ventas/'.$invoice->id);
                }
            }catch( \Throwable $ex ){
                Log::warning( "CORREO: No se pudo guardar la factura de venta via Email. Mensaje: $file->getClientOriginalName() " . $ex->getMessage());
    
                $company = Company::where('id_number',$identificacionEmisor)->first();
    
                Invoice::storeXMLError($identificacionEmisor, $file);
                //$this->notificar(2, $company->id, $company->id, 'Error recibir factura de venta por correo', 'No se pudo guardar la factura de venta via Email. Mensaje: '.$file->getClientOriginalName().' ','danger','EmailController/processAttachment -> saveInvoice','empresa-'.$identificacionEmisor.'/facturas_ventas/error/email/'.$file->getClientOriginalName());
            }
        }catch(\Throwable $e){
           //Log::error("Error en email recibido: " . $e);
        }
        return true;
    }
    
    

}
