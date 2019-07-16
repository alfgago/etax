<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
	<?php
		$ivaData = json_decode($data->iva_data);
		
		//Compra de servicios
    $iva_compras_S1 = $data->applyRatios(1, $ivaData->bS001); 
    $iva_compras_S2 = $data->applyRatios(2, $ivaData->bS002); 
    $iva_compras_S3 = $data->applyRatios(13, $ivaData->bS003); 
    $iva_compras_S4 = $data->applyRatios(4, $ivaData->bS004); 
    $iva_importaciones_S1 = $data->applyRatios(1, $ivaData->bS021);
    $iva_importaciones_S2 = $data->applyRatios(2, $ivaData->bS022);
    $iva_importaciones_S3 = $data->applyRatios(13, $ivaData->bS023);
    $iva_importaciones_S4 = $data->applyRatios(4, $ivaData->bS024);
    
    $iva_compras_S1e = $ivaData->iS061; 
    $iva_compras_S2e = $ivaData->iS062; 
    $iva_compras_S3e = $ivaData->iS063;  
    $iva_compras_S4e = $ivaData->iS064;  
    $iva_importaciones_S1e = $ivaData->iS041; 
    $iva_importaciones_S2e = $ivaData->iS042; 
    $iva_importaciones_S3e = $ivaData->iS043; 
    $iva_importaciones_S4e = $ivaData->iS044; 
    
    //Compra de bienes
    $iva_compras_B1 = $data->applyRatios(1, $ivaData->bB001); 
    $iva_compras_B2 = $data->applyRatios(2, $ivaData->bB002); 
    $iva_compras_B3 = $data->applyRatios(13, $ivaData->bB003); 
    $iva_compras_B4 = $data->applyRatios(4, $ivaData->bB004); 
    $iva_importaciones_B1 = $data->applyRatios(1, $ivaData->bB021 + $ivaData->bB015);
    $iva_importaciones_B2 = $data->applyRatios(2, $ivaData->bB022);
    $iva_importaciones_B3 = $data->applyRatios(13, $ivaData->bB023 + $ivaData->bB016);
    $iva_importaciones_B4 = $data->applyRatios(4, $ivaData->bB024);
    
    $iva_compras_B1e = $ivaData->iB061; 
    $iva_compras_B2e = $ivaData->iB062; 
    $iva_compras_B3e = $ivaData->iB063; 
    $iva_compras_B4e = $ivaData->iB064; 
    $iva_importaciones_B1e = $ivaData->iB041 + $ivaData->iB035; 
    $iva_importaciones_B2e = $ivaData->iB042; 
    $iva_importaciones_B3e = $ivaData->iB043 + $ivaData->iB036; 
    $iva_importaciones_B4e = $ivaData->iB044; 

		//Bienes de capital
    $iva_ppp_L1 = $data->applyRatios(1, $ivaData->bB011);
    $iva_ppp_L2 = $data->applyRatios(2, $ivaData->bB012);
    $iva_ppp_L3 = $data->applyRatios(13, $ivaData->bB013);
    $iva_ppp_L4 = $data->applyRatios(4, $ivaData->bB014);

    $iva_ppp_I1 = $data->applyRatios(1, $ivaData->bB031);
    $iva_ppp_I2 = $data->applyRatios(2, $ivaData->bB032);
    $iva_ppp_I3 = $data->applyRatios(13, $ivaData->bB033);
    $iva_ppp_I4 = $data->applyRatios(4, $ivaData->bB034);
    
    $iva_ppp_L1e = $ivaData->iB051; 
    $iva_ppp_L2e = $ivaData->iB052; 
    $iva_ppp_L3e = $ivaData->iB053; 
    $iva_ppp_L4e = $ivaData->iB054; 

    $iva_ppp_I1e = $ivaData->iB071; 
    $iva_ppp_I2e = $ivaData->iB072; 
    $iva_ppp_I3e = $ivaData->iB073; 
    $iva_ppp_I4e = $ivaData->iB074; 
    
    //Totales
    $totales1 = $iva_ppp_L1 + $iva_ppp_I1 + $iva_importaciones_B1 + $iva_importaciones_S1 + $iva_compras_B1 + $iva_compras_S1;
    $totales2 = $iva_ppp_L2 + $iva_ppp_I2 + $iva_importaciones_B2 + $iva_importaciones_S2 + $iva_compras_B2 + $iva_compras_S2;
    $totales3 = $iva_ppp_L3 + $iva_ppp_I3 + $iva_importaciones_B3 + $iva_importaciones_S3 + $iva_compras_B3 + $iva_compras_S3;
    $totales4 = $iva_ppp_L4 + $iva_ppp_I4 + $iva_importaciones_B4 + $iva_importaciones_S4 + $iva_compras_B4 + $iva_compras_S4;
    $totalesSum = $totales1 + $totales2 + $totales3 + $totales4;
    
    //Totales con identificacion
    $totales1e = $iva_ppp_L1e + $iva_ppp_I1e + $iva_importaciones_B1e + $iva_importaciones_S1e + $iva_compras_B1e + $iva_compras_S1e;
    $totales2e = $iva_ppp_L2e + $iva_ppp_I2e + $iva_importaciones_B2e + $iva_importaciones_S2e + $iva_compras_B2e + $iva_compras_S2e;
    $totales3e = $iva_ppp_L3e + $iva_ppp_I3e + $iva_importaciones_B3e + $iva_importaciones_S3e + $iva_compras_B3e + $iva_compras_S3e;
    $totales4e = $iva_ppp_L4e + $iva_ppp_I4e + $iva_importaciones_B4e + $iva_importaciones_S4e + $iva_compras_B4e + $iva_compras_S4e;
    $totalesSume = $totales1e + $totales2e + $totales3e + $totales4e;

	?>
	
	<tr class="macro-title">
    <th colspan="7">Total de créditos del periodo <input readonly value="{{ number_format( ($totalesSum*$data->prorrata_operativa) + $totalesSume, 0 ) }}"/></th>
  </tr>
	<tr class="macro-title">
    <th colspan="7">Créditos fiscales aplicables por transacciones y operaciones sujetas por tarifas <input readonly value="{{ number_format( $totalesSume, 0 ) }}"/></th>
  </tr>
	<tr class="header-tarifas">
		<th>Detalle</th>
    <th>Afectas al 13%</th>
    <th>Afectas al 8%</th>
    <th>Afectas al 4%</th>
    <th>Afectas al 2%</th>
    <th>Afectas al 1%</th>
    <th>Total</th>
  </tr>
	<tr class="true-blocked">
	  <th>Crédito de por compra de bienes</th>
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
	  	<input readonly value="{{ number_format( $iva_ppp_L3e+$iva_ppp_I3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L4e+$iva_ppp_I4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L2e+$iva_ppp_I2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L1e+$iva_ppp_I1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L3e+$iva_ppp_I3e+$iva_ppp_L4e+$iva_ppp_I4e+$iva_ppp_L2e+$iva_ppp_I2e+$iva_ppp_L1e+$iva_ppp_I1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L3e+$iva_ppp_L4e+$iva_ppp_L2e+$iva_ppp_L1e, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I3e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I4e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I2e, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I1e, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I3e+$iva_ppp_I4e+$iva_ppp_I2e+$iva_ppp_I1e, 0 ) }}" />
	  </td>
	</tr>
	
	
	<tr class="true-blocked">
	  <th>Crédito de por compra de servicios</th>
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
	
	
	<tr class="sub-title desplegar-true">
    <th class="marcar-td" colspan="2">
  		<span class="marcar">
  			¿Aplica?
  			<span class="si">Sí</span>
  			<span class="no">No</span>
  		</span>
    </th>
  	<th colspan="5" class="posrel">Créditos fiscales aplicables por transacciones destinadas indistintamente a operaciones sin derecho y con derecho a crédito de una sola tarifa (Art. 24) <input readonly value="{{ number_format( ($totalesSum*$data->prorrata_operativa), 0 ) }}"/></th>
  </tr>
	<tr class="desplegar-true">
	  <th colspan="6">Porcentaje a aplicar:</th>
	  <td colspan="1">
	  	<input readonly value="{{ number_format( $data->prorrata_operativa*100, 2 ) }}%"/>
	  </td>
	</tr>
	
	
	
	<tr class="macro-title">
    <th colspan="7">Créditos por compras locales de bienes y servicios e importaciones utilizados indistintamente en operaciones sujetas, no sujetas o exentas <input readonly value="{{ number_format( ($totalesSum), 0 ) }}"/></th>
  </tr>
	<tr class="header-tarifas">
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
	  	<input readonly value="{{ number_format( $iva_ppp_L3+$iva_ppp_I3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L4+$iva_ppp_I4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L2+$iva_ppp_I2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L1+$iva_ppp_I1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L3+$iva_ppp_I3+$iva_ppp_L4+$iva_ppp_I4+$iva_ppp_L2+$iva_ppp_I2+$iva_ppp_L1+$iva_ppp_I1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Locales</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_L3+$iva_ppp_L4+$iva_ppp_L2+$iva_ppp_L1, 0 ) }}" />
	  </td>
	</tr>
	<tr>
	  <th class="pl-4">- Importados</th>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I3, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I4, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I1, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $iva_ppp_I3+$iva_ppp_I4+$iva_ppp_I2+$iva_ppp_I1, 0 ) }}" />
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