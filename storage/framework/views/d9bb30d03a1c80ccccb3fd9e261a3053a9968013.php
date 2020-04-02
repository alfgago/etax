<?php $__env->startSection('title'); ?> 
  Editar producto
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
      <form method="POST" action="/productos/<?php echo e($product->id); ?>">
        <?php echo method_field('patch'); ?>
        <?php echo csrf_field(); ?>

        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Información de producto
            </h3>
          </div>

          <div class="form-group col-md-6">
            <label for="code">Código</label>
            <input type="text" class="form-control" name="code" id="codigo" value="<?php echo e($product->code); ?>" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" name="name" id="nombre" value="<?php echo e($product->name); ?>" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="default_iva_type">Tipo de IVA</label>
            <select class="form-control select-search" name="default_iva_type" id="tipo_iva" >
              <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($tipo['code']); ?>" attr-iva="<?php echo e($tipo['percentage']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="<?php echo e(@$tipo['hidden'] ? 'hidden' : ''); ?>"  <?php echo e($product->default_iva_type == $tipo->code ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="product_category_id">Categoría de declaración</label>
            <select class="form-control" name="product_category_id" id="tipo_producto" >
              <?php $__currentLoopData = \App\ProductCategory::whereNotNull('invoice_iva_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($tipo['id']); ?>" codigo="<?php echo e($tipo['invoice_iva_code']); ?>" posibles="<?php echo e($tipo['open_codes']); ?>" <?php echo e($product->product_category_id == $tipo->id ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="measure_unit">Unidad de medición</label>
            <select class="form-control" name="measure_unit" id="unidad_medicion" value="" required >
              <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($unit['code']); ?>" <?php echo e($unit['code'] == $product->measure_unit ? 'selected' : ''); ?>  ><?php echo e($unit['name']); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="unit_price">Precio unitario por defecto</label>
            <input type="number" class="form-control" name="unit_price" step="0.01" id="precio_unitario" value="<?php echo e($product->unit_price); ?>" required placeholder="0" onblur="validateUnitPrice();">
          </div>
          
          <div class="form-group col-md-12">
            <label for="description">Descripción</label>
            <textarea class="form-control" name="description" id="descripcion" maxlength="160" max="160" style="resize:none;"><?php echo e($product->description); ?></textarea>
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

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Product/edit.blade.php ENDPATH**/ ?>