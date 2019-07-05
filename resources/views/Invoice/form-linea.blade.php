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
        
            <label for="codigo">Código de producto</label>
            <div class="form-row">
                <div class="col-md-8">
                    <input type="text" class="form-control" id="codigo" name="code">
                </div>
                <div class="col-md-3 pull-left-1" style="margin-left: -1em !important;">
                    <div class="btn btn-agregar btn-agregar-cliente" onclick="buscarProducto();">Buscar</div>
                </div>
            </div>
        
    </div>
    <div class="form-group col-md-6">
        <label for="nombre">Nombre / Descripción</label>
        <input type="text" class="form-control" id="nombre" value="" name="description">
    </div>
    <?php

        if( $document_type == "08"){
    ?>
        <div class="form-group col-md-4">
            <label for="nombre">Partida Arancelaria</label>
            <input type="text" class="form-control" id="tariff_heading" value="" name="tariff_heading">
        </div>
    <?php
        $class = 'form-group col-md-8';
      }else{
          $class = 'form-group col-md-12';
      } ?>

    <div class="<?php echo $class ?>">
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
          <option value="{{ $tipo['codigo'] }}" porcentaje="{{ $tipo['porcentaje'] }}" class="{{ @$tipo['hide'] ? 'hidden' : '' }}" >{{ $tipo['nombre'] }}</option>
        @endforeach
      </select>
    </div>
  
    <div class="form-group col-md-1">
      <label for="porc_iva">% IVA</label>
      <input type="number" min="0" class="form-control pr-0" id="porc_iva" placeholder="13" value="13" readonly>
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox">
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

    <div class="form-group col-md-3">
      <label for="unidad_medicion">Unidad de medición</label>
       
      <select class="form-control" id="unidad_medicion" value="" >
        @foreach ($units as $unit )
          <option value="{{ $unit['code'] }}" >{{ $unit['name'] }}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-3">
      <label for="cantidad">Cantidad</label>
      <input type="number" min="1" class="form-control" id="cantidad" value="1"  >
    </div>

    <div class="form-group col-md-3">
      <label for="precio_unitario">Precio unitario</label>
      <input type="number" min="0" class="form-control" id="precio_unitario" value="" number >
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
      <label for="item_subtotal">Subtotal</label>
      <input type="number" min="0" class="form-control" id="item_subtotal" placeholder="" readonly="true" >
    </div>

    <div class="form-group col-md-3">
        <label for="item_total" id="etiqTotal">Monto Total Linea</label>
      <input type="text" class="form-control" id="item_total" placeholder="" readonly="true" >
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox hidden">
      <label for="is_identificacion_especifica">
        <span>¿Asociado a compras con identificación específica?</span>
        <input type="checkbox" class="form-control" id="is_identificacion_especifica" placeholder="" readonly="true" >
      </label>
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox">
        <label for="checkExoneracion">
            <span>Incluir exoneraci&oacute;n</span>
            <input type="checkbox" class="form-control" id="checkExoneracion" onchange="mostrarCamposExoneracion();">
        </label>
    </div>
    
    <div class="exoneracion-cont col-md-12" style="display:none;">
        <div class="form-row">
          
            <div class="form-group col-md-6">
                <label for="typeDocument">Tipo de Documento de Exoneraci&oacute;n</label>
                <select class="form-control" id="typeDocument" name="typeDocument" value="" >
                    <option value="" selected>-- Seleccione --</option>
                    <option value="01">Compras Autorizadas</option>
                    <option value="02">Ventas exentas a diplomáticos</option>
                    <option value="03">Autorizado por Ley Especial</option>
                    <option value="04">Exenciones Dirección General de Hacienda</option>
                    <option value="05">Transitorio V</option>
                    <option value="06">Transitorio IX</option>
                    <option value="07">Transitorio XVII</option>
                    <option value="99">Otros</option>
                </select>
            </div>
            
            <div class="form-group col-md-6">
                <label for="numeroDocumento">N&uacute;mero Documento de Exoneraci&oacute;n *</label>
                <input type="text" class="form-control" id="numeroDocumento" placeholder="">
            </div>
            <div class="form-group col-md-6">
                <label for="nombreInstitucion">Nombre de Instituci&oacute;n *</label>
                <input type="text" class="form-control" id="nombreInstitucion" placeholder="">
            </div>

            <div class="form-group col-md-6">
                <label for="exoneration_date">Fecha</label>
                <div class='input-group date inputs-fecha'>
                    <input id="exoneration_date" class="form-control input-fecha" placeholder="dd/mm/yyyy"  value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
                    <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                </div>
            </div>

            <div class="form-group col-md-1">
                <label for="porcentajeExoneracion">% *</label>
                <input type="text" class="form-control" id="porcentajeExoneracion" placeholder="%" onkeyup="calcularMontoExoneracion();">
            </div>
            <div class="form-group col-md-3">
                <label for="montoExoneracion">Monto Exonerado *</label>
                <input type="text" class="form-control" id="montoExoneracion"  readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="impuestoNeto">Impuesto Neto </label>
                <input type="text" class="form-control" id="impuestoNeto"  readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="montoTotalLinea">Monto Total Linea </label>
                <input type="text" class="form-control" id="montoTotalLinea"  readonly>
            </div>
        </div>
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


