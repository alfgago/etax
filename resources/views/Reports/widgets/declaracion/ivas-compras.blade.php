<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
	<?php
		$impuestos = $dataDeclaracion['impuestos'];
		
		$iva_compras_S1 = $impuestos['iva_compras_S1'];	
		$iva_compras_S2 = $impuestos['iva_compras_S2'];	
		$iva_compras_S3 = $impuestos['iva_compras_S3'];	
		$iva_compras_S4 = $impuestos['iva_compras_S4'];	
		$iva_importaciones_S1 = $impuestos['iva_importaciones_S1'];	
		$iva_importaciones_S2 = $impuestos['iva_importaciones_S2'];	
		$iva_importaciones_S3 = $impuestos['iva_importaciones_S3'];	
		$iva_importaciones_S4 = $impuestos['iva_importaciones_S4'];	
		$iva_compras_S1e = $impuestos['iva_compras_S1e'];	
		$iva_compras_S2e = $impuestos['iva_compras_S2e'];	
		$iva_compras_S3e = $impuestos['iva_compras_S3e'];	
		$iva_compras_S4e = $impuestos['iva_compras_S4e'];	
		$iva_importaciones_S1e = $impuestos['iva_importaciones_S1e'];	
		$iva_importaciones_S2e = $impuestos['iva_importaciones_S2e'];	
		$iva_importaciones_S3e = $impuestos['iva_importaciones_S3e'];	
		$iva_importaciones_S4e = $impuestos['iva_importaciones_S4e'];	
		$iva_compras_B1 = $impuestos['iva_compras_B1'];	
		$iva_compras_B2 = $impuestos['iva_compras_B2'];	
		$iva_compras_B3 = $impuestos['iva_compras_B3'];	
		$iva_compras_B4 = $impuestos['iva_compras_B4'];	
		$iva_importaciones_B1 = $impuestos['iva_importaciones_B1'];	
		$iva_importaciones_B2 = $impuestos['iva_importaciones_B2'];	
		$iva_importaciones_B3 = $impuestos['iva_importaciones_B3'];	
		$iva_importaciones_B4 = $impuestos['iva_importaciones_B4'];	
		$iva_compras_B1e = $impuestos['iva_compras_B1e'];	
		$iva_compras_B2e = $impuestos['iva_compras_B2e'];	
		$iva_compras_B3e = $impuestos['iva_compras_B3e'];	
		$iva_compras_B4e = $impuestos['iva_compras_B4e'];	
		$iva_importaciones_B1e = $impuestos['iva_importaciones_B1e'];	
		$iva_importaciones_B2e = $impuestos['iva_importaciones_B2e'];	
		$iva_importaciones_B3e = $impuestos['iva_importaciones_B3e'];	
		$iva_importaciones_B4e = $impuestos['iva_importaciones_B4e'];	
		$iva_bc_L1 = $impuestos['iva_bc_L1'];	
		$iva_bc_L2 = $impuestos['iva_bc_L2'];	
		$iva_bc_L3 = $impuestos['iva_bc_L3'];	
		$iva_bc_L4 = $impuestos['iva_bc_L4'];	
		$iva_bc_I1 = $impuestos['iva_bc_I1'];	
		$iva_bc_I2 = $impuestos['iva_bc_I2'];	
		$iva_bc_I3 = $impuestos['iva_bc_I3'];	
		$iva_bc_I4 = $impuestos['iva_bc_I4'];	
		$iva_bc_L1e = $impuestos['iva_bc_L1e'];	
		$iva_bc_L2e = $impuestos['iva_bc_L2e'];	
		$iva_bc_L3e = $impuestos['iva_bc_L3e'];	
		$iva_bc_L4e = $impuestos['iva_bc_L4e'];	
		$iva_bc_I1e = $impuestos['iva_bc_I1e'];	
		$iva_bc_I2e = $impuestos['iva_bc_I2e'];	
		$iva_bc_I3e = $impuestos['iva_bc_I3e'];	
		$iva_bc_I4e = $impuestos['iva_bc_I4e'];	
		$totales1 = $impuestos['totales1'];	
		$totales2 = $impuestos['totales2'];	
		$totales3 = $impuestos['totales3'];	
		$totales4 = $impuestos['totales4'];	
		$totalesSum = $impuestos['totalesSum'];	
		$totales1e = $impuestos['totales1e'];	
		$totales2e = $impuestos['totales2e'];	
		$totales3e = $impuestos['totales3e'];	
		$totales4e = $impuestos['totales4e'];	
		$totalesSume = $impuestos['totalesSume'];
		$totalCreditosPeriodo = $impuestos['totalCreditosPeriodo'];
		$creditosAcreditablesPorTarifa = $impuestos['creditosAcreditablesPorTarifas'];
		
	?>
	
	<tr class="macro-title">
    <th colspan="7">Total de créditos del periodo <input readonly value="{{ number_format( $totalCreditosPeriodo, 0) }}"/></th>
  </tr>
	<tr class="macro-title">
    <th colspan="7">Créditos fiscales aplicables por transacciones y operaciones sujetas por tarifas <input readonly value="{{ number_format( $totalesSume, 0 ) }}"/></th>
  </tr>
	<tr class="header-tarifas header-tarifas-7">
		<th>Detalle</th>
    <th>Afectas al 13%</th>
    <th>Afectas al 8%</th>
    <th>Afectas al 4%</th>
    <th>Afectas al 2%</th>
    <th>Afectas al 1%</th>
    <th>Total</th>
  </tr>
	<tr class="true-blocked">
	  <th>Crédito por compra de bienes</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3e+$iva_importaciones_B3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B4e+$iva_importaciones_B4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B2e+$iva_importaciones_B2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B1e+$iva_importaciones_B1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3e+$iva_importaciones_B3e+$iva_compras_B4e+$iva_importaciones_B4e+$iva_compras_B2e+$iva_importaciones_B2e+$iva_compras_B1e+$iva_importaciones_B1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-3">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3e+$iva_compras_B4e+$iva_compras_B2e+$iva_compras_B1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B3e+$iva_importaciones_B4e+$iva_importaciones_B2e+$iva_importaciones_B1e, 0 ) }}" />
	  </td>
	</tr>
			
			
	<tr class="true-blocked">
	  <th>Crédito por adquisición de bienes de capital</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3e+$iva_bc_I3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L4e+$iva_bc_I4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L2e+$iva_bc_I2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L1e+$iva_bc_I1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3e+$iva_bc_I3e+$iva_bc_L4e+$iva_bc_I4e+$iva_bc_L2e+$iva_bc_I2e+$iva_bc_L1e+$iva_bc_I1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3e+$iva_bc_L4e+$iva_bc_L2e+$iva_bc_L1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I3e+$iva_bc_I4e+$iva_bc_I2e+$iva_bc_I1e, 0 ) }}" />
	  </td>
	</tr>
	
	
	<tr class="true-blocked">
	  <th>Crédito por adquisición de servicios</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3e+$iva_importaciones_S3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S4e+$iva_importaciones_S4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S2e+$iva_importaciones_S2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S1e+$iva_importaciones_S1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3e+$iva_importaciones_S3e+$iva_compras_S4e+$iva_importaciones_S4e+$iva_compras_S2e+$iva_importaciones_S2e+$iva_compras_S1e+$iva_importaciones_S1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-3">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3e+$iva_compras_S4e+$iva_compras_S2e+$iva_compras_S1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S3e+$iva_importaciones_S4e+$iva_importaciones_S2e+$iva_importaciones_S1e, 0 ) }}" />
	  </td>
	</tr>
	
	<tr class="true-blocked">
	  <th>Subtotales</th>
	  <td>
	  	<input readonly value="{{ number_format( $totales3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totales4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totales2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totales1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totalesSume, 0 ) }}" />
	  </td>
	</tr>
	
	<tr class="true-blocked">
	  <th>Crédito por devoluciones sobre ventas y en consignación</th>
	  <td>
	  	<input readonly value="0"/>
	  </td>
	  <td>
	  	<input readonly value="0"/>
	  </td>
	  <td>
	  	<input readonly value="0"/>
	  </td>
	  <td>
	  	<input readonly value="0"/>
	  </td>
	  <td>
	  	<input readonly value="0" />
	  </td>
	  <td>
	  	<input readonly value="0" />
	  </td>
	</tr>
	
	<tr class="sub-title desplegar-true">
    <th class="marcar-td" colspan="2">
  		<span class="marcar">
  			¿Aplica?
  			<span class="si">Sí</span>
  			<span class="no">No</span>
  		</span>
    </th>
  	<th colspan="5" class="posrel">Créditos fiscales aplicables por transacciones destinadas indistintamente a operaciones sin derecho y con derecho a crédito de una sola tarifa (Art. 24) <input readonly value="{{ number_format( $creditosAcreditablesPorTarifa, 0 ) }}"/></th>
  </tr>
	<tr class="desplegar-true">
	  <th colspan="6">Porcentaje a aplicar:</th>
	  <td colspan="1">
	  	<input readonly value="{{ number_format( $dataDeclaracion['prorrataOperativa']*100, 2 ) }}%"/>
	  </td>
	</tr>
	
	<tr class="macro-title">
    <th colspan="7">Créditos por compras locales de bienes y servicios e importaciones utilizados indistintamente en operaciones sujetas, no sujetas o exentas <input readonly value="{{ number_format( ($totalesSum), 0 ) }}"/></th>
  </tr>
	<tr class="header-tarifas header-tarifas-7">
		<th>Detalle</th>
    <th>Afectas al 13%</th>
    <th>Afectas al 8%</th>
    <th>Afectas al 4%</th>
    <th>Afectas al 2%</th>
    <th>Afectas al 1%</th>
    <th>Total</th>
  </tr>
	<tr class="true-blocked">
	  <th>Crédito por compra de bienes</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3+$iva_importaciones_B3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B4+$iva_importaciones_B4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B2+$iva_importaciones_B2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B1+$iva_importaciones_B1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3+$iva_importaciones_B3+$iva_compras_B4+$iva_importaciones_B4+$iva_compras_B2+$iva_importaciones_B2+$iva_compras_B1+$iva_importaciones_B1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-3">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B4, 0 ) }}   "/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_B3+$iva_compras_B4+$iva_compras_B2+$iva_compras_B1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_B3+$iva_importaciones_B4+$iva_importaciones_B2+$iva_importaciones_B1, 0 ) }}" />
	  </td>
	</tr>
			
			
	<tr class="true-blocked">
	  <th>Crédito por adquisición de bienes de capital</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3+$iva_bc_I3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L4+$iva_bc_I4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L2+$iva_bc_I2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L1+$iva_bc_I1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3+$iva_bc_I3+$iva_bc_L4+$iva_bc_I4+$iva_bc_L2+$iva_bc_I2+$iva_bc_L1+$iva_bc_I1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_L3+$iva_bc_L4+$iva_bc_L2+$iva_bc_L1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_bc_I3+$iva_bc_I4+$iva_bc_I2+$iva_bc_I1, 0 ) }}" />
	  </td>
	</tr>
	
	
	<tr class="true-blocked">
	  <th>Crédito de por compra de servicios</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3+$iva_importaciones_S3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S4+$iva_importaciones_S4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S2+$iva_importaciones_S2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S1+$iva_importaciones_S1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3+$iva_importaciones_S3+$iva_compras_S4+$iva_importaciones_S4+$iva_compras_S2+$iva_importaciones_S2+$iva_compras_S1+$iva_importaciones_S1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-3">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_compras_S3+$iva_compras_S4+$iva_compras_S2+$iva_compras_S1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_importaciones_S3+$iva_importaciones_S4+$iva_importaciones_S2+$iva_importaciones_S1, 0 ) }}" />
	  </td>
	</tr>
	
	<tr class="true-blocked">
	  <th>Subtotales</th>
	  <td>
	  	<input readonly value="{{ number_format( $totales3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totales4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totales2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totales1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $totalesSum, 0 ) }}" />
	  </td>
	</tr>
	
	</tbody>
</table>

<style>
	tr.header-tarifas-7 th {
	    width: calc(100% / 7);
	}
</style>