<div class="input-validate-iva">
  <select class="form-control iva_type" name="items[<?php echo e($item->id); ?>][iva_type]" placeholder="Seleccione un código eTax" required >
      <?php
        $preselectos = array();
        foreach($company->repercutidos as $repercutido){
          $preselectos[] = $repercutido->id;
        }
      ?>
      <?php if(@$company->repercutidos[0]->id): ?>
        <?php $__currentLoopData = $cat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden' : ''); ?>" <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <option class="mostrarTodos" value="1">Mostrar Todos</option>
      <?php else: ?>
        <?php $__currentLoopData = $cat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"  <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
    </select>
</div><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Invoice/ext/select-codigos.blade.php ENDPATH**/ ?>