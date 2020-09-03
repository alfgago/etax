@extends('layouts/app')

@section('title') 
  Ver factura recibida
@endsection

@section('content') 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="" class="show-form">

          <input type="hidden" id="current-index" value="{{ count($bill->items) }}">

          <div class="form-row">
            <div class="col-md">
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <h3>
                        Proveedor
                      </h3>
                    </div>

                    <div class="form-group col-md-12 with-button">
                      <label for="provider_id">Seleccione el proveedor</label>
                      <select class="form-control select-search" name="provider_id" id="provider_id" placeholder="" required>
                        <option value='' >-- Seleccione un proveedor --</option>
                        @foreach ( currentCompanyModel()->providers as $proveedor )
                          <option {{ $bill->provider_id == $proveedor->id ? 'selected' : '' }} value="{{ $proveedor->id }}" >{{ $proveedor->id_number }} - {{ $proveedor->first_name }}</option>
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
                        <option value="crc" {{ $bill->currency == 'crc' ? 'selected' : '' }}>CRC</option>
                        <option value="usd" {{ $bill->currency == 'usd' ? 'selected' : '' }}>USD</option>
                      </select>
                    </div>
  
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" name="currency_rate" id="tipo_cambio" value="{{ $bill->currency_rate }}" required>
                    </div>
                  </div>
                </div>
              </div>  
              
              <div class="form-row">  
              
                <div class="form-group col-md-12">
                  <h3>
                    Datos de aceptación
                  </h3>
                </div>
                
                <div class="form-group col-md-3">
                  <label for="currency">XML de factura</label>
                  <input type="text" class="form-control" readonly="true" value="{{ $bill->xml_schema == 43 ? '4.3' : '4.2' }}">
                </div>
                  
                <div class="form-group col-md-9">
                    <label for="activity_company_verification">Actividad Comercial</label>
                    <div class="input-group">
                      <select id="activity_company_verification" name="activity_company_verification" class="form-control" required disabled readonly>
                          @foreach ( $arrayActividades as $actividad )
                              <option {{ $bill->activity_company_verification == $actividad->codigo ? 'selected' : '' }} value="{{ $actividad->codigo }}" >{{ $actividad->codigo }} - {{ $actividad->actividad }}</option>
                          @endforeach
                      </select>
                    </div>
                </div>
                  
                <div class="form-group col-md-12 inline-form inline-checkbox">
                  <label for="accept_status">
                    <span>¿Aceptada desde otro proveedor?</span>
                    <input type="checkbox" class="form-control" id="accept_status" name="accept_status" onchange="toggleInfoAceptacion();" disabled readonly {{ $bill->accept_status == 1 ? 'checked' : '' }} >
                  </label>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_condition">Condición de acceptación</label>
                    <select class="form-control" name="accept_iva_condition" id="accept_iva_condition" disabled readonly>
                      <option value="01" {{ $bill->accept_iva_condition == "01" ? 'selected' : '' }}>Genera crédito IVA</option>
                      <option value="02" {{ $bill->accept_iva_condition == "02" ? 'selected' : '' }}>Genera crédito parcial del IVA</option>
                      <option value="03" {{ $bill->accept_iva_condition == "03" ? 'selected' : '' }}>Bienes de capital</option>
                      <option value="04" {{ $bill->accept_iva_condition == "04" ? 'selected' : '' }}>Gasto corriente (no genera IVA)</option>
                      <option value="05" {{ $bill->accept_iva_condition == "05" ? 'selected' : '' }}>Proporcionalidad</option>
                    </select>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_acreditable">IVA acreditable</label>
                    <div class="input-group">
                      <input type="number" id="accept_iva_acreditable" name="accept_iva_acreditable" class="form-control" value="{{ $bill->accept_iva_acreditable }}" disabled readonly />
                    </div>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_gasto">IVA al gasto</label>
                    <div class="input-group">
                      <input type="number" id="accept_iva_gasto" name="accept_iva_gasto" class="form-control" value="{{ $bill->accept_iva_gasto }}" disabled readonly />
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

                <div class="form-group col-md-4 {{ $bill->total_iva_devuelto ?? 'hidden' }}" id="total_iva_devuelto-cont">
                  <label for="total">IVA Devuelto</label>
                  <input type="text" class="form-control total" name="total_iva_devuelto" id="total_iva_devuelto" value="{{ $bill->total_iva_devuelto }}" placeholder="" readonly="true" required>
                </div>

                <div class="form-group col-md-4 hidden" id="total_otros_cargos-cont">
                  <label for="total">Otros cargos</label>
                  <input type="text" class="form-control total" name="total_otros_cargos" id="total_otros_cargos" placeholder="" readonly="true" required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" >
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
                    <input type="text" class="form-control" name="document_number" id="document_number" value="{{ $bill->document_number }}" required>
                  </div>
  
                  <div class="form-group col-md-6">
                    <label for="document_key">Clave de factura</label>
                    <input type="text" class="form-control" name="document_key" id="document_key" value="{{ $bill->document_key }}" >
                  </div>
                
                  <div class="form-group col-md-4">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{ $bill->generatedDate()->format('d/m/Y') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" class="form-control input-hora" name="hora" required value="{{ $bill->generatedDate()->format('g:i A') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ $bill->dueDate()->format('d/m/Y') }}">
                      <span class="input-group-addon">
                        <i class="icon-regular i-Calendar-4"></i>
                      </span>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                  <label for="sale_condition">Condición de venta</label>
                  <div class="input-group">
                    <select id="condicion_venta" name="sale_condition" class="form-control" required>
                      <option {{ $bill->sale_condition == '01' ? 'selected' : '' }} value="01">Contado</option>
                      <option {{ $bill->sale_condition == '02' ? 'selected' : '' }} value="02">Crédito</option>
                      <option {{ $bill->sale_condition == '03' ? 'selected' : '' }} value="03">Consignación</option>
                      <option {{ $bill->sale_condition == '04' ? 'selected' : '' }} value="04">Apartado</option>
                      <option {{ $bill->sale_condition == '05' ? 'selected' : '' }} value="05">Arrendamiento con opción de compra</option>
                      <option {{ $bill->sale_condition == '06' ? 'selected' : '' }} value="06">Arrendamiento en función financiera</option>
                      <option {{ $bill->sale_condition == '99' ? 'selected' : '' }} value="99">Otros</option>
                    </select>
                  </div>
                </div>
  
                <div class="form-group col-md-6">
                  <label for="payment_type">Método de pago</label>
                  <div class="input-group">
                    <select id="medio_pago" name="payment_type" class="form-control" required>
                      <option {{ $bill->payment_type == '01' ? 'selected' : '' }} value="01" selected>Efectivo</option>
                      <option {{ $bill->payment_type == '02' ? 'selected' : '' }} value="02">Tarjeta</option>
                      <option {{ $bill->payment_type == '03' ? 'selected' : '' }} value="03">Cheque</option>
                      <option {{ $bill->payment_type == '04' ? 'selected' : '' }} value="04">Transferencia-Depósito Bancario</option>
                      <option {{ $bill->payment_type == '05' ? 'selected' : '' }} value="05">Recaudado por terceros</option>
                      <option {{ $bill->payment_type == '99' ? 'selected' : '' }} value="99">Otros</option>
                    </select>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="other_reference">Referencia</label>
                  <input type="text" class="form-control" name="other_reference" id="referencia" value="{{ $bill->other_reference }}" >
                </div>

                <div class="form-group col-md-6">
                  <label for="buy_order">Orden de compra</label>
                  <input type="text" class="form-control" name="buy_order" id="orden_compra" value="{{ $bill->buy_order }}" >
                </div>

                <div class="form-group col-md-12">
                  <label for="description">Notas</label>
                  <input type="text" class="form-control" name="description" id="notas" placeholder="" value="{{ $bill->description }}">
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
                    <th>Tipo IVA</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                   @foreach ( $bill->items as $item )
                   <tr class="item-tabla item-index-{{ $loop->index }}" index="{{ $loop->index }}" attr-num="{{ $loop->index }}" id="item-tabla-{{ $loop->index }}">
                      <td><span class="numero-fila">{{ $loop->index+1 }}</span>
                        <input type="hidden" class='numero' name="items[{{ $loop->index }}][item_number]" value="{{ $loop->index+1 }}">
                        <input type="hidden" class="item_id" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}"> </td>
                      <td>{{ $item->code }}
                        <input type="hidden" class='codigo' name="items[{{ $loop->index }}][code]" value="{{ $item->code }}">
                      </td>
                      <td>{{ $item->name }}
                        <input type="hidden" class='nombre' name="items[{{ $loop->index }}][name]" value="{{ $item->name }}">
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
                      <td>{{ \App\Variables::getTipoSoportadoIVAName($item->iva_type) }}
                        <input type="hidden" class='tipo_iva' name="items[{{ $loop->index }}][iva_type]" value="{{ $item->iva_type }}">
                        <input type='hidden' class='porc_identificacion_plena' name='items[{{ $loop->index }}][porc_identificacion_plena]' value='{{ $item->porc_identificacion_plena }}'>
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
                        <input class="is_identificacion_especifica" type="hidden" name="items[{{ $loop->index }}][is_identificacion_especifica]" value="{{ $item->is_identificacion_especifica }}">
                      </td>
                      <td class='acciones'>
                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem({{ $loop->index }});"> <i class="fa fa-pencil" aria-hidden="true"></i> </span> 
                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ $loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
            <div class="form-row" id="tabla-otroscargos-factura" style="{{ isset($bill->otherCharges[0]) ? '' : 'display: none;'}}">  

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
                    @foreach ( $bill->otherCharges as $item )
                     <tr class="otros-tabla otros-index-{{ $loop->index }}" index="{{ $loop->index }}" attr-num="{{ $loop->index }}" id="otros-tabla-{{ $loop->index }}">
                        <td><span class="numero-fila">{{ $loop->index+1 }}</span></td>
                        <td>{{ $item->getTypeString() }}</td>
                        <td>{{ $item->provider_id_number }} {{ $item->provider_name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->amount,2) }} </td>
                        <td class='acciones'>
                          <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('otros-popup'); cargarFormOtros({{ $loop->index }});"> <i class="fa fa-pencil" aria-hidden="true"></i> </span> 
                          <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarOtros({{ $loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                        </td>
                        <td class="hidden">
                          <input type="hidden" class='otros-item_number' name="otros[{{ $loop->index }}][item_number]" itemname="item_number" value="{{ $loop->index+1 }}">
                          <input type="hidden" class="otros_id" name="otros[{{ $loop->index }}][id]" itemname="id" value="{{ $item->id }}"> 
                          <input type="hidden" class="otros-document_type" name="otros[{{ $loop->index }}][document_type]" itemname="document_type" value="{{ $item->document_type }}"> 
                          <input type="hidden" class='otros-provider_id_number' name="otros[{{ $loop->index }}][provider_id_number]" itemname="provider_id_number" value="{{ $item->provider_id_number }}">
                          <input type="hidden" class='otros-provider_name' name="otros[{{ $loop->index }}][provider_name]" itemname="provider_name" value="{{ $item->provider_name }}">
                          <input type="hidden" class='otros-description' name="otros[{{ $loop->index }}][description]" itemname="description" value="{{ $item->description }}">
                          <input type="hidden" class='otros-amount' name="otros[{{ $loop->index }}][amount]" itemname="amount" value="{{ $item->amount }}">
                          <input type="hidden" class='otros-percentage' name="otros[{{ $loop->index }}][percentage]" itemname="percentage" value="{{ $item->percentage }}">
                        </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            
          </div>
        

        </form>
  </div>  
</div>
@endsection

@section('footer-scripts')

<script>

$(document).ready(function(){
  
  $('#tipo_iva').val('3');
  
  var subtotal = 0;
  var monto_iva = 0;
  var total = 0;
  var iva_exonerado = 0;
  var otros_cargos = 0;

  $('.item-tabla').each(function(){
    var s = parseFloat($(this).find('.subtotal').val());
    var m = parseFloat($(this).find('.monto_iva').val());
    var t = parseFloat($(this).find('.total').val());
    subtotal += s;
    monto_iva += m;	
    total += t;
  });

    $('.otros-tabla').each(function(){
        var ot = parseFloat($(this).find('.otros-amount').val());

        if(!ot){ ot = 0; }
        otros_cargos += ot;
    });

  $('#subtotal').val( fixComas(subtotal) );
  $('#monto_iva').val( fixComas(monto_iva) );
  var devuelto = parseFloat( $('#total_iva_devuelto').val() );
  total = total - devuelto + otros_cargos;
  $('#total').val( fixComas(total) );
    $('#total_otros_cargos').val(otros_cargos);
  toggleRetencion();
  
});
</script>

<style>
  
  td.acciones {
      display: none;
  }
  
  .table .thead-dark th:last-of-type {
      display: none;
  }
  
  form.show-form input, 
  form.show-form select,
  .select2-selection--single {
      border: 0 !important;
      background: #eee !important;
      cursor: not-allowed;
      pointer-events: none;
  }
  
</style>


@endsection
