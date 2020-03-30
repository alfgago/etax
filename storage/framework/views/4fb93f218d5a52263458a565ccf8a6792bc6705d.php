<select class="form-control select-search" name="providers[<?php echo e($proveedorEtax->id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como proveedor nuevo --</option>
    <?php $__currentLoopData = $proveedoresQb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proveedorQb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($proveedorQb->qb_id == $proveedorEtax->qbid ? 'selected' : ''); ?> value="<?php echo e($proveedorQb->qb_id); ?>" ><?php echo e($proveedorQb->full_name ." / ". $proveedorQb->email); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Providers/select-qb.blade.php ENDPATH**/ ?>