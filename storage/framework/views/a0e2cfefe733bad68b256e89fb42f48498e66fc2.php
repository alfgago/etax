<?php $__env->startSection('title'); ?>
    Perfil de empresa: <?php echo e(currentCompanyModel()->name); ?>

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
                        <a class="nav-link <?php if($item->link == '/empresas/equipo'): ?> active <?php endif; ?>" aria-selected="false"  style="color: #ffffff;" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
                    </li>
                <?php } ?>
                   
                </ul>
            </div>
            <div class="col-sm-9">
                <div class="tab-content">
                    <div class="col-md-12 col-sm-12">
                       <h3 class="card-title">Miembros</h3>
                            <?php if(auth()->user()->isOwnerOfTeam($team)): ?>
                                <a class="btn btn-sm btn-primary pull-right m-0" href="<?php echo e(route('teams.members.assign_permissions', $team)); ?>">Editar permisos de usuario</a>
                            <?php endif; ?>
                            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if( $team->users->count() ): ?>
                                        <?php $__currentLoopData = $team->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(substr($user->email,-3) != ".gs"): ?>
                                                <tr>
                                                    <td><?php echo e($user->first_name.' '.$user->last_name.' '.$user->last_name2); ?></td>
                                                    <td><?php echo e($user->email); ?></td>
                                                    <td>
                                                        <?php if( auth()->user()->isOwnerOfTeam($team) ): ?>
                                                            <?php if(auth()->user()->getKey() !== $user->getKey()): ?>
                                                            <form style="display: inline-block;" action="<?php echo e(route('teams.members.destroy', [$team, $user])); ?>" method="post">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('delete'); ?>
                
                                                                <button type="submit" class="text-danger mr-2" title="Quitar de equipo" style="display: inline-block; background: none; border: 0;">
                                                                    <i class="fa fa-ban" aria-hidden="true"></i>
                                                                </button>                                                    
                                                            </form>
                                                            
                                                            <?php else: ?>
                                                                Admin
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <?php if(auth()->user()->isOwnerOfTeam($team)): ?>
                            <div class="col-md-12" style="padding: 2rem 0px;">
                                <div class="car mb-4">
                                    <div class="car-body text-left">
                                        <h3 class="card-title">Invitaciones pendientes</h3>
                                        <div class="row">

                                            <div class="col-sm-12">
                                                <table id="dataTable" class="table table-striped table-bordered" >
                                                    <thead>
                                                        <tr>
                                                            <th>Correo electrónico</th>
                                                            <th>Invite Type</th>
                                                            <th>Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <?php $__currentLoopData = $team->invites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($invite->email); ?></td>
                                                        <td><?php echo e(($invite->role == 'admin') ? 'Invited as admin':'Invited as read-only user'); ?></td>
                                                        <td>
                                                            <a title="Reenviar invitación" href="<?php echo e(route('teams.members.resend_invite', $invite)); ?>" class="btn btn-sm btn-default">
                                                                <i class="fa fa-envelope-o"></i> 
                                                            </a>
                                                            <form id="delete-form-<?php echo e($invite->id); ?>" class="inline-form" method="POST" action="/invite/delete/<?php echo e($invite->id); ?>" >
                                                              <?php echo csrf_field(); ?>
                                                              <?php echo method_field('delete'); ?>
                                                              <a type="button" class="text-danger mr-2" title="Eliminar invitación" onclick="confirmDelete(<?php echo e($invite->id); ?>);">
                                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                              </a>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" style="padding: 0px;">

                                <div class="car mb-4">
                                    <div class="car-body text-left">
                                        <h3 class="card-title">Invitar usuarios</h3>

                                        <form class="form-horizontal" method="post" action="<?php echo e(route('teams.members.invite', $team)); ?>">
                                            <?php echo csrf_field(); ?>

                                            <div class="row">

                                                <div class="col-xs-4 col-sm-4 col-md-4">
                                                    <div class="form-group <?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                                        <label>Correo electrónico *</label>
                                                        <input type="text" name="email" class="form-control" placeholder="Correo electrónico" value="<?php echo e(old('email')); ?>" required>                                                        
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-6 col-md-offset-4">
                                                        <label>&nbsp;</label>
                                                        <button type="submit" class="btn btn-sm btn-primary mt-0"><i class="fa fa-btn fa-envelope-o mr-2"></i>Enviar invitación</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
<button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar equipo</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>

<script>
    
function confirmDelete( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea eliminar la invitación',
    text: "Esto invalidará los correos de invitación enviados actualmente. Podrá enviarlos de nuevo sin problema.",
    type: 'warning',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero eliminarla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
    
    
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/Company/edit-team.blade.php ENDPATH**/ ?>