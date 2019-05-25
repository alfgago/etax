@extends('layouts/app')

@section('title') 
  	Validación de códigos eTax en facturas recibidas
@endsection

@section('breadcrumb-buttons')
    <div onclick="abrirPopup('importar-recibidas-popup');" class="btn btn-primary">Importar facturas recibidas</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
          
      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th>Moneda</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="1">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @if ( $bills->count() )
              @foreach ( $bills as $data )
                <tr>
                  <td>{{ $data->document_number }}</td>
                  <td>{{ $data->currency }}</td>
                  <td>{{ number_format( $data->subtotal, 2 ) }}</td>
                  <td>{{ number_format( $data->iva_amount, 2 ) }}</td>
                  <td>{{ number_format( $data->total, 2 ) }}</td>
                  <td>
                    <form class="inline-form validaciones" method="POST" action="/facturas-recibidas/confirmar-validacion/">
                      @csrf
                      @method('patch')
                      
                      <div class="input-validate-iva">
										      <select class="form-control" id="tipo_iva" >
										        @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
										          <option value="{{ $tipo['codigo'] }}" {{ $tipo['codigo'] == '103' ? 'selected' : '' }}
										          	porcentaje="{{ $tipo['porcentaje'] }}" class="{{ @$tipo['hide'] ? 'hidden' : '' }}" >{{ $tipo['nombre'] }}</option>
										        @endforeach
										      </select>
										  </div>
										                      
                      <button type="submit" class="text-success mr-2" title="Confirmar código" style="display: inline-block; background: none;">
                      	<i class="fa fa-check mr-2" aria-hidden="true"></i> Confirmar código
                      </button>
                      
                      <button type="button" class="text-info mr-2" title="Validación por línea de factura" style="display: inline-block; background: none;">
                      	<i class="fa fa-file-text-o" aria-hidden="true"></i> Validación por línea de factura
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $bills->links() }}
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