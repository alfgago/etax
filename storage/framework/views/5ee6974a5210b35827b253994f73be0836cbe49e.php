<div class="input-validate-iva">
  <select class="form-control iva_type" name="items[<?php echo e($item->id); ?>][iva_type]" placeholder="Seleccione un código eTax" required >
      <?php
        $preselectos = array();
        foreach($company->soportados as $soportado){
          $preselectos[] = $soportado->id;
        }
      ?>
      <?php if(@$company->soportados[0]->id): ?>
        <?php $__currentLoopData = $cat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden' : ''); ?>" <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?> is_identificacion_plena="<?php echo e($tipo['is_identificacion_plena']); ?>"><?php echo e($tipo['name']); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <option class="mostrarTodos" value="1">Mostrar Todos</option>
      <?php else: ?>
        <?php if(currentCompanyModel()->id==1110 || currentCompanyModel()->id==437) { ?>
          <?php $__currentLoopData = $cat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"  <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?>  is_identificacion_plena="<?php echo e($tipo['is_identificacion_plena']); ?>"><?php echo e(\App\SMInvoice::parseFormatoSM($tipo->code)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php }else{ ?>
          <?php $__currentLoopData = $cat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"  <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?>  is_identificacion_plena="<?php echo e($tipo['is_identificacion_plena']); ?>"><?php echo e($tipo['name']); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php } ?>
      <?php endif; ?>
    </select>
</div><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/ext/select-codigos.blade.php ENDPATH**/ ?>