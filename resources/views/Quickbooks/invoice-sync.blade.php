@extends('layouts/app')

@section('title') 
    Comparativo de facturas emitidas - QuickBooks
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12">
        
      <form method="POST" action="/productos">

        @csrf

        <div class="form-row">
          
            <div class="form-group col-md-6">  
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                        Facturas Quickbooks
                        </h3>
                    </div>
                    
                    <div class="form-group col-md-12">
                        <table id="dataTable" class="table table-striped table-bordered comparativa" cellspacing="0" width="100%" >
                            <thead class="thead-dark">
                                <tr>
                                    <th># Documento</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facturas as $fac)
                                <tr class="item-tabla item-index-{{ $loop->index }}">
                                    <td>{!! @$fac->numero_qb ?? "No existe en Quickbooks. <a href='#'>COPIAR</a>" !!}</td>
                                    <td>{{ @$fac->cliente_qb ?? '-' }}</td>
                                    <td>{{ @$fac->fecha_qb ?? '-' }}</td>
                                    <td>{{ @$fac->total_qb ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="form-group col-md-6">  
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                        Facturas eTax
                        </h3>
                    </div>
                    
                    <div class="form-group col-md-12">
                        <table id="dataTable" class="table table-striped table-bordered comparativa" cellspacing="0" width="100%" >
                            <thead class="thead-dark">
                                <tr>
                                    <th># Documento</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facturas as $fac)
                                <tr class="item-tabla item-index-{{ $loop->index }}">
                                    <td>{!! @$fac->numero_etax ?? "No existe en eTax. <a href='#'>COPIAR</a>" !!}</td>
                                    <td>{{ @$fac->cliente_etax ?? '-' }}</td>
                                    <td>{{ @$fac->fecha_etax ?? '-' }}</td>
                                    <td>{{ @$fac->total_etax ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
         
        <button id="btn-submit" type="submit" class="hidden">Guardar variables</button>
        
      </form> 
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar</button>
@endsection 

@section('footer-scripts')
<script>
    
</script>
<style>
   .comparativa td {
       font-size: 0.75rem !important;
   }
   .comparativa a {
        color: #6565b9;
        font-size: 0.9rem;
        line-height: 0.75rem;
        font-weight: bold;
    }
</style>
@endsection
