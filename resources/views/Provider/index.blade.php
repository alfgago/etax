@extends('layouts/app')

@section('title') 
  Proveedores
@endsection

@section('breadcrumb-buttons')        
      <a type="submit" class="btn btn-primary" href="/proveedores/create">Ingresar proveedor nuevo</a>
      <div onclick="abrirPopup('importar-proveedores-popup');" class="btn btn-primary">Importar proveedores</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
      
      <table id="provider-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
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

@endsection

@section('footer-scripts')
<script>
  
$(function() {
  $('#provider-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('Provider.data') }}",
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
  
</script>
@endsection