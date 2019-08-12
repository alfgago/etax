
@if($title)
<tr class="sub-title {{ $desplegar }}">
  <th class="marcar-td" colspan="1">
		<span class="marcar">
			¿Aplica?
			<span class="si">Sí</span>
			<span class="no">No</span>
		</span>
  </th>
	<th style="position:relative;" colspan="6">{{ $title }} <input class="sumtot" readonly="" value="{{ number_format($totales, 2) }}"></th>
</tr>
@endif
@if( $desplegar == "desplegar-true" )
  <tr class="header-tarifas desplegar-true">
  		@if($cols)
	    <th>Detalle</th>
	    <th class="{{ $col3 }}">Afectas al 13%</th>
	    <th class="{{ $col8 }}">Afectas al 8%</th>
	    <th class="{{ $col4 }}">Afectas al 4%</th>
	    <th class="{{ $col2 }}">Afectas al 2%</th>
	    <th class="{{ $col1 }}">Afectas al 1%</th>
	    @else
		  	<th colspan="4">Detalle</th>
		    <th colspan="2">Monto</th>
		  @endif
  </tr>
  
	@foreach( $cats as $info )
		@if($cols)
		<tr class="desplegar-true">
		  <th>{{ $info['name'] }}</th>
		  <td class="{{ $col3 }}">
		  	<input readonly value="{{ number_format( $info['monto3'], 0 ) }}"/>
		  </td>
		  <td class="{{ $col8 }}">
		  	<input readonly value="{{ number_format( 0, 0 ) }}"/>
		  </td>
		  <td class="{{ $col4 }}">
		  	<input readonly value="{{ number_format( $info['monto4'], 0 ) }}"/>
		  </td>
		  <td class="{{ $col2 }}">
		  	<input readonly value="{{ number_format( $info['monto2'], 0 ) }}"/>
		  </td>
		  <td class="{{ $col1 }}">
		  	<input readonly value="{{ number_format( $info['monto1'], 0 ) }}" />
		  </td>
		</tr>
		@else
			<tr class="desplegar-true">
		  	<th colspan="4">{{ $info['name'] }}</th>
			  <td colspan="2">
			  	<input readonly value="{{ number_format( $info['monto0'], 2 ) }}"/>
			  </td>
			</tr>
		@endif
	@endforeach
@endif