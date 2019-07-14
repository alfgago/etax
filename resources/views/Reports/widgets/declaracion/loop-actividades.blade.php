<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion">
	<tbody>
			<?php
				$desplegar = "desplegar-false";
			?>
			@foreach( \App\ProductCategory::all() as $tipo )
					<?php
						$tipoID = $tipo->id;
						$varName = "$actividad-type$tipoID";
						$varName0 = "$actividad-type$tipoID-0";
						$varName1 = "$actividad-type$tipoID-1";
						$varName2 = "$actividad-type$tipoID-2";
						$varName3 = "$actividad-type$tipoID-13";
						$varName4 = "$actividad-type$tipoID-4";
						$varNameTotal = "$actividad-type$tipoID";
						$ivaData = json_decode($data->iva_data);
						
						$monto0 = $ivaData->$varName0;
						$monto1 = $ivaData->$varName1;
						$monto2 = $ivaData->$varName2;
						$monto3 = $ivaData->$varName3;
						$monto4 = $ivaData->$varName4;
						$totalMontos = $ivaData->$varNameTotal;
						
						$col13 = "false";
						$col8 = "false";
						$col4 = "false";
						$col2 = "false";
						$col1 = "false";
						$macroTitle = false;
						$subTitle = false;
						$cols = true;
						
						if($loop->index == 0) {
							$macroTitle = "TOTAL DE VENTAS, SUJETAS, EXENTAS Y NO SUJETAS";
							$subTitle = "BIENES Y SERVICIOS AFECTOS AL 1%";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 5) {
							$subTitle = "BIENES Y SERVICIOS AFECTOS AL 2%";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 9) {
							$subTitle = "BIENES Y SERVICIOS AFECTOS AL 4%";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 14) {
							$subTitle = "BIENES Y SERVICIOS AFECTOS AL 13%";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 19) {
							$subTitle = "TOTAL OTROS DetalleS A INCLUIR EN LA BASE IMPONIBLE";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 21) {
							$subTitle = "VENTAS EXENTAS";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 38) {
							$subTitle = "VENTAS AUTORIZADAS SIN IMPUESTO (órdenes especiales y otros transitorios)";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 45) {
							$subTitle = "VENTAS A NO SUJETOS";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 48) {
							$macroTitle = "TOTAL DE COMPRAS CON Y SIN DERECHO A CRÉDITO FISCAL";
							$subTitle = "Compras de bienes y servicios locales utilizados en operaciones sujetas y no exentas";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 51) {
							$subTitle = "Importaciones de bienes y adquisición de servicios del exterior utilizadas en operaciones sujetas y no exentas";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}elseif($loop->index == 54) {
							$subTitle = "Compras sin derecho a crédito fiscal";
							$desplegar = $totalMontos ? "desplegar-true" : "desplegar-false";
						}
						
						
						if($loop->index < 5) {
							//Bienes al 1
							$col1 = "true";
						}elseif($loop->index < 9) {
							//Bienes al 2
							$col2 = "true";
						}elseif($loop->index < 14) {
							//Bienes al 4
							$col4 = "true";
						}elseif($loop->index < 19) {
							//Bienes al 13
							$col13 = "true";
						}elseif($loop->index < 21) {
							//Total Detalles en base imponible
							$col13 = "true";
							$col8 = "true-blocked";
							$col4 = "true";
							$col2 = "true";
							$col1 = "true-blocked";
						}elseif($loop->index < 38) {
							//Exentas
							$cols = false;
						}elseif($loop->index < 45) {
							//Autorizadas sin impuesto
							$cols = false;
						}elseif($loop->index < 48) {
							//No sujetas
							$cols = false;
						}elseif($loop->index < 51) {
							//Compras locales
							$col13 = "true";
							$col8 = "true-blocked";
							$col4 = "true";
							$col2 = "true";
							$col1 = "true-blocked";
						}elseif($loop->index < 54) {
							//Importaciones
							$col13 = "true";
							$col8 = "true-blocked";
							$col4 = "true";
							$col2 = "true";
							$col1 = "true-blocked";
						}else{
							//Compras sin derecho a crédito
							$cols = false;
						}
						
					?>
					
					@if($macroTitle)
						<tr class="macro-title">
					    <th colspan="6">{{$macroTitle}}</th>
					  </tr>
					@endif
					@if($subTitle)
						<tr class="sub-title {{ $desplegar }}">
					    <th class="marcar-td" colspan="1">
				    		<span class="marcar">
				    			¿Aplica?
				    			<span class="si">Sí</span>
				    			<span class="no">No</span>
				    		</span>
					    </th>
				    	<th colspan="6">{{$subTitle}}</th>
					  </tr>
					  <tr class="header-tarifas {{ $desplegar }}">
						  @if($cols)
						    <th>Detalle</th>
						    <th>Afectas al 13%</th>
						    <th>Afectas al 8%</th>
						    <th>Afectas al 4%</th>
						    <th>Afectas al 2%</th>
						    <th>Afectas al 1%</th>
						  @else
						  	<th colspan="4">Detalle</th>
						    <th colspan="2">Monto</th>
						  @endif
					  </tr>
					@endif
					
					@if($cols)
						<tr class="{{ $desplegar }}">
						  <th>{{ $tipo->name }}</th>
						  <td class="{{ $col13 }}">
						  	<input readonly value="{{ number_format( $monto3, 0 ) }}"/>
						  </td>
						  <td class="{{ $col8 }}">
						  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
						  </td>
						  <td class="{{ $col4 }}">
						  	<input readonly value="{{ number_format( $monto4, 0 ) }}"/>
						  </td>
						  <td class="{{ $col2 }}">
						  	<input readonly value="{{ number_format( $monto2, 0 ) }}"/>
						  </td>
						  <td class="{{ $col1 }}">
						  	<input readonly value="{{ number_format( $monto1, 0 ) }}" />
						  </td>
						</tr>
					@else
						<tr class="{{ $desplegar }}">
						  <th colspan="4">{{ $tipo->name }}</th>
						  <td colspan="2">
						  	<input readonly value="{{ number_format( $monto0, 2 ) }}"/>
						  </td>
						</tr>
						
					@endif
			@endforeach
  </tbody>
</table>


<style>
	
</style>