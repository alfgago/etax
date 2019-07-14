<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
	<?php
		$book = $data->book;
		
		$ventas1 = $book->cc_ventas_1_iva;
		$ventas2 = $book->cc_ventas_2_iva;
		$ventas13 = $book->cc_ventas_13_iva;
		$ventas4 = $book->cc_ventas_4_iva;
		$ventasTotal = $ventas1+$ventas2+$ventas13+$ventas4;
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
	  	<input readonly value="{{ number_format( $ventas1, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $ventas2, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $ventas13, 0 ) }}"/>
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $ventas4, 0 ) }}" />
	  </td>
	  <td>
	  	<input readonly value="{{ number_format( $ventasTotal, 0 ) }}" />
	  </td>
	</tr>
	
	<tr class="sub-title desplegar-false">
    <th class="marcar-td" colspan="2">
  		<span class="marcar">
  			¿Aplica?
  			<span class="si">Sí</span>
  			<span class="no">No</span>
  		</span>
    </th>
  	<th colspan="5">Casinos y juegos de azar</th>
  </tr>
	<tr class="sub-title desplegar-false">
    <th class="marcar-td" colspan="2">
  		<span class="marcar">
  			¿Aplica?
  			<span class="si">Sí</span>
  			<span class="no">No</span>
  		</span>
    </th>
  	<th colspan="5">Bienes usados</th>
  </tr>
	
	</tbody>
</table>