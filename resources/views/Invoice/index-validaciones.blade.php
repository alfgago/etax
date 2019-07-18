@extends('layouts/app')

@section('title') 
  	Validación de códigos eTax en facturas emitidas
@endsection

@section('breadcrumb-buttons')
    <div onclick="abrirPopup('importar-emitidas-popup');" class="btn btn-primary">Importar facturas emitidas</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
          
      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Receptor</th>
              <th>Moneda</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="1">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @if ( $invoices->count() )
              @foreach ( $invoices as $data )
                <tr>
                  <td>{{ $data->document_number }}</td>
                  <td>{{ @$data->client->fullname }}</td>
                  <td>{{ $data->currency }}</td>
                  <td>{{ number_format( $data->subtotal, 2 ) }}</td>
                  <td>{{ number_format( $data->iva_amount, 2 ) }}</td>
                  <td>{{ number_format( $data->total, 2 ) }}</td>
                  <td>
                    <form class="inline-form validaciones" method="POST" action="/facturas-emitidas/confirmar-validacion/{{ $data->id }}">
                      @csrf
                      @method('patch')
                      
                      <div class="input-validate-iva">
										      <select class="form-control" id="tipo_iva" name="tipo_iva">
										        @foreach ( \App\CodigoIvaRepercutido::all() as $tipo )
                              <option value="{{ $tipo['code'] }}" attr-iva="{{ $tipo['percentage'] }}" {{ $tipo['codigo'] == '103' ? 'selected' : '' }} porcentaje="{{ $tipo['percentage'] }}" class="{{ @$tipo['hidden'] ? 'hidden' : '' }}">{{ $tipo['name'] }}</option>
                            @endforeach
										      </select>
										  </div>
										                      
                      <button type="submit" class="text-success mr-2" title="Confirmar código" style="display: inline-block; background: none;">
                      	<i class="fa fa-check mr-2" aria-hidden="true"></i> Confirmar código
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $invoices->links() }}
  </div>  
</div>

@endsection

@section('footer-scripts')

<style>

	form.inline-form.validaciones,
	form.inline-form.validaciones .input-validate-iva, 
	form.inline-form.validaciones .input-validate-iva select {
	    width: 100%;
	}
	
	form.inline-form.validaciones button {
	    border: 1px solid;
	    margin-top: 0.5rem !important;
	}

</style>

@endsection