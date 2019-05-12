<div class="form-group col-md-12">
  <h3>
    Datos de facturación
  </h3>
</div>

<div class="form-group col-md-6">
  <label for="use_invoicing">¿Desea emitir facturas electrónicas con eTax?</label>
  <select class="form-control checkEmpty" name="use_invoicing" id="use_invoicing" required>
    <option value="1" selected>Sí</option>
    <option value="0" >No</option>
  </select>
</div>

<div class="form-group col-md-6">
  <label for="last_document" style="padding-top:1.4em;">Último documento emitido</label>
  <input type="text" class="form-control" name="last_document" id="last_document" value="{{ @$company->last_document }}" >
  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
</div>

<div class="form-group col-md-12">
  <label for="default_vat_code">Tipo de IVA por defecto</label>
  <select class="form-control" id="default_vat_code" name="default_vat_code" >
    @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
      <option value="{{ $tipo['codigo'] }}" porcentaje="{{ $tipo['porcentaje'] }}" {{ '103' == $tipo['codigo']  ? 'selected' : '' }}>{{ $tipo['nombre'] }}</option>
    @endforeach
  </select>
</div>

<div class="form-group col-md-6">
  <label for="default_currency">Tipo de moneda por defecto</label>
  <select class="form-control" name="default_currency" id="default_currency" >
    <option value="crc" selected>CRC</option>
    <option value="usd" >USD</option>
  </select>
</div>

<div class="form-group col-md-12">
  <label for="default_invoice_notes">Notas por defecto</label>
  <textarea class="form-control" name="default_invoice_notes" id="default_invoice_notes" >{{ @$company->default_invoice_notes }}</textarea>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step2');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step4');">Siguiente paso</button>
</div>