@extends('layouts/app')

@section('title')
    Nota de credito
@endsection

@section('content')
    <div class="row form-container">
        <div class="col-md-12">
            <form method="POST" action="/facturas-emitidas/anular/{{ $invoice->id }}" class="show-form">
                @csrf
                @method('patch')
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


                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <h3>
                                    Información de referencia
                                </h3>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="subtotal">Tipo</label>
                                <select name="code_note" id="code_note" class="form-control" required>
                                    <option value="01" selected="">Anula documento de referencia</option>
                                    <option value="02">Corrige texto de ocumento de referencia</option>
                                    <option value="03">Corrige monto</option>
                                    <option value="04">Referencia a otro documento</option>
                                    <option value="05">Sustituye comprobante provisional por contigencia</option>
                                    <option value="99">Otros</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="iva_amount">Razón</label>
                                <input type="text" class="form-control" name="reason" id="reason" placeholder="">
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
                                <th>Tipo/Categoría IVA</th>
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
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->item_count }}</td>
                                    <td>{{ \App\Variables::getUnidadMedicionName($item->measure_unit) }}</td>
                                    <td>{{ $item->unit_price }} </td>
                                    <td>{{ \App\Variables::getTipoRepercutidoIVAName($item->iva_type) }} <br> - {{ @\App\ProductCategory::find($item->product_type)->name }} </td>
                                    <td>{{ $item->subtotal }}</td>
                                    <td>{{ $item->iva_amount }}</td>
                                    <td>{{ $item->total }}</td>
                                    <td class='acciones'>
                                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem({{ $loop->index }});"> <i class="fa fa-pencil" aria-hidden="true"></i> </span>
                                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ $loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span>
                                    </td>
                                    <td class="hidden">
                                        <input type="hidden" class='numero' name="items[{{ $loop->index }}][item_number]" value="{{ $loop->index+1 }}">
                                        <input type="hidden" class="item_id" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}"> </td>
                                    <input type="hidden" class='codigo' name="items[{{ $loop->index }}][code]" value="{{ $item->code }}">
                                    <input type="hidden" class='nombre' name="items[{{ $loop->index }}][name]" value="{{ $item->name }}">
                                    <input type="hidden" class='tipo_producto' name="items[{{ $loop->index }}][product_type]" value="{{ $item->product_type }}">
                                    <input type="hidden" class='cantidad' name="items[{{ $loop->index }}][item_count]" value="{{ $item->item_count }}">
                                    <input type="hidden" class='unidad_medicion' name="items[{{ $loop->index }}][measure_unit]" value="{{ $item->measure_unit }}">
                                    <input type="hidden" class='precio_unitario' name="items[{{ $loop->index }}][unit_price]" value="{{ $item->unit_price }}">
                                    <input type="hidden" class='tipo_iva' name="items[{{ $loop->index }}][iva_type]" value="{{ $item->iva_type }}">
                                    <input type='hidden' class='porc_identificacion_plena' value='0'>
                                    <input type='hidden' class='discount_type' name='items[{{ $loop->index }}][discount_type]' value='{{ $item->discount_type }}'>
                                    <input type='hidden' class='discount' name='items[{{ $loop->index }}][discount]' value='{{ $item->discount }}'>
                                    <input class="subtotal" type="hidden" name="items[{{ $loop->index }}][subtotal]" value="{{ $item->subtotal }}">
                                    <input class="porc_iva" type="hidden" name="items[{{ $loop->index }}][iva_percentage]" value="{{ $item->iva_percentage }}">
                                    <input class="monto_iva" type="hidden" name="items[{{ $loop->index }}][iva_amount]" value="{{ $item->iva_amount }}">
                                    <input class="total" type="hidden" name="items[{{ $loop->index }}][total]" value="{{ $item->total }}">
                                    <input class="is_identificacion_especifica" type="hidden" name="items[{{ $loop->index }}][is_identificacion_especifica]" value="{{ $item->is_identificacion_especifica }}">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @include( 'Invoice.form-linea' )
                <div class="btn-holder hidden">

                    <button id="btn-submit" type="submit" class="btn btn-primary">Enviar factura electrónica</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('breadcrumb-buttons')
    <button id='btn-submit-fe' onclick="$('#btn-submit').click();" class="btn btn-primary">Enviar nota de credito</button>
@endsection
@section('footer-scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script>
        $(document).ready(function(){

            $('#tipo_producto').val(17).change();

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