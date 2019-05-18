<div class="form-group col-md-12">
  <h3>
  Certificado digital
  </h3>
  <p class="description">No podrá enviar facturas por medio de eTax hasta haber registrado su certificado de ATV. Si usted <u>no</u> va a facturar con eTax, puede ignorar este paso.</p>
</div>

<div class="form-group col-md-6">
  <label for="user">Usuario ATV *</label>
  <input type="text" class="form-control checkEmpty" name="user" id="user" value="{{ @$certificate->user }}" required>
</div>

<div class="form-group col-md-6">
  <label for="password">Contraseña ATV *</label>
  <input type="password" class="form-control checkEmpty" name="password" id="password" value="{{ @$certificate->password }}" required>
</div>

<div class="form-group col-md-6">
  <label for="cert">Llave criptográfica *</label>
  <div class="fallback">
    <input name="cert" class="form-control checkEmpty" type="file" multiple="false" required>
  </div>
</div>

<div class="form-group col-md-6">
  <label for="pin">PIN de llave criptográfica *</label>
  <input type="text" class="form-control checkEmpty" name="pin" id="pin" value="{{ @$certificate->pin }}" required>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step3');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step5');">Siguiente paso</button>
</div>