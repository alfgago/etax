@include('Reports.widgets.cuentas-contables-compras', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data])
@include('Reports.widgets.cuentas-contables-ventas', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data])
@include('Reports.widgets.cuentas-contables-ajustes', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data])