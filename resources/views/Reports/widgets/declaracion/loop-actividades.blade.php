<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion">
	<tbody>
		
			<tr class="macro-title">
		    <th colspan="6">TOTAL DE VENTAS, SUJETAS, EXENTAS Y NO SUJETAS</th>
		  </tr>
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V1']['title'], 
				'desplegar' => $actividad['V1']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V1']['cats'],
				'cols' 	 => true, 
				'col1' => 'true',
				'col2' => 'false',
				'col3' => 'false',
				'col4' => 'false',
				'col8' => 'false',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V2']['title'], 
				'desplegar' => $actividad['V2']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V2']['cats'],
				'cols' 	 => true, 
				'col1' => 'false',
				'col2' => 'true',
				'col3' => 'false',
				'col4' => 'false',
				'col8' => 'false',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V13']['title'], 
				'desplegar' => $actividad['V13']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V13']['cats'],
				'cols' 	 => true, 
				'col1' => 'false',
				'col2' => 'false',
				'col3' => 'true',
				'col4' => 'false',
				'col8' => 'false',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V4']['title'], 
				'desplegar' => $actividad['V4']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V4']['cats'],
				'cols' 	 => true, 
				'col1' => 'false',
				'col2' => 'false',
				'col3' => 'false',
				'col4' => 'true',
				'col8' => 'false',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['BI']['title'], 
				'desplegar' => $actividad['BI']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['BI']['cats'],
				'cols' 	 => true, 
				'col1' => 'true-blocked',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true-blocked',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['VEX']['title'], 
				'desplegar' => $actividad['VEX']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['VEX']['cats'],
				'cols' 	 => false, 
				'col1' => 'true',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['VAS']['title'], 
				'desplegar' => $actividad['VAS']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['VAS']['cats'],
				'cols' 	 => false, 
				'col1' => 'true',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['VNS']['title'], 
				'desplegar' => $actividad['VNS']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['VNS']['cats'],
				'cols' 	 => false, 
				'col1' => 'true',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true',
			])
			<tr class="macro-title">
		    <th colspan="6">TOTAL DE COMPRAS CON Y SIN DERECHO A CRÉDITO FISCAL</th>
		  </tr>
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['CL']['title'], 
				'desplegar' => $actividad['CL']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['CL']['cats'],
				'cols' 	 => true, 
				'col1' => 'true-blocked',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true-blocked',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['CI']['title'], 
				'desplegar' => $actividad['CI']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['CI']['cats'],
				'cols' 	 => true, 
				'col1' => 'true-blocked',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true-blocked',
			])
		  @include('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['CE']['title'], 
				'desplegar' => $actividad['CE']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['CE']['cats'],
				'cols' 	 => false, 
				'col1' => 'true',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true',
			])
  </tbody>
</table>


<style>
	
</style>