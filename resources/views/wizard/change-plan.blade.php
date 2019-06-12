@extends('layouts/app')

@section('title') 
  Configuración inicial
@endsection

@section('slug', 'wizard')

@section('header-scripts')

<style>
	
	.cuadro-planes {
	    width: 100%;
	}
	
	.cuadro-planes .tier {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
	    font-size: 1rem;
    	width: 100%;
	}
	
	.cuadro-planes .tier > div {
			-webkit-box-flex: 1;
	    -ms-flex: 1;
	    flex: 1;
	    font-weight: 600;
	    padding: .9rem;
    	margin-bottom: .75rem;
		
	}
	
	.cuadro-planes .opcion {
	    position: relative;
	    color: #fff;
	    background: rgb(120,128,142);
	    background: -webkit-gradient(linear, left top, right top, from(rgba(120,128,142,1)), to(rgba(91,98,111,1)));
	    background: -webkit-linear-gradient(left, rgba(120,128,142,1) 0%, rgba(91,98,111,1) 100%);
	    background: -o-linear-gradient(left, rgba(120,128,142,1) 0%, rgba(91,98,111,1) 100%);
	    background: linear-gradient(90deg, rgba(120,128,142,1) 0%, rgba(91,98,111,1) 100%);
	    text-align: center;
	    border-radius: 15px;
	    margin-left: .75rem;
    	margin-bottom: .75rem;
	    -webkit-transition: .5s ease all;
	    -o-transition: .5s ease all;
	    transition: .5s ease all;
	    cursor: pointer;
	    overflow: hidden;
	}
	
	.cuadro-planes .tier > div.titulo {
		    max-width: 100px;
	}
	
	.cuadro-planes .opcion span {
		position: relative;
		z-index: 1;
		-webkit-transition: .5s ease all;
		-o-transition: .5s ease all;
		transition: .5s ease all;
    font-size: .9rem;
	}
	
	.cuadro-planes .opcion:before {
	    position: absolute;
	    width: 100%;
	    height: 100%;
	    left: 0;
	    top: 0;
	    background: #F1CB61;
	    content: '';
	    opacity: 0;
	    -webkit-transition: .5s ease all;
	    -o-transition: .5s ease all;
	    transition: .5s ease all;
	}
	
	.cuadro-planes .opcion.is-active:before {
	    opacity: 1;
	}
		
	.cuadro-planes .opcion.is-active {
	    color: #333;
	}
	
	.detalle {
	    background: #e5e5e5;
	    padding: 1rem;
	    border-radius: 15px;
	    margin: auto;
	    width: 100%;
	    text-align: center;
	}
	
	.detalle-plan {
	    display: none;
	    margin: auto;
	    text-align: left;
	}
		
	.detalle-plan.is-active {
		display: inline-block;
	}
	
	.plan-feature {
	    font-size: .9rem;
	    margin-bottom: .5rem;
	}
	
	.plan-feature i {
	    padding-right: .5rem;
	    color: #999;
	    text-align: center;
	    width: 25px;
	}
	
	.plan-feature span {
	    font-size: 1rem;
	    font-weight: 400;
	    padding: 0 .2rem;
	    color: #2845A4;
	}
	
	.wizard-popup .form-container {
	    margin-right: auto;
	}
	
	.bigtext {
	    font-size: 1.5rem;
	    color: #000;
	    line-height: 1.1;
	    margin: 1.5rem 0;
	}
	
	.bigtext span {
	    font-weight: bold;
	    color: #2845A4;
	}
	
	.cuadro-planes .opcion span.precio {
		position: absolute;
		top: 0;
		left: 50%;
		width: 50%;
		text-align: 
		-webkit-auto;
		line-height: 1;
		top: 50%;
		-webkit-transform: translateY(-50%);
		    -ms-transform: translateY(-50%);
		        transform: translateY(-50%);
		font-size: .8rem;
		margin-left: 3px;
		padding-left: 3px;
		border-left: 3px solid #000;
		-webkit-transition: .5s ease all;
		-o-transition: .5s ease all;
		transition: .5s ease all;
		opacity: 0;
		
	}
	
	.cuadro-planes .opcion span.precio small {
	    font-weight: 400;
	    display: block;
	}
	
	.cuadro-planes .opcion.is-active span.precio {
	    opacity: 1;
	}
	
	.cuadro-planes .opcion.is-active span:not(.precio) {
	    margin-right: 50%;
	}
	
	.wizard-container .precio-text {
	    background: #d6d4cc;
	    padding: .5rem;
	    font-size: 1.5rem;
	    line-height: 1;
	    border-radius: 5px;
	}
		
	.wizard-container .form-group label {
	    font-size: 1.2rem;
	}
		
	.wizard-container .form-group select {
	    font-size: 1.5rem;
	    line-height: 1.1;
	    height: auto;
	}
	
	.wizard-container .precio-text span {
	    font-size: 2rem;
	    font-weight: bold;
	}
	
	@media screen and (max-width: 600px) {
	
		.cuadro-planes .tier > div {
	    	font-size: .8rem;
		    padding: .75rem .33rem;
		}
		
		.cuadro-planes .opcion {
		    margin-left: .33rem;
		}
		
		.cuadro-planes .tier > div.titulo {
		    max-width: 80px;
		}
		
		.cuadro-planes .opcion span.precio {
		    left: 55%;
		}
		
		.cuadro-planes .tier > div.titulo {
		    max-width: none;
		    width: 100%;
    		font-size: 1.5rem;
    		margin-bottom: 0;
    		padding-bottom: 0;
		}
		
		.cuadro-planes .tier {
		    -ms-flex-wrap: wrap;
		        flex-wrap: wrap;
		}
		
		.cuadro-planes .tier > div {
		    max-width: 30%;
		    width: 30%;
		    -webkit-box-flex: 1;
		        -ms-flex: auto;
		            flex: auto;
		    margin-right: 1%;
		}
		
		.cuadro-planes .opcion.is-active span:not(.precio) {
		    margin: 0;
		}
		
		.cuadro-planes .opcion span.precio {
		    position: relative;
		    display: block;
		    left: auto;
		    top: auto;
		    text-align: center;
		    border: 0;
		    width: 100%;
		    margin: 0;
		    padding: 0;
		    -webkit-transform: none;
		        -ms-transform: none;
		            transform: none;
		}
		
		.cuadro-planes .opcion span {
		    display: block;
		}
		
		.bigtext {
		    font-size: 1.3rem;
		    color: #000;
		    line-height: 1.1;
		    margin: 1.5rem 0;
		}
		
		.wizard-container .btn-holder {
		    font-size: .8rem;
		}

	}
	
	@media screen and (max-width: 380px) {
		.wizard-container .btn-holder a{
		    font-size: .8rem;
		}
		
		.wizard-container .btn-holder .btn {
		    width: 100%;
		}
	}
	
</style>

@endsection

@section('content') 
<div class="wizard-container">
  <div class="wizard-popup">
  	
   	<div class="titulo-bienvenida">
    	<h2>¡Bienvenido a eTax!</h2>
    	<p>En poco tiempo podrá utilizar la herramienta más fiable para el cálculo de IVA y facturación electrónica.</p>
    </div>
    
    <div class="form-container">
    
	    <form method="POST" action="/usuario/confirmar-plan" class="wizard-form" enctype="multipart/form-data">

	       @csrf
				
				<div class="step-section step1 is-active">
			      <div class="form-row">
			        <div class="form-group col-md-12">
							  <h3 class="mt-0">
							    Confirme su plan
							  </h3>
							</div>
							
							<div class="form-group col-md-6">
							  <label for="plan-sel">Plan </label>
							  <select class="form-control " name="plan-sel" id="plan-sel" onchange="togglePlan();">
							  	<option value="p" selected>Profesional</option>
							  	<option value="e">Empresarial</option>
							  	<option value="c">Contador</option>
							  </select>
							</div>
							
							<div class="form-group col-md-6 hide-contador">
							  <label for="product_id">Plan </label>
							  <select class="form-control " name="product_id" id="product_id" onchange="toggleProducto();">
							  	<option class="p" value="1" precio="9.99">Básico</option>
							  	<option class="p" value="2" precio="12.99">Intermedio</option>
							  	<option class="p" value="3" precio="19.99">Pro</option>
							  	<option class="e" value="4" precio="32.99">Básico</option>
							  	<option class="e" value="5" precio="82.99">Intermedio</option>
							  	<option class="e" value="6" precio="124.99">Pro</option>
							  	<option class="c" value="7" precio="124.99">Contador</option>
							  </select>
							</div>
							
							<div class="form-group col-md-12 mt-4">
								<span class="precio-text">
									Desde <span id="precio-plan">9.99</span>
								</span>
			        </div>
			        
			        <div class="form-group col-md-12">
				        <div class="bigtext">
				        	Elija el plan de su conveniencia y empiece a calcular el IVA con eTax. Disfrute de una <span>prueba gratis</span> válida hasta el 14 de Junio.
				        </div>
			        </div>
			        
			        <div class="btn-holder">
			        	<a class="btn btn-primary btn-prev" target="_blank" href="https://etaxcr.com/planes">Ver detalle de planes</a>
  							<button type="submit" id="btn-submit" class="btn btn-primary btn-next" >Confirme su plan</button>
							</div>
	
			      </div>
				</div>				
	
	    </form>
	  </div>  
	  
  </div>
</div>  

@endsection



@section('footer-scripts')

<script>
	
	function togglePlan() {
		var planId = $("#plan-sel").val();
    $("#product_id option").hide();
    $("#product_id ."+planId).show();
    $("#product_id").val( $("#product_id ."+planId).first().val() );
    toggleProducto();
  }
  
  function toggleProducto() {
  	var precio = $('#product_id :selected').attr('precio');
    $("#precio-plan").text(precio);
  }
  
  togglePlan();01
  
</script>

@endsection