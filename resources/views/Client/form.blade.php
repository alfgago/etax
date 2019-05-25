
    <input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" required>
    
    <div class="form-group col-md-4">
      <label for="code">Código *</label>
      <input type="text" class="form-control" name="code" id="code" value="{{ @$client->code }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="tipo_persona">Tipo de persona *</label>
      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
        <option value="1" {{ @$client->tipo_persona == 1 ? 'selected' : '' }} >Física</option>
        <option value="2" {{ @$client->tipo_persona == 2 ? 'selected' : '' }}>Jurídica</option>
        <option value="3" {{ @$client->tipo_persona == 3 ? 'selected' : '' }}>DIMEX</option>
        <option value="4" {{ @$client->tipo_persona == 5 ? 'selected' : '' }}>NITE</option>
        <option value="5" {{ @$client->tipo_persona == 4 ? 'selected' : '' }}>Extranjero</option>
        <option value="6" {{ @$client->tipo_persona == 6 ? 'selected' : '' }}>Otro</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="id_number">Número de identificación *</label>
      <input type="text" class="form-control" name="id_number" id="id_number" value="{{ @$client->id_number }}" required onchange="getJSONCedula(this.value);">
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
      <input type="text" class="form-control" name="email" id="email" value="{{ @$client->email }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="phone">Teléfono</label>
      <input type="text" class="form-control" name="phone" id="phone" value="{{ @$client->phone }}" >
    </div>
    
    <div class="form-group col-md-4"></div>
    
    <div class="form-group col-md-4">
      <label for="country">País *</label>
      <select class="form-control" name="country" id="country" value="{{ @$client->country }}" required >
        <option value="CR" selected>Costa Rica</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="state">Provincia</label>
      <select class="form-control" name="state" id="state" value="{{ @$client->state }}" onchange="fillCantones();">
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="city">Canton</label>
      <select class="form-control" name="city" id="city" value="{{ @$client->city }}" onchange="fillDistritos();">
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="district">Distrito</label>
      <select class="form-control" name="district" id="district" value="{{ @$client->district }}" onchange="fillZip();" >
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="neighborhood">Barrio</label>
      <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$client->neighborhood }}" >
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="zip">Zip</label>
      <input type="text" class="form-control" name="zip" id="zip" value="{{ @$client->zip }}" readonly >
    </div>
    
    <div class="form-group col-md-12">
      <label for="address">Dirección</label>
      <textarea class="form-control" name="address" id="address" >{{ @$client->address }}</textarea>
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
