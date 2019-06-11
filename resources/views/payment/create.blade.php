@extends('layouts/app')
@section('title') 
  Configuración inicial
@endsection
@section('slug', 'wizard')
@section('header-scripts')
<style>
	.cuadro-planes {
	    width: 100%;
        margin-left: -9px;
	}
	.cuadro-planes .tier {
        display: flex;
	    font-size: 1rem;
    	width: 100%;
	}
	.cuadro-planes .tier > div {
        -webkit-box-flex: 1;
	    -ms-flex: 1;
	    flex: 1;
	    font-weight: 600;
	    padding: .9rem;
    	margin-bottom: .75rem;
		
	}
	.cuadro-planes .opcion {
	    position: relative;
	    color: #fff;
	    background: rgb(120,128,142);
	    background: -webkit-gradient(linear, left top, right top, from(rgba(120,128,142,1)), to(rgba(91,98,111,1)));
	    background: -webkit-linear-gradient(left, rgba(120,128,142,1) 0%, rgba(91,98,111,1) 100%);
	    background: -o-linear-gradient(left, rgba(120,128,142,1) 0%, rgba(91,98,111,1) 100%);
	    background: linear-gradient(90deg, rgba(120,128,142,1) 0%, rgba(91,98,111,1) 100%);
	    text-align: center;
	    border-radius: 15px;
	    margin-left: .75rem;
    	margin-bottom: .75rem;
	    -webkit-transition: .5s ease all;
	    -o-transition: .5s ease all;
	    transition: .5s ease all;
	    cursor: pointer;
	    overflow: hidden;
	}
	.cuadro-planes .opcion span {
		position: relative;
		z-index: 1;
	}
	.cuadro-planes .opcion:before {
	    position: absolute;
	    width: 100%;
	    height: 100%;
	    left: 0;
	    top: 0;
	    background: #F1CB61;
	    content: '';
	    opacity: 0;
	    -webkit-transition: .5s ease all;
	    -o-transition: .5s ease all;
	    transition: .5s ease all;
	}
	.cuadro-planes .opcion.is-active:before {
	    opacity: 1;
	}
	.cuadro-planes .opcion.is-active {
	    color: #333;
	}
	.detalle {
	    background: #e5e5e5;
	    padding: 1rem;
	    border-radius: 15px;
	    margin: auto;
	    width: 100%;
	    text-align: center;
	}
	.detalle-plan {
	    display: none;
	    margin: auto;
	    text-align: left;
	}
	.detalle-plan.is-active {
		display: inline-block;
	}
	.plan-feature {
	    font-size: .9rem;
	    margin-bottom: .5rem;
	}
	.plan-feature i {
	    padding-right: .5rem;
	    color: #999;
	    text-align: center;
	    width: 25px;
	}
	.plan-feature span {
	    font-size: 1rem;
	    font-weight: 400;
	    padding: 0 .2rem;
	    color: #2845A4;
	}
	.wizard-popup .form-container {
	    margin-right: auto;
	}
    .tituloDePago{
        margin-top: 3% !important;
    }
    .custom-payment{
        border-radius: 20px;
        width: 99% !important;
        height: 5em !important;
        background-color:#a1a4a8;
    }
    .custom-payment:hover {
        background-color:#f1cb61;
        cursor: pointer;
    }
    .custom-payment-checked{
        border-radius: 20px;
        width: 99% !important;
        height: 5em !important;
        background-color:#f1cb61;
        cursor: pointer;
    }
    .labelpayment{
        margin-top: 15px;
        padding-bottom: 10px;
        padding-left: 45px;
        padding-right: 43px;
    }
    .line {
        width: 90px;
        height: 0;
        border: 1px solid #131212;
        margin: 0px 0px 10px 0px;
        display: inline-block;
        cursor: pointer;
    }
    .radioPayment{
        margin-top: -10px;
        margin-left: -30px;
        cursor: pointer;
    }
    input[type=radio] {
        border: 0px;
        width: 100%;
        height: 15px;
    }
    .labelPaymentCost{
        border-radius: 10px;
        width: 53% !important;
        height: 30% !important;
        background-color: #5c5d60;
        cursor: pointer;
        margin-left: 29%;
        margin-top: 18px;
        font-size: 23px;
    }
    .labelCostPayment{
        color: white !important;
        font-size: 20px !important;
        margin-top: 6px;
    }
    .rowSpecial{
        margin-left: -19px !important;
    }
    .montly{
        margin-top: -10px;
    }
    .pointered{
        cursor: pointer;
    }
    input[type='radio']:after {
        width: 25px;
        height: 25px;
        border-radius: 15px;
        top: -9px;
        left: 34px;
        position: relative;
        background-color: #ffffff;
        content: '';
        display: inline-block;
        visibility: visible;
        border: 2px solid #a0a3a7;
    }

    input[type='radio']:checked:after {
        width: 25px;
        height: 25px;
        border-radius: 15px;
        top: -9px;
        left: 34px;
        position: relative;
        background-color: #5b5c5f;
        content: '';
        display: inline-block;
        visibility: visible;
        border: 2px solid white;
    }
</style>
@endsection
@section('content')
<div class="wizard-container">
  	<div class="wizard-popup">  	
	   	<div class="titulo-bienvenida">
	    	<h2>¡Bienvenido a eTax!</h2>
	    	<p>En poco tiempo podrá utilizar la herramienta más fiable para el cálculo de IVA y facturación electrónica.</p>
	    </div>    
	    <div class="form-container">
		    <form method="POST" action="/payment/payment-checkout" class="wizard-form" enctype="multipart/form-data">
	       		@csrf				
				<div class="step-section step1 is-active">
			      	<div class="form-row">
				        <div class="form-group col-md-12">
                            <h3 class="mt-0 tituloDePago">
						    	Seleccione su plan
						  	</h3>
						</div>
                        <div class="cuadro-planes">
                            <div class="form-group col-md-12" style="white-space: nowrap;">
                                <div class="row rowSpecial pointered">
                                    <div class="form-group col-md-4" style="white-space: nowrap;">
                                        <div class="custom-payment-checked" id="custom-payment-1" onclick="changeColor(this);">
                                            <label for="payment1" class="labelpayment">Pago Mensual
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="line"></div>
                                                            <input type="radio" value="1" checked="checked" name="selectedPlan" id="payment1" class="radioPayment">
                                                            <div class="col-md-12 montly">
                                                                <label><strong>{{$sale->subscription_plan->monthly_price}} + IVA</strong></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4" style="white-space: nowrap;">
                                        <div class="custom-payment" id="custom-payment-2" onclick="changeColor(this);">
                                            <label for="payment2" class="labelpayment">Pago Semestral
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="line"></div>
                                                            <input type="radio" value="2" name="selectedPlan" id="payment2" class="radioPayment">
                                                            <div class="col-md-12 montly">
                                                                <label ><strong>{{$sale->subscription_plan->six_price * 6}} + IVA</strong></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4" style="white-space: nowrap;">
                                        <div class="custom-payment" id="custom-payment-3" onclick="changeColor(this);">
                                            <label for="payment3" class="labelpayment">Pago Anual
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="form-group">
                                                            <div class="line"></div>
                                                            <input type="radio" value="3" name="selectedPlan" id="payment3" class="radioPayment">
                                                            <div class="col-md-12 montly">
                                                                <label><strong>{{$sale->subscription_plan->annual_price * 12}} + IVA</strong></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="number" id="costomensual" value="{{$sale->subscription_plan->monthly_price}}" hidden>
                            <input type="number" id="costosemestral" value="{{$sale->subscription_plan->six_price * 6}}" hidden>
                            <input type="number" id="costoanual" value="{{$sale->subscription_plan->annual_price * 12}}" hidden>
                            <div class="form-group col-md-12 labelPaymentCost">
                                <label id="labelPayment" class="labelCostPayment">Costo total del pago: $13.55</label>
                            </div>
                        </div>
                        <div class="btn-holder">
                            <button type="submit" id="btn-submit" class="btn btn-primary btn-next">Confirmar pago</button>
                        </div>
                    </div>
				</div>				
		    </form>
		</div>  
	</div>
</div>
@endsection
@section('footer-scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $("input[type='radio']").change(function(){
            var radioValue = $("input[name='selectedPlan']:checked").val();
            var costomensual = $('#costomensual').val();
            var costosemestral = $('#costosemestral').val();
            var costoanual = $('#costoanual').val();
            switch (radioValue) {
                case "1":
                    var costoPago = costomensual;
                break;
                case "2":
                    var costoPago = costosemestral;
                break;
                case "3":
                    var costoPago = costoanual;
                break;
            }
            var newValue = parseFloat(parseFloat(costoPago) + parseFloat(costoPago * 0.13)).toFixed(2);
            if(newValue) {
                var text = 'Costo total del pago: $' + newValue;
                $("#labelPayment").empty();
                $('#labelPayment').text(text);
            }
        });
    });
    function changeColor(elem){
        $("input[type='radio']").change(function(){
            var elem_id = elem.id;
            switch (elem_id) {
                case 'custom-payment-1':
                    var classList2 = document.getElementById('custom-payment-2').className.split(/\s+/);
                    var classList3 = document.getElementById('custom-payment-3').className.split(/\s+/);
                    if(classList2[0]=='custom-payment-checked'){
                        document.getElementById('custom-payment-2').classList.remove('custom-payment-checked');
                        document.getElementById('custom-payment-2').classList.add('custom-payment');
                    }
                    if(classList3[0]=='custom-payment-checked'){
                        document.getElementById('custom-payment-3').classList.remove('custom-payment-checked');
                        document.getElementById('custom-payment-3').classList.add('custom-payment');
                    }
                    document.getElementById('custom-payment-1').classList.remove('custom-payment');
                    document.getElementById('custom-payment-1').classList.add('custom-payment-checked');
                break
                case 'custom-payment-2':
                    var classList1 = document.getElementById('custom-payment-1').className.split(/\s+/);
                    var classList3 = document.getElementById('custom-payment-3').className.split(/\s+/);
                    if(classList1[0]=='custom-payment-checked'){
                        document.getElementById('custom-payment-1').classList.remove('custom-payment-checked');
                        document.getElementById('custom-payment-1').classList.add('custom-payment');
                    }
                    if(classList3[0]=='custom-payment-checked'){
                        document.getElementById('custom-payment-3').classList.remove('custom-payment-checked');
                        document.getElementById('custom-payment-3').classList.add('custom-payment');
                    }
                    document.getElementById('custom-payment-2').classList.remove('custom-payment');
                    document.getElementById('custom-payment-2').classList.add('custom-payment-checked');
                break
                case 'custom-payment-3':
                    var classList1 = document.getElementById('custom-payment-1').className.split(/\s+/);
                    var classList2 = document.getElementById('custom-payment-2').className.split(/\s+/);
                    if(classList1[0]=='custom-payment-checked'){
                        document.getElementById('custom-payment-1').classList.remove('custom-payment-checked');
                        document.getElementById('custom-payment-1').classList.add('custom-payment');
                    }
                    if(classList2[0]=='custom-payment-checked'){
                        document.getElementById('custom-payment-2').classList.remove('custom-payment-checked');
                        document.getElementById('custom-payment-2').classList.add('custom-payment');
                    }
                    document.getElementById('custom-payment-3').classList.remove('custom-payment');
                    document.getElementById('custom-payment-3').classList.add('custom-payment-checked');
                break
            }
        });
    }
</script>
@endsection
