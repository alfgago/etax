<div class="form-group col-md-12">
  <h3 class="mt-0">
    Confirme su plan
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
    <input type="number" min="10" class="form-control" name="num_companies" id="num_companies" minlength="2" value="10" onblur="validarCantidad();" onchange="calcularPrecioContabilidades();" onkeyup="calcularPrecioContabilidades();">
</div>
<div class="form-group col-md-6">
  <label for="recurrency">Recurrencia de pagos </label>
  <select class="form-control " name="recurrency" id="recurrency" onchange="togglePrice();" onchange="calcularPrecioContabilidades();">
  	<option value="1" selected>Mensual</option>
  	<option value="6">Semestral</option>
  	<option value="12">Anual</option>
  </select>
</div>

<div class="form-group col-md-6">
    <label for="recurrency">Cantidad de Facturas Emitidas</label>
    <input type="text" class="form-control cantidad_facturas" readonly disabled value="30" id="cantidad_facturas">
</div>
<div class="form-group col-md-12 mt-4">
	<span class="precio-container">
    Precio de <span class="precio-text precio-inicial">4.75</span> <span class="recurrencia-text">/ mes</span> + IVA
	</span>
</div>
<input hidden id="amount" name="amount">
<div class="btn-holder">
  <a class="btn btn-primary btn-prev" target="_blank" href="https://etaxcr.com/planes">Ver detalle de planes</a>
  <button type="button" class="btn btn-primary btn-next" onclick="fowardFields();toggleStep('step2');getPrice();" onclick="trackClickEvent( 'PagosPaso2' );">Siguiente paso</button>
</div>
<script>
    function getPrice() {
        var text = $('.precio-text').text();
        var num1 = text.slice(1);
        var num = num1.split(" /");
        var amount = parseFloat(num[0]).toFixed(2);
        $('#amount').val(amount);
    }
</script>
<style>
    .biginputs .form-group select, .biginputs .form-group input {
        font-size: 1.5rem;
        line-height: 1.1;
        height: 38px;
    }
    #cantidad_facturas {
        border: 0;
        background: transparent;
        font-weight: bold;
    }
</style>
 @if( !empty( auth()->user()->teams ) && !in_array(8, auth()->user()->permisos()))
    @if( sizeof(auth()->user()->teams) > 1 )
      <div class="companyParent suscripciones">
          <label for="">Saltar suscripción y entrar como:</label>
          <div class="form-group">
              <select class="form-control" id="company_change" onchange="companyChange(false);">
                 
                  <option value="" selected >Seleccione compañia </option>
                  @foreach( auth()->user()->teams as $row )
                      <?php  
                      $c = $row->company;  
                      if($c) {
                          $name = $c->name ? $c->name.' '.$c->last_name.' '.$c->last_name2 : '-- Nueva Empresa --';  
                      ?>
                          <option value="{{ $c->id }}" > {{ $name }} </option>
                      <?php  } ?>
                  @endforeach
              </select>
          </div>
      </div>
    @endif
@endif

