@extends('layouts/app')

<?php 
  $tipoHacienda = "SJB";
  $titulo = "Factura de inversión del sujeto pasivo";
  $document_type = '08';

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
                    <div class="form-group col-md-12">
                      <h3>
                        Cliente
                      </h3>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="actual">Empresa actual</label>
                      <input disabled readonly class="form-control" type="text" value="{{ $company->id_number . ' - ' . $company->name.' '.$company->last_name.' '.$company->last_name2 }}">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="send_email">Enviar copia a:</label>
                      <input type="email" class="form-control" name="send_email" id="send_email" value="">
                      <div class="description">Automáticamente se le enviará al correo de su empresa como una factura electrónica de compra.</div>
                    </div>
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
                        <option value="CRC"  data-rate="{{$rate}}" {{$company->default_currency == 'CRC' ? 'selected' : ''}}>CRC</option>
                        <option value="USD"  data-rate="{{$rate}}" {{$company->default_currency == 'USD' ? 'selected' : ''}}>USD</option>
                      </select>
                    </div>
      
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" data-rates="{{$rate}}" name="currency_rate" id="tipo_cambio" value="{{$company->default_currency == 'USD' ? $rate : '1.00'}}"required>
                    </div>
                  </div>
                </div>
              </div>
              
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

                <div class="form-group col-md-4 hidden" id="total_iva_devuelto-cont">
                  <label for="total">IVA Devuelto</label>
                  <input type="text" class="form-control total" name="total_iva_devuelto" id="total_iva_devuelto" placeholder="" readonly="true" required>
                </div>

                <div class="form-group col-md-4 hidden" id="total_iva_exonerado-cont">
                  <label for="total">IVA Exonerado</label>
                  <input type="text" class="form-control total" name="total_iva_exonerado" id="total_iva_exonerado" placeholder="" readonly="true" required>
                </div>

                <div class="form-group col-md-4 hidden" id="total_otros_cargos-cont">
                  <label for="total">Otros cargos</label>
                  <input type="text" class="form-control total" name="total_otros_cargos" id="total_otros_cargos" placeholder="" readonly="true" required>
                </div>
    
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" required>
                </div>
                
                <div class="form-group col-md-12">
                  <div onclick="agregarNuevaLinea();" class="btn btn-dark btn-agregar">Agregar línea</div>
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
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->addDays(3)->format('d/m/Y') }}" maxlength="10">
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
                  
                  <div class="form-group col-md-12" id="field-retencion" style="display:none; !important">
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
          
          <div class="form-row" id="tabla-otroscargos-factura" style="display: none;">  

            <div class="form-group col-md-12">
              <h3>
                Otros cargos
              </h3>
            </div>
            
            <div class="form-group col-md-12" >
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Receptor</th>
                    <th>Detalle</th>
                    <th>Monto del cargo</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                
                </tbody>
              </table>
            </div>
          </div>
          
        @include('Invoice.form-linea')
        @include( 'Invoice.form-otros-cargos' )
          @include( 'Invoice.form-nuevo-cliente' )
            <input type="text" hidden value="{{ $document_type }}" name="document_type" id="document_type">
          <div class="btn-holder hidden">
           
            <button id="btn-submit" type="submit" class="btn btn-primary">Enviar factura electrónica</button>
          </div>

      </form>
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button id='btn-submit-fe' onclick="$('#btn-submit').click();" class="btn btn-primary">Enviar factura electrónica</button>
@endsection

@section('footer-scripts')

<script>
$(document).ready(function(){
  $('#tipo_iva').val('B260').change();

  $('#moneda').change(function() {
    if ($(this).val() == 'USD') {
      $('#tipo_cambio').val($('#tipo_cambio').data('rates'))
    } else {
      $('#tipo_cambio').val('1.00')
    }
  });
});

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
