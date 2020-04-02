<?php if( !$data->trashed()  ): ?>
<a href="/productos/<?php echo e($data->id); ?>/edit" title="Editar producto" class="text-success mr-2"> 
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>

<form id="delete-form-<?php echo e($data->id); ?>" class="inline-form" method="POST" action="/productos/<?php echo e($data->id); ?>" >
  <?php echo csrf_field(); ?>
  <?php echo method_field('delete'); ?>
  <a type="button" class="text-danger mr-2" title="Eliminar producto" style="display: inline-block; background: none; border: 0;" onclick="confirmDelete(<?php echo e($data->id); ?>);">
    <i class="fa fa-trash-o" aria-hidden="true"></i>
  </a>
</form>
<?php else: ?>

<form id="recover-form-<?php echo e($data->id); ?>" class="inline-form" method="POST" action="/productos/<?php echo e($data->id); ?>/restore" >
  <?php echo csrf_field(); ?>
  <?php echo method_field('patch'); ?>
  <a type="button" class="text-success mr-2" title="Restaurar producto" style="display: inline-block; background: none; border: 0;" onclick="confirmRecover(<?php echo e($data->id); ?>);">
    <i class="fa fa-refresh" aria-hidden="true"></i>
  </a>
</form>

<?php endif; ?>
<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Product/actions.blade.php ENDPATH**/ ?>