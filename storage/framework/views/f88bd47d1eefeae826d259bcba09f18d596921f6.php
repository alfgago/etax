<?php $__env->startSection('title'); ?>
    Perfil de empresa: <?php echo e(currentCompanyModel()->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar información</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?>

<div class="row">
  <div class="col-md-12">
  	<div class="tabbable verticalForm">
    	<div class="row">
        <div class="col-sm-3">
            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            	<?php 
				$menu = new App\Menu;
				$items = $menu->menu('menu_empresas');
				foreach ($items as $item) { ?>
					<li>
						<a class="nav-link <?php if($item->link == '/empresas/editar'): ?> active <?php endif; ?>" aria-selected="false"  style="color: #ffffff;" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
					</li>
				<?php } ?>
            </ul>
        </div>
        <div class="col-sm-9">
          <div class="tab-content">       
						
						<form method="POST" action="<?php echo e(route('Company.update', ['id' => $company->id])); ?>" enctype="multipart/form-data">
						
						  <?php echo csrf_field(); ?>
						  <?php echo method_field('patch'); ?> 
						  
						  <div class="form-row">
						  	
						    <div class="form-group col-md-5">
						      <h3>
						        Editar perfil de empresa
						      </h3>
						    </div>
							  <div class="form-group col-md-7">
								  <div class="">
									  <label for="input_logo" class="logo-input">Logo empresa</label>
									  <label id="logo-name"></label>
									  <input name="input_logo" id="input_logo" style="visibility:hidden;" type="file" multiple="false">
								  </div>
								  
								  <div class="logo-container">
								  <?php if($company->logo_url): ?>
								  	<img src="<?php echo e(\Illuminate\Support\Facades\Storage::temporaryUrl($company->logo_url, now()->addMinutes(1))); ?>" style="max-height: 75px">
								  <?php endif; ?>
								  </div>
							  </div>
						    
						    <div class="form-group col-md-4">
						      <label for="tipo_persona">Tipo de persona *</label>
						      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
						        <option value="F" <?php echo e(@$company->type == 'F' ? 'selected' : ''); ?> >Física</option>
						        <option value="J" <?php echo e(@$company->type == 'J' ? 'selected' : ''); ?>>Jurídica</option>
						        <option value="D" <?php echo e(@$company->type == 'D' ? 'selected' : ''); ?>>DIMEX</option>
						        <option value="N" <?php echo e(@$company->type == 'N' ? 'selected' : ''); ?>>NITE</option>
						        <option value="E" <?php echo e(@$company->type == 'E' ? 'selected' : ''); ?>>Extranjero</option>
						        <option value="O" <?php echo e(@$company->type == 'O' ? 'selected' : ''); ?>>Otro</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="id_number">Número de identificación *</label>
						      <input type="number" class="form-control" name="id_number" id="id_number" value="<?php echo e(@$company->id_number); ?>" required onchange="getJSONCedula(this.value);">
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="business_name">Razón Social *</label>
						      <input type="text" class="form-control" name="business_name" id="business_name" value="<?php echo e(@$company->business_name); ?>" required>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="first_name">Nombre comercial *</label>
						      <input type="text" class="form-control" name="name" id="name" value="<?php echo e(@$company->name); ?>" required>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="last_name">Apellido</label>
						      <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo e(@$company->last_name); ?>" >
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="last_name2">Segundo apellido</label>
						      <input type="text" class="form-control" name="last_name2" id="last_name2" value="<?php echo e(@$company->last_name2); ?>" >
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="email">Correo electrónico *</label>
						      <input type="email" class="form-control" name="email" id="email" value="<?php echo e(@$company->email); ?>" required>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="phone">Teléfono</label>
						      <input type="number" class="form-control" name="phone" id="phone" value="<?php echo e(@$company->phone); ?>" onblur="validatePhoneFormat();">
						    </div>
						    
						    <div class="form-group col-md-12">
                                <label for="tipo_persona">Actividades comerciales *</label>
                                <select class="form-control checkEmpty select2-tags" name="commercial_activities[]" id="commercial_activities" multiple required>
                                    <?php
                                        $listaActividades = explode(",", $company->commercial_activities);
                                    ?>
                                    <?php $__currentLoopData = $actividades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actividad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($actividad['codigo']); ?>" <?php echo e((in_array($actividad['codigo'], $listaActividades) !== false) ? 'selected' : ''); ?>><?php echo e($actividad['codigo']); ?> - <?php echo e($actividad['actividad']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
						    
						    <div class="form-group col-md-4">
						      <label for="country">País *</label>
						      <select class="form-control" name="country" id="country" value="<?php echo e(@$company->country); ?>" required >
						        <option value="CR" selected>Costa Rica</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="state">Provincia *</label>
						      <select class="form-control" name="state" id="state" value="<?php echo e(@$company->state); ?>" onchange="fillCantones();">
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="city">Cantón *</label>
						      <select class="form-control" name="city" id="city" value="<?php echo e(@$company->city); ?>" onchange="fillDistritos();">
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="district">Distrito *</label>
						      <select class="form-control" name="district" id="district" value="<?php echo e(@$company->district); ?>" onchange="fillZip();" >
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="neighborhood">Barrio</label>
						      <input class="form-control" name="neighborhood" id="neighborhood" value="<?php echo e(@$company->neighborhood); ?>" >
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="zip">Código Postal</label>
						      <input type="text" class="form-control" name="zip" id="zip" value="<?php echo e(@$company->zip); ?>" readonly >
						    </div>
						    
						    <div class="form-group col-md-12">
						      <label for="address">Dirección</label>
						      <textarea class="form-control" name="address" id="address" maxlength="250" rows="2" style="resize: none;"><?php echo e(@$company->address); ?></textarea>
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
	    
	    //Revisa si tiene estado, canton y distrito marcados.
	    <?php if( @$company->state ): ?>
	    	$('#state').val( "<?php echo e($company->state); ?>" );
	    	fillCantones();
	    	<?php if( @$company->city ): ?>
		    	$('#city').val( "<?php echo e($company->city); ?>" );
		    	fillDistritos();
		    	<?php if( @$company->district ): ?>
			    	$('#district').val( "<?php echo e($company->district); ?>" );
			    	fillZip();
			    <?php endif; ?>
		    <?php endif; ?>
	    <?php endif; ?>
	    
	  });
	</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Company/edit.blade.php ENDPATH**/ ?>