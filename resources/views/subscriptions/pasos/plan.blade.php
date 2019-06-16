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
  	<option class="p" value="1" monthly="${{ $plans[0]->plan->monthly_price }}" six="${{ $plans[0]->plan->six_price * 6 }}" annual="${{ $plans[0]->plan->annual_price * 12 }}">Básico</option>
  	<option class="p" value="2" monthly="${{ $plans[1]->plan->monthly_price }}" six="${{ $plans[1]->plan->six_price * 6 }}" annual="${{ $plans[1]->plan->annual_price * 12 }}" >Intermedio</option>
  	<option class="p" value="3" monthly="${{ $plans[2]->plan->monthly_price }}" six="${{ $plans[2]->plan->six_price * 6 }}" annual="${{ $plans[2]->plan->annual_price * 12 }}" >Pro</option>
  	<option class="e" value="4" monthly="${{ $plans[3]->plan->monthly_price }}" six="${{ $plans[3]->plan->six_price * 6 }}" annual="${{ $plans[3]->plan->annual_price * 12 }}" >Básico</option>
  	<option class="e" value="5" monthly="${{ $plans[4]->plan->monthly_price }}" six="${{ $plans[4]->plan->six_price * 6 }}" annual="${{ $plans[4]->plan->annual_price * 12 }}" >Intermedio</option>
  	<option class="e" value="6" monthly="${{ $plans[5]->plan->monthly_price }}" six="${{ $plans[5]->plan->six_price * 6 }}" annual="${{ $plans[5]->plan->annual_price * 12 }}" >Pro</option>
  	<option class="c" value="7" monthly="${{ $plans[6]->plan->monthly_price }}" six="${{ $plans[6]->plan->six_price * 6 }}" annual="${{ $plans[6]->plan->annual_price * 12 }}" >Pro</option>
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
	<span class="precio-container">
		Precio de <span class="precio-text precio-inicial">9.99</span> <span class="recurrencia-text">/ mes</span>
	</span>
</div>

<div class="form-group col-md-12">
  <div class="bigtext">
  	Elija el plan de su conveniencia y empiece a calcular el IVA con eTax. Disfrute de una <span>prueba gratis</span> válida hasta el 14 de Junio.
  </div>
</div>


<div class="btn-holder">
  <a class="btn btn-primary btn-prev" target="_blank" href="https://etaxcr.com/planes">Ver detalle de planes</a>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step2');">Siguiente paso</button>
</div>
