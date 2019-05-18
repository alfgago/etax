@extends('layouts/app')

@section('title') 
  Configuración inicial
@endsection

@section('header-scripts')


<style>
	
.wizard-container {
    position: fixed;
    display: flex;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
}

.wizard-popup {
    max-width: 750px;
    height: auto;
    margin: auto;
    margin-top: 4rem;
    position: relative;
}

.wizard-container:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #2852A6 0%, #392993 100%);
    opacity: 0.9;
}

.wizard-container .btn-holder {
	display: flex;
}

.wizard-popup .form-container {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    max-height: calc(95vh - 11.5rem);
    height: auto;
    margin-right: 12rem;
    overflow-y: auto;
}

.titulo-bienvenida {
    padding: 0 2rem;
    padding-bottom: 1rem;
}

.titulo-bienvenida h2 {
    font-size: 2rem;
    color: #fff;
    font-weight: bold;
}

.titulo-bienvenida p {
    color: #fff;
}

.breadcrumb {
    display: none;
}

.wizard-steps {
    position: absolute;
    display: block;
    counter-reset: step-counter;
    margin: 0;
    font-weight: bold;
    margin-bottom: 1rem;
    right: 0;
}

.wizard-steps .step-btn {
    flex: 1;
    text-align: center;
    position: relative;
    margin: auto;
    margin-bottom: 1.5rem;
}

.wizard-steps .step-btn span {
    position: absolute;
    text-align: left;
    color: #fff;
    top: 50%;
    transform: translateY(-50%);
    line-height: 1.5;
    font-weight: 400;
}

.wizard-steps .step-btn:after {
    position: relative;
    content: counter(step-counter);
    counter-increment: step-counter;
    width: 3rem;
    height: 3rem;
    top: 0;
    left: 0;
    background: #eee;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.5rem;
    color: #333;
    font-weight: bold;
    z-index: 1;
    transition: .5s ease all;
    margin-right: 6rem;
    border: 3px solid #fff;
}

.wizard-steps .step-btn:before {
    position: absolute;
    content: '';
    width: 7px;
    height: 5rem;
    bottom: 50%;
    left: 20.0px;
    background: #eee;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.5rem;
    color: #eee;
    font-weight: bold;
    z-index: 0;
    transition: .5s ease all;
    border: 2px solid #fff;
}

.wizard-steps .step-btn:first-of-type:before {
    display: none;
}

.wizard-steps .step-btn.is-active:after,
.wizard-steps .step-btn.is-active:before,
.step2-selected .step1:before,
.step2-selected .step1:after,
.step3-selected .step1:before,
.step3-selected .step1:after,
.step3-selected .step2:before,
.step3-selected .step2:after,
.step4-selected .step1:before,
.step4-selected .step1:after,
.step4-selected .step2:before,
.step4-selected .step2:after,
.step4-selected .step3:before,
.step4-selected .step3:after,
.step5-selected .step1:before,
.step5-selected .step1:after,
.step5-selected .step2:before,
.step5-selected .step2:after,
.step5-selected .step3:before,
.step5-selected .step3:after,
.step5-selected .step4:before,
.step5-selected .step4:after{
    background: #F0C962;
    color: #333;
}

.wizard-form .step-section {
	display: none;	
}

.wizard-form .step-section.is-active {
	display: block;
}

.wizard-container .btn-holder {
	display: block;
	width: 100%;
}

.wizard-container .btn-holder .btn-next {
    float: right;
}

.wizard-container .btn-holder .btn-prev {
    float: left;
    background: #fff;
    border-color: #15408E;
    color: #15408E;
}

.form-button {
    display: block;
    margin: 0;
    padding: 0.25rem 0.5rem;
    font-size: 0.9rem;
    height: calc(1.9695rem + 2px);
}
	
</style>

@endsection

@section('content') 
<div class="wizard-container">
  <div class="wizard-popup">
  	
   	<div class="titulo-bienvenida">
    	<h2>¡Bienvenido a eTax!</h2>
    	<p>En poco tiempo podrá utilizar la herramienta más fiable para el cálculo de IVA y facturación electrónica. Para iniciar, complete sus datos a continuación.</p>
    </div>
    	
	<div class="wizard-steps">
			<div id="step1" class="step-btn step1 is-active" onclick="toggleStep(id);"><span>Información básica</span></div>
			<div id="step2" class="step-btn step2" onclick="toggleStep(id);"><span>Ubicación</span></div>
			<div id="step3" class="step-btn step3" onclick="toggleStep(id);"><span>Facturación electrónica</span></div>
			<div id="step4" class="step-btn step4" onclick="toggleStep(id);"><span>Certificado ATV</span></div>
			<div id="step5" class="step-btn step5" onclick="toggleStep(id);"><span>Prorrata</span></div>
	</div>
    
    <div class="form-container">
    
	    <form method="POST" action="/update-wizard" class="wizard-form" enctype="multipart/form-data">
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
  
    /*function saveDatosBasicos() {
        var allow1 = checkEmptyFields('step1');
        var allow2 = checkEmptyFields('step2');
          	
        if( allow1 && allow2 )	{
            jQuery.ajax({
                url: "/empresas/update/{{ currentCompany() }}",
                type: 'POST',
                cache: false,
                data : {
                    tipo_persona: $("#tipo_persona").val(),
                    id_number: $("#id_number").val(),
                    business_name: $("#business_name").val(),
                    name: $("#name").val(),
                    last_name: $("#last_name").val(),
                    last_name2: $("#last_name2").val(),
                    email: $("#email").val(),
                    phone: $("#phone").val(),
                    country: $("#country").val(),
                    state: $("#state").val(),
                    city: $("#city").val(),
                    district: $("#district").val(),
                    neighborhood: $("#neighborhood").val(),
                    zip: $("#zip").val(),
                    address: $("#address").val(),
                    phone: $("#phone").val(),
                    _method: 'patch',
                    _token: '{{ csrf_token() }}'
                },
                success : function( response ) {
                    console.log('SUCCESS BASICOS');
                },
                error : function( response ) {
                    console.log('ERROR BASICOS' + response);
                },
                async: true
            });  
        }
    }*/
    
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