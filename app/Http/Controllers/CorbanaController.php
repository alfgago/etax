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
use App\Client;
use App\AvailableInvoices;
use App\Jobs\CreateInvoiceJob;
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
        $this->middleware('auth', ['except' => ['sendInvoice','queryBills','queryInvoice']] );
        $this->middleware('CheckSubscription', ['except' => ['sendInvoice','queryBills','queryInvoice']] );
    }
    
    public function queryBills(Request $request) {
        try{
            $request->factura[0];
            $cedulaEmpresa = '3101702429'; 
            $company = Company::where('id_number', $cedulaEmpresa)->first();
            
            /* 
            * Status 03: En el sistema de ellos.
            * Status 01: Aún no ha sido ingresado al sistema.
            */
            $bills = Bill::where('company_id', $company->id)
                    ->where('is_authorized', true)
                    ->where('is_code_validated', true)
                    ->where('status', '01')
                    ->limit(2)
                    ->with('items')->get();
            Log::debug( json_encode($bills) );   
            foreach($bills as $bill){
                //$bill->status = '03'; 
                //$bill->save();
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
                'mensaje' => 'Error ' . $e->getMessage()
            ], 200);
        }
    }
    
    public function queryInvoice(Request $request) {
        try{
            $request->factura[0];
            $cedulaEmpresa = '3101702429'; 
            $company = Company::where('id_number', $cedulaEmpresa)->first();
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
            }
            
            //Define el numero de factura
            $numeroReferencia = $factura['NO_DOCU'];
            $numeroReferencia = floatval( mb_substr( $numeroReferencia, -8, null, 'UTF-8') );
            $numeroReferencia = '95003';
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
    
    public function sendInvoice(Request $request) {
 
        try{
            $invoice = null;
            $factura = $request->factura[0];
            $items = $request->lineas;
            Log::debug("CORBANA RECIBE: FACTURA" . json_encode($factura) . " LINEAS: " . json_encode($items) );
            $metodoGeneracion = "Corbana";

            //$cedulaEmpresa = '3101707070';
            $cedulaEmpresa = '3101702429'; 
            $company = Company::where('id_number', $cedulaEmpresa)->first();
            $mainAct = $company->getActivities() ? $company->getActivities()[0]->codigo : '0';

            $codigoActividad = $mainAct;
            $xmlSchema = 43;
            //Datos de cliente
            $nombreCliente = $factura['RAZON_SOCIAL'];
            $codigoCliente = $factura['IDENTIFICACION'];
            $tipoPersona = $factura['TIPO_IDEN'];
            $identificacionCliente = $factura['IDENTIFICACION'] ?? null;
            $correoCliente = isset($factura['EMAIL_FAC_ELEC']) ? $factura['EMAIL_FAC_ELEC'] : ($factura['CORREO_CLIENTE'] ?? null);
            $telefonoCliente = null;
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
            
            $partidaArancelaria = $factura['NO_DUA'] ?? null;
            if( isset($partidaArancelaria) ){
                $tipoDocumento = '09';
                $tipoPersona = "E";
                $partidaArancelaria = mb_substr( $partidaArancelaria, -12, null, 'UTF-8') ;
            }
            
            //Define el numero de factura
            $numeroReferencia = $factura['NO_DOCU'];
            $numeroReferencia = floatval( mb_substr( $numeroReferencia, -8, null, 'UTF-8') );
            $numeroReferencia = '95013';
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
            
            $TIPO_PAGO = $factura['TIPO_PAGO'] ?? 'D'; //Usan E, T, D, Q, etc
            $metodoPago = "04"; //Default transferencia
            if($TIPO_PAGO == 'E'){
                $metodoPago = '01';
            }else if($TIPO_PAGO == 'T'){
                $metodoPago = '02';
            }else if($TIPO_PAGO == 'Q' || $TIPO_PAGO == 'C'){
                $metodoPago = '03';
            }
            
            $condicionVenta = "02";
            if($metodoPago == "E"){
                $condicionVenta = "01";
            }
            
            $tipoCambio = floatval($factura['TIPO_CAMBIO']);
            $idMoneda = "CRC";
            if($tipoCambio > 0){
                $idMoneda = "USD";
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
            $porcentajeExoneracion = 0;
            
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
                    $unidadMedicion = $TIPO_SERV == "S" ? "Sp" : 'Unid';
                    $cantidad = $item['CANTIDAD'];
                    $precioUnitario = $item['PRECIO_UNITARIO'];
                    $subtotalLinea = $cantidad*$precioUnitario;
                    $montoIva = $subtotalLinea * ($porcentajeIVA/100);
                    $totalLinea = $subtotalLinea+$montoIva;
                    $montoDescuento = $item['DESCUENTO'];
                    $categoriaHacienda = null;
                    $montoExoneracion = isset($documentoExoneracion) ? $montoIva : 0;
                    $totalMontoLinea = $subtotalLinea + $montoIva - $montoExoneracion;
                    
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
            Log::debug($invoiceList);
            foreach($invoiceList as $fac){
               $invoice = $this->saveCorbanaInvoice($fac);
            }
            $invoice->load('items');
            return response()->json([
                'mensaje' => 'Exito',
                'factura' => $invoice
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
        
        foreach( $lineas as $linea ){
            $linea['invoice_id'] = $invoice->id;
            $invoice->subtotal = $invoice->subtotal + $linea['subtotal'];
            $invoice->iva_amount = $invoice->iva_amount + $linea['iva_amount'];
            $descuentos = $descuentos + $linea['discount'];
            $item = InvoiceItem::updateOrCreate(
            [
                'invoice_id' => $linea['invoice_id'],
                'item_number' => $linea['item_number'],
            ], $linea);
            $item->fixIvaType();
            $item->fixCategoria();
        }
        $invoice->total = $invoice->subtotal + $invoice->iva_amount - $descuentos;
        
        $invoice->client_email = "alfgago@gmail.com";
        $invoice->save();
        Log::info("CORBANA Enviada: " . $invoice);
        clearInvoiceCache($invoice);
        
        if($invoice->hacienda_status){
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
            CreateInvoiceJob::dispatch($invoice, $tokenApi)->onQueue('invoicing');
        }
                    
        return $invoice;  
    
    }
    
}
