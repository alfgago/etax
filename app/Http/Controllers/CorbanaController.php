<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use \Carbon\Carbon;
use App\Bill;
use App\BillItem;
use App\Invoice;
use App\InvoiceItem;
use App\OtherInvoiceData;
use App\Company;
use App\Provider;
use App\Client;
use App\AvailableInvoices;
use App\Jobs\CreateInvoiceJob;
use App\Jobs\ProcessInvoice;
use App\Utils\BridgeHaciendaApi;
use App\Http\Controllers\CacheController;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CorbanaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['sendInvoice','queryBills','queryInvoice','anularInvoice','aceptarRechazar','queryBillFiles','queryInvoiceFiles']] );
        $this->middleware('CheckSubscription', ['except' => ['sendInvoice','queryBills','queryInvoice','anularInvoice','aceptarRechazar','queryBillFiles','queryInvoiceFiles']] );
    }
    
    public function queryBills(Request $request) {
        try{
            $pCia = $request->pCia;
            $pAct = $request->pAct;
            $cedulaEmpresa = $this->parseCorbanaIdToCedula($pCia, $pAct);
            //$cedulaEmpresa = "3101702429";
            $company = Company::where('id_number', $cedulaEmpresa)->first();
            
            /* 
            * Status 03: En el sistema de ellos.
            * Status 01: Aún no ha sido ingresado al sistema.
            */
            $bills = Bill::where('company_id', $company->id)
                    ->where('is_void', false)
                    ->where('status', '01')
                    ->whereHas('haciendaResponse', function($q){
                        $q->where('mensaje', '1');
                    })
                    ->limit(10)
                    ->with('items')->with('haciendaResponse')->get();
                    
            $billUtils = new \App\Utils\BillUtils();
            foreach($bills as $bill){
                $bill->status = '03'; 
                $bill->save();
                $pdf = $billUtils->streamPdf($bill, $company);
                $bill->pdf64 = !empty($pdf) ? base64_encode($pdf) : null;
                
                $xml = $billUtils->downloadXml($bill, $company);
                $bill->xml64 = !empty($xml) ? base64_encode($xml) : null;
                
                $xmlA = $billUtils->downloadXmlAceptacion($bill, $company);
                $bill->xmlh64 = !empty($xmlA) ? base64_encode($xmlA) : null;
                
                $hasFiles = 0;
                if( !empty($xml) && !empty($xmlA) && !empty($pdf) ){
                    $hasFiles = 1;
                }
                $bill->has_files = $hasFiles;
            }         
            if(isset($bills)){
                return response()->json([
                    'mensaje' => $bills->count() . ' facturas',
                    'facturas' => $bills
                ], 200);
            }
        
        }catch(\Exception $e){
            Log::error("Error en Corbana" . $e);
            return response()->json([
                'mensaje' => 'Error ' . $e->getMessage(),
                'facturas' => []
            ], 200);
        }
        
        return response()->json([
            'mensaje' => '0 facturas',
            'facturas' => []
        ], 200);
        
    }
    
    public function queryBillFiles(Request $request) {
        try{
            $billId = $request->pId;
            
            $bill = Bill::where('id', $billId)->with('company')->first();
            
            $billUtils = new \App\Utils\BillUtils();
            if( isset($bill) ){
                $company = $bill->company;
                $cedula = $company->id_number;
                if( $cedula != "3101018968" && $cedula != "3101011989" && $cedula != "3101166930" && $cedula != "3007684555" && $cedula != "3130052102" && $cedula != "3101702429" ){
                    Log::warning("Error: ID de factura no le pertenece a Corbana");
                    return response()->json([
                        'mensaje' => 'Error: ID de factura no le pertenece a Corbana'
                    ], 200);
                }
            
                $pdf = $billUtils->streamPdf($bill, $company);
                $basePDF = !empty($pdf) ? base64_encode($pdf) : null;
                
                $xml = $billUtils->downloadXml($bill, $company);
                $baseXML = !empty($xml) ? base64_encode($xml) : null;
                
                $xmlA = $billUtils->downloadXmlAceptacion($bill, $company);
                $baseXMLH = !empty($xmlA) ? base64_encode($xmlA) : null;
                
                $hasFiles = 0;
                if( !empty($xml) && !empty($xmlA) && !empty($pdf) ){
                    $hasFiles = 1;
                }
              
                return response()->json([
                    'mensaje'  => "Enviando archivos. Resp($hasFiles)",
                    'pdf64' => $basePDF,
                    'xml64' => $baseXML,
                    'xmlh64' => $baseXMLH,
                    'tiene_todos' => $hasFiles
                ], 200);
            }
        
        }catch(\Exception $e){
            Log::error("Error en Corbana" . $e);
            return response()->json([
                'mensaje' => 'Error ' . $e->getMessage()
            ], 200);
        }
    }
    
    public function queryInvoice(Request $request) {
        try{
            $invoice = Invoice::where('id', $request->id)
                                ->with('items')
                                ->with('company')
                                ->first();
            
            $cedula = $invoice->company->id_number;
            if( $cedula != "3101018968" && $cedula != "3101011989" && $cedula != "3101166930" && $cedula != "3007684555" && $cedula != "3130052102" && $cedula != "3101702429" ){
                Log::warning("Error: ID de factura no le pertenece a Corbana");
                return response()->json([
                    'mensaje' => 'Error: ID de factura no le pertenece a Corbana'
                ], 200);
            }                 
            if(isset($invoice)){
                return response()->json([
                    'id_factura' => $invoice->id,
                    'hacienda_status' => $invoice->hacienda_status
                ], 200);
            }
        
        }catch(\Exception $e){
            Log::error("Error en Corbana" . $e);
            return response()->json([
                'mensaje' => 'Error ' . $e->getMessage()
            ], 200);
        }
        
        //Nunca deberia llegar a este return.
        return response()->json([
            'mensaje' => 'Factura no encontrada en eTax'
        ], 200);
    }
    
    
    public function queryInvoiceFiles(Request $request) {
        try{
            $invoiceId = $request->pId;
            
            $invoice = Invoice::where('id', $invoiceId)->with('company')->first();
            
            $invoiceUtils = new \App\Utils\InvoiceUtils();
            if( isset($invoice) ){
                $company = $invoice->company;
                $cedula = $company->id_number;
                if( $cedula != "3101018968" && $cedula != "3101011989" && $cedula != "3101166930" && $cedula != "3007684555" && $cedula != "3130052102" && $cedula != "3101702429" ){
                    Log::warning("Error: ID de factura no le pertenece a Corbana");
                    return response()->json([
                        'mensaje' => 'Error: ID de factura no le pertenece a Corbana'
                    ], 200);
                }
            
                $pdf = $invoiceUtils->streamPdf($invoice, $company);
                $basePDF = !empty($pdf) ? base64_encode($pdf) : null;
                
                $xml = $invoiceUtils->downloadXml($invoice, $company);
                $baseXML = !empty($xml) ? base64_encode($xml) : null;
                
                $xmlA = $invoiceUtils->downloadXmlAceptacion($invoice, $company);
                $baseXMLH = !empty($xmlA) ? base64_encode($xmlA) : null;
                
                $hasFiles = 0;
                if( !empty($xml) && !empty($xmlA) && !empty($pdf) ){
                    $hasFiles = 1;
                }
              
                return response()->json([
                    'mensaje'  => "Enviando archivos. Resp($hasFiles)",
                    'pdf64' => $basePDF,
                    'xml64' => $baseXML,
                    'xmlh64' => $baseXMLH,
                    'tiene_todos' => $hasFiles
                ], 200);
            }
        
        }catch(\Exception $e){
            Log::error("Error en Corbana" . $e);
            return response()->json([
                'mensaje' => 'Error ' . $e->getMessage()
            ], 200);
        }
    }
    
    public function sendInvoice(Request $request) {
 
        try{
            $invoice = null;
            $factura = $request->factura[0];
            $items = $request->lineas;
            $requestOtros = $request->otros ?? null;
            $metodoGeneracion = "etax-corbana";

            //Busca la cedula de la empresa
            $cedulaEmpresa = $this->parseCorbanaIdToCedula($factura['NO_CIA'], $factura['ACTIVIDAD']);
            $company = Company::where('id_number', $cedulaEmpresa)->first();
            $xmlSchema = 43;
            
            $codigoActividad = $factura['CODIGO_ACTIVIDAD'] ?? null;
            if( !isset($codigoActividad) ){
                $mainAct = $company->getActivities() ? $company->getActivities()[0]->codigo : '0';
                $codigoActividad = $mainAct;
            }
            
            //Datos de cliente
            $nombreCliente = $factura['RAZON_SOCIAL'];
            $codigoCliente = $factura['IDENTIFICACION'];
            $tipoPersona = $factura['TIPO_IDEN'];
            $identificacionCliente = $factura['IDENTIFICACION'] ?? null;
            $correoCliente = isset($factura['EMAIL_FAC_ELEC']) ? $factura['EMAIL_FAC_ELEC'] : ($factura['CORREO_CLIENTE'] ?? null);
            $telefonoCliente = $factura['TEL_CLIENTE'] ?? null;
            $direccion = $factura['DIRECCION_CON'] ?? "No indica";
            $codProvincia = $factura['COD_PROVINCIA'] ?? "7";
            $codCanton = $factura['COD_CANTON'] ?? "7";
            $codCanton = str_pad($codCanton, 2, '0', STR_PAD_LEFT);
            $zip = $codProvincia.$codCanton."01";
            
            //Define el tipo de documento
            $TIPO_DOC = $factura['TIPO_DOC'] ?? '01';
            $tipoDocumento = '01';
            if($TIPO_DOC == 'FA'){
                $tipoDocumento = '01';
                if($identificacionCliente == "000000000000000"){
                    $tipoDocumento = "04";
                }
            }else if($TIPO_DOC == 'NC'){
                $tipoDocumento = '03';
            }else if($TIPO_DOC == 'ND'){
                $tipoDocumento = '02';
            }
            $otherReference = $factura['NO_DOCU_REF'] ?? null;
            
            $sistema = $factura['SISTEMA'] ?? null;
            $dua = $factura['NO_DUA'] ?? null;
            $partidaArancelaria = $factura['PARTIDA_ARANCELARIA'] ?? $dua;
            if( $sistema == 'EXP' ){
                $tipoDocumento = '09';
                $tipoPersona = "E";
                $partidaArancelaria = mb_substr( $partidaArancelaria, -12, null, 'UTF-8') ;
            }
            
            //Define el numero de factura
            //$numeroReferencia = $factura['NO_DOCU'];
            //$numeroReferencia = floatval( mb_substr( $numeroReferencia, -8, null, 'UTF-8') );
            $numeroReferencia = getNextRef($tipoDocumento, $company);
            $consecutivoComprobante = getDocReference($tipoDocumento, $company, $numeroReferencia);
            $claveFactura = getDocumentKey($tipoDocumento, $company, $numeroReferencia);


            $invoice = Invoice::where('document_key', $claveFactura)
                                ->where('company_id', $company->id)
                                ->with('items')
                                ->first();
            if(isset($invoice)){
                return response()->json([
                    'mensaje' => 'Factura existente',
                    'factura' => $invoice
                ], 200);
            }
            
            $TIPO_SERV = $factura['TIPO_SERV'] ?? 'B';
            if( $tipoDocumento == '09' ){
                $TIPO_SERV = 'B';
            }
            
            $TIPO_PAGO = $factura['MODO_PAGO'] ?? 'D'; //Usan E, T, D, Q, etc
            $metodoPago = "04"; //Default transferencia
            if($TIPO_PAGO == 'E' || $TIPO_PAGO == 'C'){
                $metodoPago = '01';
            }else if($TIPO_PAGO == 'T'){
                $metodoPago = '02';
            }else if($TIPO_PAGO == 'Q'){
                $metodoPago = '03';
            }
            
            $condicionVenta = "02";
            if($metodoPago == "E"){
                $condicionVenta = "01";
            }
            
            if( $factura['MONEDA'] == '02' ){
                $idMoneda = "USD";
                $tipoCambio = floatval($factura['TIPO_CAMBIO']);
            }else{
                $idMoneda = "CRC";
                $tipoCambio = 1;
            }

            $FEC_HECHO = $factura['FEC_HECHO'];
            $fechaEmision = Carbon::parse($FEC_HECHO);
            $fechaVencimiento = Carbon::parse($FEC_HECHO)->addMonths(1);
            
            $porcentajeIVA = $factura['PORCIV'] ?? 0;
            $totalDocumento = 0;
            $descripcion = isset($factura['OBSERVACION']) ? $factura['OBSERVACION'] : '';
            $descripcion .= isset($factura['NOTA1']) ? $factura['NOTA1'] : '';
            $descripcion .= isset($factura['NOTA2']) ? $factura['NOTA2'] : '';
    
            //Exoneraciones
            $totalNeto = 0;
            $tipoDocumentoExoneracion = $factura['CODIGOTIPOEXO'] ?? null;
            $documentoExoneracion = $factura['DOCUMENTO_EXO'] ?? null;
            $companiaExoneracion = $factura['COD_INST_EXO'] ?? null;
            $companiaExoneracion = $nombreCliente;
            $fechaExoneracion = $factura['FEC_DOCU_EXO'] ?? null;
            $porcentajeExoneracion = $factura['PORC_EXONERACION'] ?? 0;;
            
            $prefijoCodigo= "B";
            if($TIPO_SERV == "S"){
                $prefijoCodigo = "S";
            }
            $codigoEtax = $prefijoCodigo.'103';
            if( !isset($porcentajeIVA) ){
                $codigoEtax = $prefijoCodigo.'170';
            }
            if( isset($documentoExoneracion) ){
                $porcentajeIVA = !isset($porcentajeIVA) ? $porcentajeIVA : 13;
                $porcentajeExoneracion = 100;
                $codigoEtax = $prefijoCodigo.'183';
            }
            $impuestoNeto = 0;
            if($tipoDocumento == '09'){
                $codigoEtax = "B150";
            }
            
            //Datos de lineas
            $i = 0;
            $invoiceList = array();
            foreach($items as $item){
                $i++;
                $detalleProducto = $item['DESCRIPCION'];   
                //Revisa que no sean las lineas de diferencial cambiario ni tarifa general
                if ( $detalleProducto != "DIFERENCIAL CAMBIARIO" && !strpos($detalleProducto, "RIFA GENERAL") ) {
                    $numeroLinea = $item['LINEA'];
                    $codigoProducto = $item['CODIGO'] ?? "0";
                    
                    $unidadMedicion = $item['UM'] ?? null;
                    if( $unidadMedicion == 'N' ){
                        $unidadMedicion = $TIPO_SERV == "S" ? "Sp" : 'Unid';
                    }
                    $unidadMedicion = ucfirst(strtolower($unidadMedicion));
                    
                    $cantidad = $item['CANTIDAD'];
                    
                    $precioUnitario = $item['PRECIO'];
                    $montoDescuento = $item['DESCUENTO'];
                    
                    $subtotalLinea = $cantidad*$precioUnitario - $montoDescuento;
                    $montoIva = $subtotalLinea * ($porcentajeIVA/100);
                    $totalLinea = $subtotalLinea+$montoIva;
                    $categoriaHacienda = null;
                    $montoExoneracion = isset($documentoExoneracion) ? $montoIva : 0;
                    $totalMontoLinea = $subtotalLinea + $montoIva - $montoExoneracion - $montoDescuento;
                    
                    $cantidad = round($cantidad, 5);
                    $precioUnitario = round($precioUnitario, 5);
                    $subtotalLinea = round($subtotalLinea, 5);
                    $montoIva = round($montoIva, 5);
                    $totalLinea = round($totalLinea, 5);
                    $montoDescuento = round($montoDescuento, 5);
                    $totalMontoLinea = round($totalMontoLinea, 5);
                
                    $arrayInsert = array(
                        'metodoGeneracion' => $metodoGeneracion,
                        'idEmisor' => $cedulaEmpresa,
                        /****Empiezan datos cliente***/
                        'nombreCliente' => $nombreCliente,
                        'descripcion' => $descripcion,
                        'codigoCliente' => $codigoCliente,
                        'tipoPersona' => $tipoPersona,
                        'identificacionCliente' => $identificacionCliente,
                        'correoCliente' => $correoCliente,
                        'telefonoCliente' => $telefonoCliente,
                        'direccion'     => $direccion,
                        'zip'     => $zip,
                        /****Empiezan datos factura***/
                        'claveFactura' => $claveFactura,
                        'consecutivoComprobante' => $consecutivoComprobante,
                        'numeroReferencia' => $numeroReferencia,
                        'condicionVenta' => $condicionVenta,
                        'metodoPago' => $metodoPago,
                        'numeroLinea' => $numeroLinea,
                        'fechaEmision' => $fechaEmision->format('d/m/Y'),
                        'fechaVencimiento' => $fechaVencimiento->format('d/m/Y'),
                        'moneda' => $idMoneda,
                        'tipoCambio' => $tipoCambio,
                        'totalDocumento' => $totalDocumento,
                        'totalNeto' => $totalNeto,
                        'otherReference' => $otherReference,
                        /**** Empiezan datos lineas ****/
                        'cantidad' => $cantidad,
                        'precioUnitario' => $precioUnitario,
                        'porcentajeIva' => $porcentajeIVA,
                        'totalLinea' => $totalLinea,
                        'montoIva' => $montoIva,
                        'codigoEtax' => $codigoEtax,
                        'montoDescuento' => $montoDescuento,
                        'subtotalLinea' => $subtotalLinea,
                        'tipoDocumento' => $tipoDocumento,
                        'codigoProducto' => $codigoProducto,
                        'detalleProducto' => $detalleProducto,
                        'unidadMedicion' => $unidadMedicion,
                        'tipoDocumentoExoneracion' => $tipoDocumentoExoneracion,
                        'documentoExoneracion' => $documentoExoneracion,
                        'companiaExoneracion' => $companiaExoneracion,
                        'porcentajeExoneracion' => $porcentajeExoneracion,
                        'montoExoneracion' => $montoExoneracion,
                        'impuestoNeto' => $impuestoNeto,
                        'totalMontoLinea' => $totalMontoLinea,
                        'xmlSchema' => $xmlSchema,
                        'codigoActividad' => $codigoActividad,
                        'categoriaHacienda' => $categoriaHacienda,
                        'partidaArancelaria' => $partidaArancelaria,
                        'acceptStatus' => true,
                        'isAuthorized' => true,
                        'codeValidated' => true
                    );
                    
                    $invoiceList = Invoice::importInvoiceRow($arrayInsert, $invoiceList, $company);
                }
                
            }
            foreach($invoiceList as $fac){
               $invoice = $this->saveCorbanaInvoice($fac);
               $company->setLastReference($tipoDocumento, $numeroReferencia, $consecutivoComprobante);
            }
            $invoice->load('items');
            
            $otherData = $this->setOtherInvoiceData($invoice, $factura, $items, $requestOtros);
            
            $invoiceUtils = new \App\Utils\InvoiceUtils();
            $pdf = $invoiceUtils->streamPdf($invoice, $invoice->company);
            $basePDF = !empty($pdf) ? base64_encode($pdf) : null;
            $xml = $invoiceUtils->downloadXml($invoice, $invoice->company);
            $baseXML = !empty($xml) ? base64_encode($xml) : null;
            
            return response()->json([
                'mensaje' => 'Exito',
                'factura' => $invoice,
                'pdf64' => $basePDF,
                'xml64' => $baseXML,
            ], 200);
        }catch(\Exception $e){
            Log::error("Error en Corbana" . $e);
            return response()->json([
                'mensaje' => 'Error ' . $e->getMessage()
            ], 200);
        }
        
        //Nunca deberia llegar a este return.
        return response()->json([
            'mensaje' => 'Error indefinido'
        ], 200);
        
    }
    
    private function setOtherInvoiceData($invoice, $requestInvoice, $requestItems, $requestOtros){
        
        try{
            $requestOtros = $requestOtros[0];
            $otherData = [];
            
            //Si es exportacion, agrega los datos de peso por linea
            $sistema = $requestInvoice['SISTEMA'] ?? null;
            if( $sistema == 'EXP' ){
                foreach($invoice->items as $item){
                    $pesoNeto = 0;
                    $pesoBruto = 0;
                    foreach( $requestItems as $reqItem ){
                        if($reqItem['LINEA'] == $item->item_number){
                            $pesoNeto = $reqItem['PESO_NETO'] ?? 0;
                            $pesoBruto = $reqItem['PESO_BRUTO'] ?? 0;
                        }
                    }
                    $otherData["PESO_NETO-$item->id"] = OtherInvoiceData::registerOtherData(
                        $invoice->id,
                        $item->id,
                        "PESO_NETO",
                        $pesoNeto
                    );
                    
                    
                    $otherData["PESO_BRUTO-$item->id"] = OtherInvoiceData::registerOtherData(
                        $invoice->id,
                        $item->id,
                        "PESO_BRUTO",
                        $pesoBruto
                    );
                }
            
                $otherData["COD_EXP"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "COD_EXP",
                    $requestOtros["COD_EXP"] ?? ''
                );
                $otherData["CONSIG"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "CONSIG",
                    $requestOtros["CONSIG"] ?? ''
                );
                $otherData["DIR_CONSIG"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "DIR_CONSIG",
                    $requestOtros["DIR_CONSIG"] ?? ''
                );
                $otherData["COD_EMB"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "COD_EMB",
                    $requestOtros["COD_EMB"] ?? ''
                );
                $otherData["COD_VAP"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "COD_VAP",
                    $requestOtros["COD_VAP"] ?? ''
                );
                $otherData["PRO_FRU"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "PRO_FRU",
                    $requestOtros["PRO_FRU"] ?? ''
                );
                $otherData["PUE_SAL"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "PUE_SAL",
                    $requestOtros["PUE_SAL"] ?? ''
                );
                $otherData["PUE_DES"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "PUE_DES",
                    $requestOtros["PUE_DES"] ?? ''
                );
                $otherData["NO_DUA"] = OtherInvoiceData::registerOtherData(
                    $invoice->id,
                    null,
                    "NO_DUA",
                    $requestOtros["NO_DUA"] ?? ''
                );
               
            }
            $otherData["REFERENCIA"] = OtherInvoiceData::registerOtherData(
                $invoice->id,
                null,
                "REFERENCIA",
                $requestOtros["REFERENCIA"] ?? ''
            );
            $otherData["HECHO_POR"] = OtherInvoiceData::registerOtherData(
                $invoice->id,
                null,
                "HECHO_POR",
                $requestOtros["HECHO_POR"] ?? ''
            );
            $otherData["REVISADO_POR"] = OtherInvoiceData::registerOtherData(
                $invoice->id,
                null,
                "REVISADO_POR",
                $requestOtros["REVISADO_POR"] ?? ''
            );
            
            return $otherData;
        }catch(\Exception $e){
            Log::error( "CORBANA: Error al guardar otra info: " . $e->getMessage() );
            return array();
        }
        
    }
    
    /**
     * Función utilizada para hacer el envio de Corbana a Hacienda.
     */
    private function saveCorbanaInvoice($invoiceArray){

        $fac = $invoiceArray['factura'];
        $lineas = $invoiceArray['lineas'];
        
        $invoice = Invoice::firstOrNew(	
          [	
              'company_id' => $fac->company_id,	
              'document_number' => $fac->document_number,	
              'document_key' => $fac->document_key,	
              'client_id_number' => $fac->client_id_number,	
          ], $fac->toArray()	
        );
        
        if( !$invoice->id ){
            $available_invoices = AvailableInvoices::where('company_id', $invoice->company_id)
                                  ->where('year', $invoice->year)
                                  ->where('month', $invoice->month)
                                  ->first();
            if( isset($available_invoices) ) {
              $available_invoices->current_month_sent = $available_invoices->current_month_sent + 1;
              $available_invoices->save();
            }
            $invoice->hacienda_status = '01';
            $invoice->save();
        }
        
        $invoice->subtotal = 0;
        $invoice->iva_amount = 0;
        $invoice->total = 0;
        $invoice->is_code_validated = true;
        $descuentos = 0;
        $exoneraciones = 0;
        
        foreach( $lineas as $linea ){
            $linea['invoice_id'] = $invoice->id;
            $invoice->subtotal = $invoice->subtotal + $linea['subtotal'];
            $invoice->iva_amount = $invoice->iva_amount + $linea['iva_amount'];
            $descuentos = $descuentos + $linea['discount'];
            $exoneraciones = $exoneraciones + $linea['exoneration_amount'];
            $item = InvoiceItem::updateOrCreate(
            [
                'invoice_id' => $linea['invoice_id'],
                'item_number' => $linea['item_number'],
            ], $linea);
            $item->fixIvaType();
            $item->fixCategoria();
        }
        $invoice->total = $invoice->subtotal + $invoice->iva_amount - $descuentos - $exoneraciones;
        
        //$invoice->client_email = "alfgago@gmail.com";
        $invoice->save();
        Log::info("CORBANA Enviada: " . $invoice);
        clearInvoiceCache($invoice);
        
        try{
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
            if($tokenApi){
                ProcessInvoice::dispatch($invoice, $invoice->company_id, $tokenApi)->onQueue('invoicing');
            }
        }catch(\Exception $e){ Log::error($e); }
                    
        return $invoice;  
    }
    
    public function anularInvoice(Request $request)
    {
        try {
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login();
            if ($tokenApi !== false) {
                $factura = $request->factura[0];
                $items = $request->lineas;
    
                //Busca la cedula de la empresa
                $cedulaEmpresa = $this->parseCorbanaIdToCedula($factura['NO_CIA'], $factura['ACTIVIDAD']);
                $company = Company::where('id_number', $cedulaEmpresa)->first();
                $identificacionCliente = $factura['IDENTIFICACION'] ?? null;
                
                //Define el tipo de documento
                $TIPO_DOC = $factura['TIPO_DOC'] ?? '01';
                $tipoDocumento = '01';
                if($TIPO_DOC == 'FA'){
                    $tipoDocumento = '01';
                    if($identificacionCliente == "000000000000000"){
                        $tipoDocumento = "04";
                    }
                }else if($TIPO_DOC == 'NC'){
                    $tipoDocumento = '03';
                }else if($TIPO_DOC == 'ND'){
                    $tipoDocumento = '02';
                }
                
                $partidaArancelaria = $factura['NO_DUA'] ?? null;
                if( isset($partidaArancelaria) ){
                    $tipoDocumento = '09';
                    $tipoPersona = "E";
                    $partidaArancelaria = mb_substr( $partidaArancelaria, -12, null, 'UTF-8') ;
                }
                
                //Define el numero de factura
                $numeroReferencia = $factura['NO_DOCU'];
                $numeroReferencia = floatval( mb_substr( $numeroReferencia, -8, null, 'UTF-8') );
                $consecutivoComprobante = getDocReference($tipoDocumento, $company, $numeroReferencia);
                $claveFactura = getDocumentKey($tipoDocumento, $company, $numeroReferencia);
                Log::info("CORBANA: Enviando nota de credito de factura $claveFactura de empresa $company->id_number");
                
                $invoice = Invoice::where('document_key', $claveFactura)
                            ->where('company_id', $company->id)
                            ->with('items')
                            ->first();
               
                $note = new Invoice();
                //Datos generales y para Hacienda
                $note->company_id = $company->id;
                $note->document_type = "03";
                $note->hacienda_status = '01';
                $note->payment_status = "01";
                $note->payment_receipt = "";
                $note->generation_method = "etax-corbana-anula";
                $note->reason = "Anular Factura";
                $note->code_note = "01";
                $note->reference_number = $company->last_note_ref_number + 1;
                $note->save();
                $noteData = $note->setNoteData($invoice, $invoice->items->toArray(), $note->document_type, $invoice, $company);
                //Log::debug($noteData);
                if (!empty($noteData)) {
                    $apiHacienda->createCreditNote($noteData, $tokenApi);
                }
                $company->last_note_ref_number = $noteData->reference_number;
                $company->last_document_note = $noteData->document_number;
                $company->save();

                clearInvoiceCache($invoice);
                $user = auth()->user();
                
                return response()->json([
                    'mensaje' => 'Exito',
                    'factura' => $note
                ], 200);
            }

        } catch ( \Exception $e) {
            Log::error('Error al anular facturar -->'.$e);
            return response()->json([
                'mensaje' => 'Error' . $e->getMessage()
            ], 200);
        }
        return response()->json([
            'mensaje' => 'Error'
        ], 200);

    }
    
    public function aceptarRechazar(Request $request) {
        try{
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
            if ($tokenApi !== false) {
                $bill = Bill::where('id', $request->id)
                            ->with('items')
                            ->with('company')
                            ->first();
                $haciendaStatus = $request->hacienda_status;
                $condicionAceptacion = $request->condicion_aceptacion;
                if( isset($bill) ){
                    $company = $bill->company;
                    $cedula = $company->id_number;
                    if( $cedula != "3101018968" && $cedula != "3101011989" && $cedula != "3101166930" && $cedula != "3007684555" && $cedula != "3130052102" && $cedula != "3101702429" ){
                        Log::warning("Error: ID de factura no le pertenece a Corbana");
                        return response()->json([
                            'mensaje' => 'Error: ID de factura no le pertenece a Corbana'
                        ], 200);
                    }
                    foreach($bill->items as $item){
                        $item->setIvaTypeFromCondition($condicionAceptacion);
                    }
                    $bill->is_authorized = true;
                    $bill->accept_status = 1;
                    $bill->is_code_validated = true;
                    $bill->hacienda_status = $haciendaStatus;
                    $bill->save();
                    $company->last_rec_ref_number = $company->last_rec_ref_number + 1;
                    $company->save();
                    $company->last_document_rec = getDocReference('05', $company, $company->last_rec_ref_number);
                    $company->save();
                    $apiHacienda->acceptInvoice($bill, $tokenApi);
                    clearBillCache($bill);
                    return response()->json([
                        'mensaje' => 'Factura actualizada exitosamente'
                    ], 200);  
                }
            }
        }catch(\Exception $e){
            Log::error("Error en Corbana" . $e);
            return response()->json([
                'mensaje' => 'Error ' . $e->getMessage()
            ], 200);
        }
        
        //Nunca deberia llegar a este return.
        return response()->json([
            'mensaje' => 'Factura no encontrada en eTax'
        ], 200);
    }

    /*
    01 = CORPORACION BANANERA NACIONAL SOCIEDAD ANONIMA	3101018968
    11 = COMPANIA INTERNACIONAL DE BANANO, S.A.	3101011989
    05 = AGRO FORESTALES DE SIXAOLA S.A.	3101166930
    02.05 = FONDO ESPECIAL DE PREVENCION E INFRAESTRUCTURA		3007684555
    02.15 = FONDO CONTINGENCIA DECRETO 16564/P/H/MEC CORP BANANERA NACIONAL SA	3130052102
    */
    private function parseCorbanaIdToCedula($pCia, $pAct){
        if($pCia == "01"){
            return "3101018968";
        }else if($pCia == "11"){
            return "3101011989";
        }else if($pCia == "05"){
            return "3101166930";
        }else if($pCia == "02"){
            if($pAct == "05"){
                return "3007684555";
            }else{
                return "3130052102";
            }
        }
    }
    
}