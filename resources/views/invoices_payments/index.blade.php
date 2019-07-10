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
              <th>Cliente</th>
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
                    <td>{{@$invoice->generated_date}}</td>
                    <td>{{@$invoice->amount}}</td>
                    <td>{{@$invoice->client_first_name}} {{@$invoice->client_last_name}} {{@$invoice->client_last_name2}}</td>
                    <td>{{@$invoice->bank}}</td>
                    <td>{{@$invoice->proof}}</td>
                    <td>{{@$invoice->payment_date}}</td>
                    <td class="text-center">
                        <a  class="btn btn-primary" href="/consolidad_pagos/consolidar/{{@$invoice->invoice_id}}">Consolidar</a>
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
  $('#invoices-payments-table').DataTable({
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
});
  

  
</script>
@endsection