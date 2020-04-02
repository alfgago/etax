<select class="form-control select-search" name="products[<?php echo e($productoQb->qb_id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como producto nuevo --</option>
    <?php $__currentLoopData = $productosEtax; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productoEtax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($productoQb->product_id == $productoEtax->id ? 'selected' : ''); ?> value="<?php echo e($productoEtax->id); ?>" ><?php echo e(@$productoEtax->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Products/select.blade.php ENDPATH**/ ?>