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
            width: 45% !important;
            height: 25% !important;
            background-color:#5c5d60;
            cursor: pointer;
            margin-left: 2%;
        }
        .labelCostPayment{
            color: white !important;
            font-size: 17px !important;
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
        .alertCardValid{
            font-size: 8px;
            color: red !important;
        }
        .alertCard{
            border:2px solid red !important;
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
                <form method="POST" action="/payment/payment-card" class="wizard-form tarjeta" enctype="multipart/form-data">
                    <div class="form-group col-md-12">
                        <h3 class="mt-0 tituloDePago">
                            Genere su pago
                        </h3>
                    </div>
                    <div class='card-wrapper'></div>
                    @csrf
                    <div class="step-section step1 is-active">
                        <div class="form-row">
                            <div class="form-group col-md-5" style="white-space: nowrap;">
                                <label for="number">N&#250;mero de tarjeta</label>
                                <input type="text" inputmode="numeric" class="form-control checkEmpty" name="number" id="number" placeholder="N&#250;mero de tarjeta:" required onblur="valid_credit_card(this.value);">
                                <label id="alertCardValid" class="alertCardValid"></label>
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="expiry">Expira</label>
                                <input type="text" inputmode="numeric" class="form-control checkEmpty" name="expiry" id="expiry" placeholder="Mes / A&#241;o:" required>
                            </div>
                            <div class="form-group col-md-2" style="white-space: nowrap;">
                                <label for="cardCcv">CVV</label>
                                <input type="text" inputmode="numeric" class="form-control checkEmpty" name="cvc" id="cvc" placeholder="CVV:" required>
                            </div>
                            <div class="form-group col-md-2" style="white-space: nowrap;">
                                <label for="coupon">Tengo un cup&oacute;n:</label>
                                <input type="text" class="form-control checkEmpty" name="coupon" id="coupon" placeholder="Cup&oacute;n:" onblur="fusb();">
                            </div>
                            <div class="form-group col-md-12" style="white-space: nowrap;">
                                Datos del tarjetahabiente
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="first-name">Nombre:</label>
                                <input type="text" class="form-control checkEmpty" name="first-name" id="first-name" value="" required placeholder="Nombre" >
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="last-name">Apellido:</label>
                                <input type="text" class="form-control checkEmpty" name="last-name" id="last-name" value="" required placeholder="Apellido">
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="street1">Direcci&#243;n:</label>
                                <input type="text" class="form-control checkEmpty" name="street1" id="street1" value="" required placeholder="Direcci&#243;n">
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="city">Cant&#243;n:</label>
                                <input type="text" class="form-control checkEmpty" name="city" id="city" value="" required placeholder="Cant&#243;n">
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="state">Provincia:</label>
                                <input type="text" class="form-control checkEmpty" name="state" id="state" value="" required placeholder="Provincia">
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="postalCode">C&oacute;digo Postal:</label>
                                <input type="text" class="form-control checkEmpty" name="postalCode" id="postalCode" value="" required placeholder="C&oacute;digo postal">
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="country">Pa&iacute;s:</label>
                                <input type="text" class="form-control checkEmpty" name="country" id="country" value="" required placeholder="Pa&iacute;s">
                            </div>
                            <div class="form-group col-md-3" style="white-space: nowrap;">
                                <label for="email">Correo electr&oacute;nico:</label>
                                <input type="email" class="form-control checkEmpty" name="email" id="email" value="" required placeholder="Correo electr&oacute;nico">
                            </div>

                            <div class="form-group"></div>
                            <input type="text" hidden value="{{$sale->subscription_plan->id}}" name="planId">
                            <input type="text" hidden value=" {{$sale->id}}" name="saleId">
                            <input type="text" hidden id="IpAddress" name="IpAddress">
                            <input type="text" hidden id="deviceFingerPrintID" name="deviceFingerPrintID">
                            <input type="text" hidden value="{{$planSelected}}" name="planSelected">
                            <input type="text" hidden id="first_name" name="first_name">
                            <input type="text" hidden id="last_name" name="last_name">
                            <input type="text" hidden id="cardMonth" name="cardMonth">
                            <input type="text" hidden id="cardYear" name="cardYear">
                            <div class="btn-holder">
                                <h6>Nota: Los datos sensibles de su tarjeta no se guardar&aacute;n en nuestra base de datos, ser&aacute;n utilizados solamente para procesar su pago</h6>
                                <button type="submit" id="btn-submit" class="btn btn-primary btn-next" onclick="CambiarNombre();">Confirmar pago</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.css" />
    <script src="../assets/js/cybs_devicefingerprint.js"></script>
    <script type="text/javascript">
        var card = new Card({
            form: 'form.tarjeta',
            container: '.card-wrapper',
            formSelectors: {
                nameInput: 'input[name="first-name"], input[name="last-name"]'
            }
        });
        $.getJSON('https://api.ipify.org?format=json', function(data){
            $("#IpAddress").val(data.ip);
        });
        function fusb() {
            var exp = $("#expiry").val();
            console.log(exp);
        }
        function CambiarNombre() {
            $("#first_name").val($('#first-name').val());
            $("#last_name").val($('#last-name').val());
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
        function toggleStep(id) {
            $('.step-section, .step-btn').removeClass('is-active');
            $('.'+id).addClass('is-active');
            $('.wizard-container').prop('class', id+'-selected wizard-container');
        }

        //document.write('Session Id <input type="text" name="deviceFingerprintID" value="' + cybs_dfprofiler("tc_cr_01100XXXX","test") + '">');
    </script>

@endsection
