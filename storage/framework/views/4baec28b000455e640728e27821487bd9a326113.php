<?php $__env->startSection('title'); ?> 
  Productos
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
 <?php 
  $menu = new App\Menu;
  $items = $menu->menu('menu_productos');
  foreach ($items as $item) { ?>
    <a class="btn btn-primary" style="color: #ffffff;" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
  <?php } ?>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?>

<div class="row">
  <div class="col-md-12">
          <div class="filters mb-4 pb-4">
              <label>Filtrar productos por</label>
              <div class="periodo-selects">
                <select id="filtro-select" name="filtro" onchange="reloadDataTable();">
                    <option selected value="1">Productos activos</option>
                    <option value="0">Productos eliminados</option>
                </select>
              </div>
          </div>
        
         <table id="products-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Código</th>
              <th>Nombre</th>
              <th>Unidad de medición</th>
              <th>Precio unitario</th>
              <th>Tipo de IVA</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
        
  </div>  
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-scripts'); ?>
<script>
  
var datatable;
$(function() {
  
  datatable = $('#products-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/api/products',
        data: function(d){
            d.filtro = $( '#filtro-select' ).val();
        }
    },
    columns: [
      { data: 'code', name: 'code' },
      { data: 'name', name: 'name' },
      { data: 'unidad_medicion', name: 'measure_unit' },
      { data: 'unit_price', name: 'unit_price', 'render': $.fn.dataTable.render.number( ',', '.', 2 ) },
      { data: 'tipo_iva', name: 'default_iva_type' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
  
});

function reloadDataTable() {
  datatable.ajax.reload();
}

function confirmDelete( id ) {
  
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea eliminar el producto',
    text: "El producto será eliminado de su catálogo. Las facturas donde se utilizó el producto no se verán afectadas.",
    type: 'warning',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero eliminarlo'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}

function confirmRecover( id ) {
  
  var formId = "#recover-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea restaurar el producto',
    text: "El producto será agregado nuevamente a su catálogo.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero restaurarlo',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
  
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/Product/index.blade.php ENDPATH**/ ?>