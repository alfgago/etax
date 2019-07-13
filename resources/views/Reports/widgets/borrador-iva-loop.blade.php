<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion">
	<tbody>
			
			@foreach( \App\ProductCategory::all() as $tipo )
					<?php
						$varName = "$actividad-type$tipo->id";
						$varName0 = "$actividad-type$tipo->id-0";
						$varName1 = "$actividad-type$tipo->id-1";
						$varName2 = "$actividad-type$tipo->id-2";
						$varName3 = "$actividad-type$tipo->id-13";
						$varName4 = "$actividad-type$tipo->id-4";
						$ivaData = json_decode($data->iva_data);
					?>
					
					@if($loop->index == 0)
						<tr class="macro-title">
					    <th colspan="6">TOTAL DE VENTAS , SUJETAS, EXENTAS Y NO SUJETAS</th>
					  </tr>
					  <tr class="sub-title">
					    <th colspan="6">BIENES Y SERVICIOS AFECTOS AL 1%</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					@if($loop->index == 5)
						<tr class="sub-title">
					    <th colspan="6">BIENES Y SERVICIOS AFECTOS AL 2%</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					@if($loop->index == 9)
						<tr class="sub-title">
					    <th colspan="6">BIENES Y SERVICIOS AFECTOS AL 4%</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					@if($loop->index == 14)
						<tr class="sub-title">
					    <th colspan="6">BIENES Y SERVICIOS AFECTOS AL 13%</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					@if($loop->index == 19)
						<tr class="sub-title">
					    <th colspan="6">TOTAL OTROS RUBROS A INCLUIR EN LA BASE IMPONIBLE</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					@if($loop->index == 21)
						<tr class="sub-title">
					    <th colspan="6">VENTAS EXENTAS</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>0%</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					  </tr>
					@endif
					@if($loop->index == 38)
						<tr class="sub-title">
					    <th colspan="6">VENTAS AUTORIZADAS SIN IMPUESTO (órdenes especiales y otros transitorios)</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					  </tr>
					@endif
					@if($loop->index == 45)
						<tr class="sub-title">
					    <th colspan="6">VENTAS A NO SUJETOS</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					    <th>-</th>
					  </tr>
					@endif
					@if($loop->index == 48)
						<tr class="macro-title">
					    <th colspan="6">TOTAL DE COMPRAS</th>
					  </tr>
					  <tr class="sub-title">
					    <th colspan="6">Compras de bienes y servicios locales utilizados en operaciones sujetas y no exentas</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					@if($loop->index == 51)
						<tr class="sub-title">
					    <th colspan="6">Importaciones de bienes y adquisición de servicios del exterior utilizadas en operaciones sujetas y no exentas</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					@if($loop->index == 54)
						<tr class="sub-title">
					    <th colspan="6">Compras sin derecho a crédito fiscal</th>
					  </tr>
					  <tr class="header-tarifas">
					    <th>Rubro</th>
					    <th>13%</th>
					    <th>8%</th>
					    <th>4%</th>
					    <th>2%</th>
					    <th>1%</th>
					  </tr>
					@endif
					
					<tr>
					  <th>{{ $tipo->name }}</th>
					  <td><input readonly value="{{ $ivaData->$varName0 ? number_format( $ivaData->$varName0, 2 ) : number_format( $ivaData->$varName3, 0 ) }}"/></td>
					  <td><input readonly value="{{ number_format( 0, 0 ) }}"/></td>
					  <td><input readonly value="{{ number_format( $ivaData->$varName4, 0 ) }}"/></td>
					  <td><input readonly value="{{ number_format( $ivaData->$varName2, 0 ) }}"/></td>
					  <td><input readonly value="{{ number_format( $ivaData->$varName1, 0 ) }}" /></td>
					</tr>
			@endforeach
  </tbody>
</table>