@extends('layouts/app') 

@section('title') Reportes @endsection 

@section('header-scripts')

<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>

@endsection

@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="row">
      
      <div class="form-group col-md-4">
        <label for="reportes-select">Reporte</label>
        <select class="form-control" id="reportes-select">
          <option>-- Seleccione un reporte --</option>
          <option selected value="cc-mes">Cuentas contables del mes actual</option>
          <option value="cc-periodo">Cuentas contables del periodo actual</option>
          <option value="detalle-debito">Detalle de débito fiscal</option>
          <option value="detalle-credito">Detalle de crédito fiscal</option>
          <option value="resumen-ejecutivo">Resumen ejecutivo</option>
          <option value="reporte-iva">Borrador de presentación de IVA</option>
          <option style="display:none;" value="d140">Borrador de D-140</option>
          <option style="display:none;" value="d104">Borrador de D-104</option>
        </select>
      </div>
      
      <div class="form-group col-md-4">
        <label for="">&nbsp;</label>
        <div>
          <button onclick="verReporte();" class="btn btn-primary form-btn">Ver reporte</button>
        </div>
      </div>
      
      <div class="col-md-12 mb-4 reporte" style="padding: 3rem 15px;" id="reporte-cc-mes">
        @include('dashboard.widgets.cuentas-contables-compras', ['titulo' => 'Cuentas contables del mes actual', 'data' => $m])
        @include('dashboard.widgets.cuentas-contables-ventas', ['titulo' => 'Cuentas contables del mes actual', 'data' => $m])
        @include('dashboard.widgets.cuentas-contables-ajustes', ['titulo' => 'Cuentas contables del mes actual', 'data' => $m])
      </div>
      
      <div class="col-md-12 mb-4 hidden reporte" style="padding: 3rem 15px;" id="reporte-cc-periodo">
        @include('dashboard.widgets.cuentas-contables-compras', ['titulo' => 'Cuentas contables del periodo actual', 'data' => $acumulado])
        @include('dashboard.widgets.cuentas-contables-ventas', ['titulo' => 'Cuentas contables del periodo actual', 'data' => $acumulado])
        @include('dashboard.widgets.cuentas-contables-ajustes', ['titulo' => 'Cuentas contables del periodo actual', 'data' => $acumulado])
      </div>

      <div class="col-lg-12 col-md-12 hidden reporte" style="padding: 3rem 15px;" id="reporte-detalle-debito">
        @include('dashboard.widgets.detalle-debito-fiscal', ['titulo' => 'Detalle de débito fiscal'])
      </div>
    
      <div class="col-lg-12 col-md-12 hidden reporte" style="padding: 3rem 15px;" id="reporte-detalle-credito">
        @include('dashboard.widgets.detalle-credito-fiscal', ['titulo' => 'Detalle de crédito fiscal'])
      </div>
      
      <div class="col-lg-12 col-md-12 hidden reporte" style="padding: 3rem 15px; margin-left: -15px;" id="reporte-resumen-ejecutivo">
        <div class="iframe-container">
          <iframe src="http://app.calculodeiva.com/reportes/reporte-ejecutivo"></iframe>
        </div>
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
  
  $( document ).ready(function() {
    $(".ivas-table tbody tr").each( function(){
    	var contenido =  $(this).find('td').text().replace(/[^0-9]/gi, '');
    	var number = parseInt(contenido);
    	if( !number ){
    		$(this).hide();
        }
    });
  });
  

  
</script>

@endsection