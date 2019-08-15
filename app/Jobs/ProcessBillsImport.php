<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Bill;
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

class ProcessBillsImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $collection = null;
    private $company = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($collection, $company)
    {
        $this->collection = $collection;
        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info($this->company->id_number . " importanto Excel compras con ".count($this->collection)." lineas");
            $company = $this->company;
            $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
            foreach (array_chunk ( $this->collection, 100 ) as $facturas) {
                Log::info("Procesando batch de 100...");
                sleep(1);
                //foreach ($arr500 as $facturas) {
                    //\DB::transaction(function () use ($facturas, &$i) {
                        $inserts = array();
                        foreach ($facturas as $row){
                            $i++;
    
                            $metodoGeneracion = "XLSX";
                            
                            if( isset($row['consecutivocomprobante']) ){
                                //Datos de proveedor
                                $nombreProveedor = $row['nombreproveedor'];
                                $codigoProveedor = $row['codigoproveedor'] ? $row['codigoproveedor'] : '';
                                $tipoPersona = (int)$row['tipoidentificacion'];
                                if( isset($row['identificacionproveedor']) ){
                                    $identificacionProveedor = $row['identificacionproveedor'];
                                }else{
                                    $identificacionProveedor = $row['identificacionreceptor'];
                                }
                                if( isset($row['correoproveedor']) ){
                                    $correoProveedor = $row['correoproveedor'];
                                }else{
                                    $correoProveedor = $row['correoreceptor'];
                                }
                                
                                $telefonoProveedor = null;
        
                                //Datos de factura
                                $consecutivoComprobante = $row['consecutivocomprobante'];
                                $claveFactura = isset($row['clavefactura']) ? $row['clavefactura'] : '';
                                $condicionVenta = str_pad((int)$row['condicionventa'], 2, '0', STR_PAD_LEFT);
                                $metodoPago = str_pad((int)$row['metodopago'], 2, '0', STR_PAD_LEFT);
                                $numeroLinea = isset($row['numerolinea']) ? $row['numerolinea'] : 1;
                                $fechaEmision = $row['fechaemision'];
                                $fechaVencimiento = isset($row['fechavencimiento']) ? $row['fechavencimiento'] : $fechaEmision;
                                $moneda = $row['moneda'];
                                $tipoCambio = $row['tipocambio'];
                                $totalDocumento = $row['totaldocumento'];
                                $tipoDocumento = str_pad((int)$row['tipodocumento'], 2, '0', STR_PAD_LEFT);
                                $descripcion = isset($row['descripcion']) ? $row['descripcion'] : '';
        
                                //Datos de linea
                                $codigoProducto = $row['codigoproducto'];
                                $detalleProducto = $row['detalleproducto'];
                                $unidadMedicion = $row['unidadmedicion'];
                                $cantidad = isset($row['cantidad']) ? $row['cantidad'] : 1;
                                $precioUnitario = $row['preciounitario'];
                                $subtotalLinea = (float)$row['subtotallinea'];
                                $totalLinea = $row['totallinea'];
                                $montoDescuento = isset($row['montodescuento']) ? $row['montodescuento'] : 0;
                                $codigoEtax = $row['codigoivaetax'];
                                $categoriaHacienda = isset($row['categoriaHacienda']) ? $row['categoriaHacienda'] : null;
                                $montoIva = (float)$row['montoiva'];
                                $acceptStatus = isset($row['aceptada']) ? $row['aceptada'] : 1;
                                $codigoActividad = $row['actividadcomercial'] ?? $mainAct;
                                $xmlSchema = $row['xmlschema'] ?? 43;
        
                                //Datos de exoneracion
                                $totalNeto = 0;
                                $tipoDocumentoExoneracion = $row['tipodocumentoexoneracion'] ?? null;
                                $documentoExoneracion = $row['documentoexoneracion'] ?? null;
                                $companiaExoneracion = $row['companiaexoneracion'] ?? null;
                                $porcentajeExoneracion = $row['porcentajeexoneracion'] ?? 0;
                                $montoExoneracion = $row['montoexoneracion'] ?? 0;
                                $impuestoNeto = $row['impuestoneto'] ?? 0;
                                $totalMontoLinea = $row['totalmontolinea'] ?? 0;
        
                                $codigoEtax = str_pad($codigoEtax, 3, '0', STR_PAD_LEFT);
        
                                $arrayImportBill = array(
                                    'metodoGeneracion' => $metodoGeneracion,
                                    'idReceptor' => 0,
                                    'nombreProveedor' => $nombreProveedor,
                                    'codigoProveedor' => $codigoProveedor,
                                    'tipoPersona' => $tipoPersona,
                                    'identificacionProveedor' => $identificacionProveedor,
                                    'correoProveedor' => $correoProveedor,
                                    'telefonoProveedor' => $telefonoProveedor,
                                    'claveFactura' => $claveFactura,
                                    'consecutivoComprobante' => $consecutivoComprobante,
                                    'condicionVenta' => $condicionVenta,
                                    'metodoPago' => $metodoPago,
                                    'numeroLinea' => $numeroLinea,
                                    'fechaEmision' => $fechaEmision,
                                    'fechaVencimiento' => $fechaVencimiento,
                                    'moneda' => $moneda,
                                    'tipoCambio' => $tipoCambio,
                                    'totalDocumento' => $totalDocumento,
                                    'totalNeto' => $totalNeto,
                                    'tipoDocumento' => $tipoDocumento,
                                    'codigoProducto' => $codigoProducto,
                                    'detalleProducto' => $detalleProducto,
                                    'unidadMedicion' => $unidadMedicion,
                                    'cantidad' => $cantidad,
                                    'precioUnitario' => $precioUnitario,
                                    'subtotalLinea' => $subtotalLinea,
                                    'totalLinea' => $totalLinea,
                                    'montoDescuento' => $montoDescuento,
                                    'codigoEtax' => $codigoEtax,
                                    'montoIva' => $montoIva,
                                    'descripcion' => $descripcion,
                                    'isAuthorized' => true,
                                    'codeValidated' => true,
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
                                    'acceptStatus' => $acceptStatus
                                );
                                $bill = Bill::importBillRow($arrayImportBill, $company);
                            }
                        }
                    //});
                //}
            };
        }catch( \Throwable $ex ){
            Log::error("Error importando Excel. Empresa: ".$this->company->id_number.", Archivo:" . $ex);
        }
    
        $this->company->save();
    }

}
