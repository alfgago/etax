<select class="form-control select-search" name="clients[<?php echo e($clienteEtax->id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como cliente nuevo --</option>
    <?php $__currentLoopData = $clientesQb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clienteQb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($clienteQb->qb_id == $clienteEtax->qbid ? 'selected' : ''); ?> value="<?php echo e($clienteQb->qb_id); ?>" ><?php echo e($clienteQb->full_name ." / ". $clienteQb->email); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Clients/select-qb.blade.php ENDPATH**/ ?>