<?php

namespace App\Utils;


use App\Company;
use App\Invoice;

use App\AvailableInvoices;
use App\Provider;
use App\Variables;
use App\XmlHacienda;
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
        $pdfRoute = 'Pdf/invoice';
        $provider = null;
        if( $company->id_number == "3101018968" || $company->id_number == "3101011989" || $company->id_number == "3101166930" || $company->id_number == "3007684555" || $company->id_number == "3130052102" ) {
            $pdfRoute = 'Pdf/custom/corbana';
        }
        if ($invoice->document_type == '08') {
            $provider = Provider::find($invoice->provider_id);
        }
        $pdf = PDF::loadView($pdfRoute, [
            'data_invoice' => $invoice,
            'company' => $company,
            'provider' => $provider
        ]);
        
        return $pdf->stream('Invoice.pdf');
    }
	
	
	public function downloadPdf( $invoice, $company )
    {
        $pdfRoute = 'Pdf/invoice';
        $provider = null;
        if( $company->id_number == "3101018968" || $company->id_number == "3101011989" || $company->id_number == "3101166930" || $company->id_number == "3007684555" || $company->id_number == "3130052102" ) {
            $pdfRoute = 'Pdf/custom/corbana';
        }
        if ($invoice->document_type == '08') {
            $provider = Provider::find($invoice->provider_id);
        }
        $pdf = PDF::loadView($pdfRoute, [
            'data_invoice' => $invoice,
            'company' => $company,
            'provider' => $provider
        ]);
        
        return $pdf->download("$invoice->document_key.pdf");
    }
    
    public function downloadXml( $invoice, $company, $type = '01', $returnPath = false)
    {
        $file = false;
        $xml = XMLHacienda::firstOrCreate([
            "invoice_id" => $invoice->id    
        ]);
        
        if($type == "MH"){
            $path = $xml->xml_message;
        }else{
            $path = $xml->xml;
        }
        if (Storage::exists($path)) {
            $file = Storage::get($path);
            return $returnPath ? $path : $file;
        }
        
        if (!$file) {
            if($type == "MH"){
                $path = 'empresa-' . $company->id_number . "/facturas_ventas/$invoice->year/$invoice->month/MH-$invoice->document_key.xml";
            }else {
                $path = 'empresa-' . $company->id_number . "/facturas_ventas/$invoice->year/$invoice->month/$invoice->document_key.xml";
            }
            
            if ( Storage::exists($path)) {
                $file = Storage::get($path); 
            }
        }
        
        //Si no encontró el archivo, lo busca en rutas que se usaban antes.
        if (!$file) {
        	$cedulaEmpresa = $company->id_number;
        	$cedulaCliente = $invoice->client_id_number;
        	$consecutivoComprobante = $invoice->document_number;
        	
        	
            if($type == "03"){
                $path = 'empresa-' . $company->id_number . "/notas_credito_ventas/$invoice->year/$invoice->month/$invoice->document_key.xml";
                if ( Storage::exists($path)) {
		          $file = Storage::get($path);
		        }
            }
        	if (!$file) {
            	//Lo busca primero dentro de facturas_ventas
            	$path = "empresa-$cedulaEmpresa/facturas_ventas/$cedulaCliente-$consecutivoComprobante.xml";
    	        if (Storage::exists($path)) {
    	          $file = Storage::get($path);
    	        }
        	}
	        if (!$file) {
	        	//Lo busca en el root de la empresa
        		$path = "empresa-$cedulaEmpresa/$cedulaCliente-$consecutivoComprobante.xml";
		        if ( Storage::exists($path)) {
		          $file = Storage::get($path);
		        }
	        }
	        //Lo busca en la ruta de NC
        }
        
        //Si llego hasta aqui es porque el XML no estaba en BD, por esto lo debe guardar.
        if($file){
            if($type == "MH"){
                $xml->xml_message = $path;
            }else{
                $xml->xml = $path;
            }
            $xml->save();
        }
        return $returnPath ? $path : $file;
    }
    
    public function downloadXmlAceptacion( $invoice, $company )
    {
        return $this->downloadXml( $invoice, $company, 'MH');
    }
    
    
    
    public function setRealDocumentKey($invoice){
        $file = $this->downloadXml( $invoice, $invoice->company, $invoice->document_key);
        if( isset($file) ){
            $xml = simplexml_load_string( $file );
            $json = json_encode( $xml ); // convert the XML string to JSON
            $arr = json_decode( $json, TRUE );
            
            $consecutivoComprobante = $arr['NumeroConsecutivo'];
            $clave = $arr['Clave'];
            if( isset($clave) ){
                $invoice->document_number = $consecutivoComprobante;
                $invoice->document_key = $clave;
                $invoice->save();
            }
        }
        return $invoice;
    }
    
    public function sendInvoiceEmail( $invoice, $company, $xmlPath, $pathMh = null) {
        
        try{
            $cc = [];
            //Primero revisa si el invoice tiene un client_id
            if ( isset( $invoice->client_id ) ) {
                $client_billing_emails = trim($invoice->client->billing_emails);
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
            
            try{
                if( isset($invoice->client_email) ) {
                    $emailsArray = explode(',',$invoice->client_email);
                    if( sizeof( $emailsArray ) > 1 ){
                        foreach($emailsArray as $email){
                            $email = replaceAccents($email);
                            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                            array_push( $cc,  $email );
                        }
                    }else{
                        array_push( $cc, $invoice->client_email );
                    }
                }
            }catch(\Exception $e){ Log::error($e->getMessage()); }
            
            //Si ademas de los billing emails se tiene send_emails, tambien los agrega.
            if( isset($invoice->send_emails) ) {
                array_push( $cc,  $invoice->send_emails );
            }
            
                
            if ( !empty($cc) ) {
                Mail::to($cc)->send(new \App\Mail\Invoice([
                    'xml' => $xmlPath,
                    'xmlMH' => $pathMh,
                    'data_invoice' => $invoice, 
                    'data_company' =>$company
                ]));
            } else {
                Mail::to(!empty($invoice->client_email) ? trim($invoice->client_email) : trim($company->email))->send(new \App\Mail\Invoice([
                    'xml' => $xmlPath,
                    'xmlMH' => $pathMh,
                    'data_invoice' => $invoice, 
                    'data_company' =>$company
                ]));
            }
            Log::info('Se enviaron correos con PDF y XML: ' .$invoice->id );
        }catch( \Exception $e ){
            Log::error('Fallo el envío de correos: ' .$invoice->id );
            Log::error('Fallo el envío de correos: ' .$e );
        }
    }
    
     public function sendInvoiceNotificationEmail($invoice, $company, $xmlPath, $xmlMH, $sendPdf = false ) {
        /*if ( !app()->environment('production') ) {
            return false;    
        }*/
        try{
            $cc = [];
            //Primero revisa si el invoice tiene un client_id
            if (isset($invoice->client_id)) {
                $client_billing_emails = replaceAccents($invoice->client->billing_emails);
                if ( isset($client_billing_emails) ){
                    //Si existen, empieza con eso.
                    $arr = explode(",", $client_billing_emails);
                    foreach($arr as $correo) {
                        $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
                        
                        // Validate e-mail
                        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                             array_push($cc,  $correo);
                        }
                       
                    }
                }
            } else {
                array_push( $cc,  $company->email);
            }
            
            try{
                if( isset($invoice->client_email) ) {
                    $emailsArray = explode(',',$invoice->client_email);
                    if( sizeof( $emailsArray ) > 1 ){
                        foreach($emailsArray as $email){
                            $email = replaceAccents($email);
                            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                            array_push( $cc,  $email );
                        }
                    }else{
                        array_push( $cc, $invoice->client_email );
                    }
                }
            }catch(\Exception $e){ Log::error($e->getMessage()); }
            
            //Si ademas de los billing emails se tiene send_emails, tambien los agrega.
            if( isset($invoice->send_emails) ) {
                array_push( $cc,  replaceAccents($invoice->send_emails) );
            }
            
            if( $invoice->document_type != '02' && $invoice->document_type != '03' ){
                //Envia notificaciones de factura
                if (!empty($cc)) {
                    Mail::to($cc)->send(
                        new \App\Mail\InvoiceNotification([	
                            'xml' => $xmlPath,	
                            'data_invoice' => $invoice, 
                            'data_company' => $company,
                            'xmlMH' => $xmlMH,
                            'sendPdf' => $sendPdf
                        ]));
                } else {
                    Mail::to(replaceAccents($invoice->client_email))->send(
                        new \App\Mail\InvoiceNotification([	
                            'xml' => $xmlPath,	
                            'data_invoice' => $invoice, 
                            'data_company' => $company,
                            'xmlMH' => $xmlMH,
                            'sendPdf' => $sendPdf
                        ]));
                }
            }else{
                if (!empty($cc)) {
                    Mail::to($cc)->send(
                        new \App\Mail\CreditNoteNotificacion([	
                            'xml' => $xmlPath,
                            'xml_hacienda' => $xmlMH,
                            'data_invoice' => $invoice, 
                            'data_company' => $company,
                            'sendPdf' => $sendPdf
                        ]));
                } else {
                    Mail::to(replaceAccents($invoice->client_email))->send(
                        new \App\Mail\CreditNoteNotificacion([
                            'xml' => $xmlPath,
                            'xml_hacienda' => $xmlMH,
                            'data_invoice' => $invoice, 
                            'data_company' => $company,
                            'sendPdf' => $sendPdf
                        ])
                    );
                }
            }
            Log::info('Se enviaron correos de notificación de factura aprobada: ' .$invoice->id );
        }catch( \Exception $e ){
            Log::error('Fallo el envío de correos de notificación de factura aprobada: ' .$invoice->id." Error: $e" );
        }
    }
    
    
    public function getXmlPath( $invoice, $company, $type = null)
    {
        //Pide el tipo, porque puede ser MH. En ese caso descarga la respuesta
        if( !$type ){
            $type = $invoice->document_type;
        }
        $path = $this->downloadXml($invoice, $company, $type, true);
        return $path;
    }
    
    
    public function setDetails43($data) {
        try {
            $details = [];
            foreach ($data as $key => $value) {

                $cod = \App\CodigoIvaRepercutido::find($value->iva_type);
                $isGravado = isset($cod) ? $cod->is_gravado : true;
                $iva_amount = 0;
                if( $isGravado ) {
                    $iva_amount = $value['iva_amount'] ? round($value['iva_amount'], 5) : 0;
                }else {
                    $iva_amount = 'false'; //El api lo va a necesitar asi en String
                }
                
                $itemCount = $value['item_count'] ? round($value['item_count'], 5) : 1;
                $montoSinIva = ($value['unit_price'] && $itemCount) ? round($itemCount * $value['unit_price'], 5) : 0;
                $montoDescuento = $value['discount'] ? $this->discountCalculator($value['discount_type'], $value['discount'], $montoSinIva ) : 0;

                $details[$key] = array(
                    'cantidad' => $itemCount,
                    'unidadMedida' => $value['measure_unit'] ?? '',
                    'detalle' => $value['name'] ?? '',
                    'precioUnitario' => $value['unit_price'] ?? 0,
                    'subtotal' => $value['subtotal'] ?? 0,
                    'montoTotal' =>  $montoSinIva,
                    'montoTotalLinea' => $value['subtotal'] + ($value['iva_amount'] - $value['exoneration_amount']) ?? 0,
                    'descuento' => $montoDescuento,
                    'impuesto_codigo' => '01',
                    'tipo_iva' => $value['iva_type'],
                    'impuesto_codigo_tarifa' => $cod->invoice_code ?? '08',
                    'impuesto_tarifa' => $value['iva_percentage'] ?? 0,
                    'impuesto_factor_IVA' => $value['iva_percentage'] / 100,
                    'impuesto_monto' => $iva_amount,
                    'exoneracion_tipo_documento' => $value['exoneration_document_type'] ?? '',
                    'exoneracion_numero_documento' => $value['exoneration_document_number'] ?? '',
                    'exoneracion_fecha_emision' => $value['exoneration_date'] ?? '',
                    'exoneracion_porcentaje' => $value['exoneration_porcent'] ?? 0,
                    'exoneracion_monto' => $value['exoneration_amount'] ?? 0,
                    'exoneracion_company' => $value['exoneration_company_name'] ?? '',
                    'impuestoneto' => $value['impuesto_neto'] ?? 0,
                    'tariff_heading' => $value['tariff_heading'] ?? '',
                    'exoneracion_total_gravados' => $value['exoneration_total_gravado'] ?? 0,
                    'base_imponible' => 0,
                    'product_type' => $value['product_type'] ?? '',
                );
                $details[$key] = $this->setExonerationPercentage($details[$key]);
            }
            return json_encode($details, true);
        } catch (\Exception $e) {
            Log::error('Error en API HACIENDA -->>'. $e->getMessage());
            return false;
        }
    }

    public function setOtherCharges($data) {
        try {
            $details = [];
            foreach ($data as $key => $value) {
                    $details[$key] = array(
                        'tipodocumento' => $value['document_type'] ?? '',
                        'numeroidentidadtercero' => $value['provider_id_number'] ?? '',
                        'nombretercero' => $value['provider_name'] ?? '',
                        'detalle' => $value['description'] ?? '',
                        'porcentaje' => $value['percentage'] ?? '',
                        'montocargo' => $value['amount'] ?? 0
                );
            }
            return json_encode($details, true);
        } catch (\Exception $error) {
            Log::error('Error al crear otros cargos request -->>'. $error->getMessage() );
            return false;
        }
    }

    public function setInvoiceData43( Invoice $data, $details, $otherCharges = false, $returnRequest = true ) {
        try {
            $provider = null;
            $company = $data->company;
            
            if( !$company->id_number || !$company->business_name ) {
                Log::error('Error enviando factura: No se encuentra company' );
                return false;
            }
            
            /*$ref = getInvoiceReference($company->last_invoice_ref_number) + 1;
            $data->reference_number = $ref;
            $data->save();*/
            $ref = $data->reference_number;
            $receptorPostalCode = trim($data['client_zip']);
            if( empty($receptorPostalCode) ){
                $receptorPostalCode = trim($data['client_state'].$data['client_city'].$data['client_district']);
                if( empty($receptorPostalCode) ){
                    $receptorPostalCode = '10101';
                }
            }
            Log::info("Set request parameters Company: $company->business_name, invoice id: $data->id, consutivo: $ref, Clave: $data->document_key . . . . ZIP: $receptorPostalCode");
            
            $invoiceData = null;
            $request = null;
            $totalServiciosGravados = 0;
            $totalServiciosExentos = 0;
            $totalServiciosExonerados = 0;
            $totalMercaderiasGravadas = 0;
            $totalMercaderiasExentas = 0;
            $totalMercaderiasExonerados = 0;
            $totalDescuentos = 0;
            $totalImpuestos = 0;
            $totalImpuestoExoneracion = 0;
            $totalIvaDevuelto = 0;
            $itemDetails = json_decode($details);
            //Spe, St, Al, Alc, Cm, I, Os
            foreach ($itemDetails as $detail) {
                $cod = \App\CodigoIvaRepercutido::find($detail->tipo_iva);
                $isGravado = isset($cod) ? $cod->is_gravado : true;
                if($detail->unidadMedida == 'Sp' || $detail->unidadMedida == 'Spe' || $detail->unidadMedida == 'St'
                    || $detail->unidadMedida == 'Al' || $detail->unidadMedida == 'Alc' || $detail->unidadMedida == 'Cm'
                    || $detail->unidadMedida == 'I' || $detail->unidadMedida == 'Os'){

                    if($detail->impuesto_monto == 0  && !$isGravado ){
                        $totalServiciosExentos += $detail->montoTotal;
                    }else{
                        $totalServiciosGravados += $detail->montoTotal;
                    }

                    if ($detail->exoneracion_tipo_documento !== "" && $detail->exoneracion_porcentaje !== "") {
                        $totalServiciosExonerados += $detail->exoneracion_total_gravados;
                    }

                } else {
                    if($detail->impuesto_monto == 0 && !$isGravado ){
                        $totalMercaderiasExentas += $detail->montoTotal;
                    }else{
                        $totalMercaderiasGravadas += $detail->montoTotal;
                    }
                    if ($detail->exoneracion_tipo_documento !== "" && $detail->exoneracion_porcentaje !== "") {
                        $totalMercaderiasExonerados += $detail->exoneracion_total_gravados;
                    }
                }

                $totalDescuentos += $detail->descuento;

                if ($detail->impuesto_monto !== 'false') {
                    $totalImpuestos += $detail->impuesto_monto;
                    $totalImpuestoExoneracion += $detail->exoneracion_monto;
                }
                if ($data['payment_type'] == '02' && $detail->product_type == 12) {
                    $totalIvaDevuelto += $detail->impuesto_monto;
                }

            }
            $totalGravado = $totalServiciosGravados + $totalMercaderiasGravadas - $totalServiciosExonerados - $totalMercaderiasExonerados;
            $totalExento = $totalServiciosExentos + $totalMercaderiasExentas;
            $totalExonerados = $totalServiciosExonerados + $totalMercaderiasExonerados;
            $totalVenta = $totalGravado + $totalExento + $totalExonerados;
            $totalNeta = $totalVenta - $totalDescuentos;
            $totalImpuestos = $totalImpuestos - $totalImpuestoExoneracion;
            $totalComprobante = $totalNeta + $totalImpuestos;
            
            $isCompanyEmisor = true;
            if ($data['document_type'] == '08') {
                $isCompanyEmisor = false;
                $provider = Provider::find($data['provider_id']);
                if( $provider->tipo_persona == 'J' || $provider->tipo_persona == '4'  || $provider->tipo_persona == '04' ||
                    $provider->tipo_persona == 'O' || $provider->tipo_persona == '6'  || $provider->tipo_persona == '06' ||
                    $provider->tipo_persona == 'E' || $provider->tipo_persona == '5'  || $provider->tipo_persona == '05' ){
                    $isCompanyEmisor = true;
                }
            }
            
            $emisorName = $isCompanyEmisor ? $company->business_name  ? trim($company->business_name) : '' : $provider->first_name ?? trim($provider->first_name);
            $emisorEmail = $isCompanyEmisor ? $company->email ? replaceAccents($company->email) : '' : $provider->email ?? replaceAccents($provider->email);
            $emisorCompany = $isCompanyEmisor && $company->business_name ? trim($company->business_name) : trim($provider->first_name);
            $emisorCity = $isCompanyEmisor && $company->city ? trim($company->city) : trim($provider->city);
            $emisorState = $isCompanyEmisor && $company->state ?  trim($company->state) : trim($provider->state);
            $emisorPostalCode = $isCompanyEmisor && $company->zip ? trim($company->zip) : trim($provider->zip);
            $emisorCountry = $isCompanyEmisor && $company->country ? trim($company->country) : trim($provider->country);
            $emisorAddress = $isCompanyEmisor ? $company->address ? trim($company->address) : '' : $provider->address ?? trim($provider->address);
            $emisorPhone = $isCompanyEmisor ? $company->phone ? trim($company->phone) : '' : $provider->phone ?? trim($provider->phone);
            $emisorCedula = $isCompanyEmisor && $company->id_number ? str_pad(preg_replace("/[^0-9]/", "", $company->id_number), 9, '0', STR_PAD_LEFT) :str_pad(preg_replace("/[^0-9]/", "", $provider->id_number), 9, '0', STR_PAD_LEFT);
            
            $receptorCedulaType = $data['client_id_type'] ?? 'F';
            if($receptorCedulaType == 'F'){
                $receptorCedulaType = 1;
            }else if($receptorCedulaType == 'E'){
                $receptorCedulaType = 5;    
            }
            if($receptorCedulaType == 5){
                $cedulaReceptor = $data['client_id_number'] ?? '';
                $cedulaReceptor = str_pad($cedulaReceptor, 6, '0', STR_PAD_LEFT);
            }else{
                $cedulaReceptor = $data['client_id_number'] ? str_pad(preg_replace("/[^0-9]/", "", $data['client_id_number']), 9, '0', STR_PAD_LEFT) : '';
                $cedulaReceptor = ltrim($cedulaReceptor, '0');
            }
            
            try{
                $emailArray = explode(',',$emisorEmail);
                if( sizeof( $emailArray ) > 1 ){
                    $emisorEmail = $emailArray[0];
                }
            }catch(\Exception $e){ Log::error($e->getMessage()); }
            
            $receptorEmail = $data['client_email'] ? replaceAccents($data['client_email']) : '';
            try{
                $receptorEmailArray = explode(',',$receptorEmail);
                if( sizeof( $receptorEmailArray ) > 1 ){
                    $receptorEmail = $receptorEmailArray[0];
                }
                //Log::debug(json_encode($receptorEmailArray) ."(".sizeof( $receptorEmailArray ).")" . " to " . $receptorEmail);
            }catch(\Exception $e){ Log::error($e->getMessage()); }
            
            $invoiceData = array(
                'key' => $data['generation_method'] ==  'etax-api' ? $data['document_key'] : '',
                'consecutivo' => $ref ?? '',
                'fecha_emision' => $data['generated_date'] ?? '',
                'metodo_pago' => $data['payment_type'] ?? '01',
                'condicion_venta' => $data['sale_condition'] ?? '01',
                'plazo_credito' => $data['credit_time'] ?? '',
                'codigo_actividad' => str_pad($data['commercial_activity'], 6, '0', STR_PAD_LEFT),
                'receptor_nombre' => trim($data['client_first_name'].' '.$data['client_last_name']),
                'receptor_ubicacion_provincia' => substr($receptorPostalCode,0,1),
                'receptor_ubicacion_canton' => substr($receptorPostalCode,1,2),
                'receptor_ubicacion_distrito' => substr($receptorPostalCode,3),
                'receptor_ubicacion_otras_senas' => $data['client_address'] ? trim($data['client_address']) : '',
                'receptor_otras_senas_extranjero' => $data['client_address'] ? trim($data['client_address']) : '',
                'receptor_email' => $receptorEmail,
                'receptor_phone' => !empty($data['client_phone']) ? preg_replace('/[^0-9]/', '', $data['client_phone']) : '00000000',
                'receptor_cedula_numero' => $cedulaReceptor,
                'receptor_cedula_tipo' => $receptorCedulaType,
                'receptor_postal_code' => $receptorPostalCode ?? '',
                'codigo_moneda' => $data['currency'] ?? '',
                'tipocambio' => $data['currency_rate'] ?? '',
                'tipo_documento' => $data['document_type'] ?? '',
                'sucursal_nro' => '001',
                'terminal_nro' => '00001',
                'emisor_name' => $emisorName,
                'emisor_email' => $emisorEmail,
                'emisor_company' => $emisorCompany,
                'emisor_city' => $emisorCity,
                'emisor_state' => $emisorState,
                'emisor_postal_code' => $emisorPostalCode,
                'emisor_country' => $emisorCountry,
                'emisor_address' => $emisorAddress,
                'emisor_phone' => $emisorPhone,
                'emisor_cedula' => $emisorCedula,
                'usuarioAtv' => $company->atv ? trim($company->atv->user) :  '',
                'passwordAtv' => $company->atv ? trim($company->atv->password) : '',
                'tipoAmbiente' => config('etax.hacienda_ambiente') ?? 01,
                'atvcertPin' => $company->atv ? trim($company->atv->pin) : '',
                //'atvcertFile' => Storage::get($company->atv->key_url),
                'servgravados' => $totalServiciosGravados - $totalServiciosExonerados,
                'servexentos' => $totalServiciosExentos,
                'servexonerados' => $totalServiciosExonerados,
                'mercgravados' => $totalMercaderiasGravadas - $totalMercaderiasExonerados,
                'mercexentos' => $totalMercaderiasExentas,
                'mercexonerados' => $totalMercaderiasExonerados,
                'totgravado' => $totalGravado,
                'totexento' => $totalExento,
                'totexonerados' => $totalExonerados,
                'totventa' => $totalVenta,
                'totdescuentos' => $totalDescuentos,
                'totventaneta' => $totalNeta,
                'totimpuestos' => $totalImpuestos,
                'totalivadevuelto' => $totalIvaDevuelto,
                'totalotroscargos' => $data['total_otros_cargos'] ?? 0,
                'totcomprobante' => $totalComprobante + $data['total_otros_cargos'] - $totalIvaDevuelto,
                'detalle' => $details,
            );

            if ($otherCharges !== false) {
                $invoiceData['otroscargos'] = $otherCharges;
            }

            if ($data['document_type'] == '03' || $data['document_type'] == '02' || $data['document_type'] == '08') {
                $invoiceData['referencia_doc_type'] = $data['reference_doc_type'];
                $invoiceData['referencia_codigo'] = $data['code_note'] ?? "01";
                $invoiceData['referencia_razon'] = $data['reason'] ?? 'Anular Factura';
                $invoiceData['fecha_emision_factura'] = $data['reference_generated_date'];
                $invoiceData['clave_factura'] = $data['reference_document_key'] ?? '';
            }

            if ($data['document_type'] == '01' &&  $data['reference_document_key'] !== '' && $data['reference_document_key'] !== null) {
                $invoiceData['referencia_doc_type'] = $data['reference_doc_type'];
                $invoiceData['referencia_codigo'] = $data['code_note'] ?? "01";
                $invoiceData['referencia_razon'] = $data['reason'] ?? 'Anular Factura';
                $invoiceData['fecha_emision_factura'] = $data['reference_generated_date'];
                $invoiceData['clave_factura'] = $data['reference_document_key'] ?? '';
            }

            Log::info("Request Data from invoices id: $data->id  --> ".json_encode($invoiceData));
            if($company->atv){
                $invoiceData['atvcertFile'] = Storage::get($company->atv->key_url);
            }else{
                $invoiceData['atvcertFile'] = null;
            }
            
            //Para el PDF, retorna el invoiceData completo. Si es para emitir, retorna el request.
            if( !$returnRequest ){
                return $invoiceData;
            }

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
        } catch (\Exception $error) {
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error);
            return false;
        }
    }

    public  function validateZip($invoice) {
        if ($invoice->reference_doc_type != '09') {
            if( !isset($invoice->client_zip) 
                && isset($invoice->client_state) 
                && isset($invoice->client_city) 
                && isset($invoice->client_district) 
            ){
                $invoice->client_zip = $invoice->client_state . str_pad($invoice->client_city, 2, '0', STR_PAD_LEFT) . str_pad($invoice->client_district, 2, '0', STR_PAD_LEFT);
            }
            return isset($invoice->client_zip) ? true : false;
        }
        return true;
    }

    private function discountCalculator($descType, $value, $amount) {
        if($descType == "01" && $value > 0 ) {
             $discount = $amount * ($value / 100);
        } else {
            $discount= $value;
        }
        return round($discount,5);
    }
    
    private function setExonerationPercentage($details){
        //if ( !app()->environment('production') ) {
            
            $ivaPerc = $details['impuesto_tarifa'];
            $currentExonPerc = $details['exoneracion_porcentaje'];
            $exonPerc = $currentExonPerc ?? 13;
            if($ivaPerc && $exonPerc){
                $ratioExonerado = 1;
                //Si el porcentaje actual de IVA es mayor a 100
                if($currentExonPerc > 13){
                    $exonPerc = $ivaPerc*($currentExonPerc/100);
                }
                $exonPerc = round($exonPerc,0);
                
                $ratioExonerado = $exonPerc/$ivaPerc;
                $totalExonerado = $details['subtotal'] * $ratioExonerado;
                
                $details['exoneracion_total_gravados'] = round($totalExonerado,5);
                $details['exoneracion_porcentaje'] = round($exonPerc,0);
            }
        //}
        return $details;
        
    }
    
}
