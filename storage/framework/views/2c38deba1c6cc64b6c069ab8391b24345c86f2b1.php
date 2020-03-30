<?php

?>
<div class="form-group col-md-12">
  <h3>
  Prorrata inicial
  </h3>
</div>

<div class="form-group col-md-6">
  <label for="first_prorrata_type">Método de cálculo de prorrata operativa inicial</label>
  <select class="form-control" name="first_prorrata_type" id="first_prorrata_type" onchange="toggleTipoProrrata();" required>
    <option value="1" selected>Registro manual</option>
    <option value="2" >Ingreso de totales por código</option>
  </select>
</div>

<div class="form-group col-md-6 hidden toggle-types type-1" >
  <label for="first_prorrata">Digite su prorrata inicial</label>
  <input type="number" class="form-control" name="first_prorrata" id="first_prorrata" step="0.01" min="1" max="100" value="0" required>
</div>

<div class="form-group col-md-6 hidden toggle-types type-1">
  <label for="operative_ratio1">Digite su proporción de ventas al 1%</label>
  <input type="number" class="form-control" name="operative_ratio1" id="operative_ratio1" step="0.01" min="0" max="100" value="0" required>
</div>

<div class="form-group col-md-6 hidden toggle-types type-1">
  <label for="operative_ratio2">Digite su proporción de ventas al 2%</label>
  <input type="number" class="form-control" name="operative_ratio2" id="operative_ratio2" step="0.01" min="0" max="100" value="0" required>
</div>

<div class="form-group col-md-6 hidden toggle-types type-1">
  <label for="operative_ratio3">Digite su proporción de ventas al 13%</label>
  <input type="number" class="form-control" name="operative_ratio3" id="operative_ratio3" step="0.01" min="0" max="100" value="100" required>
</div>

<div class="form-group col-md-6 hidden toggle-types type-1">
  <label for="operative_ratio4">Digite su proporción de ventas al 4%</label>
  <input type="number" class="form-control" name="operative_ratio4" id="operative_ratio4" step="0.01" min="0" max="100" value="0" required>
</div>

<div class="form-group col-md-6">
  <label for="saldo_favor_2018">Ingrese su saldo a favor acumulado de periodos anteriores</label>
  <input type="number" class="form-control" name="saldo_favor_2018" id="saldo_favor_2018" step="0.01" value="0" required value="0">
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step3');">Paso anterior</button>
  <button type="submit" id="btn-submit" class="btn btn-primary btn-next" onclick="trackClickEvent( 'TerminarConfigInicial' );">Terminar configuración inicial</button>
</div><?php /**PATH /var/www/resources/views/wizard/pasos/paso5.blade.php ENDPATH**/ ?>