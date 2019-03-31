<div class="popup" id="linea-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('linea-popup');cancelarEdicion();"> <i class="nav-icon i-Close"></i>  </div>

    <div class="form-group col-md-12">
      <h3>
        Linea de factura
      </h3>
    </div>
                
    <input type="hidden" class="form-control" id="lnum" value="">
    <input type="hidden" class="form-control" id="item_id" value="">
    
    <div class="form-group col-md-6">
      <label for="codigo">Código de producto</label>
      <input type="text" class="form-control" id="codigo" value="" >
    </div>

    <div class="form-group col-md-6">
      <label for="nombre">Nombre / Descripción</label>
      <input type="text" class="form-control" id="nombre" value="" >
    </div>

    <div class="form-group col-md-12">
      <label for="tipo_producto">Tipo de producto</label>
      <select class="form-control" id="tipo_producto" >
        @foreach ( \App\ProductCategory::all() as $tipo )
          <option value="{{ $tipo->id }}" codigo="{{ $tipo->invoice_iva_code }}" >{{ $tipo->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-group col-md-11">
      <label for="tipo_iva">Tipo de IVA</label>
      <select class="form-control" id="tipo_iva" >
        @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
          <option value="{{ $tipo['codigo'] }}" porcentaje="{{ $tipo['porcentaje'] }}">{{ $tipo['nombre'] }}</option>
        @endforeach
      </select>
    </div>
  
    <div class="form-group col-md-1">
      <label for="nombre_cliente">% IVA</label>
      <input type="text" class="form-control" id="porc_iva" placeholder="13" value="13" readonly>
    </div>

    <div class="form-group col-md-3">
      <label for="unidad_medicion">Unidad de medición</label>
      <select class="form-control" id="unidad_medicion" value="" >
        @foreach ( \App\Variables::unidadesMedicion() as $unidad )
          <option value="{{ $unidad['codigo'] }}" >{{ $unidad['nombre'] }}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-3">
      <label for="precio_unitario">Cantidad</label>
      <input type="text" class="form-control" id="cantidad" value="1" >
    </div>

    <div class="form-group col-md-3">
      <label for="precio_unitario">Precio unitario</label>
      <input type="text" class="form-control" id="precio_unitario" value="" >
    </div>

    <div class="form-group col-md-3">
      <label for="item_iva">Monto IVA</label>
      <input type="text" class="form-control" id="item_iva_amount" placeholder="" >
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
      <input type="text" class="form-control" id="discount" value="0" >
    </div>

    <div class="form-group col-md-3">
      <label for="nombre_proveedor">Subtotal</label>
      <input type="text" class="form-control" id="item_subtotal" placeholder="" readonly="true" >
    </div>

    <div class="form-group col-md-3">
      <label for="nombre_proveedor">Total item</label>
      <input type="text" class="form-control" id="item_total" placeholder="" readonly="true" >
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