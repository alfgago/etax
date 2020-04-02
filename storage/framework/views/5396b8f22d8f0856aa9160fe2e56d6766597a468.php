 

<?php $__env->startSection('title'); ?> Iniciar sesión <?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?>


<form method="POST" action="<?php echo e(route('login')); ?>">
  <?php echo csrf_field(); ?>
  <?php echo view('honeypot::honeypotFormFields'); ?>
  
  <div class="form-row">

    <div class="form-group col-md-12 text-center">
      <h3>
        Iniciar sesión
      </h3>
    </div>

    <div class="form-group col-md-12">
      <div>
        <input placeholder="<?php echo e(__('Correo electrónico')); ?>" id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required autofocus> 
        <?php if($errors->has('email')): ?>
        <span class="invalid-feedback" role="alert">
            <strong><?php echo e($errors->first('email')); ?></strong>
        </span> 
        <?php endif; ?>
      </div>
    </div>

    <div class="form-group col-md-12">
      <div>
        <input placeholder="<?php echo e(__('Contraseña')); ?>" id="password" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required> 
        <?php if($errors->has('password')): ?>
        <span class="invalid-feedback" role="alert">
            <strong><?php echo e($errors->first('password')); ?></strong>
        </span> 
        <?php endif; ?>
        </div>
    </div>

    <div class="form-group col-md-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old( 'remember') ? 'checked' : ''); ?>>

          <label class="form-check-label" for="remember"> <?php echo e(__('Recordarme')); ?> </label>
        </div>
    </div>
    
    <div class="form-group col-md-12 text-center">
        <div class="description">
          Al iniciar sesión y utilizar eTax en cualquier momento, está confirmando que acepta nuestros <a target="_blank" href="https://etaxcr.com/terminos-y-condiciones">Términos y condiciones</a>
        </div>
    </div>

    <div class="form-group col-md-12 text-center">
      <button type="submit" class="btn btn-primary" onclick="trackClickEvent( 'IniciarSesion' );"><?php echo e(__('Iniciar sesión')); ?></button>
    </div>
      <div class="form-group col-md-12 button-container text-center">

        <div class="inline-block text-center">
          
          <div class="login-secondary-btn-cont">
              <span class="loginbtn-label">¿No tiene cuenta?</span>
              <?php if(Route::has('register')): ?>
                  <a class="btn btn-link" onclick="trackClickEvent( 'EnlaceRegister' );" href="<?php echo e(route('register')); ?>">
                      Regístrese aquí
                  </a>
              <?php endif; ?>
          </div>
          <div class="login-secondary-btn-cont">
            <span class="loginbtn-label">¿Se le olvidó la contraseña? </span>
            <?php if(Route::has('password.request')): ?>
                <a class="btn btn-link" onclick="trackClickEvent( 'EnlacePassword' );" href="<?php echo e(route('password.request')); ?>">
                    Recupérela
                </a>
            <?php endif; ?>
           </div>
           
        </div>

      </div>
  </div>

</form>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/auth/login.blade.php ENDPATH**/ ?>