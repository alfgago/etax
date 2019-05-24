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
	
	.cuadro-planes .opcion span {
		position: relative;
		z-index: 1;
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
							
							<input id="plan_id" name="plan_id" type="hidden" value="1">
							
							<div class="cuadro-planes">
			            
			            <div class="tier">
			            	<div class="titulo"><span>Profesional</span></div>
			            	<div class="opcion opcion-1 is-active" val="1" onclick="toggleOpcion(1);"><span>Básico</span></div>
			            	<div class="opcion opcion-2" val="2" onclick="toggleOpcion(2);"><span>Intermedio</span></div>
			            	<div class="opcion opcion-3" val="3" onclick="toggleOpcion(3);"><span>Pro</span></div>
			            </div>
			            
			            <div class="tier">
			            	<div class="titulo"><span>Empresarial</span></div>
			            	<div class="opcion opcion-4" val="4" onclick="toggleOpcion(4);"><span>Básico</span></div>
			            	<div class="opcion opcion-5" val="5" onclick="toggleOpcion(5);"><span>Intermedio</span></div>
			            	<div class="opcion opcion-6" val="6" onclick="toggleOpcion(6);"><span>Pro</span></div>
			            </div>
			            
			            <div class="tier">
			            	<div class="titulo"><span>Contador</span></div>
			            	<div class="opcion opcion-7" val="7" onclick="toggleOpcion(7);"><span>Básico</span></div>
			            	<div class="opcion opcion-8" val="8" onclick="toggleOpcion(8);"><span>Intermedio</span></div>
			            	<div class="opcion opcion-9" val="9" onclick="toggleOpcion(9);"><span>Pro</span></div>
			            </div>
			          
			        </div>
			        
			        <div class="detalle">
			        	
				        <div class="detalle-plan dp-1 is-active">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5</span> facturas emitidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>40</span> facturas recibidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>1 usuario</div>
				        </div>
				        
				        <div class="detalle-plan dp-2">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>25</span> facturas emitidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>200</span> facturas recibidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>1 usuario</div>
				        </div>
				        
				        <div class="detalle-plan dp-3">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>50</span> facturas emitidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>400</span> facturas recibidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 2 usuario</div>
				        </div>
				        
				        <div class="detalle-plan dp-4">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>250</span> facturas emitidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas recibidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 2 usuarios</div>
				        </div>
				        
				        <div class="detalle-plan dp-5">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>2000</span> facturas emitidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas recibidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 4 usuarios</div>
				        </div>
				        
				        <div class="detalle-plan dp-6">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5000</span> facturas emitidas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas recibidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 10 usuarios</div>
				        </div>
				        
				        <div class="detalle-plan dp-7">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas emitidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas recibidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 10 empresas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 2 usuarios por empresa</div>
				        </div>
				        
				        <div class="detalle-plan dp-8">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas emitidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas recibidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 25 empresas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 5 usuarios por empresa</div>
				        </div>
				        
				        <div class="detalle-plan dp-9">
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas emitidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Facturas recibidas <span>ilimitadadas</span></div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 50 empresas</div>
					        <div class="plan-feature"><i class="fa fa-caret-right" aria-hidden="true"></i>Hasta 10 usuarios por empresa</div>
				        </div>
			        
			        </div>
			        
			        <div class="btn-holder">
  							<button type="submit" id="btn-submit" class="btn btn-primary btn-next" >Confirmar e iniciar periodo de pruebas</button>
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
	
	function toggleOpcion(plan) {
    $(".detalle-plan, .opcion").removeClass("is-active");
    $(".dp-"+plan+", .opcion-"+plan).addClass("is-active");
    $("#plan_id").val(plan);
  }
  
</script>

@endsection