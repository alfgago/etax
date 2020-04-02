<div class="form-group col-md-12">
  <h3>
    Ingrese la información de su empresa
  </h3>
</div>
<div class="form-group col-md-4">
  <label for="tipo_persona">Tipo de persona *</label> 
  <select class="form-control checkEmpty" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
    <option value="F" <?php echo e(@$company->type == 'F' ? 'selected' : ''); ?> >Física</option>
    <option value="J" <?php echo e(@$company->type == 'J' ? 'selected' : ''); ?>>Jurídica</option>
    <option value="D" <?php echo e(@$company->type == 'D' ? 'selected' : ''); ?>>DIMEX</option>
    <option value="N" <?php echo e(@$company->type == 'N' ? 'selected' : ''); ?>>NITE</option>
    <option value="E" <?php echo e(@$company->type == 'E' ? 'selected' : ''); ?>>Extranjero</option>
    <option value="O" <?php echo e(@$company->type == 'O' ? 'selected' : ''); ?>>Otro</option>
  </select>
</div>

<div class="form-group col-md-4" style="white-space: nowrap;">
  <label for="id_number">Número de identificación *</label>
  <input type="number" class="form-control checkEmpty" name="id_number" id="id_number" value="<?php echo e(@$company->id_number); ?>" required onchange="getJSONCedula(this.value);" <?php if(in_array(8, auth()->user()->permisos()) ): ?> readonly <?php endif; ?>>
</div>

<div class="form-group col-md-4">
  <label for="business_name">Razón Social *</label>
  <input type="text" class="form-control " name="business_name" id="business_name" value="<?php echo e(@$company->business_name); ?>">
</div>

<div class="form-group col-md-4">
  <label for="first_name">Nombre comercial *</label>
  <input type="text" class="form-control checkEmpty" name="name" id="name" value="<?php echo e(@$company->name); ?>" required>
</div>

<div class="form-group col-md-4">
  <label for="last_name">Apellido *</label>
  <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo e(@$company->last_name); ?>" >
</div>

<div class="form-group col-md-4">
  <label for="last_name2">Segundo apellido</label>
  <input type="text" class="form-control" name="last_name2" id="last_name2" value="<?php echo e(@$company->last_name2); ?>" >
</div>

<div class="form-group col-md-4">
  <label for="email">Correo electrónico *</label>
  <input type="email" class="form-control checkEmpty" name="email" id="email" value="<?php echo e(@$company->email); ?>" required>
</div>

<div class="form-group col-md-4">
  <label for="phone">Teléfono</label>
  <input type="number" class="form-control" name="phone" id="phone" value="<?php echo e(@$company->phone); ?>" onblur="validatePhoneFormat();">
</div>

<div class="form-group col-md-4">
  <label for="input_logo">Logo Empresa *</label>
  <div class="fallback">
    <input name="input_logo" id="input_logo" class="form-control " type="file" multiple="false" >
  </div>
</div>

<div class="form-group col-md-12">
  <label for="commercial_activities">Actividad comercial principal *</label>
  <select class="form-control checkEmpty select-search-wizard" name="commercial_activities" id="commercial_activities" required>
      <option value='' selected>-- No seleccionado --</option>
      <?php $__currentLoopData = $actividades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actividad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($actividad['codigo']); ?>" ><?php echo e($actividad['codigo']); ?> - <?php echo e($actividad['actividad']); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step2');">Siguiente paso</button>
</div>
<?php if(in_array(8, auth()->user()->permisos()) ): ?>
  <script>
    $(document).ready(function(){
      getJSONCedula(114670729);
      toggleApellidos();
    });
  </script> 
<?php endif; ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/wizard/pasos/paso1.blade.php ENDPATH**/ ?>