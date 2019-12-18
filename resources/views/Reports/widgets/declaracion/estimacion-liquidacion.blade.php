<?php
	$isLastMonth = $dataDeclaracion['mes'] == 12;
	$toggleEstimacionAnual = !$isLastMonth ? 'hidden' : '';
	
	$det = $dataDeclaracion['determinacion'];
	$montoAnualVentasConDerechoCredito = number_format( $det['montoAnualVentasConDerechoCredito'], 0);
	$montoAnualVentasSinDerechoCredito = number_format( $det['montoAnualVentasSinDerechoCredito'], 0);
	$porcentajeProrrataFinal = number_format( $det['porcentajeProrrataFinal'], 2);
	$creditoFiscalAnualTotal = number_format( $det['creditoFiscalAnualTotal'], 0);
	$creditoFiscalAnualDeducible = number_format( $det['creditoFiscalAnualDeducible'], 0);
	$creditoAnualFinal = number_format( $det['creditoAnualFinal'], 0);
	$saldoFavorAnual = number_format( $det['saldoFavorAnual'], 0);
	$saldoDeudorAnual = number_format( $det['saldoDeudorAnual'], 0);
	$impuestoOperacionesGravadas = number_format( $det['impuestoOperacionesGravadas'], 0);
	$totalCreditosPeriodo = number_format( $det['totalCreditosPeriodo'], 0);
	$devolucionIva = number_format( $det['devolucionIva'], 0);
	$saldoFavorPeriodo = number_format( $det['saldoFavorPeriodo'], 0);
	$saldoDeudorPeriodo = number_format( $det['saldoDeudorPeriodo'], 0);
	$saldoFavorProrrataReal = number_format( $det['saldoFavorProrrataReal'], 0);
	$saldoDeudorProrrataReal = number_format( $det['saldoDeudorProrrataReal'], 0);
	$saldoFavorFinalPeriodo = number_format( $det['saldoFavorFinalPeriodo'], 0);
	$impuestoFinalPeriodo = number_format( $det['impuestoFinalPeriodo'], 0);
	$retencionImpuestos = number_format( $det['retencionImpuestos'], 0);
	$saldoFavorAnterior = number_format( $det['saldoFavorAnterior'], 0);
	
?>

<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion " style="width:100%;">
<tbody>
		<tr class="macro-title">
	    <th colspan="6">Estimación y liquidación anual de la proporcionalidad</th>
	  </tr>
		<tr class="sub-title {{ $isLastMonth ? 'desplegar-true' : 'desplegar-false' }}">
	    <th class="marcar-td" colspan="1">
    		<span class="marcar">
    			¿Aplica?
    			<span class="si">Sí</span>
    			<span class="no">No</span>
    		</span>
	    </th>
    	<th colspan="5">¿Es esta la última declaración del IVA que presenta por desinscripción como Contribuyente ante la Administración Tributaria?</th>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Monto anual de ventas con derecho a crédito fiscal aplicados</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $montoAnualVentasConDerechoCredito }}" > </td>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Monto anual de ventas con derecho y sin derecho a crédito fiscal</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $montoAnualVentasSinDerechoCredito }}" > </td>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Porcentaje a aplicar como liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $porcentajeProrrataFinal }}%" > </td>
	  </tr>
		<tr class="sub-title {{ $toggleEstimacionAnual }}">
	    <th colspan="6">Liquidación final de la regla de la proporcionalidad</th>
	  </tr>
	  <tr class="header-tarifas {{ $toggleEstimacionAnual }}">
	    <th>Detalle</th>
	    <th colspan="5"> Monto </th>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Crédito fiscal anual sobre el que se aplica el porcentaje de proporcionalidad</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $creditoFiscalAnualTotal }}" > </td>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Crédito fiscal generado por aplicación final del porcentaje de proporcionalidad</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $creditoFiscalAnualDeducible }}" > </td>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Crédito aplicado de enero a la fecha de la liquidación final según regla de proporcionalidad</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $creditoAnualFinal }}" > </td>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Saldo a favor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoFavorAnual }}" > </td>
	  </tr>
	  <tr class="{{ $toggleEstimacionAnual }}">
	    <th>Saldo deudor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoDeudorAnual }}" > </td>
	  </tr>
	</tbody>
</table>

<h2 class="card-subtitle sub2">Determinación del impuesto por operaciones gravadas del periodo</h2>
<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>	  
		<tr class="macro-title">
	    <th colspan="6"></th>
	  </tr>
	 	<tr>
	    <th>Impuesto generado por operaciones gravadas</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $impuestoOperacionesGravadas }}" > </td>
	  </tr>
	 	<tr>
	    <th>Total de créditos del periodo</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $totalCreditosPeriodo }}" > </td>
	  </tr>
	 	<tr>
	    <th>Devolución del IVA por servicios de salud privada pagados con tarjeta de crédito y/o débito</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $devolucionIva }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo a favor del periodo</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoFavorPeriodo }}" > </td>
	  </tr>
	 	<tr>
	    <th>Impuesto neto del periodo (saldo deudor)</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoDeudorPeriodo }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo a favor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoFavorProrrataReal }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo deudor en aplicación del porcentaje de la liquidación final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoDeudorProrrataReal }}" > </td>
	  </tr>
	 	<tr>
	    <th>Saldo a favor final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoFavorFinalPeriodo }}" > </td>
	  </tr>
	 	<tr>
	    <th>Impuesto final</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $impuestoFinalPeriodo }}" > </td>
	  </tr>
	  
	</tbody>
</table>

<h2 class="card-subtitle sub2">Liquidación deuda tributaria</h2>
<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
		<tr class="macro-title">
	    <th colspan="6"></th>
	  </tr>
	  <tr>
	    <th>Retenciones pagos a cuenta del impuesto</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $retencionImpuestos }}" > </td>
	  </tr>
	  <tr>
	    <th>Saldo a favor de periodos anteriores</th>
	    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ $saldoFavorAnterior }}" > </td>
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