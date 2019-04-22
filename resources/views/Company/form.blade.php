<input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" required>

<div class="form-group col-md-4">
    <label for="code">Nombre *</label>
    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
    @if ($errors->has('name'))
    <span class="help-block">
        <strong>{{ $errors->first('name') }}</strong>
    </span>
    @endif
</div>

<div class="form-group col-md-4">
    <label for="tipo_persona">Tipo de persona *</label>
    <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
        <option value="">Select Option</option>
        <option value="fisica" <?php echo (old('tipo_persona') == 'fisica') ? 'selected' : ''; ?>>Física</option>
        <option value="juridica" <?php echo (old('tipo_persona') == 'juridica') ? 'selected' : ''; ?>>Jurídica</option>
        <option value="dimex" <?php echo (old('tipo_persona') == 'dimex') ? 'selected' : ''; ?>>DIMEX</option>
        <option value="extranjero" <?php echo (old('tipo_persona') == 'extranjero') ? 'selected' : ''; ?>>NITE</option>
        <option value="nite" <?php echo (old('tipo_persona') == 'nite') ? 'selected' : ''; ?>>Extranjero</option>
        <option value="otro" <?php echo (old('tipo_persona') == 'otro') ? 'selected' : ''; ?>>Otro</option>
    </select>
    @if ($errors->has('tipo_persona'))
    <span class="help-block">
        <strong>{{ $errors->first('tipo_persona') }}</strong>
    </span>
    @endif
</div>

<div class="form-group col-md-4">
    <label for="id_number">Número de identificación *</label>
    <input type="text" class="form-control" name="id_number" id="id_number" value="{{ old('id_number') }}" required>
    @if ($errors->has('id_number'))
    <span class="help-block">
        <strong>{{ $errors->first('id_number') }}</strong>
    </span>
    @endif
</div>

<div class="form-group col-md-4">
    <label for="last_name">Apellido</label>
    <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}">
</div>

<div class="form-group col-md-4">
    <label for="last_name2">Segundo apellido</label>
    <input type="text" class="form-control" name="last_name2" id="last_name2" value="{{ old('last_name2') }}">
</div>

<div class="form-group col-md-4">
    <label for="email">Correo electrónico *</label>
    <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}" required>
</div>

<div class="form-group col-md-4">
    <label for="phone">Teléfono</label>
    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">
</div>

<div class="form-group col-md-4">
    <label for="default_currency">Moneda predeterminada</label>
    <input type="text" class="form-control" name="default_currency" id="default_currency" value="{{!empty(old('default_currency')) ? old('default_currency'):'crc'}}">    
</div>

<div class="form-group col-md-4">
    <label for="country">País *</label>
    <select class="form-control" name="country" id="country" required>
        <option value="CR" selected>Costa Rica</option>
    </select>
</div>

<div class="form-group col-md-4">
    <label for="state">Provincia</label>
    <select class="form-control" name="state" id="state" onchange="fillCantones();"></select>
</div>

<div class="form-group col-md-4">
    <label for="city">Canton</label>
    <select class="form-control" name="city" id="city" onchange="fillDistritos();"></select>
</div>

<div class="form-group col-md-4">
    <label for="district">Distrito</label>
    <select class="form-control" name="district" id="district" onchange="fillZip();"></select>
</div>

<div class="form-group col-md-4">
    <label for="neighborhood">Barrio</label>
    <input class="form-control" name="neighborhood" id="neighborhood" value="{{ old('neighborhood') }}">    
</div>

<div class="form-group col-md-4">
    <label for="zip">Zip</label>
    <input type="text" class="form-control" name="zip" id="zip" value="{{ old('zip') }}" readonly>
</div>

<div class="form-group col-md-4">
    <label for="zip">email de factura</label>
    <input type="text" class="form-control" name="invoice_email" value="{{ old('invoice_email') }}">
</div>

<div class="form-group col-md-12">
    <label for="address">Dirección</label>
    <textarea class="form-control" name="address" id="address" >{{ old('address') }}</textarea>
</div>


<?php /* For multiple invoice emails
  <div class="form-group col-md-12">
  <label for="invoice_email">email de factura</label> {{ old('invoice_email') }}
  <div class="form-group">
  <div data-no-duplicate="true" data-pre-tags-separator="," data-no-duplicate-text="Correos duplicados" data-type-zone-class="type-zone"
  data-tag-box-class="tagging" id="invoice_email" data-tags-input-name="invoice_email">{{ old('invoice_email') }}</div>
  </div>
  </div> */ ?>

<script>

    function toggleApellidos() {
    var tipoPersona = $('#tipo_persona').val();
    if (tipoPersona == 2){
    $('#last_name, #last_name2').val('');
    $('#last_name, #last_name2').attr('readonly', 'true');
    } else{
    $('#last_name, #last_name2').removeAttr('readonly');
    }
    }

    function fillProvincias() {
    if ($('#country').val() == 'CR') {
    var sel = $('#state');
    sel.html("");
    sel.append("<option value='0' selected>-- Seleccione una provincia --</option>");
    $.each(provincias, function(i, val) {
    sel.append("<option value='" + i + "'>" + provincias[i]["Nombre"] + "</option>");
    });
    }
    }

    function fillCantones() {
    var provincia = $('#state').val();
    var sel = $('#city');
    sel.html("");
    sel.append("<option value='0' selected>-- Seleccione un cantón --</option>");
    $.each(cantones, function(i, val) {
    if (provincia == cantones[i]["Provincia"]){
    sel.append("<option value='" + i + "'>" + cantones[i]["Nombre"] + "</option>");
    }
    });
    }

    function fillDistritos() {
    var canton = $('#city').val();
    var sel = $('#district');
    sel.html("");
    sel.append("<option value='0' selected>-- Seleccione un distrito --</option>");
    $.each(distritos, function(i, val) {
    if (canton == distritos[i]["Canton"]){
    sel.append("<option value='" + i + "'>" + distritos[i]["Nombre"] + "</option>");
    }
    });
    }

    function fillZip() {
    var distrito = $('#district').val();
    var sel = $('#zip').val(distrito);
    }

    $(document).ready(function(){

    fillProvincias();
    $("#invoice_email").tagging({
    "forbidden-chars":[",", '"', "'", "?"],
            "forbidden-chars-text": "Caracter inválido: ",
            "edit-on-delete": false,
            "tag-char": "@"
    });
    toggleApellidos();
    //Revisa si tiene estado, canton y distrito marcados.
    @if (@old('state'))
            $('#state').val({{ old('state') }});
    fillCantones();
    @if (@old('city'))
            $('#city').val({{ old('city') }});
    fillDistritos();
    @if (@old('district'))
            $('#district').val({{ old('district') }});
    fillZip();
    @endif
            @endif
            @endif

    });

</script>
