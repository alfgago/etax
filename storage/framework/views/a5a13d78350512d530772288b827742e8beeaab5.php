<?php $__env->startSection('title'); ?> 
  Editar cliente
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
        <form method="POST" action="/clientes/<?php echo e($client->id); ?>">
	
          <?php echo csrf_field(); ?>
          <?php echo method_field('patch'); ?> 
          
          <div class="form-row">
            <div class="form-group col-md-12">
              <h3>
                Información de cliente
              </h3>
            </div>
            <?php echo $__env->make( 'Client.form', ['client' => $client] , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
            </div>
          
            <button id="btn-submit" type="submit" class="hidden">Guardar cliente</button>
            
        </form>
        
  </div>  
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar cliente</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>
	
		
		<script>
		  
		  $(document).ready(function(){
		    
		  	fillProvincias();
		  	
            $("#billing_emails").tagging({
              "forbidden-chars":[",",'"',"'","?"],
              "forbidden-chars-text": "Caracter inválido: ",
              "edit-on-delete": false,
              "tag-char": "@"
            });
		    
		    toggleApellidos();
		    
		    fillProvincias();
		    //Revisa si tiene estado, canton y distrito marcados.
		    <?php if( @$client->state ): ?>
		    	$('#state').val( <?php echo e($client->state); ?> );
		    	fillCantones();
		    	<?php if( @$client->city ): ?>
			    	$('#city').val( <?php echo e($client->city); ?> );
			    	fillDistritos();
			    	<?php if( @$client->district ): ?>
				    	$('#district').val( <?php echo e($client->district); ?> );
				    	fillZip();
				    <?php endif; ?>
			    <?php endif; ?>
		    <?php endif; ?>
		    
		  });
		  
		</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Client/edit.blade.php ENDPATH**/ ?>