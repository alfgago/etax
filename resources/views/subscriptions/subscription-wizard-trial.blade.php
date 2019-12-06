@extends('layouts/wizard-layout')

@section('title') 
	Configuración de plan inicial
@endsection

@section('slug', 'wizard')

@section('content') 
<div class="wizard-container">
  
  <div class="wizard-popup">
    <div class="titulo-bienvenida">
      <h2>¡Bienvenido a eTax! En poco tiempo podrá disfrutar de su prueba de 15 días.
      </h2>
      <p>En poco tiempo podrá utilizar la herramienta más fiable para el cálculo de IVA y facturación electrónica. Para iniciar, complete sus datos a continuación.
      </p>
    </div>
    
    <div class="form-container">
      <form method="POST" action="/suscripciones/confirmar-pruebas" class="wizard-form tarjeta" enctype="multipart/form-data">
        @csrf
        <div class="step-section biginputs step1 is-active">
          <div class="form-row">
            <div class="form-group col-md-12">
              <h3 class="mt-0">
                Confirme el plan que desea probar
              </h3>
            </div>
            
            <div class="form-group col-md-6">
              <label for="plan-sel">Plan </label>
              <select class="form-control " name="plan_sel" id="plan-sel" onchange="togglePlan();">
              	<option value="Profesional" selected>Profesional</option>
              	<option value="Empresarial" >Empresarial</option>
                @if(!in_array(8, auth()->user()->permisos()) )
              	   <option value="Contador">Contador</option>
                @endif
              </select>
            </div>
            
            <div class="form-group col-md-6 hide-contador">
              <label for="product_id">Tipo </label>
              <select class="form-control " name="product_id" id="product_id" onchange="togglePrice();">
                  @foreach($plans as $plan)
                    @if($plan->plan_tier != 'Gosocket')
                    <option class="{{ $plan->plan_type }}" facturas="{{ $plan->num_invoices }}" value="{{ $plan->id }}" monthly="${{ $plan->monthly_price }}" six="${{ $plan->six_price * 6 }}" annual="${{ $plan->annual_price * 12 }}" >{{ $plan->plan_tier }}</option>
                    @else
                      @if(in_array(8, auth()->user()->permisos()))
                        <option class="{{ $plan->plan_type }}" facturas="{{ $plan->num_invoices }}" value="{{ $plan->id }}" monthly="${{ $plan->monthly_price }}" six="${{ $plan->six_price * 6 }}" annual="${{ $plan->annual_price * 12 }}" selected>{{ $plan->plan_tier }}</option>
                      @endif
                    @endif
                  
                  @endforeach
              </select>
            </div>
              <div class="form-group col-md-6" id="cantidadContabilidades">
                  <label for="recurrency">Cantidad de Contabilidades</label>
                  <input type="number" min="10" class="form-control" name="num_companies" id="num_companies" value="10"  onblur="validarCantidad();" onkeyup="calcularPrecioContabilidades();" onchange="calcularPrecioContabilidades();">
              </div>
            <div class="form-group col-md-6">
              <label for="recurrency">Recurrencia de pagos </label>
              <select class="form-control " name="recurrency" id="recurrency" onchange="togglePrice();" onchange="sumarPrecioContabilidades();">
              	<option value="1" selected>Mensual</option>
              	<option value="6">Semestral</option>
              	<option value="12">Anual</option>
              </select>
            </div>

            <div class="form-group col-md-6">
              <label for="recurrency">Cantidad de Facturas Emitidas</label>
              <input type="text" class="form-control text-right" readonly disabled value="30" id="cantidad_facturas">
            </div>
            <div class="form-group col-md-12 mt-4">
            	<span class="precio-container">
            		Precio de <span class="precio-text precio-inicial">4.75</span> <span class="recurrencia-text">/ mes</span> + IVA
            	</span>
            </div>
            
            <div class="btn-holder">
              <a class="btn btn-primary btn-prev" target="_blank" href="https://etaxcr.com/planes">Ver detalle de planes</a>
              <button onclick="trackClickEvent( 'ConfirmarPago' );" type="submit" id="btn-submit-tc" class="btn btn-primary btn-next has-spinner" >Iniciar periodo de pruebas</button>
            </div>
            
             @if( !empty( auth()->user()->teams ) && !in_array(8, auth()->user()->permisos()) )
                @if( sizeof(auth()->user()->teams) > 1 )
                  <div class="companyParent suscripciones">
                      <label for="">Saltar suscripción y entrar como:</label>
                      <div class="form-group">
                          <select class="form-control" id="company_change" onchange="companyChange(false);">
                              <option value="" selected >Seleccione compañia </option>
                              @foreach( auth()->user()->teams as $row )
                                  <?php  
                                      $c = $row->company;  
                                      if( isset($c) ){
                                        $name = $c->name ? $c->name.' '.$c->last_name.' '.$c->last_name2 : '-- Nueva Empresa --';
                                        $companyId = $c->id;
                                        echo "<option value='$companyId' > $name </option>";
                                      }
                                  ?>
                              @endforeach
                          </select>
                      </div>
                  </div>
                @endif
            @endif

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

<style>
  .wizard-popup .form-container {
      margin-right: 0;
  }
  .biginputs .form-group select, .biginputs .form-group input {
      font-size: 1.5rem;
      line-height: 1.1;
      height: 38px;
  }
</style>

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
        $("#product_id ." + planId).show();
        $("#product_id").val( $("#product_id ."+planId).first().val() );
        togglePrice();
        if(planId == 'Contador'){
            $('#cantidadContabilidades').show();
            $('.hide-contador').hide();
        }else{
            $('#cantidadContabilidades').hide();
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
    }
    function CambiarNombre() {
        var exp = $("#expiry").val();
        $('#cardMonth').val(exp.substr(0,2));
        $('#cardYear').val(exp.substring(exp.length - 2, exp.length));
        var FingerprintID = cybs_dfprofiler("tc_cr_011007172","test");
        $("#deviceFingerPrintID").val(FingerprintID);
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
        var valido = 0;
        var precio_25 = 8;
        var precio_10 = 10;
        var precio_mes = 14.999;
        var precio_seis = 13.740;
        var precio_anual = 12.491;
        var recurrency = $('#recurrency').val();
        var total = 0;
        var total_extras = 0;
        var recurrencia_texto = "";

          if(cantidad > 25){
              total_extras = (cantidad - 25) * precio_25;
              cantidad = 25;
          }
          if(cantidad > 10){
              total_extras += (cantidad - 10) * precio_10;
              cantidad = 10;
          }
          if(recurrency == 1){
              total = cantidad * precio_mes;
              total = total + total_extras;
              recurrencia_texto = "mes";
          }
          if(recurrency == 6){
            total = cantidad * precio_seis;
            total = total + total_extras;
            total = total * 6;
            recurrencia_texto = "semestre";
          }
          if(recurrency == 12){
            total = cantidad * precio_anual;
            total = total + total_extras;
            total = total * 12;
            recurrencia_texto = "año";
          }
          
          var precioFinal = parseFloat(total).toFixed(2);
          $(".precio-text").text('$' + precioFinal);
          $(".recurrencia-text").text('/ '+ recurrencia_texto);
    }


    $( document ).ready(function() {
        togglePlan();
    });
</script>
@endsection
