<?php $__env->startSection('title'); ?> 
  Autorización de facturas de compra recibidas por correo electrónico
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
    
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-md-12">
        <div class="descripcion mb-4">
          Las facturas enviadas a <b>facturas@etaxcr.com</b> se verán reflejadas en esta pantalla automáticamente. <br><br>
          Este proceso NO crea la aceptación o rechazo ante Hacienda, la funcionalidad de aceptaciones se encuentra en: <a href="/facturas-recibidas/aceptaciones">este enlace</a>.
        </div>
          
        <table id="bill-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Comprobante</th>
              <th>Emisor</th>
              <th>Moneda</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th>Total</th>
              <th>F. Generada</th>
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
  $('#bill-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/api/billsAuthorize",
    order: [[ 6, 'desc' ]],
    columns: [
      { data: 'document_number', name: 'document_number' },
      { data: 'provider', name: 'provider.fullname' },
      { data: 'currency', name: 'currency', orderable: false, searchable: false },
      { data: 'subtotal', name: 'subtotal', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'iva_amount', name: 'iva_amount', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'total', name: 'total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'generated_date', name: 'generated_date' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
  ValidarFactura();
});
  
function confirmAuthorize( id ) {
  var formId = "#accept-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea autorizar la factura?',
    text: "Al autorizarla, está aceptando que se incluya entre sus facturas utilizadas para el cálculo de impuestos.",
    type: 'success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero autorizarla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
  
function confirmDelete( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea eliminar la factura?',
    text: "Al rechazarla, la factura se eliminará de esta lista. Este proceso no es reversible",
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

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/index-autorizaciones.blade.php ENDPATH**/ ?>