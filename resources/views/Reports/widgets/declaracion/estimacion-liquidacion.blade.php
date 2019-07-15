<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
<tbody>
	<?php
		$ivaData = json_decode($data->iva_data);
	?>
		<tr class="macro-title">
	    <th colspan="6">Estimación y liquidación anual de la proporcionalidad</th>
	  </tr>
		<tr class="sub-title desplegar-false">
	    <th class="marcar-td" colspan="1">
    		<span class="marcar">
    			¿Aplica?
    			<span class="si">Sí</span>
    			<span class="no">No</span>
    		</span>
	    </th>
    	<th colspan="5">¿Es esta la última declaración del IVA que presenta por desinscripción como Contribuyente ante la Administración Tributaria?</th>
	  </tr>
	  <tr class="header-tarifas">
	    <th>Detalle</th>
	    <th colspan="5"> Monto </th>
	  </tr>
	  <tr>
	    <th>Monto anual de ventas con derecho a crédito fiscal aplicados</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->numerador_prorrata, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Monto anual de ventas con derecho y sin derecho a crédito fiscal</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->invoices_subtotal, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Porcentaje a aplicar como liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->prorrata*100, 0) }}%" > </td>
	  </tr>
		<tr class="sub-title desplegar-false">
	    <th colspan="6">Liquidación final de la regla de la proporcionalidad</th>
	  </tr>
	  <tr class="header-tarifas">
	    <th>Detalle</th>
	    <th colspan="5"> Monto </th>
	  </tr>
	  <tr>
	    <th>Crédito fiscal anual sobre el que se aplica el porcentaje de proporcionalidad</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->total_bill_iva, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Crédito fiscal generado por aplicación final del porcentaje de proporcionalidad</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->iva_deducible_estimado, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Crédito aplicado de enero a la fecha de la liquidación final según regla de proporcionalidad</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->iva_por_cobrar, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Saldo a favor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->iva_por_cobrar, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Saldo deudor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->iva_por_pagar, 0) }}" > </td>
	  </tr>
	</tbody>
</table>



<h2 class="card-subtitle sub2">Determinación del impuesto por operaciones gravadas del periodo</h2>
<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>	  
		<tr class="macro-title">
	    <th colspan="6"></th>
	  </tr>
	  <tr class="header-tarifas">
	    <th>Detalle</th>
	    <th colspan="5"> Monto </th>
	  </tr>
	 	<tr>
	    <th>Impuesto generado por operaciones gravadas</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->total_invoice_iva, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Total de créditos del periodo</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_deducible_operativo, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Devolución del IVA por servicios de salud privada pagados con tarjeta de crédito y/o débito</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( 0, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo a favor del periodo</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_cobrar, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Impuesto neto del periodo (saldo deudor)</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_pagar, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo a favor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_cobrar, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo deudor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_pagar, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo a favor final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->saldo_favor, 0) }}" > </td>
	  </tr>
	 	<tr>
	    <th>Impuesto final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_pagar, 0) }}" > </td>
	  </tr>
	  
	</tbody>
</table>


<h2 class="card-subtitle sub2">Liquidación deuda tributaria</h2>
<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
		<tr class="macro-title">
	    <th colspan="6"></th>
	  </tr>
	  <tr class="header-tarifas">
	    <th>Detalle</th>
	    <th colspan="5"> Monto </th>
	  </tr>
	    <th>Retenciones pagos a cuenta del impuesto</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->retention_by_card, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Saldo a favor de periodos anteriores</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->saldo_favor_anterior, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Solicito compensar con crédito a mi favor por el monto de:</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( 0, 0) }}" > </td>
	  </tr>
	  <tr>
	    <th>Total deuda tributaria:</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="Calculado por Hacienda" > </td>
	  </tr>
	  <tr>
	    <th>Intereses</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="Calculado por Hacienda" > </td>
	  </tr>
	  <tr>
	    <th>Total deuda por pagar</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="Calculado por Hacienda" > </td>
	  </tr>
	</tbody>
</table>