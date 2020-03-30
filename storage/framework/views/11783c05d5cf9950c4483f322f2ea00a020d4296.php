<select class="form-control select-search" name="invoices[<?php echo e($facturaEtax->id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como factura nueva --</option>
    <?php $__currentLoopData = $facturasQb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facturaQb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($facturaQb->qb_id == $facturaEtax->qbid ? 'selected' : ''); ?> value="<?php echo e($facturaQb->qb_id); ?>" ><?php echo e($facturaQb->qb_doc_number); ?> / <?php echo e($facturaQb->qb_client); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Invoices/select-qb.blade.php ENDPATH**/ ?>