@extends('layouts/app')

@section('title') 
  Autorización de facturas de compra recibidas por correo electrónico
@endsection

@section('breadcrumb-buttons')
    
@endsection 

@section('content') 
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

@endsection

@section('footer-scripts')

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
      { data: 'subtotal', name: 'subtotal', 'render': $.fn.dataTable.render.number( ',', '.', 2 ) },
      { data: 'iva_amount', name: 'iva_amount', 'render': $.fn.dataTable.render.number( ',', '.', 2 ) },
      { data: 'total', name: 'total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ) },
      { data: 'generated_date', name: 'generated_date' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
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

@endsection
