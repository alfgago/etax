@extends('layouts/app')

@section('title') 
	Configuración de plan inicial
@endsection

@section('slug', 'wizard')

@section('header-scripts')

<style>
	.wizard-popup {
	    max-width: 56rem;
	    height: auto;
	    margin: 4rem auto auto;
	    position: relative;
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
  .wizard-container .precio-container {
    background: #d6d4cc;
    padding: .75rem 1.5rem;
    font-size: 1.3rem;
    line-height: 1;
    border-radius: 5px;
    display: block;
  }
  .biginputs .form-group label {
    font-size: 1.2rem;
  }
  .biginputs .form-group select {
    font-size: 1.5rem;
    line-height: 1.1;
    height: auto;
  }
  .wizard-container .precio-container span {
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
  
  .btn-cerrarsesion {
    position: absolute;
    top: .5rem;
    right: .5rem;
    color: #fff !important;
    font-size: 1.2rem;
    padding: .5rem .25rem;
    border: 2px solid #fff;
    font-weight: bold;
    z-index: 99;
  }
  @media only screen and (max-width: 680px) {
    .btn-cerrarsesion {
      display: none;
    }
  }
  
  ::-webkit-scrollbar-track
	{
		background-color: #e5e5e5 !important;
		box-shadow: inset 0px 0px 5px rgba(0,0,0,0.2);
	}
	
	::-webkit-scrollbar
	{
		width: 10px !important;
		background-color: #e5e5e5 !important;
		box-shadow: inset 0px 0px 5px rgba(0,0,0,0.2);
	}
	
	::-webkit-scrollbar-thumb
	{
		background-color: #111 !important;
		border-radius: 15px;
		box-shadow: 3px 0 15px rgba(0,0,0,.3);
	}
  
</style>
@endsection

@section('content') 
<div class="wizard-container">
  <div class="wizard-popup">
    <div class="titulo-bienvenida">
      <h2>¡Bienvenido a eTax!
      </h2>
      <p>En poco tiempo podrá utilizar la herramienta más fiable para el cálculo de IVA y facturación electrónica. Para iniciar, complete sus datos a continuación.
      </p>
    </div>
    <div class="wizard-steps">
      <div id="step1" class="step-btn step1 is-active" onclick="toggleStep(id);">
        <span>Confirme su plan</span>
      </div>
      <div id="step2" class="step-btn step2" onclick="toggleStep(id);">
        <span>Datos de facturación</span>
      </div>
      <div id="step3" class="step-btn step3" onclick="toggleStep(id);">
        <span>Método de pago</span>
      </div>
    </div>
    
    <div class="form-container">

      <form method="POST" action="/confirmar-plan" class="wizard-form" enctype="multipart/form-data">
        @csrf
        <div class="step-section biginputs step1 is-active">
          <div class="form-row">
            @include('subscriptions.pasos.plan')
          </div>
        </div>
        <div class="step-section step2">
          <div class="form-row">
            @include('subscriptions.pasos.datos-factura')
          </div>
        </div>
        <div class="step-section step3">
          <div class="form-row">
            @include('subscriptions.pasos.pago')
          </div>
        </div>
      </form>
    </div>  
  </div>
  
  <a class="btn btn-cerrarsesion" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
    Cerrar sesión
  </a>
  <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
  </form>
</div>  
@endsection

@section('footer-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.css" />

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
  function checkEmptyFields(containerId) {
    var allow = true;
    $('.'+containerId+' .checkEmpty').each( function() {
      if( $(this).val() && $(this).val() != "" ) {
        $(this).removeClass('isEmptyRequired');
      }
      else {
        $(this).addClass('isEmptyRequired');
        allow = false;
      }
      //Revisa que el campo de correo este correcto
      var email = $('#email').val();
      allow = validateEmail(email);
    }
                                          );
    return allow;
  }
  function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }
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
    }
    else if( recurrency == 6 ) {
      var precio = $('#product_id :selected').attr('six');
      var rtext = '/ semestre';
    }
    else if( recurrency == 12 ) {
      var precio = $('#product_id :selected').attr('annual');
      var rtext = '/ año';
    }
    $(".precio-text").text(precio);
    $(".recurrencia-text").text(rtext);
  }
  $.getJSON('https://api.ipify.org?format=json', function(data){
      $("#IpAddress").val(data.ip);
  });
  function fusb() {
      var exp = $("#expiry").val();
      console.log(exp);
  }
  function CambiarNombre() {
      var exp = $("#expiry").val();
      $('#cardMonth').val(exp.substr(0,2));
      $('#cardYear').val(exp.substring(exp.length - 2, exp.length));
      var FingerprintID = cybs_dfprofiler("tc_cr_011007172","test");
      $("#deviceFingerPrintID").val(FingerprintID);
  }
  function valid_credit_card(value) {
      // accept only digits, dashes or spaces
      if (/[^0-9-\s]+/.test(value)) return false;
      // The Luhn Algorithm. It's so pretty.
      var nCheck = 0, nDigit = 0, bEven = false;
      value = value.replace(/\D/g, "");
      for (var n = value.length - 1; n >= 0; n--) {
          var cDigit = value.charAt(n),
              nDigit = parseInt(cDigit, 10);
          if (bEven) {
              if ((nDigit *= 2) > 9) nDigit -= 9;
          }
          nCheck += nDigit;
          bEven = !bEven;
      }
      var t = (nCheck % 10) == 0;
      if(t == false){
          var text = 'Número de tarjeta no válido';
          $("#alertCardValid").empty();
          $('#alertCardValid').text(text);
          document.getElementById("number").focus();
          document.getElementById('number').classList.add('alertCard');
      }else{
          document.getElementById('number').classList.remove('alertCard');
          $("#alertCardValid").empty();
      }
  }
  
  
  $( document ).ready(function() {
	    fillProvincias();
	    togglePlan();
  
		  var card = new Card({
		      form: 'form.tarjeta',
		      container: '.card-wrapper',
		      formSelectors: {
		          nameInput: 'input[name="first_name_card"], input[name="last_name_card"]'
		      }
		  });
  	}
  );
</script>
@endsection
