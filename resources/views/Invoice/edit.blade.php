@extends('layouts/app')

@section('title') 
  Editar factura emitida
@endsection

@section('content') 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-emitidas/{{ $invoice->id }}">
        @method('patch')
        @csrf

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
                      <select class="form-control" name="client_id" id="client_id" placeholder="" required>
                        <option value=''>-- Seleccione un cliente --</option>
                        @foreach ( auth()->user()->companies->first()->clients as $cliente )
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
                      <select class="form-control" name="currency" id="moneda" required>
                        <option value="crc" {{ $invoice->currency == 'crc' ? 'selected' : '' }}>CRC</option>
                        <option value="usd" {{ $invoice->currency == 'usd' ? 'selected' : '' }}>USD</option>
                      </select>
                    </div>
  
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" name="currency_rate" id="tipo_cambio" value="{{ $invoice->currency_rate }}" required>
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
                  <input type="text" class="form-control" name="subtotal" id="subtotal" placeholder="" readonly="true" required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="iva_amount">Monto IVA </label>
                  <input type="text" class="form-control" name="iva_amount" id="monto_iva" placeholder="" readonly="true" required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" >
                </div>
                
                <div class="form-group col-md-12">
                  <div onclick="abrirPopup('linea-popup');" class="btn btn-dark btn-agregar">Agregar linea</div>
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

                 <div class="form-group col-md-4">
                    <label for="generated_date">Fecha</label>
                    <div class="input-group">
                      <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{ $invoice->generatedDate()->format('d/m/Y') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Calendar-4"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="hora">Hora</label>
                    <div class="input-group">
                      <input id="hora" class="form-control input-hora" name="hora" required value="{{ $invoice->generatedDate()->format('g:i A') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Clock"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class="input-group">
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ $invoice->dueDate()->format('d/m/Y') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Calendar-4"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                  <label for="sale_condition">Condición de venta</label>
                  <div class="input-group">
                    <select id="condicion_venta" name="sale_condition" class="form-control" required>
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
                    <select id="medio_pago" name="payment_type" class="form-control" required>
                      <option {{ $invoice->sale_condition == '01' ? 'selected' : '' }} value="01" selected>Efectivo</option>
                      <option {{ $invoice->sale_condition == '02' ? 'selected' : '' }} value="02">Tarjeta</option>
                      <option {{ $invoice->sale_condition == '03' ? 'selected' : '' }} value="03">Cheque</option>
                      <option {{ $invoice->sale_condition == '04' ? 'selected' : '' }} value="04">Transferencia-Depósito Bancario</option>
                      <option {{ $invoice->sale_condition == '05' ? 'selected' : '' }} value="05">Recaudado por terceros</option>
                      <option {{ $invoice->sale_condition == '99' ? 'selected' : '' }} value="99">Otros</option>
                    </select>
                  </div>
                </div>

                  <div class="form-group col-md-6">
                    <label for="other_reference">Referencia</label>
                    <input type="text" class="form-control" name="other_reference" id="referencia" value="{{ $invoice->other_reference }}" >
                  </div>

                  <div class="form-group col-md-6">
                    <label for="buy_order">Orden de compra</label>
                    <input type="text" class="form-control" name="buy_order" id="orden_compra" value="{{ $invoice->buy_order }}" >
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Notas</label>
                    <input type="text" class="form-control" name="description" id="notas" placeholder="" value="{{ $invoice->description }}">
                  </div>

              </div>
            </div>
          </div>

          <div class="form-row" id="tabla-items-factura" >  

            <div class="form-group col-md-12">
              <h3>
                Lineas de factura
              </h3>
            </div>
  
            <div class="form-group col-md-12">
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo producto</th>
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
                   @foreach ( $invoice->items as $item )
                   <tr class="item-tabla item-index-{{ $loop->index }}" index="{{ $loop->index }}" attr-num="{{ $loop->index }}" id="item-tabla-{{ $loop->index }}">
                      <td><span class="numero-fila">{{ $loop->index+1 }}</span>
                        <input type="hidden" class='numero' name="items[{{ $loop->index }}][item_number]" value="{{ $loop->index+1 }}">
                        <input type="hidden" class="item_id" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}"> </td>
                      <td>{{ $item->code }}
                        <input type="hidden" class='codigo' name="items[{{ $loop->index }}][code]" value="{{ $item->code }}">
                      </td>
                      <td>{{ $item->name }}
                        <input type="hidden" class='nombre' name="items[{{ $loop->index }}][name]" value="{{ $item->name }}">
                      </td>
                      <td>{{ $item->product_type }}
                        <input type="hidden" class='tipo_producto' name="items[{{ $loop->index }}][product_type]" value="{{ $item->product_type }}">
                      </td>
                      <td>{{ $item->item_count }}
                        <input type="hidden" class='cantidad' name="items[{{ $loop->index }}][item_count]" value="{{ $item->item_count }}">
                      </td>
                      <td>{{ \App\Variables::getUnidadMedicionName($item->measure_unit) }}
                        <input type="hidden" class='unidad_medicion' name="items[{{ $loop->index }}][measure_unit]" value="{{ $item->measure_unit }}">
                      </td>
                      <td>{{ $item->unit_price }}
                        <input type="hidden" class='precio_unitario' name="items[{{ $loop->index }}][unit_price]" value="{{ $item->unit_price }}">
                      </td>
                      <td>{{ \App\Variables::getTipoRepercutidoIVAName($item->iva_type) }}
                        <input type="hidden" class='tipo_iva' name="items[{{ $loop->index }}][iva_type]" value="{{ $item->iva_type }}">
                      </td>
                      <td>{{ $item->subtotal }}
                        <input class="subtotal" type="hidden" name="items[{{ $loop->index }}][subtotal]" value="{{ $item->subtotal }}">
                      </td>
                      <td>{{ $item->iva_amount }}
                        <input class="porc_iva" type="hidden" name="items[{{ $loop->index }}][iva_percentage]" value="{{ $item->iva_percentage }}">
                        <input class="monto_iva" type="hidden" name="items[{{ $loop->index }}][iva_amount]" value="{{ $item->iva_amount }}">
                      </td>
                      <td>
                        {{ $item->total }}
                        <input class="total" type="hidden" name="items[{{ $loop->index }}][total]" value="{{ $item->total }}">
                      </td>
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
        
          @include( 'Invoice.form-linea' )

          <div class="btn-holder hidden">
            <button id="btn-submit" type="submit" class="btn btn-primary">Guardar factura</button>
            <button type="submit" class="btn btn-primary">Enviar factura electrónica</button>
            <button type="submit" class="btn btn-primary">Programar factura</button>
          </div>

        </form>
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar factura</button>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Enviar factura electrónica</button>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Programar envío</button>
@endsection 

@section('header-scripts')
    <link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.css">
    <link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.date.css">
    <link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.time.css">
@endsection

@section('footer-scripts')
<script src="/assets/js/vendor/pickadate/picker.js"></script>
<script src="/assets/js/vendor/pickadate/picker.date.js"></script>
<script src="/assets/js/vendor/pickadate/picker.time.js"></script>
<script src="/assets/js/form-facturas.js"></script>
<script>
$(document).ready(function(){
  
  $('#tipo_iva').val('103');
  
  var subtotal = 0;
  var monto_iva = 0;
  var total = 0;
  $('.item-tabla').each(function(){
    var s = parseFloat($(this).find('.subtotal').val());
    var m = parseFloat($(this).find('.porc_iva').val()) / 100;
    var t = parseFloat($(this).find('.total').val());
    subtotal += s;
    monto_iva += s*m;	
    total += t;	
  });

  $('#subtotal').val(subtotal);
  $('#monto_iva').val(monto_iva);
  $('#total').val(total);
  
});
</script>

@endsection