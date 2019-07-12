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
          <option value="/reportes/libro-compras" hideClass=".opt-acumulado" >Libro de compras</option>
          <option value="/reportes/libro-ventas" hideClass=".opt-acumulado" >Libro de ventas</option>
          <option value="/reportes/resumen-ejecutivo" hideClass=".opt-acumulado" type="iframe" >Resumen ejecutivo</option>
          <option value="/reportes/reporte-proveedores" type="post">Reporte de proveedores (Muy pronto)</option>
          <option value="/reportes/reporte-clientes" type="post">Reporte de clientes (Muy pronto)</option>
          <option value="/reportes/reporte-iva" type="post">Declaración de IVA (Muy pronto)</option>
          <option style="display:none;" value="/reportes/borrador-iva" hideClass=".opt-acumulado" type="iframe">Borrador de declaración de IVA (Muy pronto)</option>
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
      <div class="row">
          <div class="col-md-1">
              <button onclick="verReporte();" class="btn btn-primary form-btn">Ver reporte</button>
          </div>
          <div class="col-md-1">
              <button onclick="descargarReporte();" class="btn btn-primary form-btn">Descargar</button>
          </div>
      </div>
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

  function JSONToCSVConvertor(String, ReportTitle, ShowLabel) {
      //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
      if(String != '' && String != undefined){
          var JSONData = JSON.parse(String);
          var CSV = '';
          CSV += ReportTitle + '\r\n\n';
          if (ShowLabel) {
              var row = "";
              for (var index in Object.keys(JSONData)) {//JSONData[0]) {
                  //Now convert each value to string and comma-separated
                  //row += index + ',';
                  row += Object.keys(JSONData)[index] + ',';
              }
              row = row.slice(0, -1);
              //append Label row with line break
              CSV += row + '\r\n';
          }
          //1st loop is to extract each row
          for (var i = 0; i < Object.keys(JSONData).length; i++) {
              var row = "";
              //2nd loop will extract each column and convert it in string comma-seprated
              for (var data in Object.keys(JSONData)[i]) {
                  //row += '"' + JSONData[i][data] + '",';
                  row += '"' + Object.values(JSONData)[i] + '",';
              }
              row.slice(0, row.length - 1);
              //add a line break after each row
              CSV += row + '\r\n';
          }
          if (CSV == '') {
              alert("Invalid data");
              return;
          }
          var fileName = "eTax__";
          //this will remove the blank-spaces from the title and replace it with an underscore
          fileName += ReportTitle.replace(/ /g, "_");
          //Initialize file format you want csv or xls
          var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
          // Now the little tricky part.
          // you can use either>> window.open(uri);
          // but this will not work in some browsers
          // or you will not get the correct file extension
          //this trick will generate a temp <a /> tag
          var link = document.createElement("a");
          link.href = uri;
          //set the visibility hidden so it will not effect on your web-layout
          link.style = "visibility:hidden";
          link.download = fileName + ".csv";
          //this part will append the anchor tag and remove it after automatic click
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
      }else{
          alert('Sin datos disponibles para generar reporte');
      }
  }

  function descargarReporte(){
      var reporteView = $("#reportes-select").val();
      var descargaReporte = null;
      var nombreReporte = null;
      var nombreMes = null;
      var mes = $("#input-mes").val();
      var ano = $("#input-ano").val();
      if(reporteView) {
          switch (mes) {
              case '0':
                  nombreMes = 'ene-dic';
                  break;
              case '1':
                  nombreMes = 'ene';
                  break;
              case '2':
                  nombreMes = 'feb';
                  break;
              case '3':
                  nombreMes = 'mar';
                  break;
              case '4':
                  nombreMes = 'abr';
                  break;
              case '5':
                  nombreMes = 'may';
                  break;
              case '6':
                  nombreMes = 'jun';
                  break;
              case '7':
                  nombreMes = 'jul';
                  break;
              case '8':
                  nombreMes = 'ago';
                  break;
              case '9':
                  nombreMes = 'set';
                  break;
              case '10':
                  nombreMes = 'oct';
                  break;
              case '11':
                  nombreMes = 'nov';
                  break;
              case '12':
                  nombreMes = 'dic';
              break;
          }
          switch (reporteView) {
              case '/reportes/cuentas-contables':
                  descargaReporte = '/reportes/export-cuentas-contables';
                  nombreReporte = 'cuentas_contables_' + nombreMes + '_' + ano;
                  break;
              case '/reportes/detalle-debito':
                  descargaReporte = '/reportes/export-detalle-debito-fiscal';
                  nombreReporte = 'detalle_debito_fiscal_ + nombreMes + ' + nombreMes + '_' + ano;
                  break;
              case '/reportes/detalle-credito':
                  descargaReporte = '/reportes/export-detalle-credito-fiscal';
                  nombreReporte = 'detalle_credito_fiscal_' + nombreMes + '_' + ano;
                  break;
              case '/reportes/libro-compras':
                  descargaReporte = '/reportes/export-libro-compras';
                  nombreReporte = 'libro_compras_' + nombreMes + '_' + ano;
                  break;
              case '/reportes/libro-ventas':
                  descargaReporte = '/reportes/export-libro-ventas';
                  nombreReporte = 'libro_ventas_' + nombreMes + '_' + ano;
                  break;
              case '/reportes/resumen-ejecutivo':
                  descargaReporte = '/reportes/export-resumen-ejecutivo';
                  nombreReporte = 'resumen_ejecutivo_' + nombreMes + '_' + ano;
                  break;
              /*case '/reportes/reporte-proveedores':
                  descargaReporte = '/reportes/export-reporte-proveedores';
                  nombreReporte = 'reporte_proveedores_' + nombreMes + '_' + ano;
                  break;
              case '/reportes/reporte-clientes':
                  descargaReporte = '/reportes/export-reporte-clientes';
                  nombreReporte = 'reporte_clientes_' + nombreMes + '_' + ano;
                  break;
              case '/reportes/reporte-iva':
                  descargaReporte = '/reportes/export-reporte-iva';
                  nombreReporte = 'reporte_iva_' + nombreMes + '_' + ano;
                  break;*/
              default:
                  alert('En estos momentos este reporte no se encuentra disponible');
          }
          if(descargaReporte != ''){
              jQuery.ajax({
                  url: descargaReporte,
                  type: 'post',
                  cache: false,
                  data: {
                      mes: mes,
                      ano: ano,
                      _token: '{{ csrf_token() }}'
                  },
                  success: function (response) {
                      console.log(typeof response);
                      console.log(response.length);

                      if(response != ''){
                          JSONToCSVConvertor(response, nombreReporte, true);
                      }else{
                          alert('Sin datos disponibles para generar reporte');
                      }
                  },
                  async: true
              });
          }else{
              alert('En estos momentos este reporte no se encuentra disponible');
          }
      }else{
          alert('En estos momentos este reporte no se encuentra disponible');
      }
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

<style>
  
  .iframe-container {
    position: relative;
    width: 67.67rem;
    max-width: 100%;
    padding-bottom: 4300px;
    overflow: hidden;
}
</style>

@endsection
