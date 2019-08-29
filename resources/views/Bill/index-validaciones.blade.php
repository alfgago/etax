@extends('layouts/app')

@section('title') 
  	Validación de códigos eTax en facturas recibidas
@endsection

@section('breadcrumb-buttons')
    <a  class="btn btn-primary" href="/facturas-recibidas/aceptaciones">Aceptación de facturas</a>
    <div onclick="abrirPopup('importar-recibidas-popup');" class="btn btn-primary">Importar facturas recibidas</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
          
      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Emisor</th>
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
                  <td>{{ @$data->provider->fullname }}</td>
                  <td>{{ $data->currency }}</td>
                  <td class="text-right">{{ number_format( $data->subtotal, 2 ) }}</td>
                  <td class="text-right">{{ number_format( $data->iva_amount, 2 ) }}</td>
                  <td class="text-right">{{ number_format( $data->total, 2 ) }}</td>
                  <td>
                    <a link="/facturas-recibidas/validar/{{ $data->id }}" titulo="Verificación de compra" class="btn btn-primary m-0 verificar_compra" style="color:#fff; font-size: 0.85em;" onclick="" data-toggle="modal" data-target="#modal_estandar">Validar</a>
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
<script>
  $(function(){
      $(".verificar_compra").click(function(){
        var link = $(this).attr("link");
        var titulo = $(this).attr("titulo");
        $("#titulo_modal_estandar").html(titulo);
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
              
                $("#body_modal_estandar").html(data);

           }

        });
      });

  });

</script>
@endsection