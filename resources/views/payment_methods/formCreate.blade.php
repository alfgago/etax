<style>
    .vl {
        margin-left: 5%;
        border-left: 2px solid black;
        height: 400px;
        margin-right: 1%;
    }
    .newCard{
        margin-left: 2%;
        margin-top: 15%;
    }
    .jp-card-container {
        margin-left: 0 !important;
    }
</style>
<div class="col-md-12 offset-1">
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12" style="white-space: nowrap;">
                    <label for="number">N&#250;mero de tarjeta</label>
                    <input type="text" inputmode="numeric" class="form-control checkEmpty" name="number" id="number" placeholder="N&#250;mero de tarjeta:" required onblur="valid_credit_card(this.value);">
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="expiry">Expira</label>
                    <input type="text" inputmode="numeric" class="form-control checkEmpty" name="expiry" id="expiry" placeholder="Mes / A&#241;o:" required onblur="CambiarNombre();">
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="cardCcv">CVV</label>
                    <input type="text" inputmode="numeric" class="form-control checkEmpty" name="cvc" id="cvc" placeholder="CVV:" required>
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="first_name">Nombre:</label>
                    <input type="text" inputmode="text" class="form-control checkEmpty" name="first_name_card" id="first_name_card" placeholder="Nombre:" required>
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="last_name">Apellido:</label>
                    <input type="text" inputmode="text" class="form-control checkEmpty" name="last_name_card" id="last_name_card" placeholder="Apellido:" required>
                </div>
                <div class="form-group col-md-12" style="white-space: nowrap;">
                    <label for="street1">Ciudad:</label>
                    <input type="text" inputmode="text" class="form-control checkEmpty" name="street1" id="street1" placeholder="Ciudad:" maxlength="40" required>
                </div>
                <div class="form-group col-md-12" style="white-space: nowrap;">
                    <label for="address1">Dirección:</label>
                    <input type="text" inputmode="text" class="form-control checkEmpty" name="address1" id="address1" placeholder="Dirección:" maxlength="40" required>
                </div>

                <input type="text" hidden id="cardMonth" name="cardMonth">
                <input type="text" hidden id="cardYear" name="cardYear">
                <div class="btn-holder">
                    <div class="description">Nota: Los datos sensibles de su tarjeta no se guardar&aacute;n en nuestra base de datos, ser&aacute;n utilizados solamente para procesar sus pagos</div>
                    <p id="alertCardValid" class="alertCardValid"></p>
                </div>
                <input type="text" hidden id="IpAddress" name="IpAddress">
                <input type="text" hidden id="deviceFingerPrintID" name="deviceFingerPrintID">
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-5">
            <div class='card-wrapper newCard'></div>
        </div>
    </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.css" />
<script src="../assets/js/cybs_devicefingerprint.js"></script>
<script type="text/javascript">
    $("#deviceFingerPrintID").val(cybs_dfprofiler("tc_cr_011007172","test"));
    //document.write('Session Id <input type="text" name="deviceFingerprintID" value="' + cybs_dfprofiler("tc_cr_01100XXXX","test") + '">');
    $.getJSON('https://api.ipify.org?format=json', function(data){
        $("#IpAddress").val(data.ip);
    });
    var card = new Card({
        form: 'form.tarjeta',
        container: '.card-wrapper',
        formSelectors: {
            nameInput: 'input[name="first_name_card"], input[name="last_name_card"]'
        }
    });
    function CambiarNombre() {
        var exp = $("#expiry").val();
        $('#cardMonth').val(exp.substr(0,2));
        $('#cardYear').val(exp.substring(exp.length - 2, exp.length));
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
</script>
