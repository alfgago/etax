<select class="form-control select-search" name="bills_accounts[<?php echo e($facturaEtax->id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='0'>-- Sin asignar --</option>
    <?php $__currentLoopData = $cuentasQb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuenta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($facturaEtax->accId == $cuenta->Id ? 'selected' : ''); ?> value="<?php echo e($cuenta->Id); ?>" ><?php echo e($cuenta->Classification); ?>: <?php echo e($cuenta->FullyQualifiedName); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Bills/cuentas-contables.blade.php ENDPATH**/ ?>