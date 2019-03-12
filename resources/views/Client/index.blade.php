@extends('layouts/app')

@section('title') 
  Clientes
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        
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
            @if ( $clients->count() )
              @foreach ( $clients as $cliente )
                <tr>
                  <td>{{ $cliente->code }}</td>
                  <td>{{ $cliente->id_number }}</td>
                  <td>{{ $cliente->getFullName() }}</td>
                  <td>{{ $cliente->email }}</td>
                  <td>{{ $cliente->tipo_persona }}</td>
                  <td>{{ $cliente->es_exento ? 'Si' : 'No' }} </td>
                  
                  <td> 
                    <a href="/clientes/{{ $cliente->id }}/edit" title="Editar cliente" class="text-success mr-2"> 
                      <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                    </a>
                    <form class="inline-form" method="POST" action="/clientes/{{ $cliente->id }}" style="display: inline-block;">
                      @csrf
                      @method('delete')
                      <button type="submit" class="text-danger mr-2"  title="Eliminar cliente" style="display: inline-block; background: none; border: 0;">
                        <i class="nav-icon i-Close-Window font-weight-bold"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        <a type="submit" class="btn btn-primary" href="/clientes/create">Crear cliente nuevo</a>
      </div>  
    </div>  
  </div>  
</div>
@endsection