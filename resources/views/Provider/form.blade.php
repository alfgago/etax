

    <div class="form-group col-md-4">
      <label for="code">Código *</label>
      <input type="text" class="form-control" name="code" id="code" value="{{ @$provider->code }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="tipo_persona">Tipo de persona *</label>
      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();" onchange="cambiarDireccion();">
        <option value="F" {{ @$provider->tipo_persona == 'F' ? 'selected' : '' }}>Física</option>
        <option value="J" {{ @$provider->tipo_persona == 'J' ? 'selected' : '' }}>Jurídica</option>
        <option value="D" {{ @$provider->tipo_persona == 'D' ? 'selected' : '' }}>DIMEX</option>
        <option value="N" {{ @$provider->tipo_persona == 'N' ? 'selected' : '' }}>NITE</option>
        <option value="E" {{ @$provider->tipo_persona == 'E' ? 'selected' : '' }}>Extranjero</option>
        <option value="O" {{ @$provider->tipo_persona == 'O' ? 'selected' : '' }}>Otro</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="id_number">Número de identificación *</label>
      <input type="number" class="form-control" name="id_number" id="id_number" value="{{ @$provider->id_number }}" required onchange="getJSONCedula(this.value);">
    </div>
    
    <div class="form-group col-md-4">
      <label for="first_name">Nombre *</label>
      <input type="text" class="form-control" name="first_name" id="first_name" value="{{ @$provider->first_name }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name">Apellido</label>
      <input type="text" class="form-control" name="last_name" id="last_name" value="{{ @$provider->last_name }}" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name2">Segundo apellido</label>
      <input type="text" class="form-control" name="last_name2" id="last_name2" value="{{ @$provider->last_name2 }}" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="email">Correo electrónico *</label>
      <input type="email" class="form-control" name="email" id="email" value="{{ @$provider->email }}" required onblur="validateEmail(this.value);">
    </div>
    
    <div class="form-group col-md-4">
      <label for="phone">Teléfono</label>
      <input type="number" class="form-control" name="phone" id="phone" value="{{ @$provider->phone }}" onblur="validatePhoneFormat();">
    </div>
    
    <div class="form-group col-md-4"></div>
    
    <div class="form-group col-md-4" id="divCountry">
      <label for="country">País *</label>
      <select class="form-control" name="country" id="country" value="{{ @$provider->country }}" required onchange="cambiarTipoPersona();">
          <option value="CR" selected>Costa Rica</option>
          @foreach ( \App\CodigosPaises::orderBy('country_code', 'ASC')->get() as $pais )
              <option value="{{ $pais['country_code'] }}" {{ @$provider->country == $pais->country_code ? 'selected' : ''}}>{{ $pais['country_code'] }} - {{ $pais['country_name'] }}</option>
          @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-4" id="divState">
      <label for="state">Provincia</label>
      <select class="form-control" name="state" id="state" value="{{ @$provider->state }}"  onchange="fillCantones();">
      </select>
    </div>
    
    <div class="form-group col-md-4" id="divCity">
      <label for="city">Canton</label>
      <select class="form-control" name="city" id="city" value="{{ @$provider->city }}" onchange="fillDistritos();">
      </select>
    </div>
    
    <div class="form-group col-md-4" id="divDistrict">
      <label for="district">Distrito</label>
      <select class="form-control" name="district" id="district" value="{{ @$provider->district }}" onchange="fillZip();" >
      </select>
    </div>
    
    <div class="form-group col-md-4" id="divNeighborhood">
      <label for="neighborhood">Barrio</label>
      <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$provider->neighborhood }}" >
      </select>
    </div>
    
    <div class="form-group col-md-4" id="divZip">
      <label for="zip">Código Postal</label>
      <input type="text" class="form-control" name="zip" id="zip" value="{{ @$provider->zip }}" readonly >
    </div>
    <input hidden value="{{ @$provider->id }}" id="id_provider">
    <div class="form-group col-md-12" id="divAddress">
      <label for="address">Dirección</label>
      <textarea class="form-control" name="address" id="address" maxlength="250" rows="2" style="resize: none;">{{ @$provider->address }}</textarea>
    </div>
    <div class="form-group col-md-12" id="extranjero" hidden>
        <label for="address">Otras Señas Extranjero</label>
        <textarea class="form-control" name="foreign_address" id="foreign_address" maxlength="300" rows="2" style="resize: none;">{{ @$client->foreign_address }}</textarea>
    </div>
		<script>
		    $(document).ready(function(){
		    
		  	fillProvincias();
		    $("#billing_emails").tagging({
		      "forbidden-chars":[",",'"',"'","?"],
		      "forbidden-chars-text": "Caracter inválido: ",
		      "edit-on-delete": false,
		      "tag-char": "@"
		    });
		    
		    toggleApellidos();
		    
		    //Revisa si tiene estado, canton y distrito marcados.
		    @if( @$provider->state )
		    	$('#state').val( {{ $provider->state }} );
		    	fillCantones();
		    	@if( @$provider->city )
			    	$('#city').val( {{ $provider->city }} );
			    	fillDistritos();
			    	@if( @$provider->district )
				    	$('#district').val( {{ $provider->district }} );
				    	fillZip();
				    @endif
			    @endif
		    @endif
		    
		  });
		  
      function cambiarDireccion(){
          var tipoPersona = $('#tipo_persona').val();
          var idProvider = $('#id_provider').val();
          if(tipoPersona === 'E'){
              $('#divState').hide();
              $('#divCity').hide();
              $('#divDistrict').hide();
              $('#divNeighborhood').hide();
              $('#divZip').hide();
              $('#divAddress').hide();

              $('#extranjero').removeAttr('hidden');
              if(idProvider === ''){
                  $('#country').val('US');
              }
          }else{
              $('#divState').show();
              $('#divCity').show();
              $('#divDistrict').show();
              $('#divNeighborhood').show();
              $('#divZip').show();
              $('#divAddress').show();

              $('#extranjero').attr("hidden", true);
              $('#country').val('CR');
              setTimeout(fillProvincias, 1000);
          }
      }
      
      cambiarDireccion();
      
      function cambiarTipoPersona(){
          var country = $('#country').val();
          if(country !== 'CR'){
              $('#divState').hide();
              $('#divCity').hide();
              $('#divDistrict').hide();
              $('#divNeighborhood').hide();
              $('#divZip').hide();
              $('#divAddress').hide();

              $('#extranjero').removeAttr('hidden');
              $('#tipo_persona').val('E');
          }else{
              $('#divState').show();
              $('#divCity').show();
              $('#divDistrict').show();
              $('#divNeighborhood').show();
              $('#divZip').show();
              $('#divAddress').show();

              $('#extranjero').attr("hidden", true);
              $('#tipo_persona').val('F');
              setTimeout(fillProvincias, 1000);
          }
      }
		</script>
