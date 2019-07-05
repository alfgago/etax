<?php

namespace App\Utils;


use App\Company;
use App\Invoice;

use App\AvailableInvoices;
use App\Variables;
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
                                        'data_invoice' => $invoice, 
                                        'data_company' => $company	
                                    ]));
            } else {
                Mail::to($invoice->client_email)->send(new \App\Mail\InvoiceNotification([	
                                        'xml' => $xmlPath,	
                                        'data_invoice' => $invoice, 
                                        'data_company' => $company
                                    ]));
            }
            Log::info('Se enviaron correos de notififación de factura aprobada: ' .$invoice->id );
        }catch( \Throwable $e ){
            Log::error('Fallo el envío de correos de notififación de factura aprobada: ' .$invoice->id );
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
    
    
    public function setDetails43($data) {
        try {
            $details = null;
            foreach ($data as $key => $value) {
                $details[$key] = array(
                    'cantidad' => $value['item_count'] ?? '',
                    'unidadMedida' => $value['measure_unit'] ?? '',
                    'detalle' => $value['name'] ?? '',
                    'precioUnitario' => $value['unit_price'] ?? '',
                    'subtotal' => $value['subtotal'] ?? '',
                    'montoTotal' => $value['item_count'] * $value['unit_price'] ?? '',
                    'montoTotalLinea' => $value['subtotal'] + $value['iva_amount'] ?? '',
                    'descuento' => $value['discount'] ?? '',
                    'impuesto_codigo' => '01',
                    'tipo_iva' => $value['iva_type'],
                    'impuesto_codigo_tarifa' => Variables::getCodigoTarifaVentas($value['iva_type']),
                    'impuesto_tarifa' => $value['iva_percentage'] ?? '',
                    'impuesto_factor_IVA' => $value['iva_percentage'] / 100,
                    'impuesto_monto' => $value['iva_amount'] ?? '',
                    'exoneracion_tipo_documento' => $value['exoneration_document_type'] ?? '',
                    'exoneracion_numero_documento' => $value['exoneration_document_number'] ?? '',
                    'exoneracion_fecha_emision' => $value['exoneration_date'] ?? '',
                    'exoneracion_porcentaje' => $value['exoneration_porcent'] ?? '',
                    'exoneracion_monto' => $value['exoneration_amount'] ?? '',
                    'exoneracion_company' => $value['exoneration_company_name'] ?? '',
                    'impuesto_neto' => $value['impuesto_neto'] ?? '',
                    'base_imponible' => 0,
                );
            }
            return json_encode($details, true);
        } catch (ClientException $error) {
            Log::error('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
            return false;
        }
    }
    
    public function setInvoiceData43( Invoice $data, $details ) {
        try {
            $company = $data->company;
            /*$ref = getInvoiceReference($company->last_invoice_ref_number) + 1;
            $data->reference_number = $ref;
            $data->save();*/
            $ref = $data->reference_number;
            $receptorPostalCode = $data['client_zip'];
            $invoiceData = null;
            $request = null;
            $totalServiciosGravados = 0;
            $totalServiciosExentos = 0;
            $totalMercaderiasGravadas = 0;
            $totalMercaderiasExentas = 0;
            $totalDescuentos = 0;
            $totalImpuestos = 0;
            $itemDetails = json_decode($details);
            //Spe, St, Al, Alc, Cm, I, Os
            foreach ($itemDetails as $detail){

                if($detail->unidadMedida == 'Sp' || $detail->unidadMedida == 'Spe' || $detail->unidadMedida == 'St'
                    || $detail->unidadMedida == 'Al' || $detail->unidadMedida == 'Alc' || $detail->unidadMedida == 'Cm'
                    || $detail->unidadMedida == 'I' || $detail->unidadMedida == 'Os'){

                    if($detail->impuesto_monto == 0 && $detail->tipo_iva > 200 ){
                        $totalServiciosExentos += $detail->montoTotal;
                    }else{
                        $totalServiciosGravados += $detail->montoTotal;
                    }

                } else {

                    if($detail->impuesto_monto == 0 && $detail->tipo_iva > 200 ){
                        $totalMercaderiasExentas += $detail->montoTotal;
                    }else{
                        $totalMercaderiasGravadas += $detail->montoTotal;
                    }
                }
                $totalDescuentos += $detail->descuento;
                $totalImpuestos += $detail->impuesto_monto;
            }
            $totalGravado = $totalServiciosGravados + $totalMercaderiasGravadas;
            $totalExento = $totalServiciosExentos + $totalMercaderiasExentas;
            $totalVenta = $totalGravado + $totalExento;
            $invoiceData = array(
                'consecutivo' => $ref ?? '',
                'fecha_emision' => $data['generated_date']->toDateTimeString() ?? '',
                'codigo_actividad' => str_pad($data['commercial_activity'], 6, '0', STR_PAD_LEFT),
                'receptor_nombre' => $data['client_first_name'].' '.$data['client_last_name'],
                'receptor_ubicacion_provincia' => substr($receptorPostalCode,0,1),
                'receptor_ubicacion_canton' => substr($receptorPostalCode,1,2),
                'receptor_ubicacion_distrito' => substr($receptorPostalCode,3),
                'receptor_ubicacion_otras_senas' => $data['client_address'] ?? '',
                'receptor_otras_senas_extranjero' => $data['foreign_address'] ?? '',
                'receptor_email' => $data['client_email'] ?? '',
                'receptor_cedula_numero' => $data['client_id_number'] ? preg_replace("/[^0-9]/", "", $data['client_id_number']) : '',
                'receptor_postal_code' => $receptorPostalCode ?? '',
                'codigo_moneda' => $data['currency'] ?? '',
                'tipocambio' => $data['currency_rate'] ?? '',
                'tipo_documento' => $data['document_type'] ?? '',
                'sucursal_nro' => '001',
                'terminal_nro' => '00001',
                'emisor_name' => $company->business_name ?? '',
                'emisor_email' => $company->email ?? '',
                'emisor_company' => $company->business_name ?? '',
                'emisor_city' => $company->city ?? '',
                'emisor_state' => $company->state ?? '',
                'emisor_postal_code' => $company->zip ?? '',
                'emisor_country' => $company->country ?? '',
                'emisor_address' => $company->address ?? '',
                'emisor_phone' => $company->phone ?? '',
                'emisor_cedula' => $company->id_number ? preg_replace("/[^0-9]/", "", $company->id_number) : '',
                'usuarioAtv' => $company->atv->user ?? '',
                'passwordAtv' => $company->atv->password ?? '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv->pin ?? '',
                'atvcertFile' => Storage::get($company->atv->key_url),
                'servgravados' => $totalServiciosGravados,
                'servexentos' => $totalServiciosExentos,
                'mercgravados' => $totalMercaderiasGravadas,
                'mercexentos' => $totalMercaderiasExentas,
                'totgravado' => $totalGravado,
                'totexento' => $totalExento,
                'totventa' => $totalVenta,
                'totdescuentos' => $totalDescuentos,
                'totventaneta' => $totalVenta - $totalDescuentos,
                'totimpuestos' => $totalImpuestos,
                'totcomprobante' => $totalVenta + $totalImpuestos,
                'detalle' => $details
            );
            foreach ($invoiceData as $key => $values) {
                if ($key == 'atvcertFile') {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values,
                        'filename' => $invoiceData['emisor_cedula'].'.p12'
                    );
                } else {
                    $request[]=array(
                        'name' => $key,
                        'contents' => $values
                    );
                }
            }
            return $request;
        } catch (ClientException $error) {
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
            return false;
        }
    }
    
}
