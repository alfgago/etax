@extends('layouts/app')

@section('title') 
  Editar factura recibida
@endsection

@section('content') 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-recibidas/{{ $bill->id }}">
        @method('patch')
        @csrf

          <input type="hidden" id="current-index" value="{{ count($bill->items) }}">

          <input type="hidden" id="is-compra" value="1">

          <div class="form-row">
            <div class="col-md">
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <h3>
                        Proveedor
                      </h3>
                      <div onclick="abrirPopup('nuevo-proveedor-popup');" class="btn btn-agregar btn-agregar-cliente">Nuevo proveedor</div>
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
                        <option value="CRC" {{ $bill->currency == 'CRC' ? 'selected' : '' }}>CRC</option>
                        <option value="USD" {{ $bill->currency == 'USD' ? 'selected' : '' }}>USD</option>
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
                  <select class="form-control" name="xml_schema" id="xml_schema" required>
                    <option value="43" {{ $bill->xml_schema == 43 ? 'selected' : '' }}>4.3</option>
                    <option value="42" {{ $bill->xml_schema == 42 ? 'selected' : '' }}>4.2</option>
                  </select>
                </div>
                  
                <div class="form-group col-md-9">
                    <label for="activity_company_verification">Actividad Comercial</label>
                    <div class="input-group">
                      <select id="activity_company_verification" name="activity_company_verification" class="form-control" required>
                          @foreach ( $arrayActividades as $actividad )
                              <option {{ $bill->activity_company_verification == $actividad->codigo ? 'selected' : '' }} value="{{ $actividad->codigo }}" >{{ $actividad->codigo }} - {{ $actividad->actividad }}</option>
                          @endforeach
                      </select>
                    </div>
                </div>
                  
                <div class="form-group col-md-12 inline-form inline-checkbox">
                  <label for="accept_status">
                    <span>¿Aceptada desde otro proveedor?</span>
                    <input type="checkbox" class="form-control" id="accept_status" name="accept_status" {{ $bill->accept_status == 1 ? 'checked' : '' }}>
                  </label>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_condition">Condición de acceptación</label>
                    <select class="form-control" name="accept_iva_condition" id="accept_iva_condition">
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
                      <input type="number" id="accept_iva_acreditable" name="accept_iva_acreditable" class="form-control" value="{{ $bill->accept_iva_acreditable }}" />
                    </div>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_gasto">IVA al gasto</label>
                    <div class="input-group">
                      <input type="number" id="accept_iva_gasto" name="accept_iva_gasto" class="form-control" value="{{ $bill->accept_iva_gasto }}" />
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

                <div class="form-group col-md-4 hidden" id="total_iva_devuelto-cont">
                  <label for="total">IVA Devuelto</label>
                  <input type="text" class="form-control total" name="total_iva_devuelto" id="total_iva_devuelto" placeholder="" readonly="true" required>
                </div>

                <div class="form-group col-md-4 hidden" id="total_iva_exonerado-cont">
                  <label for="total">IVA Exonerado</label>
                  <input type="text" class="form-control total" name="total_iva_exonerado" id="total_iva_exonerado" placeholder="" readonly="true" required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" >
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
                    <input type="text" class="form-control" name="document_number" id="document_number" value="{{ $bill->document_number }}" required>
                  </div>
  
                  <div class="form-group col-md-6 not-required">
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

                <div class="form-group col-md-6 not-required">
                  <label for="other_reference">Referencia</label>
                  <input type="text" class="form-control" name="other_reference" id="referencia" value="{{ $bill->other_reference }}" >
                </div>

                <div class="form-group col-md-6 not-required">
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
                      <td><span class="numero-fila">{{ $loop->index+1 }}</span></td>
                      <td>{{ $item->code }}</td>
                      <td>{{ $item->name }}</td>
                      <td>{{ $item->item_count }}</td>
                      <td>{{ \App\Variables::getUnidadMedicionName($item->measure_unit) }}</td>
                      <td>{{ $item->unit_price }} </td>
                      <td>{{ \App\Variables::getTipoSoportadoIVAName($item->iva_type) }} <br> - {{ @\App\ProductCategory::find($item->product_type)->name }} </td>
                      <td>{{ $item->subtotal }}</td>
                      <td>{{ $item->iva_amount }}</td>
                      <td>{{ $item->total }} </td>
                      <td class='acciones'>
                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem({{ $loop->index }});"> <i class="fa fa-pencil" aria-hidden="true"></i> </span> 
                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ $loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                      </td>
                      <td class="hidden">
                        <input type="hidden" class='numero' name="items[{{ $loop->index }}][item_number]" value="{{ $loop->index+1 }}" itemname="item_number">
                        <input type="hidden" class="item_id" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}" itemname="id"> 
                        <input type="hidden" class='codigo' name="items[{{ $loop->index }}][code]" value="{{ $item->code }}" itemname="code">
                        <input type="hidden" class='nombre' name="items[{{ $loop->index }}][name]" value="{{ $item->name }}" itemname="name">
                        <input type="hidden" class='tipo_producto' name="items[{{ $loop->index }}][product_type]" value="{{ $item->product_type }}" itemname="product_type">
                        <input type="hidden" class='cantidad' name="items[{{ $loop->index }}][item_count]" value="{{ $item->item_count }}" itemname="item_count">
                        <input type="hidden" class='unidad_medicion' name="items[{{ $loop->index }}][measure_unit]" value="{{ $item->measure_unit }}" itemname="measure_unit">
                        <input type="hidden" class='precio_unitario' name="items[{{ $loop->index }}][unit_price]" value="{{ $item->unit_price }}" itemname="unit_price">
                        <input type="hidden" class='tipo_iva' name="items[{{ $loop->index }}][iva_type]" value="{{ $item->iva_type }}" itemname="iva_type">
                        <input type='hidden' class='porc_identificacion_plena' name='items[{{ $loop->index }}][porc_identificacion_plena]' value='{{ $item->porc_identificacion_plena }}' itemname="porc_identificacion_plena">
                        <input type='hidden' class='discount_type' name='items[{{ $loop->index }}][discount_type]' value='{{ $item->discount_type }}' itemname="discount_type">
                        <input type='hidden' class='discount' name='items[{{ $loop->index }}][discount]' value='{{ $item->discount }}' itemname="discount">
                        <input class="subtotal" type="hidden" name="items[{{ $loop->index }}][subtotal]" value="{{ $item->subtotal }}" itemname="subtotal">
                        <input class="porc_iva" type="hidden" name="items[{{ $loop->index }}][iva_percentage]" value="{{ $item->iva_percentage }}" itemname="iva_percentage">
                        <input class="monto_iva" type="hidden" name="items[{{ $loop->index }}][iva_amount]" value="{{ $item->iva_amount }}" itemname="iva_amount">
                        <input class="total" type="hidden" name="items[{{ $loop->index }}][total]" value="{{ $item->total }}" itemname="total">
                        <input class="is_identificacion_especifica" type="hidden" name="items[{{ $loop->index }}][is_identificacion_especifica]" value="{{ $item->is_identificacion_especifica }}" itemname="is_identificacion_especifica">
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
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
                      <td>{{ $item->document_type }}</td>
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
          
          @include( 'Bill.form-otros-cargos' )
          @include( 'Bill.form-linea' )
          @include( 'Bill.form-nuevo-proveedor' )
        
          <button id="btn-submit" type="submit" class="hidden">Guardar factura</button>

        </form>
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar factura</button>
@endsection 

@section('footer-scripts')

<script>

$(document).ready(function(){
  
  $('#tipo_iva').val('3');
  
  var subtotal = 0;
  var monto_iva = 0;
  var total = 0;
  $('.item-tabla').each(function(){
    var s = parseFloat($(this).find('.subtotal').val());
    var m = parseFloat($(this).find('.monto_iva').val());
    var t = parseFloat($(this).find('.total').val());
    subtotal += s;
    monto_iva += m;	
    total += t;	
  });
  
  $('#subtotal').val( fixComas(subtotal) );
  $('#monto_iva').val( fixComas(monto_iva) );
  $('#total').val( fixComas(total) );
  
  toggleRetencion();
  
});
</script>

@endsection