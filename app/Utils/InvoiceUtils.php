<?php

namespace App\Utils;


use App\Company;
use App\Invoice;

use App\AvailableInvoices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceUtils
{

    public function streamPdf( $invoice, $company )
    {
        $pdf = PDF::loadView('Pdf/invoice', [
            'data_invoice' => $invoice,
            'company' => $company
        ]);
        
        return $pdf->stream('Invoice.pdf');
    }
	
	
		public function downloadPdf( $invoice, $company )
    {
        $pdf = PDF::loadView('Pdf/invoice', [
            'data_invoice' => $invoice,
            'company' => $company
        ]);
        
        return $pdf->download('Invoice.pdf');
    }
    
    public function downloadXml( $invoice, $company )
    {
        $xml = $invoice->xmlHacienda;
        
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
        	$cedulaCliente = $invoice->client_id_number;
        	$consecutivoComprobante = $invoice->document_number;
        	
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
        
        return $file;
    }
    
    public function sendInvoiceEmail( $invoice, $company, $xmlPath ) {
        
        try{
            $cc = [];
            //Primero revisa si el invoice tiene un client_id
            if ( isset( $invoice->client_id ) ) {
                $client_billing_emails = $invoice->client->billing_emails;
                if ( isset($client_billing_emails) ){
                    //Si existen, empieza con eso.
                    $arr = explode(",", $client_billing_emails);
                    foreach($arr as $correo) {
                        $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
                        
                        // Validate e-mail
                        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                             array_push( $cc,  $correo );
                        }
                       
                    }
                }
            }
            
            //Si ademas de los billing emails se tiene send_emails, tambien los agrega.
            if( isset($invoice->send_emails) ) {
                array_push( $cc,  $invoice->send_emails );
            }
            
            if( isset($invoice->client_email) ) {
                array_push( $cc, $invoice->client_email );
            }
            
            if ( !empty($cc) ) {
                Mail::to($cc)->send(new \App\Mail\Invoice(['xml' => $xmlPath,
                    'data_invoice' => $invoice, 'data_company' =>$company]));
            } else {
                Mail::to($invoice->client_email)->send(new \App\Mail\Invoice(['xml' => $xmlPath,
                    'data_invoice' => $invoice, 'data_company' =>$company]));
            }
            Log::info('Se enviaron correos con PDF y XML: ' .$invoice->id );
        }catch( \Throwable $e ){
            Log::error('Fallo el envío de correos: ' .$invoice->id );
        }
    }
    
     public function sendInvoiceNotificationEmail( $invoice, $company, $xmlPath ) {
        
        try{
            $cc = [];
            //Primero revisa si el invoice tiene un client_id
            if ( isset( $invoice->client_id ) ) {
                $client_billing_emails = $invoice->client->billing_emails;
                if ( isset($client_billing_emails) ){
                    //Si existen, empieza con eso.
                    $arr = explode(",", $client_billing_emails);
                    foreach($arr as $correo) {
                        $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
                        
                        // Validate e-mail
                        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                             array_push( $cc,  $correo );
                        }
                       
                    }
                }
            }
            
            //Si ademas de los billing emails se tiene send_emails, tambien los agrega.
            if( isset($invoice->send_emails) ) {
                array_push( $cc,  $invoice->send_emails );
            }
            
            if( isset($invoice->client_email) ) {
                array_push( $cc, $invoice->client_email );
            }
            
            if ( !empty($cc) ) {
                Mail::to($cc)->send(new \App\Mail\InvoiceNotification([	
                                        'xml' => $xmlPath,	
                                        'data_invoice' => $invoice, 'data_company' => $company,	
                                        'xml' => ltrim($response['data']['response'], '\n')	
                                    ]));
            } else {
                Mail::to($invoice->client_email)->send(new \App\Mail\InvoiceNotification([	
                                        'xml' => $xmlPath,	
                                        'data_invoice' => $invoice, 'data_company' => $company,	
                                        'xml' => ltrim($response['data']['response'], '\n')	
                                    ]));
            }
            Log::info('Se enviaron correos con PDF y XML: ' .$invoice->id );
        }catch( \Throwable $e ){
            Log::error('Fallo el envío de correos: ' .$invoice->id );
        }
    }
    
    
    public function getXmlPath( $invoice, $company )
    {
        $xml = $invoice->xmlHacienda;
        
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
        	$cedulaCliente = $invoice->client_id_number;
        	$consecutivoComprobante = $invoice->document_number;
        	
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
