<div class="row">
	<div class="col-md-4 col-sm-12">
		<h3>Cliente</h3><br>
		<label>nombre Receptor</label>: {{$factura[0]->nombreReceptor}}<br>
		<label>tipo Identificacion</label>: {{$factura[0]->tipoIdentificacionReceptor}}<br>
		<label>Identificacion</label>: {{$factura[0]->identificacionReceptor}}<br>
		<label>correo</label>: {{$factura[0]->correoReceptor}}<br>
		<!--
		<label>provincia</label>: {{$factura[0]->provincia}}<br>
		<label>canton</label>: {{$factura[0]->canton}}<br>
		<label>distrito</label>: {{$factura[0]->distrito}}<br>-->
		<label>direccion</label>: {{$factura[0]->direccionReceptor}}<br>
	</div>
	<div class="col-md-4 col-sm-12">
		<h3>Detalle Factura</h3><br>
		<label>condicion Venta</label>: {{$factura[0]->condicionVenta}}<br>
		<label>plazo Credito</label>: {{$factura[0]->plazoCredito}}<br>
		<label>medio Pago</label>: {{$factura[0]->medioPago}}<br>
		<label>codigo Moneda</label>: {{$factura[0]->codigoMoneda}}<br>
		<label>tipo Cambio</label>: {{$factura[0]->tipoCambio}}<br>
	</div>
	<?php $total = 0; ?>
	@foreach ( $factura as $linea )
		@if($linea->tipoLinea == 1)
			<?php $total = $total + $linea->montoTotalLinea; ?>
		@endif
		@if($linea->tipoLinea == 2)
			<?php $total = $total + $linea->montoCargo; ?>
		@endif
	@endforeach
	<div class="col-md-4 col-sm-12">
		<h3>Total Factura</h3><br>
		<h2>{{$total}}</h2>
	</div>
</div>
<div class="row">
	<div class="col-md-10">
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

<div class="row">
	<div class="col-md-10">
		<h3>Otros Cargos</h3><br>
		<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
            	<th>numero Linea</th>
				<th>tipoCargo</th>
				<th>identidadTercero</th>
				<th>nombreTercero</th>
				<th>detalleCargo</th>
				<th>porcentajeCargo</th>
				<th>montoCargo</th>
            </tr>
          </thead>
          <tbody>
			@foreach ( $factura as $linea )
				@if($linea->tipoLinea == 2)
		            <tr>
		            	<td>{{$linea->numeroLinea}}</td>
						<td>{{$linea->tipoCargo}}</td>
						<td>{{$linea->identidadTercero}}</td>
						<td>{{$linea->nombreTercero}}</td>
						<td>{{$linea->detalleCargo}}</td>
						<td>{{$linea->porcentajeCargo}}</td>
						<td>{{$linea->montoCargo}}</td>
		            </tr>
	            @endif
			@endforeach
           </tbody>
        </table>
	</div>
</div>