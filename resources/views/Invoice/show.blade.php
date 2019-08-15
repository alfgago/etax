@extends('layouts/app')

@section('title') 
  Ver factura emitida
@endsection
@section('breadcrumb-buttons')
  <button type="submit" onclick="$('#btn-submit-form').click();"  class="btn btn-primary">Guardar factura</button>
@endsection 
@section('content') 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-emitidas/actualizar-categorias" class="show-form"> 
          @csrf
          @method('post') 
          <input type="hidden" id="current-index" value="{{ count($invoice->items) }}">

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
                    
                    <div class="form-group col-md-12 with-button">
                      <label for="cliente">Seleccione el cliente</label>
                      <select class="form-control select-search" name="client_id" id="client_id" placeholder="" required disabled>
                        <option value=''>-- Seleccione un cliente --</option>
                        @foreach ( currentCompanyModel()->clients as $cliente )
                          <option  {{ $invoice->client_id == $cliente->id ? 'selected' : '' }} value="{{ $cliente->id }}" >{{ $cliente->toString() }}</option>
                        @endforeach
                      </select>
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
                      <select class="form-control" name="currency" id="moneda" required disabled>
                        <option value="CRC" {{ $invoice->currency == 'CRC' ? 'selected' : '' }}>CRC</option>
                        <option value="USD" {{ $invoice->currency == 'USD' ? 'selected' : '' }}>USD</option>
                      </select>
                    </div>
  
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" disabled class="form-control" name="currency_rate" id="tipo_cambio" value="{{ $invoice->currency_rate }}" required>
                    </div>
                  </div>
                </div>
              </div>  

              <div class="form-row">    
                <div class="form-group col-md-12">
                  <h3>
                    Total de factura
                  </h3>
                </div>
      
                 <div class="form-group col-md-4">
                  <label for="subtotal">Subtotal </label>
                  <input type="text" class="form-control" value="{{number_format($invoice->subtotal, 2)}}" disabled name="subtotal"  required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="iva_amount">Monto IVA </label>
                  <input type="text" class="form-control" value="{{number_format($invoice->iva_amount, 2)}}" disabled name="iva_amount" required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" value="{{number_format($invoice->total, 2)}}" disabled name="total"  >
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
                    <input type="text" class="form-control" disabled name="document_number" id="document_number" value="{{ $invoice->document_number }}" required>
                  </div>
  
                  <div class="form-group col-md-6">
                    <label for="document_key">Clave de factura</label>
                    <input type="text" class="form-control" disabled name="document_key" id="document_key" value="{{ $invoice->document_key }}" >
                  </div>

                  <div class="form-group col-md-4">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" disabled class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{ $invoice->generatedDate()->format('d/m/Y') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" disabled class="form-control input-hora" name="hora" required value="{{ $invoice->generatedDate()->format('g:i A') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" disabled class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ $invoice->dueDate()->format('d/m/Y') }}">
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
                                  <option {{ $invoice->commercial_activity == $actividad->codigo ? 'selected' : '' }} value="{{ $actividad->codigo }}" >{{ $actividad->codigo }} - {{ $actividad->actividad }} </option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <input type="text" value="{{$invoice->id}}" name="invoice_id" hidden>
                  <div class="form-group col-md-6">
                  <label for="sale_condition">Condición de venta</label>
                  <div class="input-group">
                    <select id="condicion_venta" disabled name="sale_condition" class="form-control" required>
                      <option {{ $invoice->sale_condition == '01' ? 'selected' : '' }} value="01">Contado</option>
                      <option {{ $invoice->sale_condition == '02' ? 'selected' : '' }} value="02">Crédito</option>
                      <option {{ $invoice->sale_condition == '03' ? 'selected' : '' }} value="03">Consignación</option>
                      <option {{ $invoice->sale_condition == '04' ? 'selected' : '' }} value="04">Apartado</option>
                      <option {{ $invoice->sale_condition == '05' ? 'selected' : '' }} value="05">Arrendamiento con opción de compra</option>
                      <option {{ $invoice->sale_condition == '06' ? 'selected' : '' }} value="06">Arrendamiento en función financiera</option>
                      <option {{ $invoice->sale_condition == '99' ? 'selected' : '' }} value="99">Otros</option>
                    </select>
                  </div>
                </div>
  
                <div class="form-group col-md-6">
                  <label for="payment_type">Método de pago</label>
                  <div class="input-group">
                    <select id="medio_pago" disabled name="payment_type" class="form-control" onchange="toggleRetencion();" required>
                      <option {{ $invoice->payment_type == '01' ? 'selected' : '' }} value="01" selected>Efectivo</option>
                      <option {{ $invoice->payment_type == '02' ? 'selected' : '' }} value="02">Tarjeta</option>
                      <option {{ $invoice->payment_type == '03' ? 'selected' : '' }} value="03">Cheque</option>
                      <option {{ $invoice->payment_type == '04' ? 'selected' : '' }} value="04">Transferencia-Depósito Bancario</option>
                      <option {{ $invoice->payment_type == '05' ? 'selected' : '' }} value="05">Recaudado por terceros</option>
                      <option {{ $invoice->payment_type == '99' ? 'selected' : '' }} value="99">Otros</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group col-md-12" id="field-retencion" style="display:none;">
                  <label for="retention_percent">Porcentaje de retención</label>
                  <div class="input-group">
                    <select id="retention_percent" disabled name="retention_percent" class="form-control" required>
                      <option value="6" {{ $invoice->retention_percent == 6 ? 'selected' : '' }}>6%</option>
                      <option value="3" {{ $invoice->retention_percent == 3 ? 'selected' : '' }}>3%</option>
                      <option value="0" {{ $invoice->retention_percent == 0 ? 'selected' : '' }}>Sin retención</option>
                    </select>
                  </div>
                </div>

                  <div class="form-group col-md-6">
                    <label for="other_reference">Referencia</label>
                    <input type="text" disabled class="form-control" name="other_reference" id="referencia" value="{{ $invoice->other_reference }}" >
                  </div>

                  <div class="form-group col-md-6">
                    <label for="buy_order">Orden de compra</label>
                    <input type="text" disabled class="form-control" name="buy_order" id="orden_compra" value="{{ $invoice->buy_order }}" >
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Notas</label>
                    <input type="text" disabled class="form-control" name="description" id="notas" placeholder="" value="{{ $invoice->description }}">
                  </div>

              </div>
            </div>
          </div>

          <div class="form-row" id="tabla-items-factura" >  

            <div class="form-group col-md-12">
              <h3>
                Líneas de factura
              </h3>
            </div>
  
            <div class="form-group col-md-12">
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Cant.</th>
                    <th>Unidad</th>
                    <th>Precio unitario</th>
                    <th>Código / Categoría</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                   @foreach ( $invoice->items as $item )
                   <tr class="item-tabla item-index-{{ $loop->index }}" index="{{ $loop->index }}" attr-num="{{ $loop->index }}" id="item-tabla-{{ $loop->index }}">
                      <td><span class="numero-fila">{{ $loop->index+1 }}</span>
                        <input type="hidden" class="item_id" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}"> </td>
                      <td>{{ $item->code }}</td>
                      <td>{{ $item->name }}</td>
                      <td>{{ $item->item_count }}</td>
                      <td>{{ \App\UnidadMedicion::getUnidadMedicionName($item->measure_unit) }}</td>
                      <td>{{ $item->unit_price }}</td>
                      <td>
                        <select class="form-control tipo_iva tipo_iva_{{ $loop->index+1 }} select-search" name="items[{{ $loop->index }}][tipo_iva]" >
                            @foreach( $codigos as $tipo)
                              <option {{ $item->iva_type == $tipo->id ? 'selected' : '' }} value="{{ $tipo['code'] }}" attr-iva="{{ $tipo['percentage'] }}" porcentaje="{{ $tipo['percentage'] }}" class="{{ @$tipo['hidden'] ? 'hidden' : '' }} " identificacion="{{$tipo->is_identificacion_plena}}">{{ $tipo['name'] }}</option>
                            @endforeach
                        </select>
                        <select class="mt-2 form-control tipo_producto" curr="{{$item->product_type}}" numero="{{ $loop->index+1 }}" name="items[{{ $loop->index }}][category_product]">
                            
                            @foreach( $product_categories as $tipo)
                              <option {{ $item->product_type == $tipo->id ? 'selected' : '' }} value="{{ $tipo['id'] }}" codigo="{{ $tipo['invoice_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" >{{ $tipo['name'] }}</option>
                            @endforeach
                        </select>
                      </td>
                      <td>{{ number_format($item->subtotal, 2) }}</td>
                      <td>{{ number_format($item->iva_amount, 2) }}</td>
                      <td>{{ number_format($item->total, 2) }}</td>
                      <td class='acciones'>
                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem({{ $loop->index }});"><i class='nav-icon i-Pen-2'></i> </span> 
                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ $loop->index }});' ><i class='nav-icon i-Close-Window'></i> </span> 
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        
          <div class="btn-holder hidden">
            <button id="btn-submit-form" type="submit" class="btn btn-primary">Guardar factura</button>
          </div>

        </form>
  </div>  
</div>
@endsection


@section('footer-scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="/assets/js/form-facturas.js?v=1"></script>

<script>
$(document).ready(function(){
  
  $(".tipo_iva").change(function(){
      var codigoIVA = $(this).find(':selected').val();
      var parent = $(this).parents('tr');
      parent.find('.tipo_producto option').hide();
      var tipoProducto = 0;
      parent.find(".tipo_producto option").each(function(){
        var posibles = $(this).attr('posibles').split(",");
      	if(posibles.includes(codigoIVA)){
          	$(this).show();
          	if( !tipoProducto ){
              tipoProducto = $(this).val();
            }
          }
      });
      parent.find('.tipo_producto').val( tipoProducto ).change();
  });
  
  toggleRetencion();
  
  $('.tipo_iva').change();
  $(".tipo_producto").each(function(){
    $(this).val($(this).attr('curr')).change();
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

<style>
  
  td.acciones {
      display: none;
  }
  
  .table .thead-dark th:last-of-type {
      display: none;
  }
  

  
</style>


@endsection