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
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th>Total</th>
              <th>Notas</th>
              <th>F. Generada</th>
              <th>F. Vencimiento</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @if ( $bills->count() )
              @foreach ( $bills as $bill )
                <tr>
                  <td>{{ $bill->id }}</td>
                  <td>{{ $bill->provider }}</td>
                  <td>{{ $bill->subtotal }}</td>
                  <td>{{ $bill->iva_amount }}</td>
                  <td>{{ $bill->total }}</td>
                  <td>{{ $bill->description }}</td>
                  <td>{{ $bill->generatedDate()->format('d/m/Y') }}</td>
                  <td>{{ $bill->dueDate()->format('d/m/Y') }}</td>
                  
                  <td> 
                    <a href="/facturas-recibidas/{{ $bill->id }}/edit" title="Editar factura" class="text-success mr-2"> 
                      <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                    </a>
                    <form class="inline-form" method="POST" action="/facturas-recibidas/{{ $bill->id }}" style="display: inline-block;">
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
        <a type="submit" class="btn btn-primary" href="/facturas-recibidas/create">Ingresar factura nueva</a>
      </div>  
    </div>  
  </div>  
</div>
@endsection