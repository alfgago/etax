@extends('layouts/wizard-layout')

@section('title') 
  Configuración inicial
@endsection

@section('slug', 'wizard')

@section('header-scripts')

@endsection

@section('content') 
<div class="wizard-container">
  <div class="wizard-popup">
  	
   	<div class="titulo-bienvenida">
    	<h2>Configuración inicial</h2>
    	<p>Para iniciar con eTax, complete sus datos a continuación.</p>
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

		<a style=""
	  class="btn btn-cerrarsesion" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
        Cerrar sesión
    </a>
    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    
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
    		
    		if(allow) {
	    		//Revisa que el campo de correo este correcto
	    		var email = $('#email').val();
	    		allow = validateEmail(email);
    		}
    		
    	});
    	return allow;
    }
    
    function validateEmail(email) {
		  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		  return re.test(email);
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

	$("#input-cert").change(function () {
		var ext = this.value.match(/\.(.+)$/)[1];
		switch (ext) {
			case 'p12':
				$('#uploadButton').attr('disabled', false);
				break;
			default:
				alert('El archivo no es un certificado.');
				this.value = '';
		}
	});

	$("#input_logo").change(function () {
		var ext = this.value.match(/\.(.+)$/)[1];
		switch (ext) {
			case 'jpg':
			case 'jpeg':
			case 'png':
				$('#uploadButton').attr('disabled', false);
				break;
			default:
				alert('El archivo no es de tipo imagen.');
				this.value = '';
		}

		var file_size = $("#input_logo")[0].files[0].size;
		if(file_size > 2097152) {
			alert('El el archivo debe tener un maximo de 2MB.');
			$("#input_logo").value = '';
		}
	});

</script>

@endsection
