@extends('layouts/app')

@section('title') 
  Facturas emitidas
@endsection

@section('breadcrumb-buttons')
    <a class="btn btn-primary" href="/facturas-emitidas/create">Ingresar factura nueva</a>
    <div onclick="abrirPopup('importar-emitidas-popup');" class="btn btn-primary">Importar facturas emitidas</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
    
      <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Receptor</th>
              <th>Moneda</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th>Total</th>
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
                  <td>{{ $invoice->currency }}</td>
                  <td>{{ number_format( $invoice->subtotal, 2 ) }}</td>
                  <td>{{ number_format( $invoice->iva_amount, 2 ) }}</td>
                  <td>{{ number_format( $invoice->total, 2 ) }}</td>
                  <td>{{ $invoice->generatedDate()->format('d/m/Y') }}</td>
                  <td>{{ $invoice->dueDate()->format('d/m/Y') }}</td>
                  <td> 
                    <a href="/facturas-emitidas/{{ $invoice->id }}/edit" title="Editar factura" class="text-success mr-2"> 
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
                    <form class="inline-form" method="POST" action="/facturas-emitidas/{{ $invoice->id }}" >
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
        {{ $invoices->links() }}
        
        <div style="margin: 1rem;">-- Aqui van opciones para exportar facturas en XML, CSV o formato de Hacienda --</div>  
  </div>  
</div>

@endsection
