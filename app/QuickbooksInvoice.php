<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use \Carbon\Carbon;
use App\Invoice;
use App\Client;
use App\Quickbooks;
use App\Company;
use QuickBooksOnline\API\Facades\Invoice as qboInvoice;

class QuickbooksInvoice extends Model
{

    protected $guarded = [];
    use Sortable, SoftDeletes;
    
    protected $casts = [
        'qb_data' => 'array'
    ];

	
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }  

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
    
    //Devuelve la fecha de generación en formato Carbon
    public function generatedDate()
    {
        return Carbon::parse($this->qb_date);
    }
    
    public static function syncMonthlyInvoices($dataService, $year, $month, $company = false){
        if(!$company){
            $company = currentCompanyModel();
        }
        
        $facturas = new \stdClass();
        
        $etaxInvoices = Invoice::where("company_id", $company->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->with('client')
                        ->get();
        foreach($etaxInvoices as $invoice){
            $docNumber = $invoice->document_number;
            $fac = new \stdClass();
            $fac->numero_etax = $docNumber;
            $fac->fecha_etax = $invoice->generatedDate()->format('d/m/Y');
            $fac->total_etax = $invoice->total;
            $fac->cliente_etax = "$invoice->client_first_name $invoice->client_last_name $invoice->client_last_name2";
            $fac->etax_id = $invoice->id;
            $facturas->$docNumber = $fac;
        }
        
        $qbInvoices = QuickBooksInvoice::select('qb_id', 'qb_doc_number', 'qb_date', 'qb_total', 'qb_client')
                         ->where('year', $year)
                         ->where('month', $month)
                         ->with('invoice')
                         ->get();
        foreach($qbInvoices as $invoice){
            $docNumber = $invoice->qb_doc_number;
            if( !isset($facturas->$docNumber) ){
                $fac = new \stdClass();
            }else{
                $fac = $facturas->$docNumber;
            }
            $fac->numero_qb = $docNumber;
            $fac->fecha_qb = $invoice->generatedDate()->format('d/m/Y');
            $fac->total_qb = $invoice->qb_total;
            $fac->cliente_qb = $invoice->qb_client;
            $fac->qb_id = $invoice->qb_id;
            $facturas->$docNumber = $fac;
        }
        
        return $facturas;
    }
    
    public static function loadQuickbooksInvoices($dataService, $year, $month, $company){
        
        $qbInvoices = QuickbooksInvoice::getMonthlyInvoices($dataService, $year, $month);
        QuickbooksInvoice::syncMonthlyClients($dataService, $year, $month, $company);
        $qbInvoices = $qbInvoices ?? [];

        foreach($qbInvoices as $invoice){
            $date = Carbon::createFromFormat("Y-m-d", $invoice->TxnDate);
            
            $cachekey = "qb-invoice-$company->id_number-$invoice->Id"; 
            if ( !Cache::has($cachekey) ) {
                $clientName = Quickbooks::getClientName($company, $invoice->CustomerRef);
                $qbInvoice = QuickBooksInvoice::updateOrCreate(
                  [
                    "qb_id" => $invoice->Id
                  ],
                  [
                    "qb_doc_number" => $invoice->DocNumber,
                    "qb_date" => $date,
                    "qb_total" => $invoice->TotalAmt,
                    "qb_client" => $clientName,
                    "qb_data" => $invoice,
                    "generated_at" => 'quickbooks',
                    "month" => $month,
                    "year" => $year,
                    "company_id" => $company->id
                  ]
                );
                Cache::put($cachekey, $qbInvoice, 86400); //Cache por 24 horas.
            }
        }
    }
    
    public static function getMonthlyInvoices($dataService, $year, $month){
        $dateFrom = Carbon::createFromDate($year, $month, 1)->firstOfMonth()->toAtomString();
        $dateTo = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toAtomString();
        $qbInvoices = $dataService->Query("SELECT * FROM Invoice WHERE TxnDate >= '$dateFrom' AND TxnDate <= '$dateTo'");
        return $qbInvoices;
    }
    
    public static function getMonthlyClients($dataService, $year, $month, $company) {
        $cachekey = "qb-clients-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $dateFrom = Carbon::createFromDate($year, $month, 1)->firstOfMonth()->toAtomString();
            $dateTo = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toAtomString();
            $clients = $dataService->Query("SELECT * FROM Customer WHERE MetaData.LastUpdatedTime >= '$dateFrom' AND MetaData.LastUpdatedTime <= '$dateTo'");
            Cache::put($cachekey, $clients, 15); //Cache por 15 segundos.
        }else{
            $clients = Cache::get($cachekey);
        }
        return $clients;
    }
    
    public static function saveInvoiceFromEtax($dataService, $invoice) {
        
    }
    
    public function saveInvoiceFromQuickbooks() {
        try{
            $invoice = null;
            $invoiceData = $this->qb_data;
            $items = $invoiceData["Line"];
            

            //Datos generales
            $company = $this->company;
            $metodoGeneracion = "quickbooks";
            $xmlSchema = 43;
            
            dd($invoiceData);
            
            // POR RESOLVER: Quickbooks no indica el código de actividad.
            /*$codigoActividad = $invoiceData['CODIGO_ACTIVIDAD'] ?? null;
            if( !isset($codigoActividad) ){
                $mainAct = $company->getActivities() ? $company->getActivities()[0]->codigo : '0';
                $codigoActividad = $mainAct;
            }*/ 
            
            //Datos de cliente
            $customer = Quickbooks::getClientInfo($company, $invoiceData["CustomerRef"]);
            if( !$customer ){
                return [
                    'status'  => '400',
                    'mensaje' => 'Verifique que el cliente se encuentre correctamente sincronizado e indique el tipo de persona y código postal.'
                ];
            }
            //dd($invoiceData, $customer);
            $nombreCliente = $customer['nombreCliente'];
            $codigoCliente = $customer['codigoCliente'];
            $tipoPersona = $customer['tipoPersona'];
            $identificacionCliente = $customer['identificacionCliente'];
            $correoCliente = $customer['correoCliente'];
            $telefonoCliente = $customer['telefonoCliente'];
            $direccion = $customer['direccion'];
            $codProvincia = $customer['codProvincia'];
            $codCanton = $customer['codCanton'];
            $codDistrito = $customer['zip'];
            $zip = $customer['zip'];
            
            //Define el tipo de documento
            $tipoDocumento = $tipoPersona == "F" || $tipoPersona == '1' || $tipoPersona == '01' ? '01' : '04';
            $tipoDocumento = '01';
            if( !$codDistrito || $zip ){
                $tipoDocumento = "04";
            }
            
            $numeroReferencia = $invoiceData['DocNumber'];
            $consecutivoComprobante = $invoiceData['DocNumber'];
            $claveFactura = getDocumentKey($tipoDocumento, $company, $numeroReferencia);


            $invoice = Invoice::where('document_number', $consecutivoComprobante)
                                ->where('company_id', $company->id)
                                ->with('items')
                                ->first();
            if(isset($invoice)){
                return [
                    'status'  => '400',
                    'mensaje' => 'Factura existente.'
                ];
            }
            
            $TIPO_SERV = $invoiceData['TIPO_SERV'] ?? 'B';
            if( $tipoDocumento == '09' ){
                $TIPO_SERV = 'B';
            }
            
            $TIPO_PAGO = $invoiceData['TIPO_PAGO'] ?? 'D'; //Usan E, T, D, Q, etc
            $condicionVenta = "02";
            if($TIPO_PAGO == "E"){
                $condicionVenta = "01";
            }
            
            $MODO_PAGO = $invoiceData['MODO_PAGO'] ?? 'D'; //Usan E, T, D, Q, etc
            $metodoPago = "04"; //Default transferencia
            if($MODO_PAGO == 'E' || $MODO_PAGO == 'C'){
                $metodoPago = '01';
            }else if($MODO_PAGO == 'T'){
                $metodoPago = '02';
            }else if($MODO_PAGO == 'Q'){
                $metodoPago = '03';
            }
            
            if( $invoiceData['MONEDA'] == '02' ){
                $idMoneda = "USD";
                $tipoCambio = floatval($invoiceData['TIPO_CAMBIO']);
            }else{
                $idMoneda = "CRC";
                $tipoCambio = 1;
            }

            $FEC_HECHO = $invoiceData['FEC_HECHO'];
            $fechaEmision = Carbon::parse($FEC_HECHO);
            $fechaVencimiento = Carbon::parse($FEC_HECHO)->addMonths(1);
            
            $porcentajeIVA = $invoiceData['PORCIV'] ?? 0;
            $totalDocumento = 0;
            $descripcion = isset($invoiceData['OBSERVACION']) ? $invoiceData['OBSERVACION'] : '';
            $descripcion .= isset($invoiceData['NOTA1']) ? $invoiceData['NOTA1'] : '';
            $descripcion .= isset($invoiceData['NOTA2']) ? $invoiceData['NOTA2'] : '';
    
            //Exoneraciones
            $totalNeto = 0;
            $tipoDocumentoExoneracion = $invoiceData['CODIGOTIPOEXO'] ?? null;
            $documentoExoneracion = $invoiceData['DOCUMENTO_EXO'] ?? null;
            $companiaExoneracion = $invoiceData['COD_INST_EXO'] ?? null;
            $companiaExoneracion = $nombreCliente;
            $fechaExoneracion = $invoiceData['FEC_DOCU_EXO'] ?? null;
            $porcentajeExoneracion = $invoiceData['PORC_EXONERACION'] ?? 0;;
            
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
            
            $otherData = $this->setOtherInvoiceData($invoice, $invoiceData, $items, $requestOtros);
            
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
            dd($e);
        }
    }
    
    public static function syncMonthlyClients($dataService, $year, $month, $company){
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        $qbCustomers = QuickbooksInvoice::getMonthlyClients($dataService, $year, $month, $company);
        $qbCustomers = $qbCustomers ?? [];
        $clientes = [];
        foreach($qbCustomers as $customer){
            $cliente = new \stdClass();
            $cliente->full_name = $customer->FullyQualifiedName;
            $cliente->client_id = 0;
            $clientes[$customer->Id] = $cliente;
        }
        $qb->clients_json = $clientes;
        $qb->save();
    }
    
}
