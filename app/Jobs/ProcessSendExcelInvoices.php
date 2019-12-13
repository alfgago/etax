<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
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

class ProcessSendExcelInvoices implements ShouldQueue
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
    public function __construct($excelCollection, $companyId, $fileType)
    {
        $this->excelCollection = $excelCollection;
        $this->companyId = $companyId;
        $this->fileType = $fileType;
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
        $fileType = $this->fileType;
        
        Log::notice("$company->id_number importando ".count($excelCollection)." lineas... Last Invoice: $company->last_invoice_ref_number");
        $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
        $i = 0;
        $invoiceList = array();
        foreach ($excelCollection as $row){
            try{

                $metodoGeneracion = "etax-bulk";
                    
                if( isset($row['doc_identificacion']) ){
                    $descripcion = isset($row['descripcion']) ? $row['descripcion'] : ($row['descricpion'] ?? null);
                    $totalDocumento = $row['total'];
                    if( ! Invoice::where("description", $descripcion)->where('total', $totalDocumento)->count() ){
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
                        $ordenCompra = $row['num_factura'] ?? 'No indica';
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
                        if($fileType == '03'){
                            try{
                                $invoice = Invoice::where("description", $descripcion)->where('total', $totalDocumento)->where('hacienda_status', '01')->first();
                                if( isset($invoice) ){
                                    $otherReference = $row['refer_factura'] ?? null;
                                    Log::info("Actualizando data de NC: $otherReference");
                                    if ( isset($otherReference) ) {
                                        $ref = Invoice::where('company_id', $company->id)
                                          ->where('buy_order', $otherReference)
                                          ->first();
                                        $invoice->code_note = '01';
                                        $invoice->resend_attempts = 0;
                                        $invoice->in_queue = false;
                                        $invoice->reason = 'Factura anulada';
                                        $invoice->other_reference = $ref->reference_number;
                                        $invoice->reference_generated_date = $ref->generated_date;
                                        $invoice->reference_document_key = $ref->document_key;
                                        $invoice->reference_doc_type = $ref->document_type;
                                        $invoice->save();
                                    }
                                }
                            }catch(\Exception $e){
                                Log::error("Error en import NC SM: " . $e);
                            }
                        }
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
