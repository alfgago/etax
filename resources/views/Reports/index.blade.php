@extends('layouts/app') 

@section('title') Reportes @endsection 

@section('header-scripts')

<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>

@endsection

@section('content')
<?php $ratios = currentCompanyModel()->operative_ratio1 + currentCompanyModel()->operative_ratio2 + currentCompanyModel()->operative_ratio3 + currentCompanyModel()->operative_ratio4;?>
    
<div class="row">
  <div class="col-md-12">
    <div class="row">
      @if(!$ratios)
      <div class="col-md-8">
        <div class="alert alert-warning"> 
          <strong>Configuración de empresa incompleta.</strong><br><br>
          Para obtener datos precisos en su declaración y otros reportes, debe ingresar su proporción de ventas del año 2018 para cada una de las posibles tarifas (1%, 2%, 13% y 4%). 
          Puede hacerlo ingresando a la sección de <a href="/empresas/configuracion">configuración avanzada</a>.
  		  </div>
		  </div>
		  <div class="col-md-12"></div>
		  @endif
		  
      <div class="form-group col-md-3">
        <label for="reportes-select">Reporte</label>
        <select class="form-control" id="reportes-select" onchange="toggleFilters();">
          <option>-- Seleccione un reporte --</option>
          <option value="/reportes/cuentas-contables" type="post" selected ano="1" mes="1">Cuentas contables</option>
          <option value="/reportes/detalle-debito" hideClass="#input-mes" type="post" ano="1" mes="1">Detalle de débito fiscal</option>
          <option value="/reportes/detalle-credito" hideClass="#input-mes" type="post" ano="1" mes="1">Detalle de crédito fiscal</option>
          <option value="/exportar-libro-compras" type="download" hideClass=".opt-acumulado" >Libro de compras</option>
          <option value="/exportar-libro-ventas" type="download" hideClass=".opt-acumulado" >Libro de ventas</option>
          <option value="/reportes/resumen-ejecutivo" hideClass=".opt-acumulado" type="iframe" >Resumen ejecutivo</option>
          <option style="" value="/reportes/borrador-iva" hideClass=".opt-acumulado" type="iframe">Borrador de declaración de IVA</option>
          <option type="post">Reporte de proveedores (Muy pronto)</option>
          <option type="post">Reporte de clientes (Muy pronto)</option>
          <!--@if( getCurrentSubscription()->status == 4 )
            <option style="" value="" hideClass=".opt-acumulado" type="iframe">Declaración de IVA (No disponible en periodo gratis)</option>
          @else-->
          <!--@endif-->
        </select>
      </div>
      
      <div class="form-group col-md-3">
        <div class="periodo-actual inline-form">
          <?php 
            $mes = \Carbon\Carbon::now()->month;
            $ano = \Carbon\Carbon::now()->year;
          ?>
          <label>Filtrar por fecha</label>
          <div class="periodo-selects">
            <select class="form-control" id="input-ano" name="input-ano">
                <option value="2019">2019</option>
                <option selected value="2020">2020</option>
            </select>
            <select class="form-control" id="input-mes" name="input-mes">
                <option class="opt-acumulado" value="0" selected>Acumulado</option>
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
      <div class="col-md-12">
        <button onclick="verReporte();" class="btn btn-primary form-btn">Ver reporte</button>
      </div>

      <div id="reporte-container" class="col-md-12 mb-4 reporte" style="padding: 3rem 15px;">
        
      </div>
		  <!--
		  @if( getCurrentSubscription()->status == 4 )
		  <div class=" ml-4 mb-4 pb-4" style="background: #eee; padding: 1rem 2rem; width: auto; display: inline-block; box-shadow: 0 0 15px rgba(0,0,0,0.3);">
        
          <h2>¡Tu declaración te espera!</h2>
          ¿Ya usaste eTax para ingresar tus facturas, viste todos los reportes, y ya querés hacer tu declaración?<br> Ingresá aquí y comprá tu plan ahora. 
          <div>
          <a  style="background: #f0c960;
            color: #1f2642 !important;
            border: 0;
            -webkit-box-shadow: 0.2rem 0.2rem #261c65 !important;
            box-shadow: 0.2rem 0.2rem #261c65 !important;
            font-size: 0.9rem;
            margin: 0; display: inline-block;
            font-weight: bold; margin-top: .5rem;
            width: auto;" class="btn btn-primary btn-buynow" href="/elegir-plan" title="Comprar ahora">Comprar ahora</a>.
  		    </div>
		  </div>
		  <div class="col-md-12"></div>
		  @endif
      -->
      <div class="col-md-12" hidden id="export-btn-container" style="margin-top:-2em;">
        <a id="btnExport" download='reporteEtax' href='javascript:exportarTablas()' class="btn btn-primary form-btn">Descargar</a>
      </div>  
      
    </div>
  </div>
</div>

@endsection 

@section('footer-scripts')

<script>

  var d = new Date();
  var dayNumber = d.getMonth() + 1;
  $("#input-mes").val(dayNumber);
  
  function toggleFilters(){
    
    var hideClass = $("#reportes-select :selected").attr("hideClass");
    $(".form-control, .form-control option").show();
    $(hideClass).hide();
    $("#input-mes").val(dayNumber);
    
  }

 function exportarTablas(){
      var uri = 'data:application/vnd.ms-excel;base64,',
          template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel;charset=UTF-8;base64"><head></head><body><table>{table}</table></body></html>',
          base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) },
          format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }

      var table = 'reporte-container';
      var name = 'nombre_hoja_calculo';

      if (!table.nodeType) table = document.getElementById(table);
      var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML };
      
      var link = document.createElement("a");
          link.download = "reporte-etax.xls";
          link.href = uri + base64(format(template, ctx));
          link.click();
      
      //window.location.href = uri + base64(format(template, ctx));
  }
  
  function verReporte() {
    var reporteView = $("#reportes-select").val();
    var formType = $("#reportes-select :selected").attr("type");
    
    if(reporteView){
      var mes = $("#input-mes").val();
      var ano = $("#input-ano").val();
      		  
      if( formType == "download"){
        reporteView = reporteView + "/"+ano+"/"+mes;
        window.open(reporteView, '_blank');
      }else if(formType != "iframe"){
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
              setTimeout(function(){
                $("table").attr('id', 'reporte-container');
                $('#export-btn-container').attr('hidden', false);
              }, 1000);
          },
          async: true
        });  
        
      }else{
        reporteView = reporteView + "?ano="+ano+"&mes="+mes;
        $('#reporte-container').html( "<div class='iframe-container'> <iframe id='report-iframe' onload='resizeIframe(this);' src='"+reporteView+"'></iframe> </div>" );
      }
      
    }else{
      alert('Por favor seleccione el reporte que desea visualizar.')
    }
  }
  
  $( document ).ready(function() {
    clearEmptyRows();
    
    @if(!$ratios)
      var title = 'Configuración de empresa incompleta.';
      var texto = "Para obtener datos precisos en su declaración y otros reportes, debe ingresar su proporción de ventas del año 2018 para cada una de las posibles tarifas (1%, 2%, 13% y 4%). Puede hacerlo ingresando a la sección de configuración avanzada.";
      Swal.fire({
        title: title,
        text: texto,
        type: 'warning',
        showCloseButton: true,
        confirmButtonText: 'Ok'
      });
    @endif
  });
  
  function resizeIframe() {
    var iframe = document.getElementById('report-iframe');
    iframe.style.height = iframe.contentWindow.document.body.offsetHeight + 'px';
  }
  
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

<style>
  
  .iframe-container {
    position: relative;
    width: 75rem;
    max-width: 100%;
    overflow: hidden;
    height: auto;
    padding-bottom: 3rem;
  }
  
  .iframe-container iframe {
      position: relative;
      top: 0;
      left: 0;
      width: 100%;
      border: 0;
  }
  
</style>

@endsection
