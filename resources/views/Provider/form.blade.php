

    <div class="form-group col-md-4">
      <label for="code">Código *</label>
      <input type="text" class="form-control" name="code" id="code" value="{{ @$provider->code }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="tipo_persona">Tipo de persona *</label>
      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
        <option value="F" {{ @$provider->tipo_persona == 1 ? 'selected' : '' }} >Física</option>
        <option value="J" {{ @$provider->tipo_persona == 2 ? 'selected' : '' }}>Jurídica</option>
        <option value="D" {{ @$provider->tipo_persona == 3 ? 'selected' : '' }}>DIMEX</option>
        <option value="E" {{ @$provider->tipo_persona == 4 ? 'selected' : '' }}>Extranjero</option>
        <option value="N" {{ @$provider->tipo_persona == 5 ? 'selected' : '' }}>NITE</option>
        <option value="O" {{ @$provider->tipo_persona == 6 ? 'selected' : '' }}>Otro</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="id_number">Número de identificación *</label>
      <input type="text" class="form-control" name="id_number" id="id_number" value="{{ @$provider->id_number }}" required onchange="getJSONCedula(this.value);">
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
      <input type="text" class="form-control" name="email" id="email" value="{{ @$provider->email }}" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="phone">Teléfono</label>
      <input type="text" class="form-control" name="phone" id="phone" value="{{ @$provider->phone }}" >
    </div>
    
    <div class="form-group col-md-4"></div>
    
    <div class="form-group col-md-4">
      <label for="country">País *</label>
      <select class="form-control" name="country" id="country" value="{{ @$provider->country }}" required >
        <option value="CR" selected>Costa Rica</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="state">Provincia</label>
      <select class="form-control" name="state" id="state" value="{{ @$provider->state }}"  onchange="fillCantones();">
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="city">Canton</label>
      <select class="form-control" name="city" id="city" value="{{ @$provider->city }}" onchange="fillDistritos();">
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="district">Distrito</label>
      <select class="form-control" name="district" id="district" value="{{ @$provider->district }}" onchange="fillZip();" >
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="neighborhood">Barrio</label>
      <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$provider->neighborhood }}" >
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="zip">Zip</label>
      <input type="text" class="form-control" name="zip" id="zip" value="{{ @$provider->zip }}" readonly >
    </div>
    
    <div class="form-group col-md-12">
      <label for="address">Dirección</label>
      <textarea class="form-control" name="address" id="address" >{{ @$provider->address }}</textarea>
    </div>
		
		<script>
		
		  function toggleApellidos() {
		    var tipoPersona = $('#tipo_persona').val();
		    if( tipoPersona == 2 ){
		      $('#last_name, #last_name2').val('');
		      $('#last_name, #last_name2').attr('readonly', 'true');
		    }else{
		      $('#last_name, #last_name2').removeAttr('readonly');
		    }
		  }
		  
		  function fillProvincias() {
		    if( $('#country').val() == 'CR' ) {
		      var sel = $('#state');
		      sel.html("");
		      sel.append( "<option val='0' selected>-- Seleccione una provincia --</option>" );
		      $.each(provincias, function(i, val) {
		        sel.append( "<option value='"+i+"'>"+ provincias[i]["Nombre"] +"</option>" );
		      });
		    }
		  }
		  
		  function fillCantones() {
		    var provincia = $('#state').val();
		    var sel = $('#city');
		    sel.html("");
		    sel.append( "<option val='0' selected>-- Seleccione un cantón --</option>" );
		    $.each(cantones, function(i, val) {
		      if( provincia == cantones[i]["Provincia"] ){
		         sel.append( "<option value='"+i+"'>"+ cantones[i]["Nombre"] +"</option>" );
		      }
		    });
		  }
		  
		  function fillDistritos() {
		    var canton = $('#city').val();
		    var sel = $('#district');
		    sel.html("");
		    sel.append( "<option val='0' selected>-- Seleccione un distrito --</option>" );
		    $.each(distritos, function(i, val) {
		      if( canton == distritos[i]["Canton"] ){
		         sel.append( "<option value='"+i+"'>"+ distritos[i]["Nombre"] +"</option>" );
		      }
		    });
		  }
		  
		  function fillZip() {
		    var distrito = $('#district').val();
		    var sel = $('#zip').val(distrito);
		  }
		  
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
		  
		</script>