<?php

namespace App\Utils;


use App\Company;
use App\Bill;
use App\HaciendaResponse;
use App\Variables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class BillUtils
{

    public function streamPdf( $bill, $company )
    {
        $path = "empresa-$company->id_number/facturas_compras/$bill->year/$bill->month/$bill->document_number.pdf";
    	if ( Storage::exists($path)) {
          $pdf = Storage::get($path);
          if( isset($pdf) ){
            return $pdf;
          }
        }
        
        $pdf = PDF::loadView('Pdf/bill', [
            'data_bill' => $bill,
            'company' => $company
        ]);
        
        return $pdf->stream('Bill.pdf');
    }
	
	
	public function downloadPdf( $bill, $company )
    {
        $path = "empresa-$company->id_number/facturas_compras/$bill->year/$bill->month/$bill->document_number.pdf";
    	if ( Storage::exists($path)) {
          $pdf = Storage::get($path);
          if( isset($pdf) ){
            return $pdf;
          }
        }
        
        $pdf = PDF::loadView('Pdf/bill', [
            'data_bill' => $bill,
            'company' => $company
        ]);
        
        return $pdf->download("$bill->document_key.pdf");
    }
    
    public function downloadXml( $bill, $company, $isResponse = false)
    {
        if($isResponse){
            $response = HaciendaResponse::where('bill_id', $bill->id)->first();
                if($response){
                $path = $response->s3url;
                if ( Storage::exists($path)) {
    	          $file = Storage::get($path);
    	          return $file;
    	        }
            }else{
                return false;
            }
        }
        
        $xml = $bill->xmlHacienda;
        $file = false;
        if( isset($xml) ) {
        	$path = $xml->xml;
        	if ( Storage::exists($path)) {
	          $file = Storage::get($path);
	        }
        }
        
        //Si no encontró el archivo, lo busca en 2 posibles rutas.
        if( !isset($file) ){
        	$cedulaEmpresa = $company->id_number;
        	$cedulaCliente = $bill->client_id_number;
        	$consecutivoComprobante = $bill->document_number;
        	
        	//Lo busca primero dentro de facturas_ventas
        	$path = "empresa-$cedulaEmpresa/facturas_compras/$cedulaCliente-$consecutivoComprobante.xml";
	        if ( Storage::exists($path)) {
	          $file = Storage::get($path);
	        }
	        if( !isset($file) ){
	        	//Lo busca en el root de la empresa
        		$path = "empresa-$cedulaEmpresa/$cedulaCliente-$consecutivoComprobante.xml";
		        if ( Storage::exists($path)) {
		          $file = Storage::get($path);
		        }
	        }
        }
        
        return $file;
    }
    
    public function downloadXmlAceptacion( $bill, $company )
    {
        $xml = $bill->xmlHacienda;
        
        $file = false;
        if( isset($xml) ) {
        	$path = $xml->xml_message;
        	if ( Storage::exists($path)) {
	          $file = Storage::get($path);
	        }
        }
        
        //Si no encontró el archivo, lo busca en 2 posibles rutas.
        if( !isset($file) ){
        	$cedulaEmpresa = $company->id_number;
        	$consecutivoComprobante = $bill->document_number;
        	
        	//Lo busca primero dentro de facturas_ventas
        	$path = "empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/mensaje-$consecutivoComprobante.pdf";
	        if ( Storage::exists($path)) {
	          $file = Storage::get($path);
	        }
        }
        
        return $file;
    }
    
    public function getXmlPath( $bill, $company )
    {
        $xml = $bill->xmlHacienda;
        
        $file = false;
        if( isset($xml) ) {
        	$path = $xml->xml;
        	if ( Storage::exists($path)) {
	          $file = Storage::get($path);
	        }
        }
        
        //Si no encontró el archivo, lo busca en 2 posibles rutas.
        if( !isset($file) ){
        	$cedulaEmpresa = $company->id_number;
        	$cedulaCliente = $bill->client_id_number;
        	$consecutivoComprobante = $bill->document_number;
        	
        	//Lo busca primero dentro de facturas_ventas
        	$path = "empresa-$cedulaEmpresa/facturas_ventas/$cedulaCliente-$consecutivoComprobante.xml";
	        if ( Storage::exists($path)) {
	          $file = Storage::get($path);
	        }
	        if( !isset($file) ){
	        	//Lo busca en el root de la empresa
        		$path = "empresa-$cedulaEmpresa/$cedulaCliente-$consecutivoComprobante.xml";
		        if ( Storage::exists($path)) {
		          $file = Storage::get($path);
		        }
	        }
        }
        
        return $path;
    }
    
}
