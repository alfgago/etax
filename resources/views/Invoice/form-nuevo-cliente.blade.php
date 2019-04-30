<div class="popup" id="nuevo-cliente-popup">
  <div class="popup-container nuevo-cliente-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('nuevo-cliente-popup');"> <i class="nav-icon i-Close"></i>  </div>

    <div class="form-group col-md-12">
      <h3>
        Nuevo cliente
      </h3>
    </div>


    <input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" >
    
    <div class="form-group col-md-4">
      <label for="code">Código *</label>
      <input type="text" class="form-control checkEmpty" name="code" id="code" value="{{ @$client->code }}" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="tipo_persona">Tipo de persona *</label>
      <select class="form-control checkEmpty" name="tipo_persona" id="tipo_persona"  onclick="toggleApellidos();">
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
      <input type="text" class="form-control checkEmpty" name="id_number" id="id_number" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="first_name">Nombre *</label>
      <input type="text" class="form-control checkEmpty" name="first_name" id="first_name" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name">Apellido</label>
      <input type="text" class="form-control" name="last_name" id="last_name" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name2">Segundo apellido</label>
      <input type="text" class="form-control" name="last_name2" id="last_name2" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="email">Correo electrónico *</label>
      <input type="text" class="form-control checkEmpty" name="email" id="email" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="phone">Teléfono</label>
      <input type="text" class="form-control" name="phone" id="phone" >
    </div>
    
    <div></div>
    
    <div class="form-group col-md-4">
      <label for="country">País *</label>
      <select class="form-control checkEmpty" name="country" id="country" >
        <option value="CR" selected>Costa Rica</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="state">Provincia</label>
      <select class="form-control" name="state" id="state" onchange="fillCantones();">
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="city">Canton</label>
      <select class="form-control" name="city" id="city" onchange="fillDistritos();">
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="district">Distrito</label>
      <select class="form-control" name="district" id="district" onchange="fillZip();" >
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="neighborhood">Barrio</label>
      <input class="form-control" name="neighborhood" id="neighborhood" >
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="zip">Zip</label>
      <input type="text" class="form-control" name="zip" id="zip" readonly >
    </div>
    
    <div class="form-group col-md-12">
      <label for="address">Dirección</label>
      <textarea class="form-control" name="address" id="address" ></textarea>
    </div>
    
    <div class="form-group col-md-4">
        <label for="es_exento">Exento de IVA</label>
        <select class="form-control" name="es_exento" id="es_exento" >
          <option value="0" >No</option>
          <option value="1" >Sí</option>
        </select>
    </div>

    <div class="form-group col-md-12">
      <div class="botones-agregar">
        <div onclick="agregarClienteNuevo();" class="btn btn-dark m-1 ml-0">Confirmar cliente</div>
        <div onclick="cerrarPopup('nuevo-cliente-popup');" class="btn btn-danger m-1">Cancelar</div>
      </div>
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
		  
		  function agregarClienteNuevo() {
      	var allow = true;
      	$('.checkEmpty').each( function() {
      		if( $(this).val() && $(this).val() != "" ){ 
      			$(this).attr('style', 'border-color: ;');
      		}else{
      			$(this).attr('style', 'border-color:red;');
      			allow = false;
          }
      	});
      	
      	if( allow ){
              var cnombre = $('#first_name').val() + " " + $('#last_name').val() + " " + $('#last_name2').val() ;
              if(! $('#nuevo-cliente-opt').length ) {
                  $("select#client_id > option:nth-of-type(1)").after("<option id='nuevo-cliente-opt' value='-1'> NUEVO - "+ cnombre +" </option>");
              }else{
                  $('#nuevo-cliente-opt').text( "NUEVO - "+ cnombre );
              }
              $('select#client_id').select2();
              $('select#client_id').val('-1');
              $('select#client_id').change();
              cerrarPopup('nuevo-cliente-popup');
      	}
      }
      
      $(document).ready(function(){
		    
		  	fillProvincias();
		    toggleApellidos();
		    
		  });
      		  
		</script>
		

  </div>
</div>