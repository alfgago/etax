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
	        font-size: 13px !important;
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
			    max-width: 1200px;
			    width: 100%;
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
			    text-align: center;
			}
			
			td input {
				width: 100px;
				background: #f5f5f5;
				padding: 3px;
			}
			
			.card-title {
			    font-size: 2rem;
			    margin-bottom: 3rem;
			    border-bottom: 0.45rem solid #F0C962;
			    display: inline-block;
			}
			
			.card-subtitle {
			    font-size: 1.2rem;
			    margin-bottom: 1.25rem;
			    margin-top: 3rem;
			    border-bottom: 0.45rem solid #ccc;
			    display: inline-block;
			}
			
			tr.header-tarifas {
			    text-align: center;
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
        
        <a class='btn btn-imprimir' onclick='window.print();return false;'> <i class="fa fa-print" style="margin-top: -75px; margin-right: 10px;" aria-hidden="true"></i> Imprimir reporte</a>
        
        <div class='print-content'  style="">
        	<div class="container-fluid" >
						<div class="row">
							<h1 class="card-title">Declaración de IVA {{ $nombreMes }} {{ $ano }} </h1>
						  <div class="col-sm-12 pl-0 pr-0">
						  	
						  	@foreach( $arrayActividades as $act )
						  		<h3 class="card-subtitle">ACTIVIDAD COMERCIAL: {{ $act->codigo }} - {{ $act->actividad }}</h3>
						  		@include('Reports.widgets.borrador-iva-loop', ['actividad' => $act->codigo, 'data' => $data])	
						  	@endforeach
						  	
						  	<h3 class="card-subtitle sub2">ESTIMACIÓN Y LIQUIDACIÓN ANUAL DE LA PROPORCIONALIDAD</h3>
						  	
						  	<table class="text-12 text-muted m-0 p-2 ivas-table bigtext borrador-presentacion" style="width:100%;">
									<tbody>
										<?php
											$ivaData = json_decode($data->iva_data);
										?>
											<tr class="macro-title">
										    <th colspan="6">Estimación del porcentaje final de la regla de proporcionalidad</th>
										  </tr>
										  <tr class="header-tarifas">
										    <th>Rubro</th>
										    <th colspan="5"> Monto </th>
										  </tr>
										  <tr>
										    <th>Monto anual de ventas con derecho a crédito fiscal aplicados</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->numerador_prorrata, 0) }}" > </td>
										  </tr>
										  <tr>
										    <th>Monto anual de ventas con derecho y sin derecho a crédito fiscal</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->invoices_subtotal, 0) }}" > </td>
										  </tr>
										  <tr>
										    <th>Porcentaje a aplicar como liquidación final</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->prorrata*100, 0) }}%" > </td>
										  </tr>
										  
										  
											<tr class="macro-title">
										    <th colspan="6">Liquidación final de la regla de la proporcionalidad</th>
										  </tr>
										  <tr class="header-tarifas">
										    <th>Rubro</th>
										    <th colspan="5"> Monto </th>
										  </tr>
										  <tr>
										    <th>Crédito fiscal anual sobre el que se aplica el porcentaje de proporcionalidad</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->total_bill_iva, 0) }}" > </td>
										  </tr>
										  <tr>
										    <th>Crédito fiscal generado por aplicación final del porcentaje de proporcionalidad</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->iva_deducible_estimado, 0) }}" > </td>
										  </tr>
										  
										  
											<tr class="macro-title">
										    <th colspan="6">Crédito aplicado de enero a la fecha de la liquidación final según regla de proporcionalidad</th>
										  </tr>
										  <tr class="header-tarifas">
										    <th>Rubro</th>
										    <th colspan="5"> Monto </th>
										  </tr>
										    <th>Saldo a favor en aplicación del porcentaje de la liquidación final</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->iva_por_cobrar, 0) }}" > </td>
										  </tr>
										  <tr>
										    <th>Saldo deudor en aplicación del porcentaje de la liquidación final</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $acumulado->iva_por_pagar, 0) }}" > </td>
										  </tr>
										  
										  
											<tr class="macro-title">
										    <th colspan="6">Determinación del impuesto por operaciones gravadas del periodo</th>
										  </tr>
										  <tr class="header-tarifas">
										    <th>Rubro</th>
										    <th colspan="5"> Monto </th>
										  </tr>
										 	<tr>
										    <th>Impuesto generado por operaciones gravadas</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->total_invoice_iva, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Total de créditos del periodo</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_deducible_operativo, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Devolución del IVA por servicios de salud privada pagados con tarjeta de crédito y/o débito</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( 0, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Saldo a favor del periodo</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_cobrar, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Impuesto neto del periodo (saldo deudor)</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_pagar, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Saldo a favor en aplicación del porcentaje de la liquidación final</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_cobrar, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Saldo deudor en aplicación del porcentaje de la liquidación final</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->iva_por_pagar, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Saldo a favor final</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->saldo_favor, 0) }}" > </td>
										  </tr>
										 	<tr>
										    <th>Impuesto final</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->numerador_prorrata, 0) }}" > </td>
										  </tr>
										  
										  
											<tr class="macro-title">
										    <th colspan="6">Liquidación deuda tributaria</th>
										  </tr>
										  <tr class="header-tarifas">
										    <th>Rubro</th>
										    <th colspan="5"> Monto </th>
										  </tr>
										    <th>Retenciones pagos a cuenta del impuesto</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->numerador_prorrata, 0) }}" > </td>
										  </tr>
										  <tr>
										    <th>Saldo a favor de periodos anteriores</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->sum_repercutido_exento_sin_credito, 0) }}" > </td>
										  </tr>
										  <tr>
										    <th>Solicito compensar con crédito a mi favor por el monto de:</th>
										    <td colspan="5"> <input style="width:100%;" type="text" readonly value="{{ number_format( $data->sum_repercutido_exento_sin_credito, 0) }}" > </td>
										  </tr>
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
