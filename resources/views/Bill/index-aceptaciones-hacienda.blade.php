@extends('layouts/app')

@section('title') 
  Aceptación de facturas ante Hacienda
@endsection

@section('breadcrumb-buttons')
    
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

@include('Bill.import-accepts')

@endsection

@section('footer-scripts')

<script>
  
$(function() {
  $('#bill-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/api/billsAccepts",
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
  
</script>

@endsection
