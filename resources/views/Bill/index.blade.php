@extends('layouts/app')

@section('title') 
  Facturas recibidas
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
      <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Proveedor</th>
              <th>Fecha</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th>Total</th>
              <th>Notas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @if ( $facturas->count() )
              @foreach ( $facturas as $factura )
                <tr>
                  <td>{{ $factura->id }}</td>
                  <td>{{ $factura->proveedor }}</td>
                  <td>{{ $factura->fecha_recibida }}</td>
                  <td>{{ $factura->subtotal }}</td>
                  <td>{{ $factura->monto_iva }}</td>
                  <td>{{ $factura->total }}</td>
                  <td>{{ $factura->notas }}</td>
                  <td> 
                    <a href="/facturas-recibidas/{{ $factura->id }}/edit" title="Editar factura" class="text-success mr-2"> 
                      <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                    </a>
                    <form class="inline-form" method="POST" action="/facturas-recibidas/{{ $factura->id }}" style="display: inline-block;">
                      @csrf
                      @method('delete')
                      <button type="submit" class="text-danger mr-2" style="display: inline-block; background: none; border: 0;">
                        <i class="nav-icon i-Close-Window font-weight-bold"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        <a type="submit" class="btn btn-primary" href="/facturas-recibidas/create">Crear factura nueva</a>
      </div>  
    </div>  
  </div>  
</div>
@endsection