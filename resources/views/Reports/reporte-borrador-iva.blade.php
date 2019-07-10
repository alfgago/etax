<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <!-- CSRF Token -->
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resumen ejecutivo | eTax </title>
  
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tippy.js/3.4.1/tippy.css" />
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/select2.min.css')}}">
    <link rel="stylesheet" href="{{mix('assets/styles/css/themes/eva.min.css')}}?v=8">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/perfect-scrollbar.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    
    <script src="https://app.calculodeiva.com/assets/js/common-bundle.js?v=8"></script>
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
			
			.ivas-table.bigtext tbody th:first-of-type {
			    border-bottom: .25rem solid #fff;
			}
			
			.ivas-table td {
			    border: 0;
			}
			
			.ivas-table.bigtext tbody th:first-of-type:after {
			    display: none;
			}
			
			tr.macro-title th {
			    background: #000000 !important;
			    color: #fff !important;
			    text-transform: uppercase;
			}
			
			
			tr.sub-title th {
			    background: #737ebf !important;
			    color: #fff !important;
			    text-transform: uppercase;
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
							
						  <div class="col-sm-12">
			          <table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion">
			            <thead>
			              <tr>
			                <th>Rubro</th>
			                <th>Monto</th>
			              </tr>
			            </thead>
			            <tbody>
			            	
										@foreach( \App\ProductCategory::all() as $tipo )
										<?php
											$varName = "type$tipo->id";
											$ivaData = json_decode($data->iva_data);
										?>
										@if($loop->index == 0)
											<tr class="macro-title">
				                <th colspan="2">TOTAL DE VENTAS , SUJETAS, EXENTAS Y NO SUJETAS</th>
				              </tr>
				              <tr class="sub-title">
				                <th colspan="2">BIENES Y SERVICIOS AFECTOS AL 1%</th>
				              </tr>
										@endif
										@if($loop->index == 5)
											<tr class="sub-title">
				                <th colspan="2">BIENES Y SERVICIOS AFECTOS AL 2%</th>
				              </tr>
										@endif
										@if($loop->index == 9)
											<tr class="sub-title">
				                <th colspan="2">BIENES Y SERVICIOS AFECTOS AL 4%</th>
				              </tr>
										@endif
										@if($loop->index == 14)
											<tr class="sub-title">
				                <th colspan="2">BIENES Y SERVICIOS AFECTOS AL 13%</th>
				              </tr>
										@endif
										@if($loop->index == 19)
											<tr class="sub-title">
				                <th colspan="2">TOTAL OTROS RUBROS A INCLUIR EN LA BASE IMPONIBLE</th>
				              </tr>
										@endif
										@if($loop->index == 21)
											<tr class="sub-title">
				                <th colspan="2">VENTAS EXENTAS</th>
				              </tr>
										@endif
										@if($loop->index == 38)
											<tr class="sub-title">
				                <th colspan="2">VENTAS AUTORIZADAS SIN IMPUESTO (órdenes especiales y otros transitorios)</th>
				              </tr>
										@endif
										@if($loop->index == 45)
											<tr class="sub-title">
				                <th colspan="2">VENTAS A NO SUJETOS</th>
				              </tr>
										@endif
										@if($loop->index == 48)
											<tr class="macro-title">
				                <th colspan="2">TOTAL DE COMPRAS</th>
				              </tr>
				              <tr class="sub-title">
				                <th colspan="2">Compras de bienes y servicios locales utilizados en operaciones sujetas y no exentas</th>
				              </tr>
										@endif
										@if($loop->index == 51)
											<tr class="sub-title">
				                <th colspan="2">Importaciones de bienes y adquisición de servicios del exterior utilizadas en operaciones sujetas y no exentas</th>
				              </tr>
										@endif
										@if($loop->index == 54)
											<tr class="sub-title">
				                <th colspan="2">Compras sin derecho a crédito fiscal</th>
				              </tr>
										@endif
										
										
			              <tr>
			                <th>{{ $tipo->name }}</th>
			                <td>{{ $ivaData->$varName }}</td>
			              </tr>
			              @endforeach
			            </tbody>
			          </table>
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
