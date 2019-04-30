<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <!-- CSRF Token -->
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resumen ejecutivo | eTax </title>
  

    <link rel="stylesheet" href="{{asset('assets/styles/vendor/select2.min.css')}}">
    <link rel="stylesheet" href="{{mix('assets/styles/css/themes/eva.min.css')}}?v=2.1">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/perfect-scrollbar.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    
    <script src="https://app.calculodeiva.com/assets/js/common-bundle.js?v=2.4"></script>
		<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
		<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
</head>
	
	<style>
	    
	    html, body {
	        font-size: 16px !important;
	    }
			
			body * {
			    visibility: visible;
			}
	    
			.print-page {
			    position: relative;
			    background: #fff;
			}
			
			.print-content {
			    position: relative;
			    background: #fff;
			}
			
			.btn-imprimir {
			    position: absolute;
			    top: 0;
			    right: 0;
			    z-index: 9;
			    color: #fff !important;
			    background: #333;
			    padding: .5rem 1rem;
			    cursor: pointer;
			}
			
			body {
			    max-width: 1000px;
			    margin: auto;
			    padding: 0;
			}
			
			html:before {
			    display: none;
			}
			
			html {
			    background: #fff;
			}
			
			.sidebar-dashboard {
			    max-width: none !important;
			}
			
			.mb-8 {
				margin-bottom: 3rem;
			}
        
      @media print {
      		body {
					    padding: 3rem .5rem;
					}
      	
          .print-page {
              padding: 0;
          }
      
          .print-content {
              max-width: 100%;
              -webkit-print-color-adjust: exact;
          }
          
          .print-content:before {
              -webkit-print-color-adjust: exact;
          }
      
          .dashboard-row,
          .reporte-subpaso {
              page-break-inside: avoid;
          }
          
          .btn-imprimir {
              display: none;
          }
      }
        
	</style>
	
</head>
<body>
    
    <div class='print-page'>
        
        <a class='btn btn-imprimir' onclick='window.print();return false;'> <i class="fa fa-print" style="margin-right: 10px;" aria-hidden="true"></i> Imprimir reporte</a>
        
        <div class='print-content'>
        	<div class="container-fluid">
						<div class="row">
						  <div class="col-md-12">
						    
						    <div class="row">
						      
						      <div class="col-12 mb-8">
						        @include('Reports.widgets.grafico-mensual', ['titulo' => "Resumen de IVA $ano"])
						      </div>
						      
						      <div class="col-6 mb-8">
						        @include('Reports.widgets.proporcion-porcentajes', ['titulo' => "Porcentaje de ventas del $ano por tipo de IVA", 'data' => $acumulado])
						      </div>
						      
						      <div class="col-6 mb-8">
						        
						        @include('Reports.widgets.grafico-prorrata', ['titulo' => 'Prorrata operativa vs prorrata estimada', 'data' => $acumulado])
						        
						      </div>
						      
						    </div>
						    
						  </div>
						  
						  <div class=" col-md-12">
						  	
						    <div class="row">
						      
						      <div class="col-6 mb-8">
						        @include('Reports.widgets.resumen-periodo', ['titulo' => "$nombreMes $ano", 'data' => $dataMes])
						      </div>
						      
						      <div class="col-6 mb-8">
						        @include('Reports.widgets.resumen-periodo', ['titulo' => "Acumulado $ano", 'data' => $acumulado])
						      </div>
						      
						    </div> 
						   
						  </div>
						  
						</div>
          </div>  
        </div>
        
    </div>

    <script src="/assets/js/ubicacion.js"></script>
    <script src="/assets/js/vendor/tagging.min.js"></script>
    <script src="{{asset('assets/js/es5/script.js')}}"></script>
    
</body>
</html>
