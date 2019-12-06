@extends('layouts/wizard-layout')

@section('title') 
	Configuración de plan inicial
@endsection

@section('slug', 'wizard')

@section('content')

<?php
    $company = currentCompanyModel();
    if( isset($old)) {
        $company = $old;
    }
?>

<div class="wizard-container">
  
  <div class="wizard-popup">
    <div class="titulo-bienvenida">
      <h2>¡Bienvenido a eTax!
      </h2>
      <p>En poco tiempo podrá utilizar la herramienta más fiable para el cálculo de IVA y facturación electrónica. Para iniciar, complete sus datos a continuación.
      </p>
    </div>
    <div class="wizard-steps">
      <div id="step1" class="step-btn step1 is-active">
        <span>Confirme su plan</span>
      </div>
      <div id="step2" class="step-btn step2">
        <span>Datos de facturación</span>
      </div>
      <div id="step3" class="step-btn step3">
        <span>Método de pago</span>
      </div>
    </div>
    
    <div class="form-container">
      <form method="POST" action="/payment/confirm-payment" class="wizard-form tarjeta" enctype="multipart/form-data">
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
  @if(!in_array(8, auth()->user()->permisos()))
  <a class="btn btn-cerrarsesion" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
    Cerrar sesión
  </a>
  <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
  </form>
  @endif
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
      allowEmails = validateEmail(email);
      if( !allowEmails ) {
        allow = false;
      }
    });
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
    if(planId == 'Contador'){
          $('#cantidadContabilidades').show();
          $('#copunContador').show();
          $('.hide-contador').hide();
      }else{
          $('#cantidadContabilidades').hide();
          $('#copunContador').hide();
          $('.hide-contador').show();
      }
  }
  function togglePrice() {
      var planId = $("#plan-sel").val();
      if(planId != 'Contador'){
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
      }else{
        calcularPrecioContabilidades();
      }
      var facturas = $('#product_id :selected').attr('facturas');
      $("#cantidad_facturas").val(facturas);
  }
  $.getJSON('https://api.ipify.org?format=json', function(data){
      $("#IpAddress").val(data.ip);
  });
  function cambiarPrecio() {
      var precio = $(".precio-inicial").text().replace('$', "");
      var numero = parseFloat(precio);

      var binsBn = ['541254', '493824', '450777', '451418', '410865', '419556', '512905', '518668',
          '518439', '450776', '404144', '552882', '524471', '456949', '514006', '480853', '529164', '542178',
          '527552', '529060', '520026', '510980', '477280', '548711', '493823', '525843', '281010', '517784',
          '410864', '483126', '456337', '502107', '411061', '483189', '523587', '523592', '483190', '456338',
          '464137', '552450', '528080', '478019', '402520', '502108', '101001', '404980', '461131', '483103',
          '489353', '515575', '516681', '517588', '517871', '518214', '518541', '519995', '519996', '523671',
          '524308', '531643', '542133', '551898', '557683', '559727'];
      var tarjeta = $('#number').val();
      var tarjeta1 = tarjeta.replace(/ /g, "");
      var binEnviado = tarjeta1.substr(0, 6);
      var binDescuento = binsBn.indexOf(binEnviado);

      var precioFinal = numero;
      var etiqueta = '';
      if(binDescuento != -1){
          var descuento = parseFloat(numero * 0.1);
          var precioDescuento = parseFloat(numero - descuento).toFixed(2);
          precioFinal = precioDescuento;
          etiqueta = '(descuento del Banco Nacional)';
          $('#bncupon').val(1);
      }

      $(".precio-final").text('$' + precioFinal);
      $(".etiqueta-descuento").text(etiqueta);
  }
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
          cambiarPrecio();
      }
  }
  function validarCantidad(){
      var cantidad = $('#num_companies').val();
      //cantidad = parseInt(cantidad, 10);
      if(cantidad != '' && cantidad != undefined){
          if(cantidad < 10){
              alert('Este plan requiere un mínimo de 10 (diez) contabilidades');
              $('#num_companies').val(10).change();
          }
      }else{
          $('#num_companies').val(10).change();
      }
  }
  
  function calcularPrecioContabilidades() {
      var cantidad = parseFloat($('#num_companies').val());

      var recurrency = $('#recurrency').val();
      var total = 0;
      var total_extras = 0;
      if(cantidad > 25){
          total_extras = (cantidad - 25) * 8;
          cantidad = 25;
      }
      if(cantidad > 10){
          total_extras += (cantidad - 10) * 10;
          cantidad = 10;
      }
      if(recurrency == 1){
          total = cantidad * 14.999;
          total = total + total_extras;
      }
      if(recurrency == 6){
        total = cantidad * 13.740;
        total = total + total_extras;
        total = total * 6;
      }
      if(recurrency == 12){
        total = cantidad * 12.491;
        total = total + total_extras;
        total = total * 12;
      }
      var precioFinal = parseFloat(total).toFixed(2);
      $(".precio-text").text('$' + precioFinal);

  }

  $( document ).ready(function() {
	    fillProvincias();
	    togglePlan();
		  toggleApellidos();

		  var card = new Card({
		      form: 'form.tarjeta',
		      container: '.card-wrapper',
		      formSelectors: {
		          nameInput: 'input[name="first_name_card"], input[name="last_name_card"]'
		      }
		  });
		  
		  @if( @$company->state )
	    	$('#state').val( {{ $company->state }} );
	    	fillCantones();
	    	@if( @$company->city )
		    	$('#city').val( {{ $company->city }} );
		    	fillDistritos();
		    	@if( @$company->district )
			    	$('#district').val( {{ $company->district }} );
			    	fillZip();
			    @endif
		    @endif
	    @endif
		  
  	}
  );
</script>
@endsection
