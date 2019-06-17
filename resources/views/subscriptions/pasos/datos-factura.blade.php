<div class="form-group col-md-12">
  <h3 class="mt-0">
    Datos de facturación
  </h3>
  <p class="description">Ingrese los datos del receptor de la factura de su suscripción a eTax.</p>
</div>

<div class="form-group col-md-4">
    <label for="tipo_persona">Tipo de persona *</label>
    <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
        <option value="F" >Física</option>
        <option value="J" >Jurídica</option>
        <option value="D" >DIMEX</option>
        <option value="N" >NITE</option>
        <option value="E" >Extranjero</option>
        <option value="O" >Otro</option>
    </select>
</div>

<div class="form-group col-md-4" style="white-space: nowrap;">
    <label for="id_number">Número de identificación *</label>
    <input type="text" class="form-control checkEmpty" name="id_number" id="id_number" onchange="getJSONCedula(this.value);" value="{{ @old('id_number') }}">
</div>

<div class="form-group col-md-4">
    <label for="first_name">Nombre *</label>
    <input type="text" class="form-control checkEmpty" name="first_name" id="first_name" value="{{ @old('first_name') }}" >
</div>

<div class="form-group col-md-4">
    <label for="last_name">Apellido</label>
    <input type="text" class="form-control " name="last_name" id="last_name" value="{{ @old('last_name') }}" >
</div>

<div class="form-group col-md-4">
    <label for="last_name2">Segundo apellido</label>
    <input type="text" class="form-control " name="last_name2" id="last_name2" value="{{ @old('last_name2') }}" >
</div>

<div class="form-group col-md-4">
    <label for="email">Correo electrónico *</label>
    <input type="text" class="form-control checkEmpty" name="email" id="email" value="{{ @old('email') }}" >
</div>

<div class="form-group col-md-4">
    <label for="phone">Teléfono</label>
    <input type="text" class="form-control checkEmpty" name="phone" id="phone" value="{{ @old('phone') }}" >
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
    <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @old('neighborhood') }}" >
    </select>
</div>

<div class="form-group col-md-4">
    <label for="zip">Zip</label>
    <input type="text" class="form-control" name="zip" id="zip" readonly >
</div>
<div class="form-group col-md-8">
    <label for="address">Dirección</label>
    <input class="form-control checkEmpty" name="address" id="address" value="{{ @old('address') }}">
</div>

<div class="form-group col-md-4 hidden">
    <label for="es_exento">Exento de IVA</label>
    <select class="form-control" name="es_exento" id="es_exento" >
        <option value="0" >No</option>
        <option value="1" >Sí</option>
    </select>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step1');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step3');">Siguiente paso</button>
</div>
