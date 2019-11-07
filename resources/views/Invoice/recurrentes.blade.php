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
                  <td>{{ $data->next_send}}</td>
                  <td>
                    <!--<a link="/facturas-emitidas/validar/{{ $data->id }}" titulo="Verificación de venta" class="btn btn-primary m-0 verificar_compra" style="color:#fff; font-size: 0.85em;" onclick="" data-toggle="modal" data-target="#modal_estandar">Validar</a>-->
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