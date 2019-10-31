<div class="row">
	<div class="col-md-3 col-sm-12">
		<h3>Cliente</h3><br>
		<label>nombre Receptor</label>: {{$factura[0]->nombreReceptor}}<br>
		<label>tipo Identificacion</label>: {{$factura[0]->tipoIdentificacionReceptor}}<br>
		<label>Identificacion</label>: {{$factura[0]->IdentificacionReceptor}}<br>
		<label>correo</label>: {{$factura[0]->correoReceptor}}<br>
		<!--
		<label>provincia</label>: {{$factura[0]->provincia}}<br>
		<label>canton</label>: {{$factura[0]->canton}}<br>
		<label>distrito</label>: {{$factura[0]->distrito}}<br>-->
		<label>direccion</label>: {{$factura[0]->direccionReceptor}}<br>
	</div>
	<div class="col-md-3 col-sm-12">
		<h3>Detalle Factura</h3><br>
		<label>condicion Venta</label>: {{$factura[0]->condicionVenta}}<br>
		<label>plazo Credito</label>: {{$factura[0]->plazoCredito}}<br>
		<label>medio Pago</label>: {{$factura[0]->medioPago}}<br>
		<label>codigo Moneda</label>: {{$factura[0]->codigoMoneda}}<br>
		<label>tipo Cambio</label>: {{$factura[0]->tipoCambio}}<br>
	</div>
	<div class="col-md-3 col-sm-12">
		<h3>Totales Detallados</h3><br>
		<label>total Servicios Gravados</label>: {{$factura[0]->totalServGravados}}<br>
		<label>total Servicios Exentos</label>: {{$factura[0]->totalServExentos}}<br>
		<label>total Mercancias Gravadas</label>: {{$factura[0]->totalMercanciasGravadas}}<br>
		<label>total Mercancias Exentas</label>: {{$factura[0]->totalMercanciasExentas}}<br>
		<label>total Gravado</label>: {{$factura[0]->totalGravado}}<br>
		<label>total Exento</label>: {{$factura[0]->totalExento}}<br>
	</div>
	<div class="col-md-3 col-sm-12">
		<h3>Totales Factura</h3><br>
		<label>total Venta</label>: {{$factura[0]->totalVenta}}<br>
		<label>total Descuentos</label>: {{$factura[0]->totalDescuentos}}<br>
		<label>total Venta Neta</label>: {{$factura[0]->totalVentaNeta}}<br>
		<label>total Impuesto</label>: {{$factura[0]->totalImpuesto}}<br>
		<label>total Otros Cargos</label>: {{$factura[0]->totalOtrosCargos}}<br>
		<label>total Comprobante</label>: {{$factura[0]->totalComprobante}}<br>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h3>Líneas de factura</h3><br>
		<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
            	<th>numero Linea</th>
				<th>cantidad</th>
				<th>unidad Medida</th>
				<th>detalle</th>
				<th>precio Unitario</th>
				<th>monto Total</th>
				<th>monto Descuento</th>
				<th>naturaleza Descuento</th>
				<th>subTotal</th>
				<th>codigo Impuesto</th>
				<th>codigo Tarifa</th>
				<th>tarifa Impuesto</th>
				<th>monto Impuesto</th>
				<!--
				<th>tipoDocumentoExoneracion</th> 
				<th>numeroDocumentoExoneracion</th> 
				<th>nombreInstitucionExoneracion</th> 
				<th>fechaEmisionExoneracion</th> 
				<th>porcentajeExoneracionExoneracion</th> 
				<th>montoExoneracionExoneracion</th> -->
				<th>monto Total Linea</th> 
            </tr>
          </thead>
          <tbody>
			@foreach ( $factura as $linea )
				@if($linea->tipoLinea == 1)
		            <tr>
		            	<td>{{$linea->numeroLinea}}</td>
						<td>{{$linea->cantidad}}</td>
						<td>{{$linea->unidadMedida}}</td>
						<td>{{$linea->detalle}}</td>
						<td>{{$linea->precioUnitario}}</td>
						<td>{{$linea->montoTotal}}</td>
						<td>{{$linea->montoDescuento}}</td>
						<td>{{$linea->naturalezaDescuento}}</td>
						<td>{{$linea->subTotal}}</td>
						<td>{{$linea->codigoImpuesto}}</td>
						<td>{{$linea->codigoTarifa}}</td>
						<td>{{$linea->tarifaImpuesto}}</td>
						<td>{{$linea->montoImpuesto}}</td>
						<!--
						<td>{{$linea->tipoDocumentoExoneracion}}</td> 
						<td>{{$linea->numeroDocumentoExoneracion}}</td> 
						<td>{{$linea->nombreInstitucionExoneracion}}</td> 
						<td>{{$linea->fechaEmisionExoneracion}}</td> 
						<td>{{$linea->porcentajeExoneracionExoneracion}}</td> 
						<td>{{$linea->montoExoneracionExoneracion}}</td> -->
						<td>{{$linea->montoTotalLinea}}</td> 
		            </tr>
	            @endif
			@endforeach
           </tbody>
        </table>
	</div>
</div>