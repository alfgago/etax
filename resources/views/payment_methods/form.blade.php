<style>
    .vl {
        margin-left: 5%;
        border-left: 2px solid black;
        height: 400px;
        margin-right: 1%;
    }
    .newCard{
        margin-left: 10% !important;
        margin-top: 15%;
        width: 100%;
    }
    .jp-card-container {
        margin-left: 0 !important;
    }
</style>
<div class="col-md-12 offset-4">
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12" style="white-space: nowrap;">
                    <label for="number">Nuevo n&#250;mero de tarjeta</label>
                    <input type="text" inputmode="numeric" class="form-control checkEmpty" name="number" id="number" placeholder="N&#250;mero de tarjeta:" required onblur="valid_credit_card(this.value);">
                    <label id="alertCardValid" class="alertCardValid"></label>
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="expiry">Expira</label>
                    <input type="text" inputmode="numeric" class="form-control checkEmpty" value="{{$paymentMethod->due_date}}" name="expiry" id="expiry" placeholder="Mes / A&#241;o:" required onblur="CambiarNombre();">
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="cardCcv">CVV</label>
                    <input type="text" inputmode="numeric" class="form-control checkEmpty" name="cvc" id="cvc" placeholder="CVV:" required>
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="first_name_card">Nombre:</label>
                    <input type="text" inputmode="text" class="form-control checkEmpty" value="{{$paymentMethod->name}}" name="first_name_card" id="first_name_card" placeholder="Nombre tarjeta-habiente:" required>
                </div>
                <div class="form-group col-md-6" style="white-space: nowrap;">
                    <label for="last_name_card">Apellido:</label>
                    <input type="text" inputmode="text" class="form-control checkEmpty" value="{{$paymentMethod->last_name}}" name="last_name_card" id="last_name_card" placeholder="Apellido tarjeta-habiente:" required>
                </div>
            </div>
            <input type="text" hidden id="neighborhood" name="neighborhood">
            <input type="text" hidden id="address" name="address">
            <div class="btn-holder">
                <h6>Nota: Los datos sensibles de su tarjeta no se guardar&aacute;n en nuestra base de datos, ser&aacute;n utilizados solamente para procesar sus pagos</h6>
            </div>
        </div>
        <div class="vl"></div>
        <div class="col-md-5">
            <div class='card-wrapper newCard'></div>
        </div>
    </div>
</div>
<input type="text" hidden id="Id" name="Id" value="{{$Id}}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.css" />
<script type="text/javascript">
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
