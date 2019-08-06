
    <input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" required>
    
    <div class="form-group col-md-4">
      <label for="code">Código *</label>
      <input type="text" class="form-control" name="code" id="code" value="{{ @$client->code }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="tipo_persona">Tipo de persona *</label>
      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();" onchange="cambiarDireccion();">
        <option value="F" {{ @$client->tipo_persona == 'F' ? 'selected' : '' }} >Física</option>
        <option value="J" {{ @$client->tipo_persona == 'J' ? 'selected' : '' }}>Jurídica</option>
        <option value="D" {{ @$client->tipo_persona == 'D' ? 'selected' : '' }}>DIMEX</option>
        <option value="N" {{ @$client->tipo_persona == 'N' ? 'selected' : '' }}>NITE</option>
        <option value="E" {{ @$client->tipo_persona == 'E' ? 'selected' : '' }}>Extranjero</option>
        <option value="O" {{ @$client->tipo_persona == 'O' ? 'selected' : '' }}>Otro</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="id_number">Número de identificación *</label>
      <input max="20" maxlength="20" class="form-control" name="id_number" id="id_number" value="{{ @$client->id_number }}" required onchange="getJSONCedula(this.value);" onblur="validateIdentificationLength();">
    </div>

    <div class="form-group col-md-4">
      <label for="first_name">Nombre *</label>
      <input type="text" class="form-control" name="first_name" id="first_name" value="{{ @$client->first_name }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name">Apellido</label>
      <input type="text" class="form-control" name="last_name" id="last_name" value="{{ @$client->last_name }}" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name2">Segundo apellido</label>
      <input type="text" class="form-control" name="last_name2" id="last_name2" value="{{ @$client->last_name2 }}" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="email">Correo electrónico *</label>
      <input type="text" class="form-control" name="email" id="email" value="{{ @$client->email }}" required onblur="validateEmail();" maxlength="160">
    </div>
    
    <div class="form-group col-md-4">
      <label for="phone">Teléfono</label>
      <input type="number" class="form-control" name="phone" id="phone" value="{{ @$client->phone }}" maxlength="20">
    </div>
    
    <div class="form-group col-md-4"></div>
    <div class="form-group col-md-4">
      <label for="country">País *</label>
      <select class="form-control" name="country" id="country" value="{{ @$client->country }}" required>
          <option value="CR">CR - Costa Rica</option>
          @foreach ( \App\CodigosPaises::all() as $pais )
              <option value="{{ $pais['country_code'] }}" {{ $pais['country_code'] == @$client->country ? 'selected' : ''}}>{{ $pais['country_code'] }} - {{ $pais['country_name'] }}</option>
          @endforeach
      </select>
    </div>

    <div class="form-group col-md-4" id="divState">
      <label for="state">Provincia</label>
      <select class="form-control" name="state" id="state" value="{{ @$client->state }}" onchange="fillCantones();">
      </select>
    </div>

    <div class="form-group col-md-4" id="divCity">
      <label for="city">Canton</label>
      <select class="form-control" name="city" id="city" value="{{ @$client->city }}" onchange="fillDistritos();">
      </select>
    </div>

    <div class="form-group col-md-4" id="divDistrict">
      <label for="district">Distrito</label>
      <select class="form-control" name="district" id="district" value="{{ @$client->district }}" onchange="fillZip();" >
      </select>
    </div>

    <div class="form-group col-md-4" id="divNeighborhood">
      <label for="neighborhood">Barrio</label>
      <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$client->neighborhood }}" >
      </select>
    </div>

    <div class="form-group col-md-4" id="divZip">
      <label for="zip">Zip</label>
      <input type="text" class="form-control" name="zip" id="zip" value="{{ @$client->zip }}" readonly >
    </div>

    <div class="form-group col-md-12" id="divAddress">
      <label for="address">Dirección</label>
      <textarea class="form-control" name="address" id="address" >{{ @$client->address }}</textarea>
    </div>

    <div class="form-group col-md-12" id="extranjero" hidden>
        <label for="address">Otras Señas Extranjero</label>
        <textarea class="form-control" name="foreign_address" id="foreign_address" maxlength="160">{{ @$client->foreign_address }}</textarea>
    </div>
    <div class="form-group col-md-12">
      <label for="billing_emails">Correos electrónicos para facturación</label>
      <div class="form-group">
        <div data-no-duplicate="true" data-pre-tags-separator="," data-no-duplicate-text="Correos duplicados" data-type-zone-class="type-zone" 
          data-tag-box-class="tagging" id="billing_emails" data-tags-input-name="billing_emails">{{ @$client->billing_emails }}</div>
        <p class="text-muted"><small>Ingrese los correos separados por coma. Si lo deja en blanco, por defecto se enviarán las facturas al correo electrónico del cliente.</small> </p>
      </div>
    </div>
    
    <div class="form-group col-md-4">
      <label for="emisor_receptor">Emisor / Receptor</label>
      <select class="form-control" name="emisor_receptor" id="emisor_receptor" >
        <option value="ambos" {{ @$client->emisor_receptor == '1' ? 'selected' : '' }}>Emisor y receptor</option>
        <option value="receptor" {{ @$client->emisor_receptor == '2' ? 'selected' : '' }}>Receptor</option>
        <option value="emisor" {{ @$client->emisor_receptor == '3' ? 'selected' : '' }}>Emisor</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
        <label for="es_exento">Exento de IVA</label>
        <select class="form-control" name="es_exento" id="es_exento" >
          <option value="0" {{ @$client->es_exento ? '' : 'selected' }}>No</option>
          <option value="1" {{ @$client->es_exento ? 'selected' : '' }}>Sí</option>
        </select>
    </div>
<style>
    .error {
        border:1px solid red;
    }
</style>
<script>
    function cambiarDireccion() {
        var tipoPersona = $('#tipo_persona').val();
        if(tipoPersona != undefined){
            if (tipoPersona === 'E') {
                $('#divState').hide('slow');
                $('#divCity').hide('slow');
                $('#divDistrict').hide('slow');
                $('#divNeighborhood').hide('slow');
                $('#divZip').hide('slow');
                $('#divAddress').hide('slow');

                $('#extranjero').removeAttr('hidden');
            } else {
                $('#divState').show('slow');
                $('#divCity').show('slow');
                $('#divDistrict').show('slow');
                $('#divNeighborhood').show('slow');
                $('#divZip').show('slow');
                $('#divAddress').show('slow');

                $('#extranjero').attr("hidden", true);
            }
        }
    }
    cambiarDireccion();
    $("#id_number").keyup(function() {
        $("#id_number").val(this.value.match(/[0-9]*/));
    });
    function validateEmail() {
        var email = $('#email').val();
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if(re.test(String(email).toLowerCase()) != true){
            alert('La direccion de correo electronico no coincide con ningun formato de correo');
            $('#email').addClass('error');
        }else{
            $('#email').removeClass('error');
        }
    }
</script>
