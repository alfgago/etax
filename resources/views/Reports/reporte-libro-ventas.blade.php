@if( sizeof( $data ) )
	<div class="car mb-4" >
	  <div class="car-body text-left">
	      <h3 class="card-title">Libro de ventas {{ $nombreMes }} {{ $ano }} </h3>
	      <div class="row">
	        
	        <div class="col-sm-12">
	          <table class="text-12 text-muted m-0 p-2 ivas-table">
	            <thead>
	              <tr>
	                <th>Fecha</th>
	                <th>Cliente</th>
	                <th>Actividad</th>
	                <th>Consecutivo</th>
	                <th># Línea</th>
	                <th>Producto</th>
	                <th>Moneda</th>
	                <th>Subtotal</th>
	                <th>Tarifa IVA</th>
	                <th>Monto IVA</th>
	                <th>Total</th>
	              </tr>
	            </thead>
	            <tbody>
	            	@foreach($data as $item)
		            	@if( !$item->invoice->is_void && $item->invoice->is_authorized && $item->invoice->is_code_validated )
		              <tr>
		              	<?php 
		              		$factor = $item->invoice->document_type != '03' ? 1 : -1;
		              	?>
		                <td>{{ $item->invoice->generatedDate()->format('d/m/Y') }}</td>
		                <td>{{ @$item->invoice->client ? $item->invoice->client->getFullName() : 'N/A' }}</td>
		                <td>{{ @$item->invoice->commercial_activity ?? 'No indica' }}</td>
		                <td>{{ $item->invoice->document_number }}</td>
		                <td>{{ $item->item_number }}</td>
		                <td>{{ $item->name }}</td>
		                <td>{{ $item->invoice->currency }}</td>
		                <td>{{ number_format( $item->subtotal, 2) }}</td>
		                <td>{{ $item->iva_percentage }}%</td>
		                <td>{{ number_format( $item->iva_amount, 2) }}</td>
		                <td>{{ number_format( $item->total*$factor, 2) }}</td>
		              </tr>
		              @endif
	              @endforeach
	            </tbody>
	            
	          </table>
	        </div>
	        
	        
	      </div>
	  </div>
	</div>
@else
	<div class="row">
		<div style="display: inline-block;">
			<div class="alert alert-warning">
				No se encontraron movimientos durante el mes de {{ $nombreMes }} {{ $ano }}
			</div>
		</div>
	</div>
@endif