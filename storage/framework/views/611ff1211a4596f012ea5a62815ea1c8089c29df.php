<select class="form-control select-search" name="products[<?php echo e($productoEtax->id); ?>][qbid]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como producto nuevo --</option>
    <?php $__currentLoopData = $productosQb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productoQb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($productoQb->qb_id == $productoEtax->qbid ? 'selected' : ''); ?> value="<?php echo e($productoQb->qb_id); ?>" ><?php echo e($productoQb->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Products/select-qb.blade.php ENDPATH**/ ?>