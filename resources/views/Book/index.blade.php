@extends('layouts/app')

@section('title') 
  Cierres de mes
@endsection

@section('breadcrumb-buttons')        
      
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
    
        <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Periodo</th>
              <th>Ventas</th>
              <th>Compras</th>
              <th>IVA de fact. emitidas</th>
              <th>IVA de fact. recibidas</th>
              <th>IVA acreditable</th>
              <th>IVA por pagar</th>
              <th>IVA por cobrar</th>
              <th>Rentención</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @if ( $books->count() )
              @foreach ( $books as $data )
                <tr>
                  <td>{{ \App\Variables::getMonthName($data->month) }} {{ $data->year }} {{ $data->is_rectification ? '(Rectificación)' : '' }} </td>
                  <td>₡{{ number_format( $data->invoices_subtotal, 0 ) }}</td>
                  <td>₡{{ number_format( $data->bills_subtotal, 0 ) }}</td>
                  <td>₡{{ number_format( $data->total_invoice_iva, 0 ) }}</td>
                  <td>₡{{ number_format( $data->total_bill_iva, 0 ) }}</td>
                  <td>₡{{ number_format( $data->iva_deducible_operativo, 0 ) }}</td>
                  <td>₡{{ $data->balance_operativo > 0 ? number_format( $data->balance_operativo, 0 ) : 0 }} </td>
                  <td>₡{{ $data->balance_operativo < 0 ? number_format( abs($data->balance_operativo), 0 ) : 0 }} </td>
                  <td>₡{{ number_format( $data->retention_by_card, 0 ) }}</td>
                  <td>{{ $data->is_closed ? 'Cerrado' : 'Abierto' }}</td>
                  <td> 
                      <a href="/cierres/retenciones-tarjeta/{{ $data->id }}" title="Retenciones {{ $data->month }}-{{ $data->year }}" class="btn btn-danger m-0" style=" font-size: 0.9em;">
                          Retenciones
                        </a>
                    @if( !$data->is_closed  )
                      <form class="inline-form" method="POST" action="/cierres/cerrar-mes/{{ $data->id }}" >
                        @csrf
                        @method('patch')
                        <button type="submit" title="Cerrar {{ $data->month }}-{{ $data->year }}" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.9em;">
                          Cerrar mes
                        </button>
                      </form>
                    @elseif( !$data->is_final  )
                      - Tiene rectificación -
                    @else
                      @if( $data->year != '2018'  )
                        <form class="inline-form" method="POST" action="/cierres/abrir-rectificacion/{{ $data->id }}" >
                          @csrf
                          @method('patch')
                          <input type="hidden" name="id">
                          <button type="submit" title="Abrir {{ $data->month }}-{{ $data->year }}" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.9em;">
                            Abrir para rectificación
                          </button>
                        </form>
                      @else
                        -
                      @endif
                    @endif
                  </td>
                </tr>
              @endforeach
            @endif
          </tbody>
          
        </table>
        
        
        
  </div>  
</div>

@endsection