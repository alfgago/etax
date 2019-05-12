@extends('layouts/app')

@section('title') 
  Clientes
@endsection

@section('breadcrumb-buttons')
      <a type="submit" class="btn btn-primary" href="/clientes/create">Ingresar cliente nuevo</a>
      <div onclick="abrirPopup('importar-popup');" class="btn btn-primary">Importar clientes</div>
@endsection 


@section('content') 
<div class="row">
  <div class="col-md-12">
        
      <div style="margin: 1rem;"> -- Aqui van filtros de búsqueda --  </div>
      
      <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>@sortablelink('code', 'Código')</th>
              <th>@sortablelink('id_number', 'Identificación')</th>
              <th>@sortablelink('first_name', 'Nombre')</th>
              <th>@sortablelink('email', 'Correo')</th>
              <th>Tipo de persona</th>
              <th>Es exento</th>
              <th></th>
            </tr>
            
          </thead>
          <tbody>
            @if ( $clients->count() )
              @foreach ( $clients as $cliente )
                <tr>
                  <td>{{ $cliente->code }}</td>
                  <td>{{ $cliente->id_number }}</td>
                  <td>{{ $cliente->getFullName() }}</td>
                  <td>{{ $cliente->email }}</td>
                  <td>{{ $cliente->getTipoPersona() }}</td>
                  <td>{{ $cliente->es_exento ? 'Si' : 'No' }} </td>
                  
                  <td> 
                    <a href="/clientes/{{ $cliente->id }}/edit" title="Editar cliente" class="text-success mr-2"> 
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
                    <form class="inline-form" method="POST" action="/clientes/{{ $cliente->id }}" style="display: inline-block;">
                      @csrf
                      @method('delete')
                      <button type="submit" class="text-danger mr-2"  title="Eliminar cliente" style="display: inline-block; background: none; border: 0;">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $clients->links() }}
  </div>  
</div>

@include( 'Client.import' )

@endsection