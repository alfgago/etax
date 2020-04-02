<div class="input-validate-iva">
  <select curr="<?php echo e($item->product_type); ?>" class="form-control product_type" name="items[<?php echo e($item->id); ?>][product_type]" placeholder="Seleccione una categoría de hacienda" required>
      <?php $__currentLoopData = $categoriaProductos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e(@$cat->id); ?>" codigo="<?php echo e(@$cat->invoice_iva_code); ?>" posibles="<?php echo e(@$cat->open_codes); ?>" <?php echo e($item->product_type == @$cat->id ? 'selected' : ''); ?>><?php echo e(@$cat->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
</div><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Invoice/ext/select-categorias.blade.php ENDPATH**/ ?>