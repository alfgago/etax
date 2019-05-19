@extends('layouts/app')

@section('title') 
  Facturas recibidas
@endsection

@section('breadcrumb-buttons')
    <a type="submit" class="btn btn-primary" href="/facturas-recibidas/create">Ingresar factura nueva</a>
    <div onclick="abrirPopup('importar-recibidas-popup');" class="btn btn-primary">Importar facturas recibidas</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
          
        <table id="bill-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Emisor</th>
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
  $('#bill-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('Bill.data') }}",
    columns: [
      { data: 'reference_number', name: 'reference_number' },
      { data: 'document_number', name: 'document_number' },
      { data: 'provider', name: 'provider.fullname' },
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
  
</script>

@endsection
