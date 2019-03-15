@extends('layouts/app')

@section('title') 
  Facturas emitidas
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        
        <a class="btn btn-primary" href="/facturas-emitidas/create">Ingresar factura nueva</a>
        <a class="btn btn-primary" href="/facturas-emitidas/create">Importar facturas</a>
        
      <div style="margin: 1rem;"> -- Aqui van filtros de búsqueda por fecha, texto o cliente --  </div>
        
      <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Receptor</th>
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
            @if ( $invoices->count() )
              @foreach ( $invoices as $invoice )
                <tr>
                  <td>{{ $invoice->reference_number }}</td>
                  <td>{{ $invoice->client ? $invoice->client->toString() : '-' }}</td>
                  <td>{{ $invoice->subtotal }}</td>
                  <td>{{ $invoice->iva_amount }}</td>
                  <td>{{ $invoice->total }}</td>
                  <td>{{ $invoice->description }}</td>
                  <td>{{ $invoice->generatedDate()->format('d/m/Y') }}</td>
                  <td>{{ $invoice->dueDate()->format('d/m/Y') }}</td>
                  <td> 
                    <a href="/facturas-emitidas/{{ $invoice->id }}/edit" title="Editar factura" class="text-success mr-2"> 
                      <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                    </a>
                    <form class="inline-form" method="POST" action="/facturas-emitidas/{{ $invoice->id }}" >
                      @csrf
                      @method('delete')
                      <button type="submit" class="text-danger mr-2" >
                        <i class="nav-icon i-Close-Window font-weight-bold"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $invoices->links() }}
        
        <div style="margin: 1rem;">-- Aqui van opciones para exportar facturas en XML, CSV o formato de Hacienda --</div>  
      </div>  
    </div>  
  </div>  
</div>
@endsection