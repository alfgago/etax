<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
			<?php 
				$totVentasSujetas = $actividad['V1']['totales'] + $actividad['V2']['totales'] + $actividad['V13']['totales'] + $actividad['V4']['totales'] + $actividad['BI']['totales'];
			?>
			<tr class="macro-title">
		    <th colspan="6">TOTAL DE VENTAS, SUJETAS, EXENTAS Y NO SUJETAS</th>
		  </tr>
		  <tr class="macro-title withmarcar inner <?php echo e($totVentasSujetas ? 'desplegar-true' : 'desplegar-false'); ?>">
			  <th class="marcar-td" colspan="1">
					<span class="marcar">
						¿Aplica?
						<span class="si">Sí</span>
						<span class="no">No</span>
					</span>
			  </th>
		    <th class="posrel" colspan="5">Ventas sujetas (Base imponible) <input class="sumtot" readonly="" value="<?php echo e(number_format($totVentasSujetas, 0)); ?>"></th>
		  </tr>
		  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V1']['title'], 
				'totales' 	 => $actividad['V1']['totales'],
				'desplegar' => $actividad['V1']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V1']['cats'],
				'cols' 	 => true, 
				'col1' => 'true',
				'col2' => 'false',
				'col3' => 'false',
				'col4' => 'false',
				'col8' => 'false',
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V2']['title'], 
				'totales' 	 => $actividad['V2']['totales'],
				'desplegar' => $actividad['V2']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V2']['cats'],
				'cols' 	 => true, 
				'col1' => 'false',
				'col2' => 'true',
				'col3' => 'false',
				'col4' => 'false',
				'col8' => 'false',
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V4']['title'], 
				'totales' 	 => $actividad['V4']['totales'],
				'desplegar' => $actividad['V4']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V4']['cats'],
				'cols' 	 => true, 
				'col1' => 'false',
				'col2' => 'false',
				'col3' => 'false',
				'col4' => 'true',
				'col8' => 'false',
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['V13']['title'], 
				'totales' 	 => $actividad['V13']['totales'],
				'desplegar' => $actividad['V13']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['V13']['cats'],
				'cols' 	 => true, 
				'col1' => 'false',
				'col2' => 'false',
				'col3' => 'true',
				'col4' => 'false',
				'col8' => 'false',
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['BI']['title'], 
				'totales' 	 => $actividad['BI']['totales'],
				'desplegar' => $actividad['BI']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['BI']['cats'],
				'cols' 	 => true, 
				'col1' => 'true-blocked',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true-blocked',
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php 
				$totVentasExentas = $actividad['VEX']['totales'] + $actividad['VAS']['totales'];
			?>
		  <tr class="macro-title withmarcar inner <?php echo e($totVentasExentas ? 'desplegar-true' : 'desplegar-false'); ?>">
			  <th class="marcar-td" colspan="1">
					<span class="marcar">
						¿Aplica?
						<span class="si">Sí</span>
						<span class="no">No</span>
					</span>
			  </th>
		    <th class="posrel" colspan="5">Ventas exentas (Art.8) <input class="sumtot" readonly="" value="<?php echo e(number_format($totVentasExentas, 0)); ?>"></th>
		  </tr>
		  <?php if($actividad['VEX']['totales']): ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => null, 
					'totales' 	 => $actividad['VEX']['totales'],
					'desplegar' => $actividad['VAS']['totales'] || $actividad['VEX']['totales'] ? 'desplegar-true' : 'desplegar-true', 
					'cats' => $actividad['VEX']['cats'],
					'cols' 	 => false, 
					'col1' => 'true',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php endif; ?>
		  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => $actividad['VAS']['title'], 
				'totales' 	 => $actividad['VAS']['totales'],
				'desplegar' => $actividad['VAS']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['VAS']['cats'],
				'cols' 	 => false, 
				'col1' => 'true',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true',
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		  <tr class="macro-title withmarcar inner <?php echo e($actividad['VNS']['totales'] ? 'desplegar-true' : 'desplegar-false'); ?>">
			  <th class="marcar-td" colspan="1">
					<span class="marcar">
						¿Aplica?
						<span class="si">Sí</span>
						<span class="no">No</span>
					</span>
			  </th>
		    <th class="posrel" colspan="5">Ventas no sujetas (Art.9) <input class="sumtot" readonly="" value="<?php echo e(number_format($actividad['VNS']['totales'], 0)); ?>"></th>
		  </tr>
		  <?php if($actividad['VNS']['totales']): ?>
		  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
				'title' 	 => null, 
				'totales' 	 => $actividad['VNS']['totales'],
				'desplegar' => $actividad['VNS']['totales'] ? 'desplegar-true' : 'desplegar-false', 
				'cats' => $actividad['VNS']['cats'],
				'cols' 	 => false, 
				'col1' => 'true',
				'col2' => 'true',
				'col3' => 'true',
				'col4' => 'true',
				'col8' => 'true',
			], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php endif; ?>
	</tbody>
</table>
<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
	<tbody>
			<tr class="macro-title">
		    <th colspan="6">TOTAL DE COMPRAS</th>
		  </tr>
		  <?php 
		  	$totComprasAcred = $actividad['CL']['totales'] + $actividad['CI']['totales'];
		  	$totComprasNAcred = $actividad['CE']['totales'] + $actividad['CNR']['totales'] + $actividad['CNS']['totales'] +
		  											$actividad['CLI']['totales'] + $actividad['COE']['totales'];
		  ?>
		  <tr class="macro-title withmarcar inner <?php echo e($totComprasAcred ? 'desplegar-true' : 'desplegar-false'); ?>">
			  <th class="marcar-td" colspan="1">
					<span class="marcar">
						¿Aplica?
						<span class="si">Sí</span>
						<span class="no">No</span>
					</span>
			  </th>
		    <th class="posrel" colspan="5">Compras con IVA soportado acreditable <input class="sumtot" readonly="" value="<?php echo e(number_format($totComprasAcred, 0)); ?>"></th>
		  </tr>
		  <?php if($totComprasAcred): ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => $actividad['CL']['title'], 
					'totales' 	 => $actividad['CL']['totales'],
					'desplegar' => $actividad['CL']['totales'] ? 'desplegar-true' : 'desplegar-false', 
					'cats' => $actividad['CL']['cats'],
					'cols' 	 => true, 
					'col1' => 'true-blocked',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true-blocked',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => $actividad['CI']['title'], 
					'totales' 	 => $actividad['CI']['totales'],
					'desplegar' => $actividad['CI']['totales'] ? 'desplegar-true' : 'desplegar-false', 
					'cats' => $actividad['CI']['cats'],
					'cols' 	 => true, 
					'col1' => 'true-blocked',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true-blocked',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php endif; ?>
			<tr class="macro-title withmarcar inner <?php echo e($totComprasNAcred ? 'desplegar-true' : 'desplegar-false'); ?>">
			  <th class="marcar-td" colspan="1">
					<span class="marcar">
						¿Aplica?
						<span class="si">Sí</span>
						<span class="no">No</span>
					</span>
			  </th>
		    <th class="posrel" colspan="5">Compras sin IVA soportado y/o con IVA soportado no acreditable <input class="sumtot" readonly="" value="<?php echo e(number_format($totComprasNAcred, 0)); ?>"></th>
		  </tr>
			<?php if($totComprasNAcred): ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => $actividad['CE']['title'],
					'totales' 	 => $actividad['CE']['totales'],
					'desplegar' => $actividad['CE']['totales'] ? 'desplegar-true' : 'desplegar-false', 
					'cats' => $actividad['CE']['cats'],
					'cols' 	 => false, 
					'col1' => 'true',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => $actividad['CNS']['title'],
					'totales' 	 => $actividad['CNS']['totales'],
					'desplegar' => $actividad['CNS']['totales'] ? 'desplegar-true' : 'desplegar-false', 
					'cats' => $actividad['CNS']['cats'],
					'cols' 	 => false, 
					'col1' => 'true',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => $actividad['CNR']['title'],
					'totales' 	 => $actividad['CNR']['totales'],
					'desplegar' => $actividad['CNR']['totales'] ? 'desplegar-true' : 'desplegar-false', 
					'cats' => $actividad['CNR']['cats'],
					'cols' 	 => false, 
					'col1' => 'true',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => $actividad['CLI']['title'],
					'totales' 	 => $actividad['CLI']['totales'],
					'desplegar' => $actividad['CLI']['totales'] ? 'desplegar-true' : 'desplegar-false', 
					'cats' => $actividad['CLI']['cats'],
					'cols' 	 => false, 
					'col1' => 'true',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			  <?php echo $__env->make('Reports.widgets.declaracion.loop-actividades-cols', [ 
					'title' 	 => $actividad['COE']['title'],
					'totales' 	 => $actividad['COE']['totales'],
					'desplegar' => $actividad['COE']['totales'] ? 'desplegar-true' : 'desplegar-false', 
					'cats' => $actividad['COE']['cats'],
					'cols' 	 => false, 
					'col1' => 'true',
					'col2' => 'true',
					'col3' => 'true',
					'col4' => 'true',
					'col8' => 'true',
				], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php endif; ?>
		
  </tbody>
</table>


<style>
	
</style><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Reports/widgets/declaracion/loop-actividades.blade.php ENDPATH**/ ?>