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

class ProcessExcelSM implements ShouldQueue
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
            $collection = $this->collection;
            $company = $this->company;
            
            Log::info($company->id_number . " importanto Excel ventas con ".count($collection)." lineas");
            $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
            $i = 0;
            $invoiceList = array();

            foreach ($collection as $row){

                $metodoGeneracion = "etax-bulk";

                if( isset($row['doc_identificacion']) ){
                    $i++;
                    //Datos de proveedor
                    $nombreCliente = $row['nombre_tomador'];
                    $codigoCliente = isset($row['doc_identificacion']) ? $row['doc_identificacion'] : '';
                    $tipoPersona = $row['tipo_id'][0];
                    $identificacionCliente = $row['doc_identificacion'] ?? null;
                    $correoCliente = $row['correo'] ?? null;
                    $telefonoCliente = $row['telefono_celular'];

                    //Datos de factura
                    $consecutivoComprobante = $this->getDocReference('01', $company);
                    $claveFactura = $this->getDocumentKey('01', $company);
                    $company->last_invoice_ref_number = $company->last_invoice_ref_number+1;
                    $refNumber = $company->last_invoice_ref_number;
                    $condicionVenta = '02';
                    $metodoPago = str_pad((int)$row['medio_pago'], 2, '0', STR_PAD_LEFT);
                    $numeroLinea = isset($row['numerolinea']) ? $row['numerolinea'] : 1;
                    $fechaEmision = '15/07/2019';

                    $fechaVencimiento = isset($row['fechavencimiento']) ? $row['fechavencimiento'] : $fechaEmision;
                    $idMoneda = 'CRC';
                    $tipoCambio = $row['tipocambio'] ?? 1;
                    $totalDocumento = $row['total'];
                    $tipoDocumento = '01';
                    $descripcion = isset($row['descripcion']) ? $row['descripcion'] : ($row['descricpion'] ?? '');

                    //Datos de linea
                    $codigoProducto = $row['num_objeto'] ?? 'N/A';
                    $detalleProducto =isset($row['descripcion'])  ? $row['descripcion'] : $codigoProducto;
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
                    
                    $codigoActividad = $row['actividad_comercial'] ?? $mainAct;
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
                        'codeValidated' => true
                    );
                    
                    $invoiceList = Invoice::importInvoiceRow($arrayInsert, $invoiceList, $company);
                }
            }
            
            Log::info("$i procesadas...");
            $company->save();
            $user = $company->user;
            Cache::forget("cache-currentcompany-$user->id");
            
            foreach (array_chunk ( $invoiceList, 250 ) as $facturas) {
                Log::info("Mandando 250 a queue...");
                ProcessSendExcelInvoices::dispatch($facturas);
            }
            
        }catch( \Throwable $ex ){
            Log::error("Error importando excel archivo:" . $ex);
        }
    }

}
