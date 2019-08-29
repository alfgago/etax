
	
		<h3>Las siguientes facturas de venta están pendientes de validación: </h3>

		<table class="table table-striped table-bordered">
  			<thead class="thead-dark">
				<tr>
					<th>Comprobante</th>
					<th>Receptor</th>
					<th>Tipo Doc</th>
					<th>Total</th>
					<th>F. Generada</th>
				</tr>
			</thead>
			<tbody>
				@foreach( @$retorno['invoices'] as $invoice)
					<tr>
						<td>{{$invoice->document_number }}</td>
						<td>{{$invoice->clientName()}}</td>
						<td>{{$invoice->document_type }}</td>
						<td>{{$invoice->total }}</td>
						<td>{{$invoice->generated_date }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<h3>Las siguientes facturas de compra están pendientes de validación: </h3>

		<table class="table table-striped table-bordered">
  			<thead class="thead-dark">
				<tr>
					<th>Comprobante</th>
					<th>Emisor</th>
					<th>Tipo Doc</th>
					<th>Total</th>
					<th>F. Generada</th>
				</tr>
			</thead>
			<tbody>
				@foreach( @$retorno['bills'] as $bill)
					<tr>
						<td>{{$bill->document_number }}</td>
						<td>{{$bill->providerName() }}</td>
						<td>{{$bill->document_type }}</td>
						<td>{{$bill->total }}</td>
						<td>{{$bill->generated_date }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>