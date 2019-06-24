<?php

namespace App\Http\Controllers;

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
    }

    
    public function receiveEmailXML(Request $request) {
        Log::info( "Se recibió una solicitud de factura por correo electrónico." );
        
        try {  
            $file = $request->file('attachment1');
            EmailController::processAttachment( $file );
        }catch( \Throwable $ex ){
            Log::warning( "Hubo un error durante el proceso de guardar la factura via Email. Mensaje:" . $ex->getMessage());
        }
        
        try {
            $file2 = $request->file('attachment2');
            EmailController::processAttachment( $file2 );
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
        
        try {  
            if( Bill::saveBillXML( $arr, 'XML' ) ) {
                $company = Company::where('id_number', $identificacionReceptor)->first();
                $bill = Bill::where('company_id', $company->id)->where('document_number', $consecutivoComprobante)->first();
                Bill::storeXML( $bill, $file );
            }
        }catch( \Throwable $ex ){
            Log::warning( "No se pudo guardar la factura de compra via Email. Mensaje:" . $ex->getMessage());
        }
       
        try {  
            if( Invoice::saveInvoiceXML( $arr, 'XML' ) ) {
                $company = Company::where('id_number', $identificacionEmisor)->first();
                $invoice = Invoice::where('company_id', $company->id)->where('document_number', $consecutivoComprobante)->first();
                Invoice::storeXML( $invoice, $file );
            }
        }catch( \Throwable $ex ){
            Log::warning( "No se pudo guardar la factura de venta via Email. Mensaje:" . $ex->getMessage());
        }
        
        return true;
        
    }
    
    

}
