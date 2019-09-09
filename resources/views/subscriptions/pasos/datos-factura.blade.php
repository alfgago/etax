
<div class="form-group col-md-12">
  <h3 class="mt-0">
    Datos de facturación
  </h3>
  <p class="">Ingrese los datos del receptor de la factura de su suscripción a eTax. <u>Cambiar los datos para facturación no altera la información de su empresa.</u></p>
</div>

<div class="form-group col-md-4">
    <label for="tipo_persona">Tipo de persona *</label>
    <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
        <option value="F" {{ @$company->type == 'F' ? 'selected' : '' }} >Física</option>
        <option value="J" {{ @$company->type == 'J' ? 'selected' : '' }}>Jurídica</option>
        <option value="D" {{ @$company->type == 'D' ? 'selected' : '' }}>DIMEX</option>
        <option value="N" {{ @$company->type == 'N' ? 'selected' : '' }}>NITE</option>
        <option value="E" {{ @$company->type == 'E' ? 'selected' : '' }}>Extranjero</option>
        <option value="O" {{ @$company->type == 'O' ? 'selected' : '' }}>Otro</option>
    </select>
</div>

<div class="form-group col-md-4" style="white-space: nowrap;">
    <label for="id_number">Número de identificación *</label>
    <input type="number" class="form-control checkEmpty" name="id_number" id="id_number" onchange="getJSONCedula(this.value);" value="{{ @$company->id_number }}">
</div>

<div class="form-group col-md-4">
    <label for="first_name">Nombre *</label>
    <input type="text" class="form-control checkEmpty" name="first_name" id="first_name" value="{{ @$company->name }}" >
</div>

<div class="form-group col-md-4">
    <label for="last_name">Apellido</label>
    <input type="text" class="form-control " name="last_name" id="last_name" value="{{ @$company->last_name }}" >
</div>

<div class="form-group col-md-4">
    <label for="last_name2">Segundo apellido</label>
    <input type="text" class="form-control " name="last_name2" id="last_name2" value="{{ @$company->last_name2 }}" >
</div>

<div class="form-group col-md-4">
    <label for="email">Correo electrónico *</label>
    <input type="text" class="form-control checkEmpty" name="email" id="email" value="{{ @$company->email }}" >
</div>

<div class="form-group col-md-4">
    <label for="phone">Teléfono</label>
    <input type="text" class="form-control checkEmpty" name="phone" id="phone" value="{{ @$company->phone }}" >
</div>

<div></div>

<div class="form-group col-md-4">
    <label for="country">País *</label>
    <select class="form-control checkEmpty" name="country" id="country" >
        <option value="CR" selected>Costa Rica</option>
    </select>
</div>

<div class="form-group col-md-4">
    <label for="state">Provincia</label>
    <select class="form-control checkEmpty" name="state" id="state" onchange="fillCantones();">
    </select>
</div>

<div class="form-group col-md-4">
    <label for="city">Canton</label>
    <select class="form-control checkEmpty" name="city" id="city" onchange="fillDistritos();">
    </select>
</div>

<div class="form-group col-md-4">
    <label for="district">Distrito</label>
    <select class="form-control checkEmpty" name="district" id="district" onchange="fillZip();" >
    </select>
</div>

<div class="form-group col-md-4">
    <label for="neighborhood">Barrio</label>
    <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$company->neighborhood }}" >
    </select>
</div>

<div class="form-group col-md-4">
    <label for="zip">Código Postal</label>
    <input type="text" class="form-control" name="zip" id="zip" readonly >
</div>
<div class="form-group col-md-8">
    <label for="address">Dirección</label>
    <input class="form-control checkEmpty" name="address" id="address" value="{{ @$company->address }}">
</div>

<div class="form-group col-md-4 hidden">
    <label for="es_exento">Exento de IVA</label>
    <select class="form-control" name="es_exento" id="es_exento" >
        <option value="0" >No</option>
        <option value="1" >Sí</option>
    </select>
</div>
<input hidden id="cardState" name="cardState">
<input hidden id="cardCity" name="cardCity">
<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="backFields();toggleStep('step1');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step3');"  onclick="trackClickEvent( 'PagosPaso3' );">Siguiente paso</button>
</div>
<script>
    $('#state').on('change', function() {
        var state = $( "#state option:selected" ).text();
        $('#cardState').val(state);
    });
    $('#city').on('change', function() {
        var city = $( "#city option:selected" ).text();
        $('#cardCity').val(city);
    });
</script>
