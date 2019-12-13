<?php

namespace App\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillApiUtils
{
	public function store($request) {
		$fechaEmision = Carbon::parse($request->fechaEmision);
		$fechaVencimiento = Carbon::parse($request->fechaVencimiento);
		$myRequest = new Request();
		$data = [
                  'provider_id' => '-1',
                  'currency' => $request->moneda,
                  'currency_rate' => $request->tipoCambio,
                  'subtotal' => $request->resumenFactura['totalVentaNeta'] ?? 0,
                  'iva_amount' => $request->resumenFactura['totalImpuesto'] ?? 0,
                  'total_iva_devuelto' => $request->resumenFactura['totalIvaDevuelto'] ?? 0,
                  'total_iva_exonerado' => $request->resumenFactura['totalExento'] ?? 0,
                  'total_otros_cargos' => $request->resumenFactura['totalOtrosCargos'] ?? 0,
                  'total' => $request->resumenFactura['totalComprobante'] ?? 0,
                  'document_number' => $request->numeroConsecutivo,
                  'document_key' => $request->clave,
                  'generated_date' => $fechaEmision->format('Y-m-d'),
                  'hora' => $fechaEmision->format('h:m A'),
                  'due_date' => $fechaVencimiento->format('Y-m-d') ?? $fechaEmision->format('Y-m-d'),
                  'activity_company_verification' => $request->codigoActividad,
                  'sale_condition' => $request->condicionVenta,
                  'payment_type' => $request->medioPago,
                  'other_reference' => $request->referencia,
                  'buy_order' => $request->ordenCompra,
                  'code' => $request->emisor['identificacion']['numero'], 
                  'description' => $request->descripcion,
                  'is_catalogue' => false,
                  "accept_status" => "on",
                  "accept_iva_condition" => "01",
                  "accept_iva_acreditable" => "0",
                  "accept_iva_gasto" => "0" ,     	    	
            	'tipo_persona' => $request->emisor['identificacion']['tipo'],
            	'id_number' => $request->emisor['identificacion']['numero'],
            	'first_name' => $request->emisor['nombre'],
            	'email' => $request->emisor['correoElectronico'],
            	'phone' => $request->emisor['telefono']['numTelefono'],
            	'country' => $request->emisor['ubicacion']['pais'],
            	'state' => $request->emisor['ubicacion']['provincia'],
            	'neighborhood' => $request->emisor['ubicacion']['barrio'],
            	'zip' => $request->emisor['ubicacion']['codigoPostal'],
            	'address' => $request->emisor['ubicacion']['otrasSenas'],
            	'es_exento' => $request->emisor['exento'],
            	'items' => []
		];

		foreach($request->detalleServicio as $lineas){
		    $linea = [
		          "item_number" => $lineas['lineaDetalle']['numeroLinea'],
		          "id" => null,
		          "code" => $lineas['lineaDetalle']['codigoComercial']['codigo'],
		          "name" => $lineas['lineaDetalle']['detalle'],
		          "product_type" => $lineas['lineaDetalle']['codigoComercial']['tipo'],
		          "item_count" => $lineas['lineaDetalle']['cantidad'],
		          "measure_unit" => $lineas['lineaDetalle']['unidadMedida'],
		          "unit_price" => $lineas['lineaDetalle']['precioUnitario'],
		          "iva_type" => $lineas['lineaDetalle']['impuesto']['codigoTarifa'],
		          "porc_identificacion_plena" => null,
		          "discount_type" => $lineas['lineaDetalle']['tipoDescuento'] ?? null,
		          "discount" => $lineas['lineaDetalle']['descuento'] ?? 0,
		          "subtotal" => $lineas['lineaDetalle']['subTotal'],
		          "iva_percentage" => $lineas['lineaDetalle']['impuesto']['tarifa'],
		          "iva_amount" => $lineas['lineaDetalle']['impuesto']['monto'],
		          "total" => $lineas['lineaDetalle']['montoTotal'],		        
		          "is_identificacion_especifica" => null,
		          "typeDocument" => $lineas['lineaDetalle']['impuesto']['exoneracion']['tipoDocumento'],
		          "numeroDocumento" => $lineas['lineaDetalle']['impuesto']['exoneracion']['numeroDocumento'],
		          "nombreInstitucion" => $lineas['lineaDetalle']['impuesto']['exoneracion']['nombreInstitucion'],
		          "exoneration_date" => $lineas['lineaDetalle']['impuesto']['exoneracion']['fechaEmision'],
		          "porcentajeExoneracion" => $lineas['lineaDetalle']['impuesto']['exoneracion']['porcentajeExoneracion'],
		          "montoExoneracion" => $lineas['lineaDetalle']['impuesto']['exoneracion']['montoExoneracion'],
		          "impuestoNeto" => $lineas['lineaDetalle']['impuestoNeto'] ?? 0,
		          "montoTotalLinea" => $lineas['lineaDetalle']['montoTotalLinea'] ?? 0,
		          "tariff_heading" => null,
		          "exoneradalinea" => $lineas['lineaDetalle']['exento'] ?? 0
			];
			array_push($data['items'],$linea);
		}
		$myRequest->replace($data);
		return $myRequest;
	}

	private function typeDocumentClave($clave){
		return substr($clave, 22, 2);
	}
}