<?php $__env->startSection('title'); ?>
    Editar información personal
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-md-12">

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <?php 
                        $menu = new App\Menu;
                        $items = $menu->menu('menu_perfil');
                        foreach ($items as $item) { ?>
                            <li>
                                <a class="nav-link <?php if($item->link == '/usuario/perfil'): ?> active <?php endif; ?>" aria-selected="false"  style="color: #ffffff;" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
                            </li>
                        <?php } ?>
                        <?php if( auth()->user()->isContador() ): ?>
                            <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                            </li >
                        <?php endif; ?>
                        <?php if( auth()->user()->isInfluencers()): ?>
                         <li style="display:none;">
                                <a class="nav-link" aria-selected="false" href="/usuario/wallet">Billetera</a>
                           </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content">       

                        <div class="tab-pane fade show active" role="tabpanel">
                            <form method="POST" action="/usuario/update-perfil">

                                <?php echo csrf_field(); ?>
                                <?php echo method_field('patch'); ?> 

                                <div class="form-row">

                                    <div class="form-group col-md-12">
        						      <h3>
        						        Editar información personal
        						      </h3>
        						    </div>

                                    <div class="form-group col-md-4">
                                        <label for="first_name">Nombre</label>
                                        <input type="text" name="first_name" class="form-control" value="<?php echo e(@$user->first_name); ?>" required>
                                    </div>
                                    
                                    <div class="form-group col-md-4">
                                        <label for="last_name">Primer apellido</label>                                               
                                        <input type="text"name="last_name" class="form-control" value="<?php echo e(@$user->last_name); ?>" required>
                                    </div>
                                    
                                    
                                    <div class="form-group col-md-4">
                                        <label for="last_name2">Segundo apellido</label>
                                        <input type="text" name="last_name2" class="form-control" value="<?php echo e(@$user->last_name2); ?>" required>
                                    </div>
                                        
                                    <div class="form-group col-md-4">
                                        <label for="id_number">Cédula</label>
                                        <input type="text" name="id_number" class="form-control" value="<?php echo e(@$user->id_number); ?>" required>
                                    </div>
                                    
                                    <div class="form-group col-md-4">
        						      <label for="email">Correo electrónico *</label>
        						      <input type="email" class="form-control" name="email" id="email" value="<?php echo e(@$user->email); ?>" required>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="phone">Teléfono</label>
        						      <input type="text" class="form-control" name="phone" id="phone" value="<?php echo e(@$user->phone); ?>" >
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="country">País *</label>
        						      <select class="form-control" name="country" id="country" value="<?php echo e(@$user->country); ?>" required >
        						        <option value="CR" selected>Costa Rica</option>
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="state">Provincia</label>
        						      <select class="form-control" name="state" id="state" value="<?php echo e(@$user->state); ?>" onchange="fillCantones();">
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="city">Canton</label>
        						      <select class="form-control" name="city" id="city" value="<?php echo e(@$user->city); ?>" onchange="fillDistritos();">
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="district">Distrito</label>
        						      <select class="form-control" name="district" id="district" value="<?php echo e(@$user->district); ?>" onchange="fillZip();" >
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="neighborhood">Barrio</label>
        						      <input class="form-control" name="neighborhood" id="neighborhood" value="<?php echo e(@$user->neighborhood); ?>" >
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="zip">Código Postal</label>
        						      <input type="text" class="form-control" name="zip" id="zip" value="<?php echo e(@$user->zip); ?>" readonly >
        						    </div>
        						    
        						    <div class="form-group col-md-12">
        						      <label for="address">Dirección</label>
        						      <textarea class="form-control" name="address" id="address" maxlength="250" rows="2" style="resize: none;"><?php echo e(@$user->address); ?></textarea>
        						    </div>
                                    
                                    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>
                                    
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-scripts'); ?>
	
	<script>
	  
	  $(document).ready(function(){
	    
	  	fillProvincias();
	    
	    toggleApellidos();
	    
	    //Revisa si tiene estado, canton y distrito marcados.
	    <?php if( @$user->state ): ?>
	    	$('#state').val( "<?php echo e($user->state); ?>" );
	    	fillCantones();
	    	<?php if( @$user->city ): ?>
		    	$('#city').val( "<?php echo e($user->city); ?>" );
		    	fillDistritos();
		    	<?php if( @$user->district ): ?>
			    	$('#district').val( "<?php echo e($user->district); ?>" );
			    	fillZip();
			    <?php endif; ?>
		    <?php endif; ?>
	    <?php endif; ?>
	    
	  });
	  
	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/users/edit.blade.php ENDPATH**/ ?>