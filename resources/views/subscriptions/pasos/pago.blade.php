<div class="form-group col-md-12">
  <h3 class="mt-0">
    Datos de pago
  </h3>
  <p class="description">Los datos confidenciales de su tarjeta de crédito nunca serán guardados por eTax. Estos permanecerán seguros en la pasarela de pagos autorizada por el Banco Nacional.</p>
</div>

<div class="col-md-4">
    <div class="row">
        <div class="form-group col-md-12" style="white-space: nowrap;margin-top:2px;">
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="number" id="number" placeholder="N&#250;mero de tarjeta" required onblur="valid_credit_card(this.value);">
        </div>
        <div class="form-group col-md-6" style="white-space: nowrap;">
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="expiry" id="expiry" placeholder="Mes / A&#241;o" required>
        </div>
        <div class="form-group col-md-6" style="white-space: nowrap;">
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="cvc" id="cvc" placeholder="CVV" required>
        </div>
        <div class="form-group col-md-12" style="white-space: nowrap;">
            <input type="text" inputmode="text" class="form-control checkEmpty" name="first_name_card" id="first_name" placeholder="Nombre" required onblur="valid_credit_card(this.value);">
        </div>
         <div class="form-group col-md-12" style="white-space: nowrap;">
            <input type="text" inputmode="text" class="form-control checkEmpty" name="last_name_card" id="last_name" placeholder="Apellido" required onblur="valid_credit_card(this.value);">
        </div>
        <div class="form-group col-md-12">
            <label id="alertCardValid" class="alertCardValid"  style="color: red;"></label>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class='card-wrapper'></div>
</div>

<div class="biginputs form-group col-md-8" style="white-space: nowrap;">
    <label for="coupon">Tengo un cup&oacute;n:</label>
    <input type="text" class="form-control checkEmpty" name="coupon" id="coupon" placeholder="Cup&oacute;n" onblur="fusb();">
</div>

<div class="biginputs form-group col-md-4" style="white-space: nowrap;">
    <label for="coupon">&nbsp;</label>
    <input type="button" class="btn btn-dark form-button" value="Aplicar">
</div>

<div class="form-group col-md-12 mt-4">
	<span class="precio-container">
		Precio total: <span class="precio-text">9.99</span> <span class="recurrencia-text">/ mes</span>
	</span>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step1');">Paso anterior</button>
  <button type="submit" id="btn-submit" class="btn btn-primary btn-next" >Confirmar <span style="font-size:10px;">(No tiene que ser datos reales en staging)</span></button>
</div>

