@extends('layouts/app')

@section('title') 
  Cuentas bancarias
@endsection

@section('breadcrumb-buttons')
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
      
      <table id="invoices-payments-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th># Factura</th>
              <th>Fecha emitida</th>
              <th>Monto</th>
              <th>Banco</th>
              <th>Transferencia</th>
              <th>Fecha consolidación</th>
              <th></th>
            </tr>
             
          </thead>
          <tbody>
            @foreach($invoices as $invoice)

                <tr>
                    <td>{{@$invoice->document_number}}</td>
                    <td>{{@$invoice->created_at}}</td>
                    <td>{{@$invoice->amount}}</td>
                    <td>{{@$invoice->bank_account_id}}</td>
                    <td>{{@$invoice->proof}}</td>
                    <td>{{@$invoice->payment_date}}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary levantar_modal" titulo="Consolidar Pago" link="/consolidad_pagos/consolidar/" nid="{{@$invoice->id}}" data-toggle="modal" data-target="#modal_estandar">Consolidar</button>
                    </td>
                </tr>
            @endforeach
          </tbody>
      </table>
  </div>  
</div>

@endsection

@section('footer-scripts')
<script>
  

$(function() {

  
  $(".levantar_modal").click(function(){
      console.log('asdsdas');
      $("#titulo_modal_estandar").html($(this).attr("titulo"));
      $.ajax({
          type:'POST',
          url:'/getmsg',
          data:'_token = <?php echo csrf_token() ?>',
          success:function(data) {
              $("#msg").html(data.msg);
          }
      });
  });


  $('#invoices-payments-table').DataTable({
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
});
  

  
</script>
@endsection