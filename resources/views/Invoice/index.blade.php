@extends('layouts/app')

@section('title') 
  Facturas emitidas
@endsection

@section('breadcrumb-buttons')
    <a class="btn btn-primary" href="/facturas-emitidas/create">Ingresar factura nueva</a>
    <div onclick="abrirPopup('importar-emitidas-popup');" class="btn btn-primary">Importar facturas emitidas</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
      <table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Receptor</th>
              <th>Tipo Doc.</th>
              <th>Moneda</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="5">F. Generada</th>
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
  $('#invoice-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "/api/invoices",
      type: 'GET'
    },
    columns: [
      { data: 'reference_number', name: 'reference_number' },
      { data: 'document_number', name: 'document_number' },
      { data: 'client', name: 'client.fullname' },
      { data: 'document_type', name: 'document_type' },
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


function confirmDelete( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea anular la factura',
    text: "Este proceso generará una nota de crédito y no podrá ser revertido.",
    type: 'warning',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero anularla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}

</script>

@endsection
