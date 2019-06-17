@extends('layouts/app')

@section('title') 
  Nueva empresa
@endsection

@section('slug', 'wizard')

@section('header-scripts')

<style>

</style>

@endsection

@section('content') 
<div class="wizard-container">
  <div class="wizard-popup">
  	
   	<div class="titulo-bienvenida">
    	<h2>Registro de nueva empresa ¡Gracias por confiar en eTax!</h2>
    </div>
    	
	<div class="wizard-steps">
			<div id="step1" class="step-btn step1 is-active" onclick="toggleStep(id);"><span>Información básica</span></div>
			<div id="step2" class="step-btn step2" onclick="toggleStep(id);"><span>Ubicación</span></div>
			<div id="step3" class="step-btn step3" onclick="toggleStep(id);"><span>Facturación electrónica</span></div>
			<div id="step4" class="step-btn step4" onclick="toggleStep(id);"><span>Certificado ATV</span></div>
			<div id="step5" class="step-btn step5" onclick="toggleStep(id);"><span>Prorrata</span></div>
	</div>
    
    <div class="form-container">
    
	    <form method="POST" action="/store-wizard" class="wizard-form" enctype="multipart/form-data">

	        @csrf
				
			<div class="step-section step1 is-active">
		      <div class="form-row">
		        @include('wizard.pasos.paso1')
		      </div>
			</div>
				
			<div class="step-section step2">
		      <div class="form-row">
		        @include('wizard.pasos.paso2')
		      </div>
			</div>
	
			<div class="step-section step3">
		      <div class="form-row">
		        @include('wizard.pasos.paso3')
		      </div>
			</div>
				
			<div class="step-section step4">
		      <div class="form-row">
		        @include('wizard.pasos.paso4')
		      </div>
			</div>	
				
			<div class="step-section step5">
		      <div class="form-row">
		        @include('wizard.pasos.paso5')
		      </div>
			</div>				
	
	    </form>
	  </div>  
	  
  </div>
</div>  

@endsection



@section('footer-scripts')

<script>
	
	function toggleStep(id) {
	    var fromId = $('.step-btn.is-active').attr('id');
	    var allow = checkEmptyFields(fromId);
	    
	    if( allow )	{
    		$('.step-section, .step-btn').removeClass('is-active');
    		$('.'+id).addClass('is-active');
    		$('.wizard-container').prop('class', id+'-selected wizard-container');
	    }
	}

    function checkEmptyFields(id) {
        var allow = true;
        $('.'+id+' .checkEmpty').each( function() {
    		if( $(this).val() && $(this).val() != "" ) {
    		    $(this).removeClass('isEmptyRequired');
    		}
    		else {
    		    $(this).addClass('isEmptyRequired');
    		    allow = false;
    		}
    	});
    	return allow;
    }
    
    function toggleTipoProrrata() {
	  var metodo = $("#first_prorrata_type").val();
		  $( ".toggle-types" ).hide();
		  $( ".type-"+metodo ).show();
		}
    
    $( document ).ready(function() {  
        fillProvincias();
        toggleTipoProrrata();
        toggleApellidos();
    });
  
    
  
</script>

@endsection