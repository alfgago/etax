<?php $__env->startSection('title'); ?>
    Perfil de empresa: <?php echo e(currentCompanyModel()->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar certificado</button>
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
                        <a class="nav-link <?php if($item->link == '/empresas/certificado'): ?> active <?php endif; ?>" aria-selected="false"  style="color: #ffffff;" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-sm-9">
          <div class="tab-content">       
						
			<form method="POST" action="<?php echo e(route('Company.update_cert', ['id' => $company->id])); ?>" enctype="multipart/form-data">
			    
			 <?php if( @$certificate->key_url ): ?>
    		 <div class="alert alert-danger"> 
		         Usted ya subió su llave criptográfica. Cualquier edición en esta pantalla requerirá que lo suba nuevamente.
    		 </div>
			 <?php endif; ?>
			 
			 <div class="alert alert-info"> 
		         ¿No sabe cómo conseguir esta información? Contáctenos via chat, teléfono, o descarge el <a style="text-decoration:underline;" href="https://app.etaxcr.com/assets/files/guias/Manual-ConfiguracionEmpresa.pdf">Manual de configuración de empresa.</a>
    		 </div>
			 
			  <?php echo csrf_field(); ?>
			  <?php echo method_field('patch'); ?> 
			  
			  <div class="form-row">
			  	
			    <div class="form-group col-md-12">
			      <h3>
			        Certificado digital
			      </h3>
			    </div>
						    
                <div class="form-group col-md-6">
                    <label for="user">Usuario ATV</label>
                    <input type="email" class="form-control" name="user" id="user" value="<?php echo e(@$certificate->user); ?>" required>
                    <div class="description">
                        El formato de este campo debe verse similar a este: cpj-x-xxx-xxxxxx@prod.comprobanteselectronicos.go.cr
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="password">Contraseña ATV</label>
                    <input type="password" class="form-control" name="password" id="password" value="<?php echo e(@$certificate->password); ?>" required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="cert">Llave criptográfica</label>
                    <div class="fallback">
    				    <input name="cert" type="file" multiple="false">
    				</div>
    				<?php if( @$certificate->key_url ): ?>
            		 <div class="description text-danger"> 
        		         Usted ya subió su llave criptográfica de  ATV. Volver a guardar este formulario, requerirá que suba el archivo nuevamente.
            		 </div>
        			 <?php endif; ?>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="pin">PIN de llave criptográfica</label>
                    <input type="text" class="form-control" name="pin" id="pin" value="<?php echo e(@$certificate->pin); ?>" required>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/Company/edit-certificate.blade.php ENDPATH**/ ?>