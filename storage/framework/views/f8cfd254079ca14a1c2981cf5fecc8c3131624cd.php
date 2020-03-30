<?php if( @$data->book ): ?>
	<?php echo $__env->make('Reports.widgets.cuentas-contables-compras', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php echo $__env->make('Reports.widgets.cuentas-contables-ventas', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php echo $__env->make('Reports.widgets.cuentas-contables-ajustes', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php if($acumulado): ?>
		<?php echo $__env->make('Reports.widgets.cuentas-contables-liquidacion', ['titulo' => "Cuentas contables $nombreMes $ano", 'data' => $acumulado], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php endif; ?>
<?php else: ?>
	<div class="row">
		<div style="display: inline-block;">
			<div class="alert alert-warning">
				No se encontraron movimientos durante el mes de <?php echo e($nombreMes); ?>

			</div>
		</div>
	</div>
<?php endif; ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views//Reports/reporte-cuentas.blade.php ENDPATH**/ ?>