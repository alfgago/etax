<div class="form-group col-md-12">
  <h3 class="mt-0">
    Datos de pago
  </h3>
  <p class="description">Los datos confidenciales de su tarjeta de crédito nunca serán guardados por eTax. Estos permanecerán seguros en la pasarela de pagos autorizada por el Banco Nacional.</p>
</div>

<div class="col-md-4">
    <div class="row">
        <div class="form-group col-md-12" style="white-space: nowrap;margin-top:2px;">
            <input type="text" inputmode="numeric" class="form-control checkEmpty" maxlength="19" name="number" id="number" placeholder="N&#250;mero de tarjeta" required onblur="valid_credit_card(this.value);">
        </div>
        <div class="form-group col-md-6" style="white-space: nowrap;">
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="expiry" id="expiry" placeholder="Mes / A&#241;o" required>
        </div>
        <div class="form-group col-md-6" style="white-space: nowrap;">
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="cvc" id="cvc" placeholder="CVV" required>
        </div>
        <div class="form-group col-md-12" style="white-space: nowrap;">
            <input type="text" inputmode="text" class="form-control checkEmpty" name="first_name_card" id="first_name_card" placeholder="Nombre" required >
        </div>
         <div class="form-group col-md-12" style="white-space: nowrap;">
            <input type="text" inputmode="text" class="form-control checkEmpty" name="last_name_card" id="last_name_card" placeholder="Apellidos" required >
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
    <input type="text" class="form-control" name="coupon" id="coupon" placeholder="Cup&oacute;n" >
</div>

<div class="biginputs form-group col-md-4" style="white-space: nowrap;">
    <label for="coupon">&nbsp;</label>
    <input type="button" class="btn btn-dark form-button" value="Aplicar" onclick="checkCupon();">
</div>

<div class="form-group col-md-12 mt-4">
	<span class="precio-container">
		Precio total: <span class="precio-text precio-final">9.99</span> <span class="recurrencia-text">/ mes</span> <span class="etiqueta-descuento"></span> + IVA
	</span>
	<p class="description">* No se aceptan tarjetas American Express</p>
</div>
<input type="text" hidden id="bncupon" name="bncupon" value="0">
<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="backFields();toggleStep('step2');">Paso anterior</button>
  <button onclick="getCyberData();trackClickEvent( 'ConfirmarPago' );" type="submit" id="btn-submit-tc" class="btn btn-primary btn-next has-spinner" >Confirmar</button>
</div>
<div class="verificado-logos">
    <img src="/assets/images/visa.png">
    <img src="/assets/images/mastercard.png">
    <img src="/assets/images/logo-banco-nacional.png">
</div>
<style>
.verificado-logos {
    margin-top: 1.5rem;
    text-align: right;
    width: 100%;
}
.verificado-logos img {
    display: inline-block;
    max-width: 75px;
    margin: 0 1rem;
}
</style>
<script>
    function checkCupon() {
        var codigo = $('#coupon').val();
        var precio =  parseFloat( $('.precio-inicial').text().slice(1) );
        var banco =  $('#bncupon').val();
        var plan =  $('#plan-sel').val();
        var companies =  $('#num_companies').val();
        var url = '/confirmar-codigo/'+codigo+'/'+precio+'/'+banco+'/'+plan+'/'+companies;
        $.ajax({
           type:'GET',
           url:url,
           data:{},
           success:function(data){
                $('.precio-final').text( '$' + data.precio );
                $('.etiqueta-descuento').text(data.nota);
           }
        });
    }
</script>
