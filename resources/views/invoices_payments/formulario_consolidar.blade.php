@extends('layouts/app')

@section('title') 
  Cuentas bancarias
@endsection

@section('breadcrumb-buttons')
@endsection 

@section('content')
<div class="row">
  <div class="col-12">
      <b>Número de factura: </b>
      {{@$invoice->document_number}}
  </div>
  <div class="col-12">
      <b>Cliente: </b>
      {{@$invoice->client_first_name}} {{@$invoice->client_last_name}} {{@$invoice->client_last_name2}}
  </div>
  <div class="col-12">
      <b>Fecha emitida: </b>
      {{@$invoice->generated_date}}
  </div>
  <div class="col-12">
      <b>Subtotal: </b>
      {{@$invoice->subtotal}}
  </div>
  <div class="col-12">
      <b>IVA: </b>
      {{@$invoice->iva_amount}}
  </div>
  <div class="col-12">
      <b>Total: </b>
      {{@$invoice->total}}
  </div>
  <div class="col-md-12">

      <form method="POST" action="/consolidad_pagos/actualizar-consolidacion">
        @csrf
        <table id="payments_invoice_modal" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
               <th>Monto</th>
               <th>Tipo</th>
               <th>Banco</th>
               <th>Transferencia</th>
               <th>Fecha</th>
               <th>Estado</th>
               <th></th>
            </tr>
          </thead>
          <tbody>

            @foreach($payments as $payment)
            <tr id="row_{{@$i}}">
              <td>
                  <input type="number" class="amount" name="amount[]" value="{{@$payment->amount}}">
              </td>
              <td>
                <select name="payment_type[]">
                        <option value="01" @if($payment->payment_type === "01") selected @endif >Efectivo</option>
                        <option value="02" @if($payment->payment_type === "02") selected @endif >Tarjeta</option>
                        <option value="03" @if($payment->payment_type === "03") selected @endif >Cheque</option>
                        <option value="04" @if($payment->payment_type === "04") selected @endif >Tranferencia o Deposito Bancario</option>
                        <option value="05" @if($payment->payment_type === "05") selected @endif >Recaudado por Terceros</option>
                        <option value="99" @if($payment->payment_type === "99") selected @endif >Otros</option>
                  </select>
              </td>
              <td>
                  <select name="bank_account_id[]">
                        <option value="0">Seleccione una cuenta bancaria</option>
                      @foreach($cuentas as $cuenta)
                        <option value="{{@$cuenta->id}}" @if($payment->bank_account_id === $cuenta->id) selected @endif >
                              {{@$cuenta->account}} {{@$cuenta->bank}} {{@$cuenta->currency}}
                        </option>
                      @endforeach
                  </select>
              </td>
              <td><input type="text" name="proof[]" value="{{@$payment->proof}}"></td>
              <td><input type="date" name="payment_date[]" value="{{@$payment->payment_date}}"></td>
              <td>
                  <select name="status[]">
                        <option value="1" @if($payment->status === 2) selected @endif >Consolidada</option>
                        <option value="0" @if($payment->status === 1) selected @endif >Pendiente</option>

                  </select>
              </td>
              <td><i class="fa fa-trash-o text-danger eliminar_row" row="{{@$i++}}" aria-hidden="true"></i></td>
            </tr>
            @endforeach
          </tbody>
        </table>  

        <input type="number" value="{{@$invoice->id}}" id="invocie_id" name="invocie_id" hidden />
        <div id="btn-new-payment" class="btn btn-success">Agregar pago</div>
        <button id="btn-submit" type="submit" class="btn btn-success">Guardar</button>
      </form>
  </div>  
</div>
        <input type="number" value="{{@$i}}" id="cantidad_row" name="cantidad_row" hidden disabled/>
        <input type="number" value="{{@$invoice->total}}" id="total_factura" name="total_factura" hidden disabled/>
@endsection

@section('footer-scripts')
<script>
  

$(function() {

  
  $("#btn-new-payment").click(function(){
      var row = $("#cantidad_row").val();
      console.log(row);
      $.ajax({
          type:'get',
          url:'/consolidad_pagos/new_payment_form/'+row,
          data:'',
          success:function(data) {
              $("#payments_invoice_modal tbody").append(data);
          }
      });
      row++; 
      $("#cantidad_row").val(row);
      
  });

  funciones_tabla_payments_invoice();

});
  
function funciones_tabla_payments_invoice(){

  $(".eliminar_row").click(function(){
      var row = $(this).attr("row");
      $("#row_"+row).remove();
  });
}

  
</script>
@endsection
