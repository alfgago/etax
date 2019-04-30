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
      
      <div class="form-group col-md-3">
        <label for="reportes-select">Reporte</label>
        <select class="form-control" id="reportes-select" onchange="toggleFilters();">
          <option>-- Seleccione un reporte --</option>
          <option value="/reportes/cuentas-contables" type="post" selected ano="1" mes="1">Cuentas contables</option>
          <option value="/reportes/detalle-debito" hideClass="#input-mes" type="post" ano="1" mes="1">Detalle de débito fiscal</option>
          <option value="/reportes/detalle-credito" hideClass="#input-mes" type="post" ano="1" mes="1">Detalle de crédito fiscal</option>
          <option value="/reportes/resumen-ejecutivo" hideClass=".opt-acumulado" type="iframe" >Resumen ejecutivo</option>
          <option type="post">Borrador de presentación de IVA</option>
          <option type="post">Borrador D-151</option>
          <option type="post" style="display:none;">Borrador de D-140</option>
          <option type="post" style="display:none;">Borrador de D-104</option>
        </select>
      </div>
      
      <div class="form-group col-md-3">
        <div class="periodo-actual inline-form">
          <?php 
            $mes = \Carbon\Carbon::now()->month;
          ?>
          <label>Filtrar por fecha</label>
          <div class="periodo-selects">
            <select class="form-control" id="input-ano" name="input-ano">
                <option selected value="2019">2019</option>
            </select>
            <select class="form-control" id="input-mes" name="input-mes">
                <option class="opt-acumulado" value="0" selected>Acumumlado</option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Setiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="form-group col-md-12">
          <button onclick="verReporte();" class="btn btn-primary form-btn">Ver reporte</button>
      </div>
      
      <div id="reporte-container" class="col-md-12 mb-4 reporte" style="padding: 3rem 15px;">
        
      </div>

      <div class="col-lg-12 col-md-12 hidden reporte" style="padding: 3rem 15px;" id="reporte-detalle-debito">
        
      </div>
    
      <div class="col-lg-12 col-md-12 hidden reporte" style="padding: 3rem 15px;" id="reporte-detalle-credito">
        
      </div>
      
      <div class="col-lg-12 col-md-12  reporte" style="padding: 3rem 15px; margin-left: -15px;" id="reporte-resumen-ejecutivo">
        
      </div>
      
    </div>
  </div>
</div>

@endsection @section('footer-scripts')


<script>
  
  function toggleFilters(){
    
    var hideClass = $("#reportes-select :selected").attr("hideClass");
    $(".form-control, .form-control option").show();
    $(hideClass).hide();
    $("#input-mes").val(1);
    
  }
  
  function verReporte() {
    var reporteView = $("#reportes-select").val();
    var formType = $("#reportes-select :selected").attr("type");
    
    if(reporteView){
      var mes = $("#input-mes").val();
      var ano = $("#input-ano").val();
      		  
      if(formType != "iframe"){
        jQuery.ajax({
          url: reporteView,
          type: 'post',
          cache: false,
          data : {
            mes : mes,
      		  ano : ano,
      		  _token: '{{ csrf_token() }}'
          },
          success : function( response ) {
            $('#reporte-container').html(response);
            clearEmptyRows();
          },
          async: true
        });  
        
      }else{
        reporteView = reporteView + "?ano="+ano+"&mes="+mes;
        $('#reporte-container').html( "<div class='iframe-container'> <iframe src='"+reporteView+"'></iframe> </div>" );
      }
      
    }else{
      alert('Por favor seleccione el reporte que desea visualizar.')
    }
  }
  
  $( document ).ready(function() {
    clearEmptyRows();
  });
  
  function clearEmptyRows() {
    $(".ivas-table tbody tr").each( function(){
    	var contenido =  $(this).find('td').text().replace(/[^0-9]/gi, '');
    	var number = parseInt(contenido);
    	if( !number ){
    		$(this).hide();
        }
    });
  }

  
</script>

@endsection