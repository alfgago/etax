<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
	<?php
		$impuestos = $dataDeclaracion['impuestos'];
		
		$ventas1 = $impuestos['ventas1'];	
		$ventas2 = $impuestos['ventas2'];	
		$ventas4 = $impuestos['ventas4'];	
		$ventas13 = $impuestos['ventas13'];	
		$ventasTotal = $impuestos['ventasTotal'];	
	
	?>
	<tr class="macro-title">
    <th colspan="7">Total impuesto por ventas y transacciones sujetas</th>
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
	  <th>Impuesto generado por operaciones gravadas</th>
	  <td>
	  	<input readonly value="<?php echo e(number_format( $ventas13, 0 )); ?>"/>
	  </td>
	  <td>
	  	<input readonly value="<?php echo e(number_format( 0, 0 )); ?>"/>
	  </td>
	  <td>
	  	<input readonly value="<?php echo e(number_format( $ventas4, 0 )); ?>"/>
	  </td>
	  <td>
	  	<input readonly value="<?php echo e(number_format( $ventas2, 0 )); ?>"/>
	  </td>
	  <td>
	  	<input readonly value="<?php echo e(number_format( $ventas1, 0 )); ?>" />
	  </td>
	  <td>
	  	<input readonly value="<?php echo e(number_format( $ventasTotal, 0 )); ?>" />
	  </td>
	</tr>
	
	<tr class="sub-title desplegar-false">
    <th class="marcar-td" colspan="1">
  		<span class="marcar">
  			¿Aplica?
  			<span class="si">Sí</span>
  			<span class="no">No</span>
  		</span>
    </th>
  	<th colspan="6">Casinos y juegos de azar</th>
  </tr>
	<tr class="sub-title desplegar-false">
    <th class="marcar-td" colspan="1">
  		<span class="marcar">
  			¿Aplica?
  			<span class="si">Sí</span>
  			<span class="no">No</span>
  		</span>
    </th>
  	<th colspan="6">Bienes usados</th>
  </tr>
	
	</tbody>
</table><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Reports/widgets/declaracion/ivas-ventas.blade.php ENDPATH**/ ?>