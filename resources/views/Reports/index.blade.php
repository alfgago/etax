@extends('layouts/app') 

@section('title') Reportes @endsection 

@section('header-scripts')

<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>

<style>  
  .card.fullh {
    height: 100%;
  }
  small {
    font-size: .8rem !important;
  }
  .btn.form-btn {
      margin: 0;
      height: calc(1.9695rem + 2px);
      padding: 0.375rem 0.75rem;
      font-size: 0.813rem;
  }
  
  
  .ivas-table.bigtext {
      font-size: 16px;
      margin: auto !important;
  }
  
  .ivas-table th:first-of-type {
      width: auto;
      max-width: 350px;
  }
  
  .ivas-table.bigtext th, 
  .ivas-table.bigtext td {
      font-size: 14px;
      padding: 0.5rem 3em;
  }
  
  .ivas-table.bigtext thead th {
      font-weight: bold;
  }
  
  .ivas-table.bigtext thead th ,
  .ivas-table td {
      text-align: center;
  }
  
  .ivas-table.bigtext .total td, .ivas-table.bigtext .total th {
      font-size: 16px;
      font-weight: bold;
  }
    
</style>

@endsection

@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="row">
      
      <div class="form-group col-md-4">
        <label for="reportes-select">Reporte</label>
        <select class="form-control" id="reportes-select">
          <option>-- Seleccione un reporte --</option>
          <option value="cc-mes">Cuentas contables del mes actual</option>
          <option value="cc-periodo">Cuentas contables del periodo actual</option>
          <option value="detalle-debito">Detalle de débito fiscal</option>
          <option value="detalle-credito">Detalle de crédito fiscal</option>
          <option value="d104">Borrador de D-104</option>
          <option value="d140">Borrador de D-140</option>
        </select>
      </div>
      
      <div class="form-group col-md-4">
        <label for="">&nbsp;</label>
        <div>
          <button onclick="verReporte();" class="btn btn-primary form-btn">Ver reporte</button>
        </div>
      </div>
      
      <div class="col-md-12 mb-4 hidden reporte" id="reporte-cc-mes">
        @include('dashboard.widgets.cuentas-contables-compras', ['titulo' => 'Cuentas contables del mes actual', 'data' => $m])
        @include('dashboard.widgets.cuentas-contables-ventas', ['titulo' => 'Cuentas contables del mes actual', 'data' => $m])
        @include('dashboard.widgets.cuentas-contables-ajustes', ['titulo' => 'Cuentas contables del mes actual', 'data' => $m])
      </div>
      
      <div class="col-md-12 mb-4 hidden reporte" id="reporte-cc-periodo">
        @include('dashboard.widgets.cuentas-contables-compras', ['titulo' => 'Cuentas contables del periodo actual', 'data' => $acumulado])
        @include('dashboard.widgets.cuentas-contables-ventas', ['titulo' => 'Cuentas contables del periodo actual', 'data' => $acumulado])
        @include('dashboard.widgets.cuentas-contables-ajustes', ['titulo' => 'Cuentas contables del periodo actual', 'data' => $acumulado])
      </div>

      <div class="col-lg-12 col-md-12 hidden reporte" id="reporte-detalle-debito">
        @include('dashboard.widgets.detalle-debito-fiscal', ['titulo' => 'Detalle de débito fiscal'])
      </div>
    
      <div class="col-lg-12 col-md-12 hidden reporte" id="reporte-detalle-credito">
        @include('dashboard.widgets.detalle-credito-fiscal', ['titulo' => 'Detalle de crédito fiscal'])
      </div>
      
      
    </div>
  </div>
</div>

@endsection @section('footer-scripts')


<script>
  
  function verReporte() {
    var reporteId = $("#reportes-select").val();
    $(".reporte").hide();
    if(reporteId){
      $("#reporte-"+reporteId).show();
    }else{
      alert('Por favor seleccione el reporte que desea visualizar.')
    }
  }
  
</script>

@endsection