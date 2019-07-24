<div class="form-group col-md-12">
  <h3>
    Ingrese la información de su empresa
  </h3>
</div>

<div class="form-group col-md-4">
  <label for="tipo_persona">Tipo de persona *</label> 
  <select class="form-control checkEmpty" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
    <option value="F" {{ @$company->type == 'F' ? 'selected' : '' }}>Física</option>
    <option value="J" {{ @$company->type == 'J' ? 'selected' : '' }}>Jurídica</option>
    <option value="D" {{ @$company->type == 'D' ? 'selected' : '' }}>DIMEX</option>
    <option value="N" {{ @$company->type == 'N' ? 'selected' : '' }}>NITE</option>
    <option value="E" {{ @$company->type == 'E' ? 'selected' : '' }}>Extranjero</option>
    <option value="O" {{ @$company->type == 'O' ? 'selected' : '' }}>Otro</option>
  </select>
</div>

<div class="form-group col-md-4" style="white-space: nowrap;">
  <label for="id_number">Número de identificación *</label>
  <input type="number" class="form-control checkEmpty" name="id_number" id="id_number" value="{{ @$company->id_number }}" required onchange="getJSONCedula(this.value);" onblur="validateIdentificationLenght();">
</div>

<div class="form-group col-md-4">
  <label for="business_name">Razón Social *</label>
  <input type="text" class="form-control " name="business_name" id="business_name" value="{{ @$company->business_name }}">
</div>

<div class="form-group col-md-4">
  <label for="first_name">Nombre comercial *</label>
  <input type="text" class="form-control checkEmpty" name="name" id="name" value="{{ @$company->name }}" required>
</div>

<div class="form-group col-md-4">
  <label for="last_name">Apellido *</label>
  <input type="text" class="form-control" name="last_name" id="last_name" value="{{ @$company->last_name }}" >
</div>

<div class="form-group col-md-4">
  <label for="last_name2">Segundo apellido</label>
  <input type="text" class="form-control" name="last_name2" id="last_name2" value="{{ @$company->last_name2 }}" >
</div>

<div class="form-group col-md-4">
  <label for="email">Correo electrónico *</label>
  <input type="email" class="form-control checkEmpty" name="email" id="email" value="{{ @$company->email }}" required>
</div>

<div class="form-group col-md-4">
  <label for="phone">Teléfono</label>
  <input type="number" class="form-control" name="phone" id="phone" value="{{ @$company->phone }}" >
</div>

<div class="form-group col-md-4">
  <label for="logo">Logo Empresa *</label>
  <div class="fallback">
    <input name="input_logo" id="input_logo" class="form-control " type="file" multiple="false" >
  </div>
</div>

<div class="form-group col-md-12">
  <label for="tipo_persona">Actividad comercial principal *</label>
  <select class="form-control checkEmpty select-search-wizard" name="commercial_activities" id="commercial_activities" required>
      <option value='' selected>-- No seleccionado --</option>
      @foreach ( $actividades as $actividad )
          <option value="{{ $actividad['codigo'] }}" >{{ $actividad['codigo'] }} - {{ $actividad['actividad'] }}</option>
      @endforeach
  </select>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step2');">Siguiente paso</button>
</div>
<script>
    function validateIdentificationLenght(){
        var tCed = $('#tipo_persona').val();
        var identificacion = $('#id_number').val();
        switch (tCed){
            case 'F':
                if(identificacion.length != 9){
                    alert('Utilice 9 dígitos numerales para este tipo de documento');
                    $('#id_number').val('');
                }
                break;
            case 'J':
                if(identificacion.length != 10){
                    alert('Utilice 10 dígitos numerales para este tipo de documento');
                    $('#id_number').val('');
                }
                break;
            case 'D':
                if(identificacion.length != 11 || identificacion.length != 12) {
                    alert('Utilice 11 ó 12 dígitos numerales para este tipo de documento');
                    $('#id_number').val('');
                }
                break;
            case 'N':
                if(identificacion.length != 10){
                    alert('Utilice 10 dígitos numerales para este tipo de documento');
                    $('#id_number').val('');
                }
                break;
            case 'E':
                if(identificacion.length > 20){
                    alert('Utilice un máximo de 20 dígitos numerales para este tipo de documento');
                    $('#id_number').val('');
                }
                break;
            default:
                alert('Debe seleccionar un tipo de persona');
                $('#id_number').val('');
            break;
        }
    }
</script>
