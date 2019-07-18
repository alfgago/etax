@extends('layouts/app')

@section('title') 
  Retenciones del cierre {{@$data['mes']}}/{{@$data['year']}}
@endsection

@section('breadcrumb-buttons')        
      
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-9">
    
        <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Documento</th>
              <th>Cliente</th>
              <th>Monto</th>
              <th>Retencion {{$data['retencion_porcentaje']}}%</th>
            </tr>
          </thead>
          <tbody>
            @if ( $invoices->count() )
              @foreach ( $invoices as $invoice )
                <tr>
                  <td>{{$invoice->generated_date}}</td>
                  <td>{{$invoice->document_number}}</td>
                  <td>{{$invoice->client_first_name}} {{$invoice->client_last_name}} {{$invoice->client_last_name2}}</td>
                  <td>₡{{ number_format( $invoice->total, 0 ) }}</td>
                  <td>₡{{ number_format( $invoice->retencion, 0 ) }}</td>
                </tr>
              @endforeach
            @endif
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>₡{{ number_format( $data['total_facturado'], 0 ) }}</td>
              <td>₡{{ number_format( $data['total_retencion'], 0 ) }}</td>
            </tr>
          </tfoot>
        </table>
  </div>  
  <form method="POST" action="/cierres/actualizar-retencion-tarjeta">
    @csrf
          @method('post') 
    <div class="form-group">
      <label for="total_vendido">TOTAL VENTAS EN TARJETAS</label>
      <input type="text" class="form-control" id="total_vendido" name="total_vendido" disabled value="₡{{ number_format( $data['total_facturado'], 0 ) }}">
    </div>
    <div class="form-group">
      <label for="total_retencion">RETENCION PREDEFINIDA {{$data['retencion_porcentaje']}}%</label>
      <input type="text" class="form-control" id="total_retencion" name="total_retencion" disabled value="₡{{ number_format( $data['total_retencion'], 0 ) }}">
    </div>
    <div class="form-group">
      <label for="total_retenido">SALDO REAL RETENIDO</label>
      <input type="number" class="form-control" id="total_retenido" @if($data['cerrado'] === 1) disabled @endif name="total_retenido" value="{{ $data['total_retencion'] }}">
    </div>
    @if($data['cerrado'] === 0) 
        <input type="number" class="form-control" id="cierre" name="cierre" hidden value="{{@$data['cierre']}}">
        <button type="submit" class="btn btn-primary">GUARDAR</button>
    @endif
  
</form>

</div>

@endsection