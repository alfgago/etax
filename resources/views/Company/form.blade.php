<input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" required>

<div class="form-group col-md-4">
    <label for="code">Nombre *</label>
    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
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
</div>

<div class="form-group col-md-4">
    <label for="id_number">Número de identificación *</label>
    <input type="text" class="form-control" name="id_number" id="id_number" value="{{ old('id_number') }}" required>
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
    <label for="zip">Código Postal</label>
    <input type="text" class="form-control" name="zip" id="zip" value="{{ old('zip') }}" readonly>
</div>

<div class="form-group col-md-4">
    <label for="invoice_email">Correo de facturación</label>
    <input type="text" class="form-control" name="invoice_email" id="invoice_email" value="{{ old('invoice_email') }}">
</div>

<div class="form-group col-md-12">
    <label for="address">Dirección</label>
    <textarea class="form-control" name="address" id="address" maxlength="250" rows="2" style="resize: none;">{{ old('address') }}</textarea>
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
