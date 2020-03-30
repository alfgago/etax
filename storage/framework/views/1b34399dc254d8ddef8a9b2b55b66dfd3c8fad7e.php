<select class="form-control select-search" name="clients[<?php echo e($clienteQb->qb_id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como cliente nuevo --</option>
    <?php $__currentLoopData = $clientesEtax; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clienteEtax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($clienteQb->client_id == $clienteEtax->id ? 'selected' : ''); ?> value="<?php echo e($clienteEtax->id); ?>" ><?php echo e($clienteEtax->toString()); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Clients/select.blade.php ENDPATH**/ ?>