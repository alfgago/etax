 

<?php $__env->startSection('title'); ?> Recuperar contraseña <?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?>

                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('password.email')); ?>">
                        <?php echo csrf_field(); ?>
                          <?php echo view('honeypot::honeypotFormFields'); ?>
                      <div class="form-row">

                        <div class="form-group col-md-12 text-center">
                          <h3>
                            Recuperar contraseña
                          </h3>
                        </div>

                        <div class="form-group col-md-12">
                            <div>
                                <input placeholder="<?php echo e(__('Correo electrónico')); ?>" id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required>

                                <?php if($errors->has('email')): ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group col-md-12 text-center ">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo e(__('Enviar correo de recuperación')); ?>

                                </button>
                        </div>
                        
                        <div class="form-group col-md-12 button-container text-center">
                            <div class="inline-block text-left">
                                <div>
                                    No tenés cuenta? 
                                    <?php if(Route::has('register')): ?>
                                        <a class="btn btn-link" href="<?php echo e(route('register')); ?>">
                                            <?php echo e(__('Registrate aquí')); ?>

                                        </a>
                                    <?php endif; ?>
                                    </div>
                                <div>
                                  Te acordaste de tu contraseña? 
                                  <?php if(Route::has('login')): ?>
                                      <a class="btn btn-link" href="<?php echo e(route('login')); ?>">
                                          <?php echo e(__('Ingresá aquí')); ?>

                                      </a>
                                  <?php endif; ?>
                                  </div>
                              </div>
                        </div>
                        
                      </div>
                    </form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>