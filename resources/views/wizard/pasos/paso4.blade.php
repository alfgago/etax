<div class="form-group col-md-12">
  <h3>
  Certificado digital
  </h3>
  <p class="description">
    <b style="font-size: 1rem; display: block; color: red;">Si usted <u>no</u> va a facturar con eTax, puede ignorar este paso.</b>
  </p>
  
</div>

<div class="form-group col-md-6">
  <label for="user">Usuario ATV *</label>
  <input type="text" class="form-control " name="user" id="user" value="{{ @$certificate->user }}">
</div>

<div class="form-group col-md-6">
  <label for="password">Contraseña ATV *</label>
  <input type="password" class="form-control " name="password" id="password" value="{{ @$certificate->password }}">
</div>

<div class="form-group col-md-6">
  <label for="cert">Llave criptográfica *</label>
  <div class="fallback">
    <input name="cert" id="input-cert" class="form-control " type="file" multiple="false" placeholder="p12">
  </div>
</div>

<div class="form-group col-md-6">
  <label for="pin">PIN de llave criptográfica *</label>
  <input type="text" class="form-control " name="pin" id="pin" value="{{ @$certificate->pin }}">
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step3');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step5');">Siguiente paso</button>
</div>