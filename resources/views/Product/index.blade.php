@extends('layouts/app')

@section('title') 
  Productos
@endsection

@section('breadcrumb-buttons')
        <a type="submit" class="btn btn-primary" href="/productos/create">Ingresar producto nuevo</a>
        <a class="btn btn-primary" href="/productos/create">Importar productos</a>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
        
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
@endsection

@section('footer-scripts')
<script>
  
$(function() {
  $('#products-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/api/products",
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
  
</script>
@endsection