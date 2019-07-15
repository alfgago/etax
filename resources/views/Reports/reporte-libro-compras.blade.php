@if( sizeof( $data ) )
	<div class="car mb-4" >
	  <div class="car-body text-left">
	      <h3 class="card-title">Libro de compras {{ $nombreMes }} {{ $ano }} </h3>
	      <div class="row">
	        
	        <div class="col-sm-12">
	          <table class="text-12 text-muted m-0 p-2 ivas-table">
	            <thead>
	              <tr>
	                <th>Fecha</th>
	                <th>Proveedor</th>
	                <th>Actividad <small style="font-size: .8em !important;font-weight: 600;">(aceptación)</small></th>
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
		            	@if( !$item->bill->is_void && $item->bill->is_authorized && $item->bill->is_code_validated )
		              <tr>
		                <td>{{ $item->bill->generatedDate()->format('d/m/Y') }}</td>
		                <td>{{ $item->bill->provider->getFullName() }}</td>
		                <td>{{ @$item->bill->activity_company_verification ?? 'No indica' }}</td>
		                <td>{{ $item->bill->document_number }}</td>
		                <td>{{ $item->item_number }}</td>
		                <td>{{ $item->name }}</td>
		                <td>{{ $item->bill->currency }}</td>
		                <td>{{ number_format( $item->subtotal, 2) }}</td>
		                <td>{{ $item->iva_percentage }}%</td>
		                <td>{{ number_format( $item->iva_amount, 2) }}</td>
		                <td>{{ number_format( $item->total, 2) }}</td>
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