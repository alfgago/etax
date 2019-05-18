
<div class="form-group col-md-12">
  <h3>
  Prorrata inicial
  </h3>
</div>

<div class="form-group col-md-6">
  <label for="first_prorrata_type">Método de cálculo de prorrata operativa inicial</label>
  <select class="form-control" name="first_prorrata_type" id="first_prorrata_type" onchange="toggleTipoProrrata();" required>
    <option value="1" {{ @$company->first_prorrata_type == 1 ? 'selected' : '' }}>Registro manual</option>
    <option value="2" {{ @$company->first_prorrata_type == 2 ? 'selected' : '' }}>Ingreso de totales por código</option>
    <option value="3" {{ @$company->first_prorrata_type == 3 ? 'selected' : '' }}>Ingreso de facturas del 2018</option>
  </select>
</div>

<div class="form-group col-md-6 hidden toggle-types type-1" style="padding-top: 1em;">
  <label for="first_prorrata">Digite su prorrata inicial</label>
  <input type="number" class="form-control" name="first_prorrata" id="first_prorrata" step="0.01" min="1" max="100" value="{{ @$company->first_prorrata ? $company->first_prorrata : 100 }}">
</div>

<div class="form-group col-md-6">
  <label for="saldo_favor_2018">Ingrese su saldo a favor acumulado de periodos anteriores</label>
  <input type="number" class="form-control" name="saldo_favor_2018" id="saldo_favor_2018" step="0.01" value="0">
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step4');">Paso anterior</button>
  <button type="submit" id="btn-submit" class="btn btn-primary btn-next" >Terminar configuración inicial</button>
</div>