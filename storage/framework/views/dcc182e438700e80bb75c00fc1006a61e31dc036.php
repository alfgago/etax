<?php $__env->startSection('title'); ?> 
  Facturas emitidas
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <?php 
  $menu = new App\Menu;
  $items = $menu->menu('menu_ventas');
  foreach ($items as $item) { ?>
    <a class="btn btn-primary" style="color: #ffffff;" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
  <?php } ?>
    
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-md-12">
      <div class="filters mb-4 pb-4">
          <div class="div-filtro">
            <label>Filtrar documentos</label>
            <div class="periodo-selects">
              <select class="form-control" id="filtro-select" name="filtro" onchange="reloadDataTable();">
                  <option value="99" selected>Todos los documentos</option>
                  <option value="1">Facturas electrónicas</option>
                  <option value="4">Tiquete electrónico</option>
                  <option value="3">Notas de crédito</option>
                  <option value="2">Notas de debito</option>
                  <option value="8">Factura de compra</option>
                  <option value="9">Factura de exportación</option>
                  <option value="0">Documentos eliminados</option>
              </select>
            </div>
          </div>
          <div class="div-filtro">
            <label>Moneda</label>
            <div class="periodo-selects">
              <select class="form-control" id="moneda-select" name="moneda" onchange="reloadDataTable();">
                  <option value="0" selected>Todas las monedas</option>
                  <option value="CRC">CRC</option>
                  <option value="USD">USD</option>
              </select>
            </div>
          </div>
          <div class="div-filtro">
            <label>Desde</label>
            <div class="input-group date inputs-fecha">
              <input id="fecha_desde" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="fecha_desde" value=""  onchange="reloadDataTable();">
              <span class="input-group-addon">
                <i class="icon-regular i-Calendar-4"></i>
              </span>
            </div>
          </div>
          <div class="div-filtro">
            <label>Hasta</label>
            <div class="input-group date inputs-fecha">
              <input id="fecha_hasta" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="fecha_hasta" value=""  onchange="reloadDataTable();">
              <span class="input-group-addon">
                <i class="icon-regular i-Calendar-4"></i>
              </span>
            </div>
          </div>
          <div class="div-filtro">
            <label>Estado</label>
            <div class="periodo-selects">
              <select class="form-control" id="estado-select" name="estado" onchange="reloadDataTable();">
                  <option value="0" selected>Todas los estados</option>
                  <option value="03">Aceptada</option>
                  <option value="04">Rechazada</option>
                  <option value="01">Pendiente</option>
                  <option value="05">Esperando Hacienda</option>
                  <option value="99">Programada</option>
              </select>
            </div>
          </div>
      </div>

      <table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Receptor</th>
              <th data-priority="3">Actividad</th>
              <th data-priority="4">Tipo Doc.</th>
              <th data-priority="5">Moneda</th>
              <th data-priority="5">Subtotal</th>
              <th data-priority="5">Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="4">F. Generada</th>
              <th data-priority="1">Acciones</th>
              <?php if( currentCompanyModel()->use_invoicing ){ ?><th data-priority="1">Estado</th> <?php } ?>
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
  
var datatable;
$(function() {
  datatable = $('#invoice-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "/api/invoices",
      data: function(d){
          d.filtro = $( '#filtro-select' ).val();
          d.moneda = $( '#moneda-select' ).val();
          d.fecha_desde = $( '#fecha_desde' ).val();
          d.fecha_hasta = $( '#fecha_hasta' ).val();
          d.estado = $( '#estado-select' ).val();
      },
      type: 'GET'
    },
    order: [[ 8, 'desc' ]],
    columns: [
      { data: 'document_number', name: 'document_number' },
      { data: 'client', name: 'client.fullname' },
      { data: 'commercial_activity', name: 'commercial_activity' },
      { data: 'document_type', name: 'document_type' },
      { data: 'moneda', name: 'currency', orderable: false, searchable: false },
      { data: 'subtotal', name: 'subtotal', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'iva_amount', name: 'iva_amount', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'total_real', name: 'total', class: "text-right" },
      { data: 'generated_date', name: 'generated_date' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
      <?php if( currentCompanyModel()->use_invoicing ){ ?> { data: 'hacienda_status', name: 'hacienda_status' },<?php } ?>
    ],
    createdRow: function (row, data, index) {
      if(data.hide_from_taxes){
        $(row).addClass("tax-hidden");
      }
    },
    language: {
      url: "/lang/datatables-es_ES.json",
    }
  });
});

function reloadDataTable() {
  datatable.ajax.reload();
}

function confirmDelete( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea eliminar la factura' ,
    text: "Este proceso la eliminará a nivel de cálculo en eTax, sin embargo no hace anulaciones ni revierte aceptaciones ante Hacienda. Usted podrá volver a importar la factura via XML o ingreso manual.",
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

function confirmDeleteProgramada( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea eliminar la factura programada' ,
    text: "",
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


function confirmRecover( id ) {
  
  var formId = "#recover-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea restaurar la factura?',
    text: "La factura será tomada en cuenta para sus cálculos de IVA nuevamente.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero restaurarla',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}

function confirmHideFromTaxes( id ) {
  
  var formId = "#hidefromtaxes-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea ocultar la factura de los cálculos de IVA?',
    text: "La factura no será tomada en cuenta para sus cálculos de IVA.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero ocultarla del cálculo',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
      $('.inputs-fecha').datetimepicker({
          format: 'DD/MM/Y',
          allowInputToggle: true,
          icons : {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check-o',
                clear: 'fa fa-times',
                close: 'fa fa-calendar-times-o'
          }
    }).on('dp.change',function(e){
      reloadDataTable()
    });
</script>

<style>
  tr.tax-hidden td {
    text-decoration: line-through !important;
  }
  tr.tax-hidden td .btn {
    text-decoration: none !important;
  }
  .div-filtro{
    float: left;
    margin: 5px;
  }
  .filters{
    position: relative;
    margin-bottom: 3.5rem !important;
  }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/Invoice/index.blade.php ENDPATH**/ ?>