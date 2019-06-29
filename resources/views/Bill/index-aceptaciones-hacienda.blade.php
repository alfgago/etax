@extends('layouts/app')

@section('title') 
  Aceptación de facturas ante Hacienda
@endsection

@section('breadcrumb-buttons')
    <div onclick="abrirPopup('importar-aceptacion-popup');" class="btn btn-primary">Importar facturas para aceptación</div>
    <a href="/facturas-recibidas/aceptaciones-otros" class="btn btn-primary">Aceptación manual de facturas</a>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
        <div class="descripcion mb-4">
          Este proceso genera la aceptación o rechazo ante Hacienda.
        </div>
          
        <table id="bill-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Emisor</th>
              <th>Comprobante</th>
              <th>Total en <br>factura</th>
              <th>Total en <br>aceptación (₡)</th>
              <th>IVA <br>Total (₡)</th>
              <th>IVA <br>Acreditable (₡)</th>
              <th>IVA <br>Gasto (₡)</th>
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
    ajax: "/api/billsAccepts",
    columns: [
      { data: 'provider', name: 'provider.fullname' },
      { data: 'document_number', name: 'document_number' },
      { data: 'total', name: 'total' },
      { data: 'accept_total_factura', name: 'accept_total_factura', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'accept_iva_total', name: 'accept_iva_total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'accept_iva_acreditable', name: 'accept_iva_acreditable', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'accept_iva_gasto', name: 'accept_iva_gasto', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'generated_date', name: 'generated_date' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
});

function confirmAccept( id ) {
  var formId = "#accept-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea aceptar la factura?',
    text: "Al aceptarla, se enviará el mensaje de aceptación a Hacienda con los datos ingresados.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero aceptarla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
  
function confirmDecline( id ) {
  var formId = "#decline-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea rechazar la factura?',
    text: "Al rechazarla, se enviará el mensaje de rechazo a Hacienda.",
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
