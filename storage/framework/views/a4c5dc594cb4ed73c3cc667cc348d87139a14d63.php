 
<?php $__env->startSection('title'); ?> Registrar cuenta <?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?>

  <form method="POST" action="<?php echo e(route('register')); ?>">
    <?php echo csrf_field(); ?>
    <?php echo view('honeypot::honeypotFormFields'); ?>
    
    <div class="form-row">

      <div class="form-group col-md-12 text-center">
        <h3>
          Registrar cuenta
        </h3>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="<?php echo e(__('Correo electrónico')); ?>" id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" autofocus required> 
          <?php if($errors->has('email')): ?>
          <span class="invalid-feedback" role="alert">
              <strong><?php echo e($errors->first('email')); ?></strong>
          </span> 
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="<?php echo e(__('Teléfono')); ?>" id="phone" type="number" class="form-control<?php echo e($errors->has('phone') ? ' is-invalid' : ''); ?>" name="phone" value="<?php echo e(old('phone')); ?>" required autofocus> <?php if($errors->has('phone')): ?>
          <span class="invalid-feedback" role="alert">
                <strong><?php echo e($errors->first('phone')); ?></strong>
            </span> <?php endif; ?>
        </div>
      </div>

      <div class="form-group col-md-12">

        <div>
          <input placeholder="<?php echo e(__('Nombre')); ?>" id="first_name" type="text" class="form-control<?php echo e($errors->has('first_name') ? ' is-invalid' : ''); ?>" name="first_name" value="<?php echo e(old('first_name')); ?>" required autofocus> <?php if($errors->has('first_name')): ?>
          <span class="invalid-feedback" role="alert">
                                          <strong><?php echo e($errors->first('first_name')); ?></strong>
                                      </span> <?php endif; ?>
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="<?php echo e(__('Primer apellido')); ?>" id="last_name" type="text" class="form-control<?php echo e($errors->has('last_name') ? ' is-invalid' : ''); ?>" name="last_name" value="<?php echo e(old('last_name')); ?>" required autofocus> <?php if($errors->has('last_name')): ?>
          <span class="invalid-feedback" role="alert">
                                          <strong><?php echo e($errors->first('last_name')); ?></strong>
                                      </span> <?php endif; ?>
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="<?php echo e(__('Segundo apellido')); ?>" id="last_name2" type="text" class="form-control<?php echo e($errors->has('last_name2') ? ' is-invalid' : ''); ?>" name="last_name2" value="<?php echo e(old('last_name2')); ?>" required autofocus> <?php if($errors->has('last_name2')): ?>
          <span class="invalid-feedback" role="alert">
                                          <strong><?php echo e($errors->first('last_name2')); ?></strong>
                                      </span> <?php endif; ?>
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="<?php echo e(__('Contraseña')); ?>" id="password" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required> <?php if($errors->has('password')): ?>
          <span class="invalid-feedback" role="alert">
                                          <strong><?php echo e($errors->first('password')); ?></strong>
                                      </span> <?php endif; ?>
        </div>
      </div>

      <div class="form-group col-md-12 text-center">
        <div>
          <input placeholder="<?php echo e(__('Confirmar Contraseña')); ?>" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
        </div>
      </div>
      
      <div class="form-group col-md-12 text-center">
        <div class="description">
            Al registrarse y utilizar eTax en cualquier momento, está confirmando que acepta nuestros <a target="_blank" href="https://etaxcr.com/terminos-y-condiciones">Términos y condiciones</a>
        </div>
      </div>
      
      <div class="form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" onclick="trackClickEvent( 'Lead' );"><?php echo e(__('Confirmar cuenta')); ?> </button>
      </div>
      
      <div class="form-group col-md-12 button-container text-center">
        <div class="inline-block text-center">
          <div class="login-secondary-btn-cont">
          <span class="loginbtn-label">¿Ya tiene cuenta? </span>
          <?php if(Route::has('login')): ?>
              <a class="btn btn-link" href="<?php echo e(route('login')); ?>">
                  <?php echo e(__('Ingrese aquí')); ?>

              </a>
          <?php endif; ?>
          </div>
        </div>
        
      </div>

    </div>

  </form>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/auth/register.blade.php ENDPATH**/ ?>