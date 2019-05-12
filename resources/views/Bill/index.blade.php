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
          
        <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>@sortablelink('reference_number', '#')</th>
              <th>Proveedor</th>
              <th>Moneda</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th>Total</th>
              <th>@sortablelink('generated_date', 'F. Generada')</th>
              <th>F. Vencimiento</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @if ( $bills->count() )
              @foreach ( $bills as $bill )
                <tr>
                  <td>{{ $bill->reference_number }}</td>
                  <td>{{ $bill->provider ? $bill->provider->toString() : '-' }}</td>
                  <td>{{ $bill->currency }}</td>
                  <td>{{ number_format( $bill->subtotal, 2 ) }}</td>
                  <td>{{ number_format( $bill->iva_amount, 2 ) }}</td>
                  <td>{{ number_format( $bill->total, 2 ) }}</td>
                  <td>{{ $bill->generatedDate()->format('d/m/Y') }}</td>
                  <td>{{ $bill->dueDate()->format('d/m/Y') }}</td>
                  
                  <td> 
                    <a href="/facturas-recibidas/{{ $bill->id }}/edit" title="Editar factura" class="text-success mr-2"> 
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
                    <form class="inline-form" method="POST" action="/facturas-recibidas/{{ $bill->id }}" style="display: inline-block;">
                      @csrf
                      @method('delete')
                      <button type="submit" class="text-danger mr-2" title="Anular factura" style="display: inline-block; background: none; border: 0;">
                        <i class="fa fa-ban" aria-hidden="true"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $bills->links() }}
  </div>  
</div>

@endsection