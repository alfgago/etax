<div class="form-group col-md-12">
  <h3>
    Datos de facturación
  </h3>
</div>

<div class="form-group col-md-6">
  <label for="use_invoicing">¿Desea emitir facturas electrónicas con eTax?</label>
  <select class="form-control checkEmpty" name="use_invoicing" id="use_invoicing" required>
    <option value="1" <?php if(!in_array(8, auth()->user()->permisos()) ): ?> selected <?php endif; ?> >Sí</option>
    <option value="0" <?php if(in_array(8, auth()->user()->permisos()) ): ?> selected <?php endif; ?> >No</option>
  </select>
</div>

<div class="form-group col-md-6">
  <label for="last_document" >Último documento emitido</label>
  <input type="text" class="form-control" name="last_document" id="last_document" value="<?php echo e(@$company->last_document); ?>" >
  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
</div>

<div class="form-group col-md-12">
  <label for="default_vat_code">Tipo de IVA por defecto</label>
  <select class="form-control select-search" id="default_vat_code" name="default_vat_code">
    <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($tipo['code']); ?>" attr-iva="<?php echo e($tipo['percentage']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" <?php echo e($tipo['code'] == 'B103' ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
  <div class="description">Seleccione el tipo de venta y tarifa por defecto que desea utilizar en su facturación.</div>
</div>  

<div class="form-group col-md-12">
  <label for="default_category_producto_code">Categoría de declaración por defecto</label>
  <select class="form-control" id="default_category_producto_code" name="default_category_producto_code">
    <?php $__currentLoopData = \App\ProductCategory::whereNotNull('invoice_iva_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($category['id']); ?>" posibles="<?php echo e($category['open_codes']); ?>" ><?php echo e($category['name']); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
  <div class="description">Seleccione la categoría de declaración por defecto donde irán sus ventas, y facilite el proceso de automatización de su declaración.</div>
</div>  

<div class="form-group col-md-6">
  <label for="card_retention">% Retención Tarjetas</label>
  <select class="form-control" id="card_retention" name="card_retention" >
    <option value="0" <?php echo e(@$company->card_retention == 0 ? 'selected' : ''); ?>>0%</option>
    <option value="3" <?php echo e(@$company->card_retention == 3 ? 'selected' : ''); ?>>3%</option>
    <option value="6" <?php echo e(@$company->card_retention == 6 ? 'selected' : ''); ?>>6%</option>
  </select>
</div>

    
<div class="form-group col-md-6">
  <label for="default_currency">Tipo de moneda por defecto</label>
  <select class="form-control" name="default_currency" id="default_currency" >
    <option value="crc" selected>CRC</option>
    <option value="usd" >USD</option>
  </select>
</div>

<div class="form-group col-md-12">
  <label for="default_invoice_notes">Notas por defecto</label>
  <textarea class="form-control" name="default_invoice_notes" id="default_invoice_notes" maxlength="190" rows="2" style="resize: none;"><?php echo e(@$company->default_invoice_notes); ?></textarea>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step2');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step5');">Siguiente paso</button>
</div>

<script>
  
$(document).ready(function(){

    $("#default_vat_code").change(function(){
      filterCategoriasHacienda();
    });

    function filterCategoriasHacienda(){
        var codigoIVA = $('#default_vat_code :selected').val();
        $('#default_category_producto_code option').hide();
        var tipoProducto = 0;
        $("#default_category_producto_code option").each(function(){
          var posibles = $(this).attr('posibles').split(",");
        	if(posibles.includes(codigoIVA)){
            	$(this).show();
            	if( !tipoProducto ){
                tipoProducto = $(this).val();
              }
            }
        });
        $('#default_category_producto_code').val( tipoProducto ).change();
    }
  
    filterCategoriasHacienda();

});
      
</script>

<style>
.wizard-popup .select2-container {
    z-index: 999999;
}
</style><?php /**PATH /var/www/resources/views/wizard/pasos/paso3.blade.php ENDPATH**/ ?>