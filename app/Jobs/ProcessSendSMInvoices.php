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
        
        foreach ($excelCollection as $smInvoice){
            try{
                $metodoGeneracion = "etax-bulk";
                if( isset($smInvoice['doc_identificacion']) ){
                    $descripcion = isset($smInvoice['descripcion']) ? $smInvoice['descripcion'] : ($smInvoice['descricpion'] ?? null);
                    $ordenCompra = $smInvoice['num_factura'] ?? 'No indica';
                    $totalDocumento = $smInvoice['total'];
                    $fileType = $smInvoice['document_type'];
                    $existingInvoice = $existingInvoices->where('description', $descripcion)->where('buy_order', $ordenCompra)->first();
                    if( ! isset($existingInvoice) ){
                        $i++;
    
                        $tipoDocumento = $fileType ?? '01';
                        //Datos de proveedor
                        $nombreCliente = $smInvoice['nombre_tomador'];
                        $identificacionCliente = ltrim($smInvoice['doc_identificacion'], '0') ?? null;
                        $codigoCliente = $identificacionCliente;
                        $tipoPersona = $smInvoice['tipo_id'][0];
                        $correoCliente = $smInvoice['correo'] ?? null;
                        $telefonoCliente = $smInvoice['telefono_celular'] ? $smInvoice['telefono_celular'] : ( $smInvoice['telefono_habitacion'] ?? null );
                        $today = Carbon::parse( now('America/Costa_Rica') );
                    
                        //Datos de factura
                        $consecutivoComprobante = getDocReference($tipoDocumento, $company);
                        $claveFactura = getDocumentKey($tipoDocumento, $company);
                        $company->last_invoice_ref_number = $company->last_invoice_ref_number+1;
                        $company->last_document = $consecutivoComprobante;
                        $refNumber = $company->last_invoice_ref_number;
                        
                        $condicionVenta = '02';
                        $metodoPago = str_pad((int)$smInvoice['medio_pago'], 2, '0', STR_PAD_LEFT);
                        //$metodoPago = '99';
                        $numeroLinea = isset($smInvoice['numerolinea']) ? $smInvoice['numerolinea'] : 1;
                        $fechaEmision = $today->format('d/m/Y');
                        $fechaVencimiento = isset($smInvoice['fecha_pago']) ? $smInvoice['fecha_pago']."" : $fechaEmision; 
                        if (!isset($fechaVencimiento) || $fechaVencimiento == "" ){
                            $fechaVencimiento = $fechaEmision;
                        }else{
                            $fechaVencimiento = "30/".$fechaVencimiento[4].$fechaVencimiento[5]."/".$fechaVencimiento[0].$fechaVencimiento[1].$fechaVencimiento[2].$fechaVencimiento[3];
                        }
                        
                        $idMoneda = 'CRC';
                        $tipoCambio = $smInvoice['tipocambio'] ?? 1;
                        $totalDocumento = $smInvoice['total'];
                        
                        //Datos de linea
                        $codigoProducto = $smInvoice['num_objeto'] ?? 'N/A';
                        $detalleProducto = isset($descripcion)  ? $descripcion : $codigoProducto;
                        $unidadMedicion = 'Os';
                        $cantidad = isset($smInvoice['cantidad']) ? $smInvoice['cantidad'] : 1;
                        $precioUnitario = $smInvoice['precio_unitario'];
                        $subtotalLinea = (float)$smInvoice['precio_unitario'];
                        $totalLinea = $smInvoice['total'];
                        $montoDescuento = isset($smInvoice['montodescuento']) ? $smInvoice['montodescuento'] : 0;
                        $codigoEtax = $smInvoice['codigoivaetax'] ?? 'S102';
                        $categoriaHacienda = 7;
                        $montoIva = (float)$smInvoice['impuesto'];
                        $acceptStatus = isset($smInvoice['aceptada']) ? $smInvoice['aceptada'] : 1;
                        
                        //$codigoActividad = $smInvoice['actividad_comercial'] ?? $mainAct;
                        $codigoActividad = 660101; //No viene en el Excel del todo.
                        $xmlSchema = 43;
                        
                        //Exoneraciones
                        $totalNeto = 0;
                        $tipoDocumentoExoneracion = $smInvoice['tipodocumentoexoneracion'] ?? null;
                        $documentoExoneracion = $smInvoice['documentoexoneracion'] ?? null;
                        $companiaExoneracion = $smInvoice['companiaexoneracion'] ?? null;
                        $porcentajeExoneracion = $smInvoice['porcentajeexoneracion'] ?? 0;
                        $montoExoneracion = $smInvoice['montoexoneracion'] ?? 0;
                        $impuestoNeto = $smInvoice['impuestoneto'] ?? 0;
                        $totalMontoLinea = $smInvoice['totalmontolinea'] ?? 0;
                        
                        $direccion = $smInvoice['des_direccion'] ?? null;
                        $zip = $smInvoice['codigo_postal'] ?? '10101';
                    
                        $otherReference = $smInvoice['refer_factura'] ?? null;
                        
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
                        $smInvoice = SMInvoice::where('descripcion', $existingInvoice->description)->where('num_factura', $existingInvoice->buy_order)->first();
                        if( isset($smInvoice) ){
                            $smInvoice->document_key = $existingInvoice->document_key;
                            $smInvoice->invoice_id = $existingInvoice->id;
                            $smInvoice->save();
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


}
