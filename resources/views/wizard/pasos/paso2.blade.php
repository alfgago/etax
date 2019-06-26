<div class="form-group col-md-12">
  <h3>
    Ingrese la ubicación de la empresa
  </h3>
</div>

<div class="form-group col-md-4">
  <label for="country">País *</label>
  <select class="form-control checkEmpty" name="country" id="country" value="{{ @$company->country }}" required >
    <option value="CR" selected>Costa Rica</option>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="state">Provincia *</label>
  <select class="form-control checkEmpty" name="state" id="state" value="{{ @$company->state }}" onchange="fillCantones();" required>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="city">Cantón *</label>
  <select class="form-control checkEmpty" name="city" id="city" value="{{ @$company->city }}" onchange="fillDistritos();" required>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="district">Distrito *</label>
  <select class="form-control checkEmpty" name="district" id="district" value="{{ @$company->district }}" onchange="fillZip();" required>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="neighborhood">Barrio *</label>
  <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$company->neighborhood }}" >
  </select>
</div>

<div class="form-group col-md-4">
  <label for="zip">Zip</label>
  <input type="text" class="form-control" name="zip" id="zip" value="{{ @$company->zip }}" readonly >
</div>

<div class="form-group col-md-12">
  <label for="address">Dirección </label>
  <textarea class="form-control " name="address" id="address" >{{ @$company->address }}</textarea>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step1');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step3');">Siguiente paso</button>
</div>
