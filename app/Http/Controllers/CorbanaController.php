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
        $this->middleware('auth', ['except' => ['sendInvoice']] );
        $this->middleware('CheckSubscription', ['except' => ['sendInvoice']] );
    }
    
    public function sendInvoice(Request $request) {
 
        try{
            $factura = $request->factura[0];
            $lineas = $request->lineas[0];
            Log::debug("CORBANA RECIBE: FACTURA" . json_encode($factura) . " LINEAS: " . json_encode($lineas) );
            $metodoGeneracion = "Corbana";

            $cedulaEmpresa = '3101707070';
            
            //Datos de cliente
            $nombreCliente = $row['RAZON_SOCIAL'];
            $codigoCliente = $row['IDENTIFICACION'];
            $tipoPersona = $row['TIPO_IDEN'];
            $identificacionCliente = $row['IDENTIFICACION'] ?? null;
            $correoCliente = $row['CORREO_CLIENTE'] ?? null;
            $telefonoCliente = null;

            //Datos de factura
            $numeroReferencia = $row['NO_DOCU'];
            $consecutivoComprobante = $row['consecutivocomprobante'];
            $claveFactura = isset($row['clavefactura']) ? $row['clavefactura'] : $consecutivoComprobante;
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
            $categoriaHacienda = isset($row['categoriahacienda']) ? $row['categoriahacienda'] : (isset($row['categoriadeclaracion']) ? $row['categoriadeclaracion'] : null);
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
                'numeroReferencia' => $numeroReferencia,
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

            $invoiceList = Invoice::importInvoiceRow($arrayInsert, $invoiceList, $company);
        }catch(\Exception $e){
            Log::error("Error en Corbana" . $e);
            return response()->json([
                'mensaje' => 'Error ' . $e->getMessage()
            ], 200);
        }
        
        return response()->json([
            'mensaje' => 'Exito'
        ], 200);
        
        
    }
    
}
