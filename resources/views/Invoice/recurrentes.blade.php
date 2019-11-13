@extends('layouts/app')

@section('title') 
  	Facturas recurrentes
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
          
      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Cliente</th>
              <th>Recurrencia</th>
              <th>Opcion</th>
              <th data-priority="3">Total</th>
              <th data-priority="4">Proxima fecha</th>
              <th data-priority="1">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @if ( $recurrentes->count() )
              @foreach ( $recurrentes as $data )
                <tr>
                  <td>{{ $data->invoice->clientName() }}</td>
                  <td>{{ $data->tipo() }}</td>
                  <td>{{ $data->opciones() }}</td>
                  <td>{{ number_format( $data->invoice->total, 2 ) }}</td>
                  <td>{{date('d/m/Y', strtotime($data->next_send))}}</td>
                  <td>
                    <a href="/facturas-emitidas/editar-recurrente/{{ $data->id }}" alt="Editar recurrencia" class="btn btn-warning m-0" style="color:#fff; font-size: 0.85em;"><i class="fa fa-clock-o" aria-hidden="true"></i></a>
                    <a href="/facturas-emitidas/{{ $data->invoice->id }}" alt="Ver factura" class="btn btn-info m-0" style="color:#fff; font-size: 0.85em;"><i class="fa fa-eye" aria-hidden="true"></i></i></a>
                    <a href="/facturas-emitidas/editar-recurrente/{{ $data->id }}" alt="Cambiar factura" class="btn btn-success m-0" style="color:#fff; font-size: 0.85em;"><i class="fa fa-file" aria-hidden="true"></i></a>
                    <a href="/facturas-emitidas/editar-recurrente/{{ $data->id }}" alt="Eliminar recurrencia" class="btn btn-danger m-0" style="color:#fff; font-size: 0.85em;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        
  </div>  
</div>

@endsection

@section('footer-scripts')



@endsection