<select class="form-control select-search" name="providers[<?php echo e($proveedorQb->qb_id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como proveedor nuevo --</option>
    <?php $__currentLoopData = $proveedoresEtax; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proveedorEtax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($proveedorQb->provider_id == $proveedorEtax->id ? 'selected' : ''); ?> value="<?php echo e($proveedorEtax->id); ?>" ><?php echo e($proveedorEtax->toString()); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Providers/select.blade.php ENDPATH**/ ?>