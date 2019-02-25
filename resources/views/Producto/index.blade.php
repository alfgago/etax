@extends('layouts/app')

@section('title') 
  Productos
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
              <th>Nombre</th>
              <th>Unidad de medición</th>
              <th>Precio unitario</th>
              <th>Tipo de producto</th>
              <th>Tipo de IVA</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @if ( $productos->count() )
              @foreach ( $productos as $producto )
                <tr>
                  <td>{{ $producto->codigo }}</td>
                  <td>{{ $producto->nombre }}</td>
                  <td>{{ $producto->getUnidadMedicionName() }}</td>
                  <td>{{ $producto->precio_unitario }}</td>
                  <td>{{ $producto->tipo_producto }}</td>
                  <td>{{ $producto->getTipoIVAName() }} </td>
                  
                  <td> 
                    <a href="/productos/{{ $producto->id }}/edit" title="Editar producto" class="icon-holder"><i class="c-light-blue-500 ti-pencil"></i> </a>
                    <form class="inline-form" method="POST" action="/productos/{{ $producto->id }}">
                      @csrf
                      @method('delete')
                      <button title="Eliminar producto" type="submit" class="icon-holder"><i class="c-light-blue-500 ti-trash"></i> </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        <a type="submit" class="btn btn-primary" href="/productos/create">Crear producto nuevo</a>
      </div>  
    </div>  
  </div>  
</div>
@endsection