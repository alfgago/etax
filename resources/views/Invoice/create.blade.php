@extends('layouts/app')

@section('title') 
  Registrar factura emitida
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        <form method="POST" action="/facturas-emitidas">

          @csrf
          
          <input type="hidden" id="current-index" value="0">

          <div class="form-row">
            <div class="form-group col-md-6 ">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <h3>
                    Información de cliente
                  </h3>
                </div>

                <div class="form-group col-md-4">
                  <label for="client_id_type">Tipo de identificación</label>
                  <select class="form-control" name="client_id_type" id="tipo_identificacion_cliente" required>
                    <option value="fisica" selected>Física</option>
                    <option value="juridica">Jurídica</option>
                    <option value="extranjero">Cédula extanjero</option>
                    <option value="dimex">DIMEX</option>
                    <option value="nite">NITE</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label for="client_id_temp">Identificación</label>
                  <input type="text" class="form-control" name="client_id_temp" id="identificacion_cliente" placeholder="" required>
                </div>

                <div class="form-group col-md-2">
                  <label for="btn_buscar_cliente">&nbsp;</label>
                  <div>
                    <input type="button" class="btn cur-p btn-dark" name="btn_buscar_cliente" id="btn_buscar_cliente" value="Buscar cliente">
                  </div>
                </div>

                <div class="form-group col-md-12">
                  <label for="client_name">Nombre</label>
                  <input type="text" class="form-control" name="client_name" id="nombre_cliente" placeholder="" required>
                </div>
                
                <div class="form-group col-md-4">
                  <label for="code">Código Int.</label>
                  <input type="text" class="form-control" name="code" id="codigo_cliente" placeholder="">
                </div>

                <div class="form-group col-md-4">
                  <label for="send_emails">Correo electrónico</label>
                  <input type="text" class="form-control" name="send_emails" id="correos_envio" placeholder="" required>
                </div>

                <div class="form-group col-md-4">
                  <label for="phone">Teléfono</label>
                  <input type="text" class="form-control" name="phone" id="telefono" placeholder="">
                </div>
                
                <div class="form-group col-md-12">
                  <label for="address">Dirección</label>
                  <input type="text" class="form-control" name="address" id="direccion" placeholder="">
                </div>

                <div class="form-group col-md-12">
                  <div class="form-check">
                   <label for="client_is_exempt" class="form-check-label">
                   <input class="form-check-input" id="cliente_exento" name="client_is_exempt" type="checkbox"> Cliente exento de IVA
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
                    <label for="generated_date">Fecha</label>
                    <div class="input-group">
                      <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
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
                      <input id="hora" class="form-control input-hora" name="hora" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('g:i A') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Clock"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class="input-group">
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
                      <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="icon-regular i-Calendar-4"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group col-md-6"></div>

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

                  <div class="form-group col-md-6">
                    <label for="other_reference">Referencia</label>
                    <input type="text" class="form-control" name="other_reference" id="referencia" value="" >
                  </div>

                  <div class="form-group col-md-6">
                    <label for="buy_order">Orden de compra</label>
                    <input type="text" class="form-control" name="buy_order" id="orden_compra" value="" >
                  </div>

                  <div class="form-group col-md-6">
                    <label for="currency">Moneda</label>
                    <select class="form-control" name="currency" id="moneda" required>
                      <option value="crc" selected>CRC</option>
                      <option value="crc">USD</option>
                    </select>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="currency_rate">Tipo de cambio</label>
                    <input type="text" class="form-control" name="currency_rate" id="tipo_cambio" value="1.00" required>
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
              <div class="item-factura-form form-row">

                <input type="hidden" class="form-control" id="lnum" value="">
                <input type="hidden" class="form-control" id="item_id" value="">
                
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
                    @foreach ( \App\ProductCategory::all() as $tipo )
                      <option value="{{ $tipo->id }}" codigo="{{ $tipo->invoice_iva_code }}" >{{ $tipo->name }}</option>
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
                  <input type="text" class="form-control" id="item_subtotal" placeholder="" readonly="true">
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_cliente">Porcentaje IVA</label>
                  <input type="text" class="form-control" id="porc_iva" placeholder="13" value="13" readonly>
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_cliente">Total item</label>
                  <input type="text" class="form-control" id="item_total" placeholder="" readonly="true">
                </div>

                <div class="form-group col-md-3">
                  <div class="botones-agregar">
                    <div onclick="agregarEditarItem();" class="btn btn-dark btn-sm m-1">Agregar linea</div>
                  </div>
                  <div class="botones-editar">
                    <div onclick="agregarEditarItem();" class="btn btn-dark btn-sm m-1">Confirmar edición</div>
                    <div onclick="cancelarEdicion();" class="btn btn-danger btn-sm m-1">Cancelar</div>
                  </div>
                </div>

              </div>
            </div>

            <div class="form-group col-md-12" id="tabla-items-factura" style="display: none;">
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
              <label for="iva_amount">Monto IVA </label>
              <input type="text" class="form-control" name="iva_amount" id="monto_iva" placeholder="" readonly="true" required>
            </div>

            <div class="form-group col-md-4">
              <label for="total">Total</label>
              <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true">
            </div>

            <div class="form-group col-md-12">
              <label for="description">Notas</label>
              <input type="text" class="form-control" name="description" id="notas" placeholder="">
            </div>

          </div>

          <button type="submit" class="btn btn-primary">Confirmar factura</button>

        </form>
      </div>
    </div>
  </div>
</div>

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
  $(document).ready(function() {
    $('#tipo_iva').val(103);
  });
</script>

@endsection