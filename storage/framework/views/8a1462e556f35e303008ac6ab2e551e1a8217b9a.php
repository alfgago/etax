 

<?php $__env->startSection('title'); ?> 
    Escritorio
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('header-scripts'); ?>

<script src="<?php echo e(asset('assets/js/vendor/echarts.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/es5/echart.options.min.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
<div class="mobile-flex">
  <div class="periodo-actual filters">
    <form class="periodo-form">
      <?php 
        $mes = \Carbon\Carbon::now()->month;
        $ano = \Carbon\Carbon::now()->year;
      ?>
      <label>Filtrar por fecha</label>
      <div class="periodo-selects">
        <select id="input-ano" name="input-ano" onchange="loadReportes();">
            <option value="2019">2019</option>
            <option selected value="2020">2020</option>
        </select>
        <select id="input-mes" name="input-mes" onchange="loadReportes();">
            <option value="1" <?php echo e($mes == 1 ? 'selected' : ''); ?>>Enero</option>
            <option value="2" <?php echo e($mes == 2 ? 'selected' : ''); ?>>Febrero</option>
            <option value="3" <?php echo e($mes == 3 ? 'selected' : ''); ?>>Marzo</option>
            <option value="4" <?php echo e($mes == 4 ? 'selected' : ''); ?>>Abril</option>
            <option value="5" <?php echo e($mes == 5 ? 'selected' : ''); ?>>Mayo</option>
            <option value="6" <?php echo e($mes == 6 ? 'selected' : ''); ?>>Junio</option>
            <option value="7" <?php echo e($mes == 7 ? 'selected' : ''); ?>>Julio</option>
            <option value="8" <?php echo e($mes == 8 ? 'selected' : ''); ?>>Agosto</option>
            <option value="9" <?php echo e($mes == 9 ? 'selected' : ''); ?>>Setiembre</option>
            <option value="10" <?php echo e($mes == 10 ? 'selected' : ''); ?>>Octubre</option>
            <option value="11" <?php echo e($mes == 11 ? 'selected' : ''); ?>>Noviembre</option>
            <option value="12" <?php echo e($mes == 12 ? 'selected' : ''); ?>>Diciembre</option>
        </select>
      </div>
    </form>
  </div>

<?php /*
  $permisos = new App\User;
dd($permisos->permisos());

  ?>
@if(in_array(1, $permisos->permisos()) || in_array(6, $permisos->permisos()) || in_array(8, $permisos->permisos()))     
*/
?>
<?php if($mostrar_dashboard == 1): ?>
  <div class="toggle-vista filters hidden">
    <label id="vistabasica">Vista de dashboard</label>
    <div class="">
      <select id="input-vista" name="input-vista" onchange="loadReportes();">
        <option value="basica" selected > Vista básica</option>
        <option value="gerencial"> Vista avanzada</option>
      </select>
    </div>
  </div>
  
<?php endif; ?>    
  
</div>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?>

<div class="row" id="reporte-container">
  
  
</div>

<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>

<script>
  function loadReportes() {
    var vista = $("#input-vista").val();
    var mes = $("#input-mes").val();
    var ano = $("#input-ano").val();
      		  
    jQuery.ajax({
      url: "/reportes/reporte-dashboard",
      type: 'post',
      cache: false,
      data : {
        mes : mes,
  		  ano : ano,
  		  vista : vista,
  		  _token: '<?php echo e(csrf_token()); ?>'
      },
      success : function( response ) {
        $('#reporte-container').html(response);
        initHelpers();
      },
      async: true
    });  
  }
    
  $( document ).ready(function() {  
    loadReportes();
  });



</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views//Dashboard/index.blade.php ENDPATH**/ ?>