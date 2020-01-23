<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
use App\SMInvoice;
use App\Mail\CreditNoteNotificacion;
use App\Utils\BridgeHaciendaApi;
use App\Utils\InvoiceUtils;
use App\Utils\BillUtils;
use App\Jobs\ProcessReception;
use App\XmlHacienda;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ProcessSendSMInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $excelCollection = null;
    private $companyId = null;
    private $fileType = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($excelCollection, $companyId)
    {
        $this->excelCollection = $excelCollection;
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $company = Company::find($this->companyId);
        $excelCollection = $this->excelCollection;
        
        sleep (1);
        Log::notice("$company->id_number importando ".count($excelCollection)." lineas... Last Invoice: $company->last_invoice_ref_number");
        $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
        $i = 0;
        $invoiceList = array();
        $descripciones = $excelCollection->pluck('descripcion');
        $numFacturas = $excelCollection->pluck('num_factura');
        $existingInvoices = Invoice::select('id', 'description', 'total', 'document_key', 'buy_order')->where('company_id', $this->companyId)->whereIn('description', $descripciones)->whereIn('buy_order', $numFacturas)->get();
        
        foreach ($excelCollection as $row){
            try{
                $metodoGeneracion = "etax-bulk";
                if( isset($row['doc_identificacion']) ){
                    $descripcion = isset($row['descripcion']) ? $row['descripcion'] : ($row['descricpion'] ?? null);
                    $ordenCompra = $row['num_factura'] ?? 'No indica';
                    $totalDocumento = $row['total'];
                    $fileType = $row['document_type'];
                    $existingInvoice = $existingInvoices->where('description', $descripcion)->where('buy_order', $ordenCompra)->first();
                    if( ! isset($existingInvoice) ){
                        $i++;
    
                        //Datos de proveedor
                        $nombreCliente = $row['nombre_tomador'];
                        $identificacionCliente = ltrim($row['doc_identificacion'], '0') ?? null;
                        $codigoCliente = $identificacionCliente;
                        $tipoPersona = $row['tipo_id'][0];
                        $correoCliente = $row['correo'] ?? null;
                        $telefonoCliente = $row['telefono_celular'] ? $row['telefono_celular'] : ( $row['telefono_habitacion'] ?? null );
                        $today = Carbon::parse( now('America/Costa_Rica') );
                    
                        //Datos de factura
                        $consecutivoComprobante = $this->getDocReference('01', $company);
                        $claveFactura = $this->getDocumentKey('01', $company);
                        $company->last_invoice_ref_number = $company->last_invoice_ref_number+1;
                        $company->last_document = $consecutivoComprobante;
                        $refNumber = $company->last_invoice_ref_number;
                        
                        $condicionVenta = '02';
                        $metodoPago = str_pad((int)$row['medio_pago'], 2, '0', STR_PAD_LEFT);
                        //$metodoPago = '99';
                        $numeroLinea = isset($row['numerolinea']) ? $row['numerolinea'] : 1;
                        $fechaEmision = $today->format('d/m/Y');
                        $fechaVencimiento = isset($row['fecha_pago']) ? $row['fecha_pago']."" : $fechaEmision; 
                        if (!isset($fechaVencimiento) || $fechaVencimiento == "" ){
                            $fechaVencimiento = $fechaEmision;
                        }else{
                            $fechaVencimiento = "30/".$fechaVencimiento[4].$fechaVencimiento[5]."/".$fechaVencimiento[0].$fechaVencimiento[1].$fechaVencimiento[2].$fechaVencimiento[3];
                        }
                        
                        $idMoneda = 'CRC';
                        $tipoCambio = $row['tipocambio'] ?? 1;
                        $totalDocumento = $row['total'];
                        $tipoDocumento = $fileType ?? '01';
                        
                        //Datos de linea
                        $codigoProducto = $row['num_objeto'] ?? 'N/A';
                        $detalleProducto = isset($descripcion)  ? $descripcion : $codigoProducto;
                        $unidadMedicion = 'Os';
                        $cantidad = isset($row['cantidad']) ? $row['cantidad'] : 1;
                        $precioUnitario = $row['precio_unitario'];
                        $subtotalLinea = (float)$row['precio_unitario'];
                        $totalLinea = $row['total'];
                        $montoDescuento = isset($row['montodescuento']) ? $row['montodescuento'] : 0;
                        $codigoEtax = $row['codigoivaetax'] ?? 'S102';
                        $categoriaHacienda = 7;
                        $montoIva = (float)$row['impuesto'];
                        $acceptStatus = isset($row['aceptada']) ? $row['aceptada'] : 1;
                        
                        //$codigoActividad = $row['actividad_comercial'] ?? $mainAct;
                        $codigoActividad = 660101; //No viene en el Excel del todo.
                        $xmlSchema = 43;
                        
                        //Exoneraciones
                        $totalNeto = 0;
                        $tipoDocumentoExoneracion = $row['tipodocumentoexoneracion'] ?? null;
                        $documentoExoneracion = $row['documentoexoneracion'] ?? null;
                        $companiaExoneracion = $row['companiaexoneracion'] ?? null;
                        $porcentajeExoneracion = $row['porcentajeexoneracion'] ?? 0;
                        $montoExoneracion = $row['montoexoneracion'] ?? 0;
                        $impuestoNeto = $row['impuestoneto'] ?? 0;
                        $totalMontoLinea = $row['totalmontolinea'] ?? 0;
                        
                        $direccion = $row['des_direccion'] ?? null;
                        $zip = $row['codigo_postal'] ?? '10101';
                    
                        $otherReference = $row['refer_factura'] ?? null;
                        
                        $arrayInsert = array(
                            'metodoGeneracion' => trim($metodoGeneracion),
                            'idEmisor' => 0,
                            'nombreCliente' => trim($nombreCliente),
                            'descripcion' => trim($descripcion),
                            'codigoCliente' => trim($codigoCliente),
                            'tipoPersona' => trim($tipoPersona),
                            'identificacionCliente' => trim($identificacionCliente),
                            'correoCliente' => trim($correoCliente),
                            'telefonoCliente' => trim($telefonoCliente),
                            'direccion' => trim($direccion),
                            'zip' => trim($zip),
                            'claveFactura' => trim($claveFactura),
                            'consecutivoComprobante' => trim($consecutivoComprobante),
                            'numeroReferencia' => $refNumber,
                            'condicionVenta' => trim($condicionVenta),
                            'metodoPago' => trim($metodoPago),
                            'numeroLinea' => trim($numeroLinea),
                            'fechaEmision' => trim($fechaEmision),
                            'fechaVencimiento' => trim($fechaVencimiento),
                            'moneda' => trim($idMoneda),
                            'tipoCambio' => trim($tipoCambio),
                            'totalDocumento' => trim($totalDocumento),
                            'totalNeto' => trim($totalNeto),
                            'cantidad' => trim($cantidad),
                            'precioUnitario' => trim($precioUnitario),
                            'totalLinea' => trim($totalLinea),
                            'montoIva' => trim($montoIva),
                            'porcentajeIva' => 2,
                            'codigoEtax' => trim($codigoEtax),
                            'montoDescuento' => trim($montoDescuento),
                            'subtotalLinea' => trim($subtotalLinea),
                            'tipoDocumento' => trim($tipoDocumento),
                            'codigoProducto' => trim($codigoProducto),
                            'detalleProducto' => trim($detalleProducto),
                            'unidadMedicion' => trim($unidadMedicion),
                            'tipoDocumentoExoneracion' => trim($tipoDocumentoExoneracion),
                            'documentoExoneracion' => trim($documentoExoneracion),
                            'companiaExoneracion' => trim($companiaExoneracion),
                            'porcentajeExoneracion' => trim($porcentajeExoneracion),
                            'montoExoneracion' => trim($montoExoneracion),
                            'impuestoNeto' => trim($impuestoNeto),
                            'totalMontoLinea' => trim($totalMontoLinea),
                            'xmlSchema' => trim($xmlSchema),
                            'codigoActividad' => trim($codigoActividad),
                            'categoriaHacienda' => trim($categoriaHacienda),
                            'acceptStatus' => trim($acceptStatus),
                            'isAuthorized' => true,
                            'codeValidated' => true,
                            'ordenCompra' => $ordenCompra,
                            'otherReference' => $otherReference
                        );
                        
                        $invoiceList = Invoice::importInvoiceRow($arrayInsert, $invoiceList, $company);
                    }else {
                        $smInvoice = SMInvoice::where('descripcion', $invoice->description)->where('num_factura', $invoice->buy_order)->first();
                        $smInvoice->document_key = $invoice->document_key;
                        $smInvoice->invoice_id = $invoice->id;
                        $smInvoice->save();
                    }
                }
            }catch( \Throwable $ex ){
                Log::error("Error en factura SM:" . $ex);
            }
        }
        $company->save();
        $userId = $company->user_id;
        Cache::forget("cache-currentcompany-$userId");
                
        Log::debug("Agregando facturas a queue");
        foreach($invoiceList as $fac){
            ProcessSendExcelSingleInvoice::dispatch($fac)->onQueue('imports');
        }
        Log::debug($i." facturas importadas por excel");
    }
    
    private function getDocReference($docType, $company = false) {
        if(!$company){
            $company = currentCompanyModel();
        }
        if ($docType == '01') {
            $lastSale = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '03') {
            $lastSale = $company->last_note_ref_number + 1;
        }
        if ($docType == '08') {
            $lastSale = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $lastSale = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '04') {
            $lastSale = $company->last_ticket_ref_number + 1;
        }
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    private function getDocumentKey($docType, $company = false) {
        if(!$company){
            $company = currentCompanyModel();
        }
        $invoice = new Invoice();
        if ($docType == '01') {
            $ref = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '03') {
            $ref = $company->last_note_ref_number + 1;
        }
        if ($docType == '08') {
            $ref = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $ref = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '04') {
            $ref = $company->last_ticket_ref_number + 1;
        }
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType, $company).
            '1'.$invoice->getHashFromRef($ref);

        return $key;
    }

}
