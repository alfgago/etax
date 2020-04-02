<select class="form-control select-search" name="invoices[<?php echo e($facturaQb->qb_id); ?>]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como factura nueva --</option>
    <?php $__currentLoopData = $facturasEtax; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facturaEtax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option <?php echo e($facturaQb->invoice_id == $facturaEtax->id ? 'selected' : ''); ?> value="<?php echo e($facturaEtax->id); ?>" ><?php echo e($facturaEtax->document_number); ?> (<?php echo e("$facturaEtax->client_first_name $facturaEtax->client_last_name $facturaEtax->client_last_name2"); ?>)</option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </select><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/Invoices/select.blade.php ENDPATH**/ ?>