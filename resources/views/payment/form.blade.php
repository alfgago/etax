<div class='card-wrapper' style="margin-left:40% !important;margin-bottom: 5% !important;"></div>
<div class="col-md-12">
    <div class="form-row" style="margin-left: 25%;">
        <div class="form-group col-md-5" style="white-space: nowrap;">
            <label for="number">Nuevo n&#250;mero de tarjeta</label>
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="number" id="number" placeholder="N&#250;mero de tarjeta:" required onblur="valid_credit_card(this.value);">
            <label id="alertCardValid" class="alertCardValid"></label>
        </div>
        <div class="form-group col-md-3" style="white-space: nowrap;">
            <label for="expiry">Expira</label>
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="expiry" id="expiry" placeholder="Mes / A&#241;o:" required onblur="CambiarNombre();">
        </div>
        <div class="form-group col-md-2" style="white-space: nowrap;">
            <label for="cardCcv">CVV</label>
            <input type="text" inputmode="numeric" class="form-control checkEmpty" name="cvc" id="cvc" placeholder="CVV:" required>
        </div>

        <input type="text" hidden id="cardMonth" name="cardMonth">
        <input type="text" hidden id="cardYear" name="cardYear">
        <input type="text" hidden id="Id" name="Id" value="{{$Id}}">
        <div class="btn-holder">
            <h6>Nota: Los datos sensibles de su tarjeta no se guardar&aacute;n en nuestra base de datos, ser&aacute;n utilizados solamente <br> para procesar sus pagos</h6>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.css" />
<script type="text/javascript">
    var card = new Card({
        form: 'form.tarjeta',
        container: '.card-wrapper',
        formSelectors: {
            nameInput: 'input[name="first-name"], input[name="last-name"]'
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
