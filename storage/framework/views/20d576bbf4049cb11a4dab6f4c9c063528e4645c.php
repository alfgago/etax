
<a href="/clients/clients-update-view/<?php echo e($data->id); ?>" title="Editar cliente <?php echo e($data->code); ?>" class="text-success mr-2">
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>

<form id="delete-form-<?php echo e($data->id); ?>" class="inline-form" method="POST" action="/clients/clients-delete/<?php echo e($data->id); ?>" >
  <?php echo csrf_field(); ?>
  <?php echo method_field('delete'); ?>
  <a type="button" class="text-danger mr-2" title="Eliminar cliente <?php echo e($data->code); ?>"
     style="display: inline-block; background: none;" onclick="confirmDelete(<?php echo e($data->id); ?>);">
    <i class="fa fa-trash" aria-hidden="true"></i>
  </a>
</form>

<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Client/actions.blade.php ENDPATH**/ ?>