<?php if( ! @$hideEdit ): ?>
<a href="/<?php echo e($routeName); ?>/<?php echo e($id); ?>/edit" title="<?php echo e($editTitle); ?>" class="text-success mr-2"> 
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>
<?php else: ?>
<a href="/<?php echo e($routeName); ?>/<?php echo e($id); ?>" title="<?php echo e(@$showTitle); ?>" class="text-info mr-2"> 
  <i class="fa fa-eye" aria-hidden="true"></i>
</a>
<?php endif; ?>

<?php if( ! @$hideDelete ): ?>
<form id="delete-form-<?php echo e($id); ?>" class="inline-form" method="POST" action="/<?php echo e($routeName); ?>/<?php echo e($id); ?>" >
  <?php echo csrf_field(); ?>
  <?php echo method_field('delete'); ?>
  <a type="button" class="text-danger mr-2" title="<?php echo e($deleteTitle); ?>" style="display: inline-block; background: none; border: 0;" onclick="confirmDelete(<?php echo e($id); ?>);">
    <i class="<?php echo e($deleteIcon); ?>" aria-hidden="true"></i>
  </a>
</form>
<?php endif; ?>
<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/datatables/actions.blade.php ENDPATH**/ ?>