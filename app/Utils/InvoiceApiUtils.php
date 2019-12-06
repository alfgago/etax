<?php

namespace App\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceApiUtils
{
	public function sendHacienda($request) {
		$fechaEmision = Carbon::parse($request->fechaEmision);
		$fechaVencimiento = Carbon::parse($request->fechaVencimiento);
		$myRequest = new Request();
		$data = [
				'client_id' => -1,
				'invoice_id' => 0,
            	'send_email' => null,
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
            	'recurrencia' => 0,
            	//'id_recurrente' => $request->clave,
            	//'dia' => $request->clave,
            	//'primer_quincena' => $request->clave,
            	//'segunda_quincena' => $request->clave,
            	//'mensual' => $request->clave,
            	//'dia_recurrencia' => $request->clave,
            	//'mes_recurrencia' => $request->clave,
            	//'cantidad_dias' => $request->clave,
            	'commercial_activity' => $request->codigoActividad,
            	'sale_condition' => $request->condicionVenta,
            	'payment_type' => $request->medioPago,
            	'retention_percent' => 0,
            	'other_reference' => $request->referencia,
            	'buy_order' => $request->ordenCompra,
            	'notas' => null,
            	'code' => $request->receptor['identificacion']['numero'],
            	'description' => $request->descripcion,
            	'typeDocument' => $this->typeDocumentClave($request->clave),
            	'is_catalogue' => false,
            	'tipo_persona' => $request->receptor['identificacion']['tipo'],
            	'id_number' => $request->receptor['identificacion']['numero'],
            	'first_name' => $request->receptor['nombre'],
            	'email' => $request->receptor['correoElectronico'],
            	'phone' => $request->receptor['telefono']['numTelefono'],
            	'country' => $request->receptor['ubicacion']['pais'],
            	'state' => $request->receptor['ubicacion']['provincia'],
            	'neighborhood' => $request->receptor['ubicacion']['barrio'],
            	'zip' => $request->receptor['ubicacion']['codigoPostal'],
            	'address' => $request->receptor['ubicacion']['otrasSenas'],
            	'es_exento' => $request->receptor['exento'],
            	'document_type' => $this->typeDocumentClave($request->clave),
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