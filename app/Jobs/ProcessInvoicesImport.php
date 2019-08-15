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

class ProcessInvoicesImport implements ShouldQueue
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
            Log::info($this->company->id_number . " importanto Excel ventas con ".count($this->collection)." lineas");
            $company = $this->company;
            $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
            $i = 0;
            foreach (array_chunk ( $this->collection, 250 ) as $facturas) {
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
                                $nombreCliente = $row['nombrecliente'];
                                $codigoCliente = isset($row['codigocliente']) ? $row['codigocliente'] : '';
                                $tipoPersona = (int)$row['tipoidentificacion'];
                                $identificacionCliente = $row['identificacionreceptor'] ?? null;
                                $correoCliente = $row['correoreceptor'] ?? null;
                                $telefonoCliente = null;
        
                                //Datos de factura
                                $consecutivoComprobante = $row['consecutivocomprobante'];
                                $claveFactura = isset($row['clavefactura']) ? $row['clavefactura'] : '';
                                $condicionVenta = str_pad((int)$row['condicionventa'], 2, '0', STR_PAD_LEFT);
                                $metodoPago = str_pad((int)$row['metodopago'], 2, '0', STR_PAD_LEFT);
                                $numeroLinea = isset($row['numerolinea']) ? $row['numerolinea'] : 1;
                                $fechaEmision = $row['fechaemision'];
        
                                $fechaVencimiento = isset($row['fechavencimiento']) ? $row['fechavencimiento'] : $fechaEmision;
                                $idMoneda = $row['moneda'];
                                $tipoCambio = $row['tipocambio'];
                                $totalDocumento = $row['totaldocumento'];
                                $tipoDocumento = str_pad((int)$row['tipodocumento'], 2, '0', STR_PAD_LEFT);
                                $descripcion = isset($row['descripcion'])  ? $row['descripcion'] : '';
        
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
                                
                                //Exoneraciones
                                $totalNeto = 0;
                                $tipoDocumentoExoneracion = $row['tipodocumentoexoneracion'] ?? null;
                                $documentoExoneracion = $row['documentoexoneracion'] ?? null;
                                $companiaExoneracion = $row['companiaexoneracion'] ?? null;
                                $porcentajeExoneracion = $row['porcentajeexoneracion'] ?? 0;
                                $montoExoneracion = $row['montoexoneracion'] ?? 0;
                                $impuestoNeto = $row['impuestoneto'] ?? 0;
                                $totalMontoLinea = $row['totalmontolinea'] ?? 0;
                                
                                
                                $arrayInsert = array(
                                    'metodoGeneracion' => $metodoGeneracion,
                                    'idEmisor' => 0,
                                    'nombreCliente' => $nombreCliente,
                                    'descripcion' => $descripcion,
                                    'codigoCliente' => $codigoCliente,
                                    'tipoPersona' => $tipoPersona,
                                    'identificacionCliente' => $identificacionCliente,
                                    'correoCliente' => $correoCliente,
                                    'telefonoCliente' => $telefonoCliente,
                                    'claveFactura' => $claveFactura,
                                    'consecutivoComprobante' => $consecutivoComprobante,
                                    'condicionVenta' => $condicionVenta,
                                    'metodoPago' => $metodoPago,
                                    'numeroLinea' => $numeroLinea,
                                    'fechaEmision' => $fechaEmision,
                                    'fechaVencimiento' => $fechaVencimiento,
                                    'moneda' => $idMoneda,
                                    'tipoCambio' => $tipoCambio,
                                    'totalDocumento' => $totalDocumento,
                                    'totalNeto' => $totalNeto,
                                    'cantidad' => $cantidad,
                                    'precioUnitario' => $precioUnitario,
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
                                    'acceptStatus' => $acceptStatus,
                                    'isAuthorized' => true,
                                    'codeValidated' => true
                                );
                                $bill = Invoice::importInvoiceRow($arrayInsert, $company);
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
