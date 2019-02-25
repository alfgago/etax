@extends('layouts/app')

@section('title') 
  Crear factura recibida
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
                          
        <form method="POST" action="/facturas-recibidas">

          @csrf
          
          <input type="hidden" id="current-index" value="0">

          <div class="form-row">
            <div class="form-group col-md-6 ">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <h3>
                    Información de proveedor
                  </h3>
                </div>

                <div class="form-group col-md-4">
                  <label for="tipo_identificacion_proveedor">Tipo de identificación</label>
                  <select class="form-control" name="tipo_identificacion_proveedor" id="tipo_identificacion_proveedor" >
                    <option value="fisica" selected>Física</option>
                    <option value="juridica">Jurídica</option>
                    <option value="extranjero">Cédula extanjero</option>
                    <option value="dimex">DIMEX</option>
                    <option value="nite">NITE</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label for="identificacion_proveedor">Identificación</label>
                  <input type="text" class="form-control" name="identificacion_proveedor" id="identificacion_proveedor" placeholder="" >
                </div>

                <div class="form-group col-md-2">
                  <label for="btn_buscar_proveedor">&nbsp;</label>
                  <div>
                    <input type="button" class="btn cur-p btn-dark" name="btn_buscar_proveedor" id="btn_buscar_proveedor" value="Buscar proveedor">
                  </div>
                </div>

                <div class="form-group col-md-12">
                  <label for="proveedor">Nombre</label>
                  <input type="text" class="form-control" name="proveedor" id="proveedor" placeholder="" required>
                </div>
                
                <div class="form-group col-md-4">
                  <label for="codigo_proveedor">Código Int.</label>
                  <input type="text" class="form-control" name="codigo_proveedor" id="codigo_proveedor" placeholder="">
                </div>

                <div class="form-group col-md-4">
                  <label for="correos_envio">Correo electrónico</label>
                  <input type="text" class="form-control" name="correos_envio" id="correos_envio" placeholder="" >
                </div>

                <div class="form-group col-md-4">
                  <label for="telefono">Teléfono</label>
                  <input type="text" class="form-control" name="telefono" id="telefono" placeholder="">
                </div>
                
                <div class="form-group col-md-12">
                  <label for="direccion">Dirección</label>
                  <input type="text" class="form-control" name="direccion" id="direccion" placeholder="">
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
                    <label for="fecha_recibida">Fecha</label>
                    <div class="input-group">
                      <input id="fecha_recibida" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="fecha_recibida" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
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
                    <label for="fecha_vencimiento">Fecha de vencimiento</label>
                    <div class="input-group">
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="fecha_vencimiento" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
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
                      <select id="condicion_venta" name="condicion_venta" class="form-control" required>
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
                    <label for="medio_pago">Método de pago</label>
                    <div class="input-group">
                      <select id="medio_pago" name="medio_pago" class="form-control" required>
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
                    <label for="referencia">Referencia</label>
                    <input type="text" class="form-control" name="referencia" id="referencia" value="" >
                  </div>

                  <div class="form-group col-md-6">
                    <label for="orden_compra">Orden de compra</label>
                    <input type="text" class="form-control" name="orden_compra" id="orden_compra" value="" >
                  </div>

                  <div class="form-group col-md-6">
                    <label for="moneda">Moneda</label>
                    <select class="form-control" name="moneda" id="moneda" required>
                      <option value="crc" selected>CRC</option>
                      <option value="crc">USD</option>
                    </select>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="tipo_cambio">Tipo de cambio</label>
                    <input type="text" class="form-control" name="tipo_cambio" id="tipo_cambio" value="1.00" required>
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
                  <input type="text" class="form-control" id="codigo" value="" >
                </div>

                <div class="form-group col-md-4">
                  <label for="nombre">Nombre / Descripción</label>
                  <input type="text" class="form-control" id="nombre" value="" >
                </div>

                <div class="form-group col-md-3">
                  <label for="tipo_producto">Tipo de producto</label>
                  <select class="form-control" id="tipo_producto" >
                    @foreach ( \App\Variables::tiposSoportados() as $producto )
                      <option value="{{ $producto['nombre'] }}" codigo="{{ $producto['codigo_iva'] }}" >{{ $producto['nombre'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-3">
                  <label for="tipo_iva">Tipo de IVA</label>
                  <select class="form-control" id="tipo_iva" >
                    @foreach ( \App\Variables::tiposIVASoportados() as $tipo )
                      <option value="{{ $tipo['codigo'] }}" porcentaje="{{ $tipo['porcentaje'] }}">{{ $tipo['nombre'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-2">
                  <label for="unidad_medicion">Unidad de medición</label>
                  <select class="form-control" id="unidad_medicion" value="" >
                    @foreach ( \App\Variables::unidadesMedicion() as $unidad )
                      <option value="{{ $unidad['codigo'] }}" >{{ $unidad['nombre'] }}</option>
                    @endforeach
                  </select>
                </div>
                
                <div class="form-group col-md-2">
                  <label for="precio_unitario">Cantidad</label>
                  <input type="text" class="form-control" id="cantidad" value="1" >
                </div>

                <div class="form-group col-md-2">
                  <label for="precio_unitario">Precio unitario</label>
                  <input type="text" class="form-control" id="precio_unitario" value="" >
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_proveedor">Subtotal</label>
                  <input type="text" class="form-control" id="linea_subtotal" placeholder="" readonly="true" >
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_proveedor">Porcentaje IVA</label>
                  <input type="text" class="form-control" id="porc_iva" placeholder="13" value="13" readonly>
                </div>

                <div class="form-group col-md-2">
                  <label for="nombre_proveedor">Total línea</label>
                  <input type="text" class="form-control" id="linea_total" placeholder="" readonly="true" >
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
            
            <div class="form-group col-md-12" id="tabla-lineas-factura" style="display: none;">
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
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
              <label for="subtotal">Monto IVA </label>
              <input type="text" class="form-control" name="monto_iva" id="monto_iva" placeholder="" readonly="true" required>
            </div>

            <div class="form-group col-md-4">
              <label for="total">Total</label>
              <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" >
            </div>

            <div class="form-group col-md-12">
              <label for="notas">Notas</label>
              <input type="text" class="form-control" name="notas" id="notas" placeholder="">
            </div>

          </div>

          <button type="submit" class="btn btn-primary">Confirmar factura</button>

          @if ($errors->any())
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          @endif

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
$(document).ready(function(){
  $('#tipo_iva').val(3);
});
</script>


@endsection