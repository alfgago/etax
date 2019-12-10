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
                  'subtotal' => $request->totalVentaNeta,
                  'iva_amount' => $request->totalImpuesto,
                  'total_iva_devuelto' => $request->totalIVADevuelto,
                  'total_iva_exonerado' => $request->totalExento,
                  'total_otros_cargos' => $request->totalOtrosCargos,
                  'total' => $request->totalComprobante,
                  'document_number' => $request->numeroConsecutivo,
                  'document_key' => $request->clave,
                  'generated_date' => $fechaEmision->format('d/m/Y'),
                  'hora' => $fechaEmision->format('h:m A'),
                  'due_date' => $fechaVencimiento->format('d/m/Y'),
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
		          "item_number" => $lineas['numeroLinea'],
		          "id" => null,
		          "code" => $lineas['codigoComercial']['codigo'],
		          "name" => $lineas['detalle'],
		          "product_type" => $lineas['codigoComercial']['tipo'],
		          "item_count" => $lineas['cantidad'],
		          "measure_unit" => $lineas['unidadMedida'],
		          "unit_price" => $lineas['precioUnitario'],
		          "iva_type" => $lineas['impuesto']['codigoTarifa'],
		          "porc_identificacion_plena" => null,
		          "discount_type" => $lineas['tipoDescuento'] ?? null,
		          "discount" => $lineas['descuento'] ?? 0,
		          "subtotal" => $lineas['subTotal'],
		          "iva_percentage" => $lineas['impuesto']['tarifa'],
		          "iva_amount" => $lineas['impuesto']['monto'],
		          "total" => $lineas['montoTotal'],		        
		          "is_identificacion_especifica" => null,
		          "typeDocument" => $lineas['impuesto']['exoneracion']['tipoDocumento'],
		          "numeroDocumento" => $lineas['impuesto']['exoneracion']['numeroDocumento'],
		          "nombreInstitucion" => $lineas['impuesto']['exoneracion']['nombreInstitucion'],
		          "exoneration_date" => $lineas['impuesto']['exoneracion']['fechaEmision'],
		          "porcentajeExoneracion" => $lineas['impuesto']['exoneracion']['porcentajeExoneracion'],
		          "montoExoneracion" => $lineas['impuesto']['exoneracion']['montoExoneracion'],
		          "impuestoNeto" => $lineas['impuestoNeto'] ?? 0,
		          "montoTotalLinea" => $lineas['montoTotalLinea'] ?? 0,
		          "tariff_heading" => null,
		          "exoneradalinea" => $lineas['exento'] ?? 0
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