
<?php if($title): ?>
<tr class="sub-title <?php echo e($desplegar); ?>">
  <th class="marcar-td" colspan="1">
		<span class="marcar">
			¿Aplica?
			<span class="si">Sí</span>
			<span class="no">No</span>
		</span>
  </th>
	<th class="posrel" colspan="6"><?php echo e($title); ?> <input class="sumtot" readonly="" value="<?php echo e(number_format($totales, 0)); ?>"></th>
</tr>
<?php endif; ?>
<?php if( $desplegar == "desplegar-true" ): ?>
  <tr class="header-tarifas desplegar-true">
  		<?php if($cols): ?>
	    <th>Detalle</th>
	    <th class="<?php echo e($col3); ?>">Afectas al 13%</th>
	    <th class="<?php echo e($col8); ?>">Afectas al 8%</th>
	    <th class="<?php echo e($col4); ?>">Afectas al 4%</th>
	    <th class="<?php echo e($col2); ?>">Afectas al 2%</th>
	    <th class="<?php echo e($col1); ?>">Afectas al 1%</th>
	    <?php else: ?>
		  	<th colspan="4">Detalle</th>
		    <th colspan="2">Monto</th>
		  <?php endif; ?>
  </tr>
  
	<?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php if($cols): ?>
		<tr class="desplegar-true">
		  <th><?php echo e($info['name']); ?></th>
		  <td class="<?php echo e($col3); ?>">
		  	<input readonly value="<?php echo e(number_format( $info['monto3'], 0 )); ?>"/>
		  </td>
		  <td class="<?php echo e($col8); ?>">
		  	<input readonly value="<?php echo e(number_format( 0, 0 )); ?>"/>
		  </td>
		  <td class="<?php echo e($col4); ?>">
		  	<input readonly value="<?php echo e(number_format( $info['monto4'], 0 )); ?>"/>
		  </td>
		  <td class="<?php echo e($col2); ?>">
		  	<input readonly value="<?php echo e(number_format( $info['monto2'], 0 )); ?>"/>
		  </td>
		  <td class="<?php echo e($col1); ?>">
		  	<input readonly value="<?php echo e(number_format( $info['monto1'], 0 )); ?>" />
		  </td>
		</tr>
		<?php else: ?>
			<tr class="desplegar-true">
		  	<th colspan="4"><?php echo e($info['name']); ?></th>
			  <td colspan="2">
			  	<input readonly value="<?php echo e(number_format( $info['monto0'], 0 )); ?>"/>
			  </td>
			</tr>
		<?php endif; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Reports/widgets/declaracion/loop-actividades-cols.blade.php ENDPATH**/ ?>