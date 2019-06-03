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
        $file = $request->file('attachment1');
        
        try {  
            Log::info( "Se recibió una factura de compra por correo electrónico." );
            $xml = simplexml_load_string( file_get_contents($file) );
            $json = json_encode( $xml ); // convert the XML string to JSON
            $arr = json_decode( $json, TRUE );
            
            $identificacionReceptor = $arr['Receptor']['Identificacion']['Numero'];
            $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
            $consecutivoComprobante = $arr['NumeroConsecutivo'];
            
            if( Bill::saveBillXML( $arr, 'Email' ) ) {
                Bill::storeXML( $file, $consecutivoComprobante, $identificacionEmisor, $identificacionReceptor );
            }
            
            if( Invoice::saveInvoiceXML( $arr, 'Email' ) ) {
                Invoice::storeXML( $file, $consecutivoComprobante, $identificacionEmisor, $identificacionReceptor );
            }
            
            return response()->json([
                'success' => 'Exito'
            ], 200);
            
        }catch( \Throwable $ex ){
            Log::error( "Hubo un error al guardar la factura. Mensaje:" . $ex->getMessage());
            return 500;
        }
        
    }
    
    

}
