@extends('layouts/app')

@section('title') 
  Cambio de plan
@endsection

@section('slug', 'wizard')

@section('header-scripts')

<style>
	
	.wizard-popup .form-container {
	    margin-right: auto;
	}
	
	.bigtext {
	    font-size: 1.3rem;
	    color: #000;
	    line-height: 1.1;
	    margin: 1.5rem 0;
	}
	
	.bigtext span {
	    font-weight: bold;
	    color: #2845A4;
	}
	
	.wizard-container .precio-text {
	    background: #d6d4cc;
    	padding: .75rem 1.5rem;
	    font-size: 1.3rem;
	    line-height: 1;
	    border-radius: 5px;
	    display: block;
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
	    font-size: 1.75rem;
	    font-weight: bold;
	}
	
	@media screen and (max-width: 600px) {
		
		.bigtext {
		    font-size: 1.3rem;
		    color: #000;
		    line-height: 1.1;
		    margin: 1.5rem 0;
		}
		
		.wizard-container .btn-holder a{
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
    
	    <form method="POST" action="/confirmar-plan" class="wizard-form" enctype="multipart/form-data">

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
							  <label for="product_id">Tipo </label>
							  <select class="form-control " name="product_id" id="product_id" onchange="togglePrice();">
							  	<option class="p" value="1" monthly="{{ $plans[0]->plan->monthly_price }}" six="{{ $plans[0]->plan->six_price * 6 }}" annual="{{ $plans[0]->plan->annual_price * 12 }}">Básico</option>
							  	<option class="p" value="2" monthly="{{ $plans[1]->plan->monthly_price }}" six="{{ $plans[1]->plan->six_price * 6 }}" annual="{{ $plans[1]->plan->annual_price * 12 }}" >Intermedio</option>
							  	<option class="p" value="3" monthly="{{ $plans[2]->plan->monthly_price }}" six="{{ $plans[2]->plan->six_price * 6 }}" annual="{{ $plans[2]->plan->annual_price * 12 }}" >Pro</option>
							  	<option class="e" value="4" monthly="{{ $plans[3]->plan->monthly_price }}" six="{{ $plans[3]->plan->six_price * 6 }}" annual="{{ $plans[3]->plan->annual_price * 12 }}" >Básico</option>
							  	<option class="e" value="5" monthly="{{ $plans[4]->plan->monthly_price }}" six="{{ $plans[4]->plan->six_price * 6 }}" annual="{{ $plans[4]->plan->annual_price * 12 }}" >Intermedio</option>
							  	<option class="e" value="6" monthly="{{ $plans[5]->plan->monthly_price }}" six="{{ $plans[5]->plan->six_price * 6 }}" annual="{{ $plans[5]->plan->annual_price * 12 }}" >Pro</option>
							  	<option class="c" value="7" monthly="{{ $plans[6]->plan->monthly_price }}" six="{{ $plans[6]->plan->six_price * 6 }}" annual="{{ $plans[6]->plan->annual_price * 12 }}" >Pro</option>
							  </select>
							</div>
							
							<div class="form-group col-md-6">
							  <label for="recurrency">Recurrencia de pagos </label>
							  <select class="form-control " name="recurrency" id="recurrency" onchange="togglePrice();">
							  	<option value="1" selected>Mensual</option>
							  	<option value="6">Semestral</option>
							  	<option value="12">Anual</option>
							  </select>
							</div>
							
							<div class="form-group col-md-12 mt-4">
								<span class="precio-text">
									Precio de $<span id="precio-plan">9.99</span> <span id="recurrencia-text">/ mes</span>
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
    togglePrice();
  }
  
  function togglePrice() {
  	var recurrency = $('#recurrency :selected').val();
  	if( recurrency == 1 ) {
	  	var precio = $('#product_id :selected').attr('monthly');
	  	var rtext = '/ mes';
  	}else if( recurrency == 6 ) {
	  	var precio = $('#product_id :selected').attr('six');
	  	var rtext = '/ semestre';
  	}else if( recurrency == 12 ) {
	  	var precio = $('#product_id :selected').attr('annual');
	  	var rtext = '/ año';
  	}
  	$("#precio-plan").text(precio);
  	$("#recurrencia-text").text(rtext);
  }
  
  togglePlan();
  
</script>

@endsection