@extends('layouts/app')

@section('title') 
  Proveedores
@endsection

@section('breadcrumb-buttons')        
      <a type="submit" class="btn btn-primary" href="/proveedores/create">Ingresar proveedor nuevo</a>
      <a class="btn btn-primary" href="/proveedores/create">Importar proveedores</a>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">

        
      <div style="margin: 1rem;"> -- Aqui van filtros de búsqueda --  </div>
      
      <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Código</th>
              <th>Identificación</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Tipo de persona</th>
              <th>Es exento</th>
              <th></th>
            </tr>
            
          </thead>
          <tbody>
            @if ( $providers->count() )
              @foreach ( $providers as $proveedor )
                <tr>
                  <td>{{ $proveedor->code }}</td>
                  <td>{{ $proveedor->id_number }}</td>
                  <td>{{ $proveedor->getFullName() }}</td>
                  <td>{{ $proveedor->email }}</td>
                  <td>{{ $proveedor->tipo_persona }}</td>
                  <td>{{ $proveedor->es_exento ? 'Si' : 'No' }} </td>
                  
                  <td> 
                    <a href="/proveedores/{{ $proveedor->id }}/edit" title="Editar proveedor" class="text-success mr-2"> 
                      <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                    </a>
                    <form class="inline-form" method="POST" action="/proveedores/{{ $proveedor->id }}" style="display: inline-block;">
                      @csrf
                      @method('delete')
                      <button type="submit" class="text-danger mr-2"  title="Eliminar proveedor" style="display: inline-block; background: none; border: 0;">
                        <i class="nav-icon i-Close-Window font-weight-bold"></i>
                      </button>
                    </form>
                  </td>
                </tr>d
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $providers->links() }}
  </div>  
</div>
@endsection