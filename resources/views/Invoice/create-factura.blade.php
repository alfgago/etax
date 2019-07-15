@extends('layouts/app')

<?php 
  $tipoHacienda = "FE";
  $titulo = "Factura electrónica";
  if($document_type == "01"){
    $tipoHacienda = "FE";
    $titulo = "Factura electrónica";
  }else if($document_type == "04"){
    $tipoHacienda = "TE";
    $titulo = "Tiquete electrónico";
  }else if($document_type == "08"){
      $tipoHacienda = "FEC";
      $titulo = "Factura electrónica de compra";
  }else if($document_type == "09"){
      $tipoHacienda = "FEE";
      $titulo = "Factura electrónica de exportación";
  }else if($document_type == "02"){
      $tipoHacienda = "ND";
      $titulo = "Nota de débito";
  }
if(!isset($document_type)){
    $document_type = '01';
}

$company = currentCompanyModel();

?>
@section('title') 
  Enviar {{ $titulo }}
@endsection

@section('content') 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-emitidas/send">

          @csrf
          
          <input type="hidden" id="current-index" value="0">

          <div class="form-row">
            <div class="col-md">
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-row">
                    @if( $document_type != "08"  )
                    <div class="form-group col-md-12">
                      <h3>
                        Cliente
                      </h3>
                      <div onclick="abrirPopup('nuevo-cliente-popup');" class="btn btn-agregar btn-agregar-cliente">Nuevo cliente</div>
                    </div>  
                    
                    <div class="form-group col-md-12 with-button">
                      <label for="cliente">Seleccione el cliente</label>
                      <select class="form-control select-search" name="client_id" id="client_id" placeholder="" required>
                        <option value='' selected>-- Seleccione un cliente --</option>
                        @foreach ( currentCompanyModel()->clients as $cliente )
                          @if( @$cliente->canInvoice($document_type) )
                            <option value="{{ $cliente->id }}" >{{ $cliente->toString() }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>
                    @else
                      <div class="form-group col-md-12">
                        <h3>
                          Cliente
                        </h3>
                      </div>
                      <div class="form-group col-md-12">
                        <label for="actual">Empresa actual</label>
                        <input disabled readonly class="form-control" type="text" value="{{ $company->id_number . ' - ' . $company->name.' '.$company->last_name.' '.$company->last_name2 }}">
                      </div>
                      
                    @endif
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <h3>
                        Moneda
                      </h3>
                    </div>
      
                    <div class="form-group col-md-4">
                      <label for="currency">Divisa</label>
                      <select class="form-control" name="currency" id="moneda" required>
                        <option value="CRC"  data-rate="{{$rate}}" selected>CRC</option>
                        <option value="USD"  data-rate="{{$rate}}">USD</option>
                      </select>
                    </div>
      
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" data-rates="{{$rate}}" name="currency_rate" id="tipo_cambio" value="1.00"required>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-row">    
                <div class="form-group col-md-12">
                  <h3>
                    Datos de envio
                  </h3>
                </div>
              </div>
              <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="send_email">Enviar copia a:</label>
                    <input type="email" class="form-control" name="send_email" id="send_email" value="">
                  </div>
              </div>
                    <input type="date" class="form-control hidden" name="fecha_envio" id="fecha_envio" value="{{@$date_Today}}" min="{{@$date_Today}}">
                    <input name="factura_recurrente" class="form-control hidden" id="select_factura_recurrente" value="0" />
                    <input name="frecuencia" class="form-control hidden" id="frecuencia"value="0"/>
                    <input type="text" name="opciones_recurrencia" id="opciones_recurrencia" value="0" class="form-control hidden">
              <div class="form-row">    
                <div class="form-group col-md-12">
                  <h3>
                    Detalle
                  </h3>
                </div>
                
                 <div class="form-group col-md-4">
                  <label for="subtotal">Subtotal </label>
                  <input type="text" class="form-control" name="subtotal" id="subtotal" placeholder="" readonly="true" required>
                </div>
    
                <div class="form-group col-md-4">
                  <label for="iva_amount">Monto IVA </label>
                  <input type="text" class="form-control" name="iva_amount" id="monto_iva" placeholder="" readonly="true" required>
                </div>
    
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" required>
                </div>
                
                <div class="form-group col-md-12">
                  <div onclick="abrirPopup('linea-popup');" class="btn btn-dark btn-agregar">Agregar línea</div>
                </div>
    
              </div>
              
            </div>
            
            <div class="col-md offset-md-1">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <h3>
                    Datos generales
                  </h3>
                </div>

                  <div class="form-group col-md-6">
                    <label for="document_number">Número de documento</label>
                    <input type="text" class="form-control" name="document_number" id="document_number" value="{{$document_number}}" required readonly="readonly">
                  </div>
  
                  <div class="form-group col-md-6 not-required">
                    <label for="document_key">Clave de factura</label>
                    <input type="text" class="form-control" name="document_key" id="document_key" value="{{$document_key}}" required readonly="readonly">
                  </div>

                  <div class="form-group col-md-4 hidden">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4 hidden">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" class="form-control input-hora" name="hora" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('g:i A') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->addDays(3)->format('d/m/Y') }}">
                      <span class="input-group-addon">
                        <i class="icon-regular i-Calendar-4"></i>
                      </span>
                    </div>
                  </div>

                  <div class="form-group col-md-12">
                      <label for="payment_type">Actividad Comercial</label>
                      <div class="input-group">
                          <select id="commercial_activity" name="commercial_activity" class="form-control" required>
                              @foreach ( $arrayActividades as $actividad )
                                  <option value="{{ $actividad->codigo }}">{{ $actividad->codigo }} - {{ $actividad->actividad }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  
                  <div class="form-group col-md-6">
                    <label for="sale_condition">Condición de venta</label>
                    <div class="input-group">
                      <select id="condicion_venta" name="sale_condition" class="form-control" required>
                        <option selected value="01">Contado</option>
                        <option value="02">Crédito</option>
                        <option value="03">Consignación</option>
                        <option value="04">Apartado</option>
                        <option value="05">Arrendamiento con opción de compra</option>
                        <option value="06">Arrendamiento en función financiera</option>
                        <option value="99">Otros</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="payment_type">Método de pago</label>
                    <div class="input-group">
                      <select id="medio_pago" name="payment_type" class="form-control" required>
                        <option value="01" selected>Efectivo</option>
                        <option value="02">Tarjeta</option>
                        <option value="03">Cheque</option>
                        <option value="04">Transferencia-Depósito Bancario</option>
                        <option value="05">Recaudado por terceros</option>
                        <option value="99">Otros</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group col-md-12" id="field-retencion" style="display:none;">
                    <label for="retention_percent">Porcentaje de retención</label>
                    <div class="input-group">
                      <select id="retention_percent" name="retention_percent" class="form-control" required>
                        <option value="6" selected>6%</option>
                        <option value="3">3%</option>
                        <option value="0" >Sin retención</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-6 not-required">
                    <label for="other_reference">Referencia</label>
                    <input type="text" class="form-control" name="other_reference" id="referencia" value="" >
                  </div>

                  <div class="form-group col-md-6 not-required">
                    <label for="buy_order">Orden de compra</label>
                    <input type="text" class="form-control" name="buy_order" id="orden_compra" value="" >
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Notas</label>
                    <textarea class="form-control" name="notas" id="notas"  maxlength="200" placeholder=""> {{ @currentCompanyModel()->default_invoice_notes }}  </textarea>
                  </div>

              </div>
              
            </div>
          </div>

          <div class="form-row" id="tabla-items-factura" style="display: none;">  

            <div class="form-group col-md-12">
              <h3>
                Líneas de factura
              </h3>
            </div>
            
            <div class="form-group col-md-12" >
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Cant.</th>
                    <th>Unidad</th>
                    <th>Precio unitario</th>
                    <th>Tipo IVA</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                
                </tbody>
              </table>
            </div>
          </div>
          
          @include( 'Invoice.form-linea' )
          @include( 'Invoice.form-nuevo-cliente' )
            <input type="text" hidden value="{{ $document_type }}" name="document_type" id="document_type">
          <div class="btn-holder hidden">
           
            <button id="btn-submit" type="submit" class="btn btn-primary">Enviar factura electrónica</button>
          </div>

      </form>
  </div>  
</div>

<div class="modal fade" id="modal_programar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-center modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="titulo_modal_estandar">Programación del envio</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="body_modal_estandar">
        <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="send_date">Fecha de envio:</label>
                    <input type="date" class="form-control" name="fecha_envio_modal" id="fecha_envio_modal" value="{{@$date_Today}}" min="{{@$date_Today}}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="send_date">Factura recurrente:</label>
                     <select name="select_factura_recurrente_select" class="form-control" id="select_factura_recurrente_select">
                          <option value="0">No</option>
                          <option value="1">Si</option>
                  </select>
                  </div>
              </div>
              <div class="form-row" id="div_factura_recurrente">
                 <div class="form-group col-md-6">
                  <label for="subtotal">Recurrencia: </label>
                  <select name="frecuencia_select" class="form-control" id="frecuencia_select">
                          <option value="0">Nunca</option>
                          <option value="1">Semanal</option>
                          <option value="2">Quincenal</option>
                          <option value="3">Mensual</option>
                          <option value="4">Bimensual</option>
                          <option value="5">Trimestral</option>
                          <option value="6">Cuatrimestral</option>
                          <option value="7">Semestral</option>
                          <option value="8">Anual</option>
                          <option value="9">Cantidad de días</option>
                  </select>
                <input type="text" hidden  name="opciones_recurrencia_modal" id="opciones_recurrencia_modal"  class="form-control">
                </div>
                <div class="form-group col-md-6" id="div_opcciones_frecuencia">
                  
                </div>
              </div>
              <button class="btn btn-dark mt-0" id="programar_envio_btn">Programar envio</button>
     </div>
    </div>
  </div>
</div>

@endsection

@section('breadcrumb-buttons')
{{--@if( $document_type != "09"  )--}}
  <button id='btn-submit-fe' onclick="$('#btn-submit').click();" class="btn btn-primary">Enviar factura electrónica</button>
  <button titulo="Programar envio" class="btn btn-primary m-0 programar_venta" data-toggle="modal" data-target="#modal_programar">Programar envio</a>
{{--@else--}}
{{--  <p class="description mt-4">FEC temporalmente deshabilitada. Muy pronto en funcionamiento al finalizar el día. Nos disculpamos por la inconveniencia.</p>--}}
{{-- @endif--}}
@endsection

@section('footer-scripts')

<script>
$(document).ready(function(){
  $('#tipo_producto').val(17).change();

  $('#moneda').change(function() {
    if ($(this).val() == 'USD') {
      $('#tipo_cambio').val($('#tipo_cambio').data('rates'))
    } else {
      $('#tipo_cambio').val('1.00')
    }
  });

  $("#frecuencia_select").change(function(){
      var frecuencia = $(this).val();
      var html = '';
      if(frecuencia == 0){
        $("#div_opcciones_frecuencia").html('');
        $("#opciones_recurrencia_modal").val();
      }
      if(frecuencia == 1){
          html = '<label for="subtotal">Día de la semana: </label><select  class="form-control" id="frecuencia_option_semanal"><option value="0">Domingo</option><option value="1">Lunes</option><option value="2">Martes</option><option value="3">Miercoles</option><option value="4">Jueves</option><option value="5">Viernes</option><option value="6">Sabado</option></select>';
          $("#div_opcciones_frecuencia").html(html);
          get_options_semanal();
      }
      if(frecuencia == 2){
          html = '<label for="subtotal">Día de la primer quincena: </label><select  class="form-control" id="frecuencia_option_1_quincenal"></select><label for="subtotal">Día de la segunda quincena: </label><select  class="form-control" id="frecuencia_option_2_quincenal"></select>';
          $("#div_opcciones_frecuencia").html(html);
          opciones_quincenal();
          get_options_quincenal();
      }
      if(frecuencia == 3){
          html = '<label for="subtotal">Día del mes: </label><select  class="form-control" id="frecuencia_option_mensual"></select>';
          $("#div_opcciones_frecuencia").html(html);
          opciones_mes();
          get_options_mes();
      }

      if(frecuencia == 4){
          html = '<label for="subtotal">Día del mes: </label><select  class="form-control" id="frecuencia_option_mensual"></select>';
          $("#div_opcciones_frecuencia").html(html);
          opciones_mes();
          get_options_mes();
      }

      if(frecuencia == 5){
          html = '<label for="subtotal">Día del mes: </label><select  class="form-control" id="frecuencia_option_mensual"></select>';
          $("#div_opcciones_frecuencia").html(html);
          opciones_mes();
          get_options_mes();
      }

      if(frecuencia == 6){
          html = '<label for="subtotal">Día del mes: </label><select  class="form-control" id="frecuencia_option_mensual"></select>';
          $("#div_opcciones_frecuencia").html(html);
          opciones_mes();
          get_options_mes();
      }

      if(frecuencia == 7){
          html = '<label for="subtotal">Día del mes: </label><select  class="form-control" id="frecuencia_option_mensual"></select>';
          $("#div_opcciones_frecuencia").html(html);
          opciones_mes();
          get_options_mes();
      }

      if(frecuencia == 8){
          html = '<label for="subtotal">Día: </label><select  class="form-control" id="frecuencia_option_mensual"></select><label for="subtotal">Mes: </label><select  class="form-control" id="frecuencia_option_mes"><option value="01">Enero</option><option value="02">Febrero</option><option value="03">Marzo</option><option value="04">Abril</option><option value="05">Mayo</option><option value="06">Junio</option><option value="07">Julio</option><option value="08">Agosto</option><option value="09">Setiembre</option><option value="10">Octubre</option><option value="11">Noviembre</option><option value="12">Diciembre</option></select>';
          $("#div_opcciones_frecuencia").html(html);
          opciones_mes();
          get_options_anual();
      }

      if(frecuencia == 9){
          html = '<label for="subtotal">Cantidad de días: </label><input type="number"  class="form-control" id="cantidad_dias"/>';
          $("#div_opcciones_frecuencia").html(html);
          get_options_cantidad();
      }
  });

    $("#div_factura_recurrente").addClass("hidden");
    $("#select_factura_recurrente_select").change(function(){
        var recurrente = $(this).val();
        if(recurrente == 1){
            $("#div_factura_recurrente").removeClass("hidden");
        }else{
            $("#div_factura_recurrente").addClass("hidden");
        }
    });

$("#programar_envio_btn").click(function(){
  var fecha_envio_modal = $("#fecha_envio_modal").val();
  var select_factura_recurrente_select = $("#select_factura_recurrente_select").val();
  var frecuencia_select = $("#frecuencia_select").val();
  var opciones_recurrencia_modal = $("#opciones_recurrencia_modal").val();
  $("#fecha_envio").val(fecha_envio_modal);
  $("#select_factura_recurrente").val(select_factura_recurrente_select);
  $("#frecuencia").val(frecuencia_select);
  $("#opciones_recurrencia").val(opciones_recurrencia_modal);
  $(".close").click();
});


});

function opciones_quincenal(){
  var options = '';
  for(var i = 1; i <= 15; i++){
    if (i > 9){
      options += '<option value="'+i+'">'+i+'</option>';
    }else{
        options += '<option value="0'+i+'">0'+i+'</option>';
    }
  }
  $('#frecuencia_option_1_quincenal').html(options);
  options = '';
  for(var i = 15; i <= 31; i++){
      
    if (i > 9){
      options += '<option value="'+i+'">'+i+'</option>';
    }else{
        options += '<option value="0'+i+'">0'+i+'</option>';
    }
  }
  $('#frecuencia_option_2_quincenal').html(options);

}
function opciones_mes(){
  var options = '';
  for(var i = 1; i <= 31; i++){
      
    if (i > 9){
      options += '<option value="'+i+'">'+i+'</option>';
    }else{
        options += '<option value="0'+i+'">0'+i+'</option>';
    }
  }
  $('#frecuencia_option_mensual').html(options);

}

function get_options_semanal(){
  $("#opciones_recurrencia_modal").val(0);
  $("#frecuencia_option_semanal").change(function(){
        var option = $(this).val();
        $("#opciones_recurrencia_modal").val(option);
  });
}
function get_options_quincenal(){
  $("#opciones_recurrencia_modal").val('1,15');
  $("#frecuencia_option_1_quincenal").change(function(){
        var option1 = $("#frecuencia_option_1_quincenal").val();
        var option2 = $("#frecuencia_option_2_quincenal").val();
        $("#opciones_recurrencia_modal").val(option1+','+option2);
  });
  $("#frecuencia_option_2_quincenal").change(function(){
        var option1 = $("#frecuencia_option_1_quincenal").val();
        var option2 = $("#frecuencia_option_2_quincenal").val();
        $("#opciones_recurrencia_modal").val(option1+','+option2);
  });
}
function get_options_mes(){
  $("#opciones_recurrencia_modal").val('1');
  $("#frecuencia_option_mensual").change(function(){
        var option = $(this).val();
        $("#opciones_recurrencia_modal").val(option);
  });
}
function get_options_anual(){
  $("#opciones_recurrencia_modal").val('1/1');
  $("#frecuencia_option_mensual").change(function(){
        var dia = $("#frecuencia_option_mensual").val();
        var mes = $("#frecuencia_option_mes").val();
        $("#opciones_recurrencia_modal").val(dia+'/'+mes);
  });
  $("#frecuencia_option_mes").change(function(){
        var dia = $("#frecuencia_option_mensual").val();
        var mes = $("#frecuencia_option_mes").val();
        $("#opciones_recurrencia_modal").val(dia+'/'+mes);
  });
}
function get_options_cantidad(){
  $("#opciones_recurrencia_modal").val("");
  $("#cantidad_dias").change(function(){
        var option = $(this).val();
        $("#opciones_recurrencia_modal").val(option);
  });
}

function toggleRetencion() {
  var metodo = $("#medio_pago").val();
  if( metodo == '02' ){
    $("#field-retencion").show();
  }else {
    $("#field-retencion").hide();
  }
}

</script>

@endsection
