@if( @$data->book )
	@include('Reports.widgets.cuentas-contables-compras', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data])
	@include('Reports.widgets.cuentas-contables-ventas', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data])
	@include('Reports.widgets.cuentas-contables-ajustes', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data])
@else
	<div class="row">
		<div style="display: inline-block;">
			<div class="alert alert-warning">
				No se encontraron movimientos durante el mes de {{ $nombreMes }}
			</div>
		</div>
	</div>
@endif