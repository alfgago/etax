<?php $__env->startSection('title'); ?> 
  Editar proveedor
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
      <form method="POST" action="/proveedores/<?php echo e($provider->id); ?>">

        <?php echo csrf_field(); ?>
        <?php echo method_field('patch'); ?> 
        
        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Información de proveedor
            </h3>
          </div>
          <?php echo $__env->make( 'Provider.form', ['provider' => $provider] , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          
          </div>
        
          <button id="btn-submit" type="submit" class="hidden">Guardar proveedor</button>
      </form>
      
  </div>  
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar proveedor</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Provider/edit.blade.php ENDPATH**/ ?>