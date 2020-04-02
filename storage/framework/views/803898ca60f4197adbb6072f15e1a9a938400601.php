<?php $__env->startSection('title'); ?>
Editar permisos de equipo "<?php echo e($team->name); ?>"
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
<a class="btn btn-primary" href="/empresas/equipo">Atrás</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<style>
    .checkmark:after, .checkmark:before {
        display: none !important;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <form method="POST" action="/companies/permissions/<?php echo e($team->id); ?>">
            <?php echo csrf_field(); ?>

            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>                
                    <tr>
                        <th>Nombre</th>       
                        <th>Correo</th>                             
                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                if($permission->permission == 'admin') {
                                    $trans = 'Admin';
                                }else if($permission->permission == 'invoicing') {
                                    $trans = 'Ventas';
                                }else if($permission->permission == 'billing') {
                                    $trans = 'Compras';
                                }else if($permission->permission == 'validation') {
                                    $trans = 'Validaciones';
                                }else if($permission->permission == 'books') {
                                    $trans = 'Cierres';
                                }else if($permission->permission == 'reports') {
                                    $trans = 'Reportes';
                                }else if($permission->permission == 'catalogue') {
                                    $trans = 'Catálogos';
                                }
                            ?>
                            <th> <?php echo e($trans); ?> </th>                    
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                               
                    </tr>
                </thead>
                <tbody>
                    <?php if( $team->users->count() ): ?>           
                        <?php $__currentLoopData = $team->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(auth()->user()->id != $user->id): ?>
                                <?php if(substr($user->email,-3) != ".gs"): ?>
                                    <?php $user_permissions = get_user_company_permissions($team->company_id,$user->id)?>
                                    <tr>
                                        <td><?php echo e($user->first_name.' '.$user->last_name.' '.$user->last_name2); ?></td>
                                        <td><?php echo e($user->email); ?></td>                    
                                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><label class="checkbox checkbox-primary">
                                                <input type="checkbox" name="permissions[<?php echo $user->id ?>][]" value="<?php echo e($permission->id); ?>" <?php echo in_array($permission->id, $user_permissions) ? 'checked' : ''; ?>>                
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                   
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if($team->users->count() > 1): ?>
            <button type="submit" class="btn btn-primary">Confirmar permisos</button>
            <?php endif; ?>
        </form>
        <input type="hidden" id="is-company-edit" value="3">
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/teamwork/members/permissions.blade.php ENDPATH**/ ?>