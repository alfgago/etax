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
    <link rel="stylesheet" href="{{mix('assets/styles/css/themes/eva.min.css')}}?v=9">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/perfect-scrollbar.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    
    <script src="https://app.calculodeiva.com/assets/js/common-bundle.js?v=9"></script>
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
					font-size: 1.2rem !important;
			    background: #000000 !important;
			    color: #fff !important;
			    text-transform: uppercase;
			    font-weight: 400 !important;
			}
			
			
			tr.sub-title th {
					font-size: 1.2rem !important;
    			background: #7e88a9!important;
			    color: #fff !important;
			    text-transform: uppercase;
			    text-align: center;
			    font-weight: 400 !important;
			}
			
			tr.sub-title th  span {
			    color: #fff !important;
			}
			
			td input {
				width: 100%;
				background: #f5f5f5;
				padding: 3px;
			}
			
			.card-title {
			    font-size: 2.75rem;
			    margin-bottom: 2rem;
			    border-bottom: 0.45rem solid #F0C962;
			    display: inline-block;
			}
			
			h2.card-subtitle {
			    font-size: 2.1rem;
			    margin-bottom: 1rem;
			    margin-top: 2rem;
			    display: inline-block;
			}
			
			h3.card-subtitle {
			    font-size: 2rem;
			    margin-bottom: 1.25rem;
			    margin-top: 1rem;
			    border-bottom: 0.45rem solid #ccc;
			    display: inline-block;
			    margin-bottom: .5rem !important;
			    border: 0;
			    padding: .5rem 1rem;
			    background: #000;
			    color: #fff;
			    width: 100%;
			}
			
			.declaracion-content {
				border: 5px solid #ccc;
				padding: .5rem;
				margin-top: .5rem;
			}

			tr.header-tarifas {
			    text-align: center;
			}
		
			span.marcar {
			    position: relative;
			    font-size: .9rem;
			}
			
			span.marcar span {
			    position: relative;
			    margin-right: 3px;
			    margin-left: 10px;
			    font-weight: 400;
			}
			
			span.marcar span:before {
			    width: .75em;
			    height: .75em;
			    display: inline-block;
			    content: '';
			    margin-right: 3px;
			    border: 1px solid #fff;
			    background: #ddd;
			    border-radius: 50%;
			}
			
			.desplegar-true span.marcar span.si:before,
			.desplegar-false span.marcar span.no:before {
			    background: #F0C962;
			    border-color: #F0C962;
			    width: 1em;
			    height: 1em;
			    position: relative;
			    top: 1px;
			}
			
			tr.sub-title.desplegar-false {
			    display: table-row;
			}
			
			.desplegar-false {
			    display: none;
			}
			
			.borrador-presentacion th,
			.borrador-presentacion td {
			    border-bottom: .25rem solid #fff;
			}
			
			.borrador-presentacion th {
			    text-align: left !important;
			}
			
			tr.sub-title th.marcar-td {
    			background: #5c6278!important;
			    text-align: center !important;
			}
			
			.ivas-table.bigtext th {
			    padding: .75rem 1rem !important;
			}
			
			
			.borrador-presentacion input, .true input {
			    background: #f5f5f5;
			    border: 1px solid #000;
			    padding: .25rem 1em;
			}
			
			.false {
			    background: #eee;
			}
			
			.false input {
			    opacity: 0;
			    visibility: hidden;
			}
			
			.true-blocked input {
			    background: #ddd;
			    border: 1px solid #000;
			}
						
			.borrador-presentacion .macro-title th,
			.borrador-presentacion.ivas-table.bigtext th.posrel {
			    position: relative !important;
			    padding-right: 12rem !important;
			}
			
			.borrador-presentacion .macro-title input,
			.borrador-presentacion .sub-title input {
			    position:absolute;
			    width: 11rem;
			    top: 50%;
			    transform: translateY(-50%);
			    right: .4rem;
			}
			
      table tr
			table tr td,
			table tr th {
				page-break-inside: avoid;
			}
					
					
			.borrador-presentacion .macro-title th.marcar-td {
			    text-align: center !important;
			    padding-right: 1rem !important;
			    background: #333!important;
			}
			
			.borrador-presentacion .macro-title th.marcar-td span {
			    color: #fff;
			}
			
			tr.macro-title.inner th {
			    background: #444!important;
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
          
          table tr
					table tr td,
					table tr th {
						page-break-inside: avoid;
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
						  	
						  	<h2 class="card-subtitle">Consolidado de compras y ventas</h2>
						  	@foreach( $actividadDataArray as $actividad )
						  		<div class="declaracion-content">
						  			<h3 class="card-subtitle m-0">Actividad comercial: {{ $actividad['codigo'] }} - {{ $actividad['titulo'] }}</h3>
						  			@include('Reports.widgets.declaracion.loop-actividades', ['actividad' => $actividad])	
						  		</div>
						  	@endforeach
						  	
						  	<h2 class="card-subtitle sub2">Impuesto por ventas y transacciones sujetas</h2>
						  	@include('Reports.widgets.declaracion.ivas-ventas', ['data' => $data])	
						  	
						  	<h2 class="card-subtitle sub2">Créditos fiscales generados por compras</h2>
						  	@include('Reports.widgets.declaracion.ivas-compras', ['data' => $data])	
						  	
						  	<h2 class="card-subtitle sub2">Estimación y liquidación anual de la proporcionalidad</h2>
						  	@include('Reports.widgets.declaracion.estimacion-liquidacion', ['data' => $data])	
	
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
