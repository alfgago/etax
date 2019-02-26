@extends('layouts/app')

@section('title') 
  Editar factura emitida
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        <form method="POST" action="/facturas-emitidas/{{ $facturaEmitida->id }}">
          @method('patch') 
          @csrf

          <input type="hidden" id="current-index" value="{{ count($facturaEmitida->lineas) }}">
          
          <div class="form-row">
            <div class="form-group col-md-6 ">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <h3>
                    Información de cliente
                  </h3>
                </div>

                <div class="form-group col-md-4">
                  <label for="tipo_identificacion_cliente">Tipo de identificación</label>
                  <select class="form-control" name="tipo_identificacion_cliente" id="tipo_identificacion_cliente" required>
                    <option value="fisica" {{ $facturaEmitida->tipo_identificacion_cliente == 'fisica' ? 'selected' : '' }} >Física</option>
                    <option value="juridica" {{ $facturaEmitida->tipo_identificacion_cliente == 'juridica' ? 'selected' : '' }}>Jurídica</option>
                    <option value="extranjero" {{ $facturaEmitida->tipo_identificacion_cliente == 'extranjero' ? 'selected' : '' }}>Cédula extanjero</option>
                    <option value="dimex" {{ $facturaEmitida->tipo_identificacion_cliente == 'dimex' ? 'selected' : '' }}>DIMEX</option>
                    <option value="nite" {{ $facturaEmitida->tipo_identificacion_cliente == 'nite' ? 'selected' : '' }}>NITE</option>
                    <option value="otro" {{ $facturaEmitida->tipo_identificacion_cliente == 'otro' ? 'selected' : '' }}>Otro</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label for="identificacion_cliente">Identificación</label>
                  <input type="text" class="form-control" name="identificacion_cliente" id="identificacion_cliente" placeholder="" required value="{{ $facturaEmitida->identificacion_cliente }}">
                </div>

                <div class="form-group col-md-2">
                  <label for="btn_buscar_cliente">&nbsp;</label>
                  <div>
                    <input type="button" class="btn cur-p btn-dark" name="btn_buscar_cliente" id="btn_buscar_cliente" value="Buscar cliente">
                  </div>
                </div>

                <div class="form-group col-md-12">
                  <label for="nombre_cliente">Nombre</label>
                  <input type="text" class="form-control" name="nombre_cliente" id="nombre_cliente" placeholder="" required value="{{ $facturaEmitida->nombre_cliente }}">
                </div>

                <div class="form-group col-md-4">
                  <label for="codigo_cliente">Código Int.</label>
                  <input type="text" class="form-control" name="codigo_cliente" id="codigo_cliente" placeholder=""  value="{{ $facturaEmitida->codigo_cliente }}">
                </div>

                <div class="form-group col-md-4">
                  <label for="correos_envio">Correo electrónico</label>
                  <input type="text" class="form-control" name="correos_envio" id="correos_envio" placeholder="" required value="{{ $facturaEmitida->correos_envio }}">
                </div>

                <div class="form-group col-md-4">
                  <label for="telefono">Teléfono</label>
                  <input type="text" class="form-control" name="telefono" id="telefono" placeholder="" value="{{ $facturaEmitida->telefono }}">
                </div>

                <div class="form-group col-md-12">
                  <label for="direccion">Dirección</label>
                  <input type="text" class="form-control" name="direccion" id="direccion" placeholder="" value="{{ $facturaEmitida->direccion }}">
                </div>

                <div class="form-group col-md-12">
                  <div class="form-check">
                    <label class="form-check-label">
                   <input class="form-check-input" id="cliente_exento" name="cliente_exento" type="checkbox"> Cliente exento de IVA
                 </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-md-5 offset-md-1">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <h3>
                    Datos generales
                  </h3>
                </div>

                <div class="form-group col-md-6">
                    <label for="fecha_generada">Fecha</label>
                    <div class="input-group">
                      <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="fecha_generada" required value="{{ $facturaEmitida->fechaGenerada()->format('d/m/Y') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Calendar-4"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="hora">Hora</label>
                    <div class="input-group">
                      <input id="hora" class="form-control input-hora" name="hora" required value="{{ $facturaEmitida->fechaGenerada()->format('g:i A') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Clock"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="fecha_vencimiento">Fecha de vencimiento</label>
                    <div class="input-group">
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="fecha_vencimiento" required value="{{ $facturaEmitida->fechaVencimiento()->format('d/m/Y') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Calendar-4"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                <div class="form-group col-md-6"></div>

                <div class="form-group col-md-6">
                  <label for="condicion_venta">Condición de venta</label>
                  <div class="input-group">
                    <select id="condicion_venta" name="condicion_venta" class="form-control" required value="{{ $facturaEmitida->condicion_venta }}">
                      <option value="01" {{ $facturaEmitida->medio_pago == '01' ? 'selected' : '' }}>Contado</option>
                      <option value="02" {{ $facturaEmitida->medio_pago == '02' ? 'selected' : '' }} >Crédito</option>
                      <option value="03" {{ $facturaEmitida->medio_pago == '03' ? 'selected' : '' }} >Consignación</option>
                      <option value="04" {{ $facturaEmitida->medio_pago == '04' ? 'selected' : '' }} >Apartado</option>
                      <option value="05" {{ $facturaEmitida->medio_pago == '05' ? 'selected' : '' }} >Arrendamiento con opción de compra</option>
                      <option value="06" {{ $facturaEmitida->medio_pago == '06' ? 'selected' : '' }} >Arrendamiento en función financiera</option>
                      <option value="99" {{ $facturaEmitida->medio_pago == '99' ? 'selected' : '' }} >Otros</option>
                    </select>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="medio_pago">Método de pago</label>
                  <div class="input-group">
                    <select id="medio_pago" name="medio_pago" class="form-control" required value="{{ $facturaEmitida->medio_pago }}"> 
                      <option value="01" {{ $facturaEmitida->medio_pago == '01' ? 'selected' : '' }} >Efectivo</option>
                      <option value="02" {{ $facturaEmitida->medio_pago == '02' ? 'selected' : '' }}>Tarjeta</option>
                      <option value="03" {{ $facturaEmitida->medio_pago == '03' ? 'selected' : '' }}>Cheque</option>
                      <option value="04" {{ $facturaEmitida->medio_pago == '04' ? 'selected' : '' }}>Transferencia-Depósito Bancario</option>
                      <option value="05" {{ $facturaEmitida->medio_pago == '05' ? 'selected' : '' }}>Recaudado por terceros</option>
                      <option value="99" {{ $facturaEmitida->medio_pago == '99' ? 'selected' : '' }}>Otros</option>
                    </select>
                  </div>
                </div>

                <div class="form-group col-md-6">
                  <label for="referencia">Referencia</label>
                  <input type="text" class="form-control" name="referencia" id="referencia"  value="{{ $facturaEmitida->referencia }}">
                </div>

                <div class="form-group col-md-6">
                  <label for="orden_compra">Orden de compra</label>
                  <input type="text" class="form-control" name="orden_compra" id="orden_compra"  value="{{ $facturaEmitida->orden_compra }}">
                </div>

                <div class="form-group col-md-6">
                  <label for="moneda">Moneda</label>
                  <select class="form-control" name="moneda" id="moneda" required value="{{ $facturaEmitida->moneda }}">
                    <option value="crc" selected>CRC</option>
                    <option value="crc">USD</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label for="tipo_cambio">Tipo de cambio</label>
                  <input type="text" class="form-control" name="tipo_cambio" id="tipo_cambio" required value="{{ $facturaEmitida->tipo_cambio }}">
                </div>

              </div>

            </div>
          </div>


          <div class="form-row">

            <div class="form-group col-md-12">
              <h3>
                Lineas de factura
              </h3>
            </div>

            <div class="form-group col-md-12">
              <div class="linea-factura-form form-row">
                
                <input type="hidden" class="form-control" id="lnum" value="">
                <input type="hidden" class="form-control" id="linea_id" value="">
                
                <div class="form-group col-md-2">
                  <label for="codigo">Código</label>
                  <input type="text" class="form-control" id="codigo" value="">
                </div>

                <div class="form-group col-md-4">
                  <label for="nombre">Nombre / Descripción</label>
                  <input type="text" class="form-control" id="nombre" value="">
                </div>

                <div class="form-group col-md-3">
                  <label for="tipo_producto">Tipo de producto</label>
                  <select class="form-control" id="tipo_producto">
                    @foreach ( \App\Variables::tiposRepercutidos() as $producto )
                      <option value="{{ $producto['nombre'] }}" codigo="{{ $producto['codigo_iva'] }}" >{{ $producto['nombre'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-3">
                  <label for="tipo_iva">Tipo de IVA</label>
                  <select class="form-control" id="tipo_iva">
                    @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
                      <option value="{{ $tipo['codigo'] }}" porcentaje="{{ $tipo['porcentaje'] }}">{{ $tipo['nombre'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-2">
                  <label for="unidad_medicion">Unidad de medición</label>
                  <select class="form-control" id="unidad_medicion" value="">
                    @foreach ( \App\Variables::unidadesMedicion() as $unidad )
                      <option value="{{ $unidad['codigo'] }}" >{{ $unidad['nombre'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-2">
                  <label for="precio_unitario">Cantidad</label>
                  <input type="text" class="form-control" id="cantidad" value="1">
                </div>

                <div class="form-group col-md-2">
                  <label for="precio_unitario">Precio unitario</label>
                  <input type="text" class="form-control" id="precio_unitario" value="">
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_cliente">Subtotal</label>
                  <input type="text" class="form-control" id="linea_subtotal" placeholder="" readonly="true">
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_cliente">Porcentaje IVA</label>
                  <input type="text" class="form-control" id="porc_iva" placeholder="13" value="13" readonly>
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_cliente">Total línea</label>
                  <input type="text" class="form-control" id="linea_total" placeholder="" readonly="true">
                </div>

                <div class="form-group col-md-3">
                  <div class="botones-agregar">
                    <div onclick="agregarEditarLinea();" class="btn btn-dark btn-sm m-1">Agregar línea</div>
                  </div>
                  <div class="botones-editar">
                    <div onclick="agregarEditarLinea();" class="btn btn-dark btn-sm m-1">Confirmar edición</div>
                    <div onclick="cancelarEdicion();" class="btn btn-danger btn-sm m-1">Cancelar</div>
                  </div>
                </div>

              </div>
            </div>

            <div class="form-group col-md-12" id="tabla-lineas-factura" style="display: block;">
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo producto</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Precio unitario</th>
                    <th>Tipo IVA</th>
                    <th>Subtotal</th>
                    <th>Porc. IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ( $facturaEmitida->lineas as $linea )
                  <tr class="linea-tabla linea-index-{{ $loop->index }}" index="{{ $loop->index }}" attr-num="{{ $loop->index }}" id="linea-tabla-{{ $loop->index }}">
                    <td><span class="numero-fila">{{ $loop->index+1 }}</span>
                      <input type="hidden" class='numero' name="lineas[{{ $loop->index }}][numero]" value="{{ $loop->index+1 }}">
                      <input type="hidden" class="linea_id" name="lineas[{{ $loop->index }}][id]" value="{{ $linea->id }}"> </td>
                    <td>{{ $linea->codigo }}
                      <input type="hidden" class='codigo' name="lineas[{{ $loop->index }}][codigo]" value="{{ $linea->codigo }}">
                    </td>
                    <td>{{ $linea->nombre }}
                      <input type="hidden" class='nombre' name="lineas[{{ $loop->index }}][nombre]" value="{{ $linea->nombre }}">
                    </td>
                    <td>{{ $linea->tipo_producto }}
                      <input type="hidden" class='tipo_producto' name="lineas[{{ $loop->index }}][tipo_producto]" value="{{ $linea->tipo_producto }}">
                    </td>
                    <td>{{ $linea->cantidad }}
                      <input type="hidden" class='cantidad' name="lineas[{{ $loop->index }}][cantidad]" value="{{ $linea->cantidad }}">
                    </td>
                    <td>{{ \App\Variables::getUnidadMedicionName($linea->unidad_medicion) }}
                      <input type="hidden" class='unidad_medicion' name="lineas[{{ $loop->index }}][unidad_medicion]" value="{{ $linea->unidad_medicion }}">
                    </td>
                    <td>{{ $linea->precio_unitario }}
                      <input type="hidden" class='precio_unitario' name="lineas[{{ $loop->index }}][precio_unitario]" value="{{ $linea->precio_unitario }}">
                    </td>
                    <td>{{ \App\Variables::getTipoRepercutidoIVAName($linea->tipo_iva) }}
                      <input type="hidden" class='tipo_iva' name="lineas[{{ $loop->index }}][tipo_iva]" value="{{ $linea->tipo_iva }}">
                    </td>
                    <td>{{ $linea->subtotal }}
                      <input class="subtotal" type="hidden" name="lineas[{{ $loop->index }}][subtotal]" value="{{ $linea->subtotal }}">
                    </td>
                    <td>{{ $linea->porc_iva }}
                      <input class="porc_iva" type="hidden" name="lineas[{{ $loop->index }}][porc_iva]" value="{{ $linea->porc_iva }}">
                    </td>
                    <td>
                      {{ $linea->total }}
                      <input class="total" type="hidden" name="lineas[{{ $loop->index }}][total]" value="{{ $linea->total }}">
                    </td>
                    <td class='acciones'>
                      <span title='Editar linea' class='btn-editar-linea text-success mr-2' onclick='cargarFormLinea({{ $loop->index }});'><i class='nav-icon i-Pen-2'></i> </span> 
                      <span title='Eliminar linea' class='btn-eliminar-linea text-danger mr-2' onclick='eliminarLinea({{ $loop->index }});' ><i class='nav-icon i-Close-Window'></i> </span> 
                    </td>
                  </tr>
                  @endforeach
                </tbody>

              </table>
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
              <label for="subtotal">Monto IVA </label>
              <input type="text" class="form-control" name="monto_iva" id="monto_iva" placeholder="" readonly="true" required>
            </div>

            <div class="form-group col-md-4">
              <label for="total">Total</label>
              <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true">
            </div>

            <div class="form-group col-md-12">
              <label for="notas">Notas</label>
              <input type="text" class="form-control" name="notas" id="notas" placeholder="" value="{{ $facturaEmitida->notas }}">
            </div>

          </div>

          <button type="submit" class="btn btn-primary">Editar factura</button>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection @section('header-scripts')
<link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.css">
<link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.date.css">
<link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.time.css"> @endsection @section('footer-scripts')
<script src="/assets/js/vendor/pickadate/picker.js"></script>
<script src="/assets/js/vendor/pickadate/picker.date.js"></script>
<script src="/assets/js/vendor/pickadate/picker.time.js"></script>
<script src="/assets/js/form-facturas.js"></script>
<script>
  $(document).ready(function() {
    $('#tipo_iva').val(103);

    var subtotal = 0;
    var monto_iva = 0;
    var total = 0;
    $('.linea-tabla').each(function() {
      var s = parseFloat($(this).find('.subtotal').val());
      var m = parseFloat($(this).find('.porc_iva').val()) / 100;
      var t = parseFloat($(this).find('.total').val());
      subtotal += s;
      monto_iva += s * m;
      total += t;
    });

    $('#subtotal').val(subtotal);
    $('#monto_iva').val(monto_iva);
    $('#total').val(total);

  });
</script>

@endsection