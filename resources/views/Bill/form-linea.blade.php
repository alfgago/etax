<div class="popup" id="linea-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('linea-popup');cancelarEdicion();"> <i class="fa fa-times" aria-hidden="true"></i> </div>

    <div class="form-group col-md-12">
      <h3>
        Linea de factura
      </h3>
    </div>
                
    <input type="hidden" class="form-control" id="lnum" value="">
    <input type="hidden" class="form-control" id="item_id" value="">
    
    <div class="form-group col-md-6">
      <label for="codigo">Código</label>
      <input type="text" class="form-control" id="codigo" value="" maxlength="20">
    </div>

    <div class="form-group col-md-6">
      <label for="nombre">Nombre / Descripción</label>
      <input type="text" class="form-control" id="nombre" value="" maxlength="200">
    </div>
    
    <div class="form-group col-md-12">
      <label for="tipo_producto">Tipo de producto</label>
      <select class="form-control select-search" id="tipo_producto" >
        @foreach ( \App\ProductCategory::whereNotNull('bill_iva_code')->get() as $tipo )
          <option value="{{ $tipo['id'] }}" codigo="{{ $tipo['bill_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" >{{ $tipo['name'] }}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-11">
      <label for="tipo_iva">Tipo de IVA</label>
      <select class="form-control" id="tipo_iva" >
        @foreach ( \App\CodigoIvaSoportado::all() as $tipo )
          <option value="{{ $tipo['code'] }}" is_identificacion_plena="{{ $tipo['is_identificacion_plena'] }}" porcentaje="{{ $tipo['percentage'] }}" class="{{ @$tipo['hidden'] ? 'hidden' : '' }}">{{ $tipo['name'] }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-group col-md-1">
      <label for="nombre_cliente">% IVA</label>
      <input type="number" min="0" class="form-control pr-0" id="porc_iva" placeholder="13" value="13" readonly>
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox hidden">
      <label for="p1">
        <span>¿Requiere ayuda adicional para elegir el tipo de IVA?</span>
        <input type="checkbox" class="form-control" id="p1" placeholder="" readonly="true" onchange="toggleAyudaTipoIVa();" >
      </label>
    </div>
    
    @include( 'Bill.preguntas-ayuda' )
    
    <div class="form-group col-md-12 hidden" id="field_porc_identificacion_plena">
      <label for="porc_identificacion_plena">Porcentaje al que saldrá la venta</label>
      <select class="form-control" id="porc_identificacion_plena" >
        <option value="1" >1%</option>
        <option value="2" >2%</option>
        <option value="13" selected>13%</option>
        <option value="4" >4%</option>
        <option value="5" >0% con derecho a crédito</option>
      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="unidad_medicion">Unidad de medición</label>
      <select class="form-control" id="unidad_medicion" value="" >
        @foreach ($units as $unit )
          <option value="{{ $unit['code'] }}" >{{ $unit['name'] }}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-3">
      <label for="precio_unitario">Cantidad</label>
      <input type="number" min="1" class="form-control" id="cantidad" value="1" >
    </div>

    <div class="form-group col-md-3">
      <label for="precio_unitario">Precio unitario</label>
      <input type="number" min="0" class="form-control" id="precio_unitario" value="" >
    </div>

    <div class="form-group col-md-3">
      <label for="item_iva">Monto IVA</label>
      <input type="number" min="0" class="form-control" id="item_iva_amount" placeholder="" >
    </div>

    <div class="form-group col-md-3">
      <label for="discount_type">Tipo de descuento</label>
      <select class="form-control" id="discount_type" value="" >
        <option value="01" >Porcentual</option>
        <option value="02" >Monto fijo</option>
      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="discount">Descuento</label>
      <input type="number" min="0" max="100" class="form-control" id="discount" value="0" >
    </div>

    <div class="form-group col-md-3">
      <label for="nombre_proveedor">Subtotal</label>
      <input type="text" class="form-control" id="item_subtotal" placeholder="" readonly="true" >
    </div>

    <div class="form-group col-md-3">
      <label for="nombre_proveedor">Total item</label>
      <input type="text" class="form-control" id="item_total" placeholder="" readonly="true" >
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox hidden">
      <label for="is_identificacion_especifica">
        <span>¿Gastos corresponden a compras con identificación específica?</span>
        <input type="checkbox" class="form-control" id="is_identificacion_especifica" placeholder="" readonly="true" >
      </label>
    </div>

    <div class="form-group col-md-12">
      <div class="botones-agregar">
        <div onclick="agregarEditarItem();" class="btn btn-dark m-1 ml-0">Confirmar linea</div>
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
    $(function () {
        $("#cantidad").keydown(function () {
            // Save old value.
            if (!$(this).val() || parseInt($(this).val()) >= 0)
                $(this).data("old", $(this).val());
        });
        $("#cantidad").keyup(function () {
            // Check correct, else revert back to old value.
            if (!$(this).val() ||  parseInt($(this).val()) >= 0)
                ;
            else
                $(this).val($(this).data("old"));
        });
        $("#precio_unitario").keydown(function () {
            // Save old value.
            if (!$(this).val() || parseInt($(this).val()) >= 0)
                $(this).data("old1", $(this).val());
        });
        $("#precio_unitario").keyup(function () {
            // Check correct, else revert back to old value.
            if (!$(this).val() ||  parseInt($(this).val()) >= 0)
                ;
            else
                $(this).val($(this).data("old1"));
        });
        $('#item_iva_amount').change(function () {
            if (!$(this).val() || parseInt($(this).val()) >= 0)
                $(this).data("old2", $(this).val());
            else if (!$(this).val() ||  parseInt($(this).val()) >= 0)
                ;
            else
                $(this).val($(this).data("old2"));
        })
    });
</script>
