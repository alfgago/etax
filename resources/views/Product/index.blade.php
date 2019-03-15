@extends('layouts/app')

@section('title') 
  Productos
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        
        <a type="submit" class="btn btn-primary" href="/productos/create">Ingresar producto nuevo</a>
        <a class="btn btn-primary" href="/productos/create">Importar productos</a>
          
        <div style="margin: 1rem;"> -- Aqui van filtros de búsqueda --  </div>
        
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
            @if ( $products->count() )
              @foreach ( $products as $producto )
                <tr>
                  <td>{{ $producto->code }}</td>
                  <td>{{ $producto->name }}</td>
                  <td>{{ $producto->getUnidadMedicionName() }}</td>
                  <td>{{ $producto->unit_price }}</td>
                  <td>{{ $producto->productCategory->name }}</td>
                  <td>{{ $producto->getTipoIVAName() }} </td>
                  
                  <td> 
                    <a href="/productos/{{ $producto->id }}/edit" title="Editar producto" class="text-success mr-2"> 
                      <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                    </a>
                    <form class="inline-form" method="POST" action="/productos/{{ $producto->id }}" style="display: inline-block;">
                      @csrf
                      @method('delete')
                      <button type="submit" class="text-danger mr-2"  title="Eliminar producto" style="display: inline-block; background: none; border: 0;">
                        <i class="nav-icon i-Close-Window font-weight-bold"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $products->links() }}
      </div>  
    </div>  
  </div>  
</div>
@endsection