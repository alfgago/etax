<div class="form-group col-md-12">
  <h3 class="mt-0">
    Confirme su plan
  </h3>
</div>

<div class="form-group col-md-6">
  <label for="plan-sel">Plan </label>
  <select class="form-control " name="plan_sel" id="plan-sel" onchange="togglePlan();">
  	<option value="p" selected>Profesional</option>
  	<option value="e">Empresarial</option>
  	<option value="c">Contador</option>
  </select>
</div>

<div class="form-group col-md-6 hide-contador">
  <label for="product_id">Tipo </label>
  <select class="form-control " name="product_id" id="product_id" onchange="togglePrice();">
  	<option class="p" value="1" monthly="${{ $plans[0]->monthly_price }}" six="${{ $plans[0]->six_price * 6 }}" annual="${{ $plans[0]->annual_price * 12 }}">Básico</option>
  	<option class="p" value="2" monthly="${{ $plans[1]->monthly_price }}" six="${{ $plans[1]->six_price * 6 }}" annual="${{ $plans[1]->annual_price * 12 }}" >Intermedio</option>
  	<option class="p" value="3" monthly="${{ $plans[2]->monthly_price }}" six="${{ $plans[2]->six_price * 6 }}" annual="${{ $plans[2]->annual_price * 12 }}" >Pro</option>
  	<option class="e" value="4" monthly="${{ $plans[3]->monthly_price }}" six="${{ $plans[3]->six_price * 6 }}" annual="${{ $plans[3]->annual_price * 12 }}" >Básico</option>
  	<option class="e" value="5" monthly="${{ $plans[4]->monthly_price }}" six="${{ $plans[4]->six_price * 6 }}" annual="${{ $plans[4]->annual_price * 12 }}" >Intermedio</option>
  	<option class="e" value="6" monthly="${{ $plans[5]->monthly_price }}" six="${{ $plans[5]->six_price * 6 }}" annual="${{ $plans[5]->annual_price * 12 }}" >Pro</option>
  	<option class="c" value="7" monthly="${{ $plans[6]->monthly_price }}" six="${{ $plans[6]->six_price * 6 }}" annual="${{ $plans[6]->annual_price * 12 }}" >Pro</option>
  </select>
</div>
<div class="form-group col-md-6" id="cantidadContabilidades">
    <label for="recurrency">Cantidad de Contabilidades</label>
    <input type="number" min="10" class="form-control" name="num_companies" id="num_companies" minlength="2" value="10" onblur="validarCantidad();" onchange="calcularPrecioContabilidades();" onkeyup="calcularPrecioContabilidades();">
</div>
<div class="form-group col-md-6">
  <label for="recurrency">Recurrencia de pagos </label>
  <select class="form-control " name="recurrency" id="recurrency" onchange="togglePrice();">
  	<option value="1" selected>Mensual</option>
  	<option value="6">Semestral</option>
  	<option value="12">Anual</option>
  </select>
</div>

<div class="form-group col-md-6" id="copunContador">
    <label for="recurrency">Cupon de Contador</label>
    <input type="text" class="form-control" name="codigo_contador" id="codigo_contador">
    <button class="btn btn-dark">Validar cupon</button>
</div>
<div class="form-group col-md-12 mt-4">
	<span class="precio-container">
		Precio de <span class="precio-text precio-inicial">9.99</span> <span class="recurrencia-text">/ mes</span> + IVA
	</span>
</div>

<div class="btn-holder">
  <a class="btn btn-primary btn-prev" target="_blank" href="https://etaxcr.com/planes">Ver detalle de planes</a>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step2');" onclick="trackClickEvent( 'PagosPaso2' );">Siguiente paso</button>
</div>
<style>
    .biginputs .form-group select, .biginputs .form-group input {
        font-size: 1.5rem;
        line-height: 1.1;
        height: 38px;
    }
</style>
 @if( !empty( auth()->user()->teams ) )
    @if( sizeof(auth()->user()->teams) > 1 )
      <div class="companyParent suscripciones">
          <label for="">Saltar suscripción y entrar como:</label>
          <div class="form-group">
              <select class="form-control" id="company_change" onchange="companyChange(false);">
                  @foreach( auth()->user()->teams as $row )
                      <?php  
                          $c = $row->company;  
                          $name = $c->name ? $c->name.' '.$c->last_name.' '.$c->last_name2 : '-- Nueva Empresa --';  
                      ?>
                      <option value="{{ $c->id }}" {{ $c->id == currentCompany() ? 'selected' : ''  }} > {{ $name }} </option>
                  @endforeach
              </select>
          </div>
      </div>
    @endif
@endif
