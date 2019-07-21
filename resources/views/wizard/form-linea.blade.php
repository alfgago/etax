<div class="popup" id="linea-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('linea-popup');cancelarEdicion();"> <i class="nav-icon i-Close"></i>  </div>

    <div class="form-group col-md-12">
      <h3>
        Linea de sumatoria por tipo de IVA
      </h3>
    </div>
                
    <input type="hidden" class="form-control" id="lnum" value="">
    <input type="hidden" class="form-control" id="item_id" value="">
    
    <div class="form-group col-md-6 hidden">
      <label for="codigo">Código de producto</label>
      <input type="text" class="form-control" id="codigo" value="SUMATORIA" >
    </div>

    <div class="form-group col-md-6 hidden">
      <label for="nombre">Nombre / Descripción</label>
      <input type="text" class="form-control" id="nombre" value="2018" >
    </div>

    <div class="form-group col-md-12">
      <label for="tipo_producto">Categoría de producto</label>
      <select class="form-control select-search" id="tipo_producto" name="tipo_producto">
        @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $tipo )
          <option value="{{ $tipo['id'] }}" codigo="{{ $tipo['invoice_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" >{{ $tipo['name'] }}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-12">
      <label for="tipo_iva">Tipo de IVA</label>
      <select class="form-control" id="tipo_iva" >
        @foreach ( \App\CodigoIvaRepercutido::all() as $tipo )
          <option value="{{ $tipo['code'] }}" attr-iva="{{ $tipo['percentage'] }}" porcentaje="{{ $tipo['percentage'] }}" class="{{ @$tipo['hidden'] ? 'hidden' : '' }} {{ @$tipo['hideMasiva'] ? 'hidden' : '' }}">{{ $tipo['name'] }}</option>
        @endforeach
      </select>
    </div>
  
    <div class="form-group col-md-1 hidden">
      <label for="nombre_cliente">% IVA</label>
      <input type="text" class="form-control" id="porc_iva" placeholder="13" value="13" readonly>
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox hidden">
      <label for="p1">
        <span>¿Requiere ayuda adicional para elegir el tipo de IVA?</span>
        <input type="checkbox" class="form-control" id="p1" placeholder="" readonly="true" onchange="toggleAyudaTipoIVa();" >
      </label>
    </div>
    
    @include( 'Invoice.preguntas-ayuda' )
    
    <div class="form-group col-md-12 hidden" id="field_porc_identificacion_plena">
      <label for="porc_identificacion_plena">Porcentaje al que saldrá la venta</label>
      <select class="form-control" id="porc_identificacion_plena" >
        <option value="1" >1%</option>
        <option value="2" >2%</option>
        <option value="13" selected>13%</option>
        <option value="4" >4%</option>
      </select>
    </div>

    <div class="form-group col-md-3 hidden">
      <label for="unidad_medicion">Unidad de medición</label>
      <select class="form-control" id="unidad_medicion" value="" >
        @foreach ( \App\Variables::unidadesMedicion() as $unidad )
          <option value="{{ $unidad['codigo'] }}" >{{ $unidad['nombre'] }}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-3 hidden">
      <label for="cantidad">Cantidad</label>
      <input type="text" class="form-control" id="cantidad" value="1" >
    </div>

    <div class="form-group col-md-12">
      <label for="precio_unitario">Total en colones</label>
        <input class="form-control" id="precio_unitario" type="text" maxlength="20">
    </div>

    <div class="form-group col-md-4 hidden">
      <label for="item_iva">Monto IVA</label>
      <input type="text" class="form-control" id="item_iva_amount" placeholder="" >
    </div>

    <div class="form-group col-md-3 hidden">
      <label for="discount_type">Tipo de descuento</label>
      <select class="form-control" id="discount_type" value="" >
        <option value="01" >Porcentual</option>
        <option value="02" >Monto fijo</option>
      </select>
    </div>

    <div class="form-group col-md-3 hidden">
      <label for="discount">Descuento</label>
      <input type="text" class="form-control" id="discount" value="0" >
    </div>

    <div class="form-group col-md-4 hidden">
      <label for="nombre_proveedor">Subtotal</label>
      <input type="text" class="form-control" id="item_subtotal" placeholder="" readonly="true" >
    </div>

    <div class="form-group col-md-4 hidden">
      <label for="nombre_proveedor">Total</label>
      <input type="text" class="form-control" id="item_total" placeholder="" readonly="true" >
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox hidden">
      <label for="is_identificacion_especifica" class="hidden">
        <span>¿Asociado a compras con identificación específica?</span>
        <input type="checkbox" class="form-control" id="is_identificacion_especifica" placeholder="" readonly="true" >
      </label>
    </div>

    <div class="form-group col-md-12">
      <div class="botones-agregar">
        <div onclick="agregarEditarItem()" class="btn btn-dark m-1 ml-0">Confirmar linea</div>
        <div onclick="cerrarPopup('linea-popup');cancelarEdicion();" class="btn btn-danger m-1">Cancelar</div>
      </div>
      <div class="botones-editar">
        <div onclick="cerrarPopup('linea-popup');agregarEditarItem();" class="btn btn-dark m-1 ml-0">Confirmar edición</div>
        <div onclick="cerrarPopup('linea-popup');cancelarEdicion();" class="btn btn-danger m-1">Cancelar</div>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function(){
        $('#precio_unitario').on('keyup',function(){
            $(this).manageCommas();
        });
        $('#precio_unitario').on('focus',function(){
            $(this).santizeCommas();
        });
    });
    String.prototype.addComma = function() {
        return this.replace(/(.)(?=(.{3})+$)/g,"$1,").replace(',.', '.');
    }
    $.fn.manageCommas = function () {
        return this.each(function () {
            $(this).val($(this).val().replace(/(,|)/g,'').addComma());
        });
    }

    $.fn.santizeCommas = function() {
        return $(this).val($(this).val().replace(/(,| )/g,''));
    }
</script>

