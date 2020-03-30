<div class="form-group col-md-12">
  <h3>
    Ingrese la ubicación de la empresa
  </h3>
</div>

<div class="form-group col-md-4">
  <label for="country">País *</label>
  <select class="form-control checkEmpty" name="country" id="country" value="<?php echo e(@$company->country); ?>" required >
    <option value="CR" selected>Costa Rica</option>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="state">Provincia *</label>
  <select class="form-control checkEmpty" name="state" id="state" value="<?php echo e(@$company->state); ?>" onchange="fillCantones();" required>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="city">Cantón *</label>
  <select class="form-control checkEmpty" name="city" id="city" value="<?php echo e(@$company->city); ?>" onchange="fillDistritos();" required>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="district">Distrito *</label>
  <select class="form-control checkEmpty" name="district" id="district" value="<?php echo e(@$company->district); ?>" onchange="fillZip();" required>
  </select>
</div>

<div class="form-group col-md-4">
  <label for="neighborhood">Barrio *</label>
  <input class="form-control" name="neighborhood" id="neighborhood" value="<?php echo e(@$company->neighborhood); ?>" >
  </select>
</div>

<div class="form-group col-md-4">
  <label for="zip">Código Postal</label>
  <input type="text" class="form-control" name="zip" id="zip" value="<?php echo e(@$company->zip); ?>" readonly required>
</div>

<div class="form-group col-md-12">
  <label for="address">Dirección </label>
  <textarea class="form-control " name="address" id="address" maxlength="250" rows="2" style="resize: none;"><?php echo e(@$company->address); ?></textarea>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step1');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step3');">Siguiente paso</button>
</div>
<?php /**PATH /var/www/resources/views/wizard/pasos/paso2.blade.php ENDPATH**/ ?>