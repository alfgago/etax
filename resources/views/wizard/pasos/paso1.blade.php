<div class="form-group col-md-12">
  <h3>
    Ingrese la información de su empresa
  </h3>
</div>

<div class="form-group col-md-4">
  <label for="tipo_persona">Tipo de persona *</label> 
  <select class="form-control checkEmpty" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
    <option value="F" {{ @$company->type == 'F' ? 'selected' : '' }} >Física</option>
    <option value="J" {{ @$company->type == 'J' ? 'selected' : '' }}>Jurídica</option>
    <option value="D" {{ @$company->type == 'D' ? 'selected' : '' }}>DIMEX</option>
    <option value="N" {{ @$company->type == 'N' ? 'selected' : '' }}>NITE</option>
    <option value="E" {{ @$company->type == 'E' ? 'selected' : '' }}>Extranjero</option>
    <option value="O" {{ @$company->type == 'O' ? 'selected' : '' }}>Otro</option>
  </select>
</div>

<div class="form-group col-md-4" style="white-space: nowrap;">
  <label for="id_number">Número de identificación *</label>
  <input type="text" class="form-control checkEmpty" name="id_number" id="id_number" value="{{ @$company->id_number }}" required onchange="getJSONCedula(this.value);">
</div>

<div class="form-group col-md-4">
  <label for="business_name">Razón Social *</label>
  <input type="text" class="form-control " name="business_name" id="business_name" value="{{ @$company->business_name }}" required>
</div>

<div class="form-group col-md-4">
  <label for="first_name">Nombre comercial *</label>
  <input type="text" class="form-control checkEmpty" name="name" id="name" value="{{ @$company->name }}" required>
</div>

<div class="form-group col-md-4">
  <label for="last_name">Apellido</label>
  <input type="text" class="form-control" name="last_name" id="last_name" value="{{ @$company->last_name }}" >
</div>

<div class="form-group col-md-4">
  <label for="last_name2">Segundo apellido</label>
  <input type="text" class="form-control" name="last_name2" id="last_name2" value="{{ @$company->last_name2 }}" >
</div>

<div class="form-group col-md-4">
  <label for="email">Correo electrónico *</label>
  <input type="text" class="form-control checkEmpty" name="email" id="email" value="{{ @$company->email }}" required>
</div>

<div class="form-group col-md-4">
  <label for="phone">Teléfono</label>
  <input type="text" class="form-control" name="phone" id="phone" value="{{ @$company->phone }}" >
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step2');">Siguiente paso</button>
</div>


<script>
  
  
  
</script>