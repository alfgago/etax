<div class="popup" id="linea-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('linea-popup');cancelarEdicion();"> <i class="fa fa-times" aria-hidden="true"></i> </div>

    <div class="form-group col-md-12">
      <h3>
        Línea de factura
      </h3>
    </div>
                
    <input type="hidden" class="form-control" id="lnum" value="">
    <input type="hidden" class="form-control" id="item_id" value="">
    
    <div class="form-group col-md-6">
        
            <div class="form-row" id="nuevoCodigo">
                <div class="col-md" id="codigo-select-div">
                	<label for="select">Seleccionar producto</label>
                	<!--input type="text" class="form-control" id="codigo" name="code" maxlength="13"-->
                    <select class="form-control select-search" id="codigo-select" name="code-select" onchange="buscarProducto();">
                    	<option value="seleccionar">-- Seleccione --</option>
                      <option value="nuevoProducto">Nuevo Producto</option>
                    	<?php $__currentLoopData = \App\Product::where('company_id', $company->id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    			              <option value="<?php echo e($tipo->code); ?>"><?php echo e($tipo->code); ?> - <?php echo e($tipo->name); ?></option>
    			            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md pull-right-1"  id="codigo-div" style="display: none;">
                	<label for="codigo-input">Codigo</label>
                	<input type="text" class="form-control" id="codigo" value="" name="code" maxlength="13">
                    <!--div class="btn btn-agregar btn-agregar-cliente" onclick="buscarProducto();">Nuevo</div-->
                </div>
            </div>
        
    </div>
    <div class="form-group col-md-6">
        <label for="nombre">Nombre / Descripción</label>
        <input type="text" class="form-control" id="nombre" value="" name="description" maxlength="200">
    </div>

    <?php if( @$document_type == '09' ): ?>
      <div class="form-group col-md-4">
          <label for="nombre">Partida Arancelaria</label>
          <input type="text" class="form-control" id="tariff_heading" value="" maxlength="12">
      </div>
    <?php endif; ?>

    <?php if( @$document_type == '08' ): ?>
      <div class="form-group col-md-12">
        <label for="tipo_iva">Tipo de IVA</label>
        <select class="form-control select-search" id="tipo_iva">
          <?php
            $preselectos = array();
            foreach($company->soportados as $soportado){
              $preselectos[] = $soportado->id;
            }
          ?>
          <?php if(@$company->soportados[0]->id): ?>
            <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden' : ''); ?>"  is_identificacion_plena="<?php echo e($tipo['is_identificacion_plena']); ?>"><?php echo e($tipo['name']); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <option class="mostrarTodos" value="1">Mostrar Todos</option>
          <?php else: ?>
            <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option is_identificacion_plena="<?php echo e($tipo['is_identificacion_plena']); ?>" value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"  ><?php echo e($tipo['name']); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
        </select>
      </div>
      <div class="form-group col-md-11">
        <label for="tipo_producto">Categoría de declaración</label>
        <select class="form-control" id="tipo_producto" >

          <?php $__currentLoopData = \App\ProductCategory::whereNotNull('bill_iva_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($tipo['id']); ?>" codigo="<?php echo e($tipo['bill_iva_code']); ?>" posibles="<?php echo e($tipo['open_codes']); ?>" ><?php echo e($tipo['name']); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </select>
      </div>
    <?php else: ?>
      <div class="form-group col-md-12">
        <label for="tipo_iva">Tipo de IVA</label>
        <select class="form-control select-search" id="tipo_iva">
          <?php
            $preselectos = array();
            foreach($company->repercutidos as $repercutido){
              $preselectos[] = $repercutido->id;
            }
          ?>
          <?php if(@$company->repercutidos[0]->id): ?>
            <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden' : ''); ?>"  ><?php echo e($tipo['name']); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <option class="mostrarTodos" value="1">Mostrar Todos</option>
          <?php else: ?>
            <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <?php if(@$document_type == '09'): ?>
                <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e($tipo['code'] !== 'B150' ? 'hidden' : ''); ?>"><?php echo e($tipo['name']); ?></option>
             <?php else: ?>
                <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"><?php echo e($tipo['name']); ?></option>
             <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
          
            
        </select>
      </div>
      
      <div class="form-group col-md-11">
        <label for="tipo_producto">Categoría de declaración</label>
        <select class="form-control" id="tipo_producto" >
          <?php $__currentLoopData = \App\ProductCategory::whereNotNull('invoice_iva_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($tipo['id']); ?>" codigo="<?php echo e($tipo['invoice_iva_code']); ?>" posibles="<?php echo e($tipo['open_codes']); ?>" ><?php echo e($tipo['name']); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    <?php endif; ?>
  
    <div class="form-group col-md-1">
      <label for="porc_iva">% IVA</label>
      <input type="number" min="0" class="form-control pr-0" id="porc_iva" placeholder="13" value="13" readonly>
      <input type="text"  class="hidden" id="exoneradalinea"  value="0" >
    </div>
    
    <div class="form-group col-md-12 inline-form inline-checkbox hidden">
      <label for="p1">
        <span>¿Requiere ayuda adicional para elegir el tipo de IVA?</span>
        <input type="checkbox" class="form-control" id="p1" placeholder="" readonly="true" onchange="toggleAyudaTipoIVa();" >
      </label>
    </div>
    
    <?php echo $__env->make( 'Invoice.preguntas-ayuda' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
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
        <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($unit['code']); ?>" ><?php echo e($unit['name']); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    
    <div class="form-group col-md-3">
      <label for="cantidad">Cantidad</label>
      <input type="number" min="1" class="form-control to3" id="cantidad" value="1"  >
    </div>

    <div class="form-group col-md-3">
      <label for="precio_unitario">Precio unitario</label>
      <input type="number" min="0" class="form-control to5" id="precio_unitario" value="0" number >
    </div>

    <div class="form-group col-md-3">
      <label for="item_iva">Monto IVA</label>
      <input type="number" min="0" class="form-control to5" id="item_iva_amount" placeholder="" >
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
      <input type="text" min="0" class="form-control to5" id="discount" placeholder="0" >
    </div>

    <div class="form-group col-md-3">
      <label for="item_subtotal">Subtotal</label>
      <input type="text" min="0" class="form-control to5" id="item_subtotal" placeholder="" readonly="true" >
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
    
    <div class="form-group col-md-12 inline-form inline-checkbox hidden <?php echo e(@$document_type == '08' || @$document_type == '09' ? 'hidden' : ''); ?>">
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
                <input type="text" class="form-control" id="numeroDocumento" placeholder="" maxlength="40">
            </div>
            <div class="form-group col-md-6">
                <label for="nombreInstitucion">Nombre de Instituci&oacute;n *</label>
                <input type="text" class="form-control" id="nombreInstitucion" placeholder="" maxlength="160">
            </div>

            <div class="form-group col-md-6">
                <label for="exoneration_date">Fecha</label>
                <div class='input-group date inputs-fecha'>
                    <input id="exoneration_date" class="form-control input-fecha" placeholder="dd/mm/yyyy" value="<?php echo e(\Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y')); ?>">
                    <span class="input-group-addon">
                      <i class="icon-regular i-Calendar-4"></i>
                    </span>
                </div>
            </div>

            <div class="form-group col-md-2">
                <label for="porcentajeExoneracion">% *</label>
                <input type="number" class="form-control" max="100" min="0" maxlength="3" id="porcentajeExoneracion" value="100" onchange="calcularMontoExoneracion();">
            </div>
            <div class="form-group col-md-3">
                <label for="montoExoneracion">Monto Exonerado *</label>
                <input type="text" class="form-control" id="montoExoneracion" readonly>
            </div>
            <div class="form-group col-md-3">
                <label for="impuestoNeto">Impuesto Neto </label>
                <input type="text" class="form-control" id="impuestoNeto" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="montoTotalLinea">Monto Total Linea </label>
                <input type="text" class="form-control" id="montoTotalLinea" readonly>
            </div>
        </div>
    </div>
	<div class="col-md-12" id="checkbox-div" style="display: none;">
    	<div class="form-group col-md-6 checkbox-div">
    		<input type="checkbox" id="form-checkbox" name="guardar"><label style="position:absolute">Guardar en catalogo</label>
    	</div>
    </div>    
<div class="form-group col-md-12">
      <div class="botones-agregar">
        <div onclick="agregarEditarItem();" class="btn btn-dark m-1 ml-0">Confirmar línea</div>
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
    $( document ).ready(function() {
      $('#tipo_iva').on('select2:selecting', function(e){
        var selectBox = document.getElementById("tipo_iva");
        if(e.params.args.data.id == 1){
           $.each($('.tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $('.mostrarTodos').addClass("hidden");
           e.preventDefault();
        }

      });

    });   


    $('.to3').on('keydown', function(){
      var val = $(this).val()+"";
      val = (val.indexOf(".") >= 0) ? (val.substr(0, val.indexOf(".")) + val.substr(val.indexOf("."), 3)) : val;
      $(this).val( val );
    });

</script><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Invoice/form-linea.blade.php ENDPATH**/ ?>