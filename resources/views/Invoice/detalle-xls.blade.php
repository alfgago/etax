<div class="row">
	<div class="col-md-3 col-sm-12">
		<h3>Cliente</h3><br>
		<label>nombreReceptor</label>: {{$factura[0]->nombreReceptor}}<br>
		<label>tipoIdentificacion</label>: {{$factura[0]->tipoIdentificacionReceptor}}<br>
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
		<label>condicionVenta</label>: {{$factura[0]->condicionVenta}}<br>
		<label>plazoCredito</label>: {{$factura[0]->plazoCredito}}<br>
		<label>medioPago</label>: {{$factura[0]->medioPago}}<br>
		<label>codigoMoneda</label>: {{$factura[0]->codigoMoneda}}<br>
		<label>tipoCambio</label>: {{$factura[0]->tipoCambio}}<br>
	</div>
	<div class="col-md-3 col-sm-12">
		<h3>Totales Detallados</h3><br>
		<label>totalServGravados</label>: {{$factura[0]->totalServGravados}}<br>
		<label>totalServExentos</label>: {{$factura[0]->totalServExentos}}<br>
		<label>totalMercanciasGravadas</label>: {{$factura[0]->totalMercanciasGravadas}}<br>
		<label>totalMercanciasExentas</label>: {{$factura[0]->totalMercanciasExentas}}<br>
		<label>totalGravado</label>: {{$factura[0]->totalGravado}}<br>
		<label>totalExento</label>: {{$factura[0]->totalExento}}<br>
	</div>
	<div class="col-md-3 col-sm-12">
		<h3>Totales Factura</h3><br>
		<label>totalVenta</label>: {{$factura[0]->totalVenta}}<br>
		<label>totalDescuentos</label>: {{$factura[0]->totalDescuentos}}<br>
		<label>totalVentaNeta</label>: {{$factura[0]->totalVentaNeta}}<br>
		<label>totalImpuesto</label>: {{$factura[0]->totalImpuesto}}<br>
		<label>totalOtrosCargos</label>: {{$factura[0]->totalOtrosCargos}}<br>
		<label>totalComprobante</label>: {{$factura[0]->totalComprobante}}<br>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h3>Líneas de factura</h3><br>
		<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
            	<th>numeroLinea</th>
				<th>cantidad</th>
				<th>unidadMedida</th>
				<th>detalle</th>
				<th>precioUnitario</th>
				<th>montoTotal</th>
				<th>montoDescuento</th>
				<th>naturalezaDescuento</th>
				<th>subTotal</th>
				<th>codigoImpuesto</th>
				<th>codigoTarifa</th>
				<th>tarifaImpuesto</th>
				<th>montoImpuesto</th>
				<!--
				<th>tipoDocumentoExoneracion</th> 
				<th>numeroDocumentoExoneracion</th> 
				<th>nombreInstitucionExoneracion</th> 
				<th>fechaEmisionExoneracion</th> 
				<th>porcentajeExoneracionExoneracion</th> 
				<th>montoExoneracionExoneracion</th> -->
				<th>montoTotalLinea</th> 
            </tr>
          </thead>
          <tbody>
			@foreach ( $factura as $linea )
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
			@endforeach
           </tbody>
        </table>
	</div>
</div>