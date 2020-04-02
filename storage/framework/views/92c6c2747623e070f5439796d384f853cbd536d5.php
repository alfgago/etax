<div class="input-validate-iva">
 <select class="form-control porc_identificacion_plena hidden" name="items[<?php echo e($item->id); ?>][porc_identificacion_plena]" placeholder="Seleccione identificacion especifica" required >
 	<option value="13" posibles="" <?php echo e($item->porc_identificacion_plena == 13 ? 'selected': ''); ?>>13%</option>
    <option value="5" posibles="" <?php echo e($item->porc_identificacion_plena == 0 ? 'selected': ''); ?>>0% con derecho a credito</option>
    <option value="1" posibles="" <?php echo e($item->porc_identificacion_plena == 1 ? 'selected': ''); ?>>1%</option>
    <option value="2" posibles="" <?php echo e($item->porc_identificacion_plena == 2 ? 'selected': ''); ?>>2%</option>
    <option value="4" posibles="" <?php echo e($item->porc_identificacion_plena == 4 ? 'selected': ''); ?>>4%</option>
  </select>
</div><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/ext/select-identificacion.blade.php ENDPATH**/ ?>