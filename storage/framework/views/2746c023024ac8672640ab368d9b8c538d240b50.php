<?php $__env->startSection('title'); ?> 
  Clientes
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?> <?php 
  $menu = new App\Menu;
  $items = $menu->menu('menu_clientes');
  foreach ($items as $item) { ?>
    <a class="btn btn-primary" style="color: #ffffff;" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
  <?php } ?>
<?php $__env->stopSection(); ?> 


<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-md-12">
      
      <table id="client-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Código</th>
              <th data-priority="2">Identificación</th>
              <th data-priority="1">Nombre</th>
              <th data-priority="1">Apellido</th>
              <th>Correo</th>
              <th>Tipo de persona</th>
              <th>Es exento</th>
              <th data-priority="1">Acciones</th>
            </tr>
            
          </thead>
          <tbody>
          </tbody>
      </table>
  </div>  
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-scripts'); ?>
<script>
  
$(function() {
  $('#client-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/api/clients",
    columns: [
      { data: 'code', name: 'code' },
      { data: 'id_number', name: 'id_number' },
      { data: 'nombreC', name: 'first_name' },
      { data: 'last_name', name: 'last_name', 'visible': false },
      { data: 'email', name: 'email' },
      { data: 'tipo_persona', name: 'tipo_persona' },
      { data: 'es_exento', name: 'es_exento' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
});
function confirmDelete( id ) {
    var formId = "#delete-form-"+id;
    Swal.fire({
        title: '¿Está seguro que desea eliminar el cliente?',
        text: "",
        type: 'warning',
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: 'Sí, deseo eliminarlo'
    }).then((result) => {
        if (result.value) {
            $(formId).submit();
        }
    })

}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Client/index.blade.php ENDPATH**/ ?>