<?php

namespace App\Http\Controllers;

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
 
        try {  
            $file = $request->file('attachment1');
            EmailController::processAttachment( $file );
        }catch( \Throwable $ex ){}
        
        try {
            $file2 = $request->file('attachment2');
            EmailController::processAttachment( $file2 );
        }catch( \Throwable $ex ){}
        
        try {
            $file3 = $request->file('attachment3');
            EmailController::processAttachment( $file3 );
        }catch( \Throwable $ex ){}
        
        try {
            $file4 = $request->file('attachment4');
            EmailController::processAttachment( $file4 );
        }catch( \Throwable $ex ){}
        
        try {
            $file5 = $request->file('attachment5');
            EmailController::processAttachment( $file5 );
        }catch( \Throwable $ex ){}
        
        try {
            $file6 = $request->file('attachment6');
            EmailController::processAttachment( $file6 );
        }catch( \Throwable $ex ){}
        
        return response()->json([
            'success' => 'Exito'
        ], 200);
        
        
    }
    
    public static function processAttachment( $file ) {
        
        $xml = simplexml_load_string( file_get_contents($file) );
        $json = json_encode( $xml ); // convert the XML string to JSON
        $arr = json_decode( $json, TRUE );
        
        $identificacionReceptor = $arr['Receptor']['Identificacion']['Numero'];
        $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
        $consecutivoComprobante = $arr['NumeroConsecutivo'];
        $clave = $arr['Clave'];
        
        try {
            $bill = Bill::saveBillXML( $arr, 'Email' );
            if( $bill ) {
                Bill::storeXML( $bill, $file );
                Log::info( "CORREO: Se registró la factura de compra $consecutivoComprobante para la empresa $identificacionReceptor");
                $this->notify(2, $bill->company_id, 'Recibio una factura de compra por correo', 'Se registró la factura de venta $consecutivoComprobante para la empresa $identificacionReceptor','success','EmailController/processAttachment -> saveInvoice','/facturas_ventas/'.$bill->id);
            }
        }catch( \Throwable $ex ){
            Log::warning( "CORREO: No se pudo guardar la factura de compra via Email. Mensaje: $file->getClientOriginalName()" . $ex->getMessage());
            $company = Company::where('id_number',$identificacionReceptor)->first();

            $this->notify(2, $company->id, 'Error recibir factura de venta por correo', '','danger','EmailController/processAttachment -> saveBill','empresa-'.$identificacionReceptor.'/facturas_compras/error/email/'.$file->getClientOriginalName());
            Bill::storeXMLError($identificacionReceptor, $file);
        }
       
        try {
            $invoice = Invoice::saveInvoiceXML( $arr, 'Email' );
            if( $invoice ) {
                Invoice::storeXML( $invoice, $file );
                Log::info( "CORREO: Se registró la factura de venta $consecutivoComprobante para la empresa $identificacionEmisor");
                $this->notify(2, $invoice->company_id, 'Recibio una factura de venta por correo', 'Se registró la factura de venta $consecutivoComprobante para la empresa $identificacionEmisor','success','EmailController/processAttachment -> saveInvoice','/facturas_ventas/'.$invoice->id);
            }
        }catch( \Throwable $ex ){
            Log::warning( "CORREO: No se pudo guardar la factura de venta via Email. Mensaje: $file->getClientOriginalName() " . $ex->getMessage());

            $company = Company::where('id_number',$identificacionEmisor)->first();

            $this->notify(2, $company->id, 'Error recibir factura de venta por correo', '','danger','EmailController/processAttachment -> saveInvoice','empresa-'.$identificacionEmisor.'/facturas_ventas/error/email/'.$file->getClientOriginalName());
            Invoice::storeXMLError($identificacionEmisor, $file);
        }
        
        return true;
        
    }
    
    

}
