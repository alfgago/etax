<?php $__env->startSection('title'); ?> 
  Crear producto
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
      <form method="POST" action="/productos">

        <?php echo csrf_field(); ?>

        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Información de producto
            </h3>
          </div>

          <?php $company = currentCompanyModel(); ?>          
          <input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" required>
          <input type="hidden" class="form-control" id="default_product_category" value="<?php echo e($company->default_product_category); ?>">
          <input type="hidden" class="form-control" id="default_vat_code" value="<?php echo e($company->default_vat_code); ?>">
          
          <div class="form-group col-md-6">
            <label for="code">Código</label>
            <input type="text" class="form-control" name="code" id="codigo" value="" max="13" maxlength="13" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" name="name" id="nombre" value="" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="default_iva_type">Tipo de IVA</label>
            <select class="form-control select-search" name="default_iva_type" id="tipo_iva" >
              <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($tipo['code']); ?>" attr-iva="<?php echo e($tipo['percentage']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="<?php echo e(@$tipo['hidden'] ? 'hidden' : ''); ?>"><?php echo e($tipo['name']); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="product_category_id">Categoría de declaración</label>
            <select class="form-control" name="product_category_id" id="tipo_producto" >
              <?php $__currentLoopData = \App\ProductCategory::whereNotNull('invoice_iva_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($tipo['id']); ?>" codigo="<?php echo e($tipo['invoice_iva_code']); ?>" posibles="<?php echo e($tipo['open_codes']); ?>" ><?php echo e($tipo['name']); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="measure_unit">Unidad de medición</label>
            <select class="form-control" name="measure_unit" id="unidad_medicion" value="" required>
              <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($unit['code']); ?>" ><?php echo e($unit['name']); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            
          </div>
          
          <div class="form-group col-md-6">
            <label for="unit_price">Precio unitario por defecto</label>
            <input type="number" class="form-control" name="unit_price" step="0.01" id="precio_unitario" value="" required placeholder="0" onblur="validateUnitPrice();">
          </div>
          
          <div class="form-group col-md-12">
            <label for="description">Descripción</label>
            <textarea class="form-control" name="description" id="descripcion" value="" maxlength="160" style="resize:none;"></textarea>
          </div>

        </div>

        <button id="btn-submit" type="submit" class="hidden">Guardar producto</button>
        
      </form> 
  </div>  
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar producto</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>
  <script>
    if( $('#default_vat_code').length ){
      $('#tipo_iva').val( $('#default_vat_code').val() ).change();
    }else{
      $('#tipo_iva').val( 'B103' ).change();
    }
    function validateUnitPrice() {
        var price = $('#precio_unitario').val();
        if(price <= 0){
            alert('El precio debe ser mayor a cero');
            $('#precio_unitario').val(0);
        }
    }
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Product/create.blade.php ENDPATH**/ ?>