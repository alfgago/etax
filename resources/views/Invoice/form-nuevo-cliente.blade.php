<div class="popup" id="nuevo-cliente-popup">
  <div class="popup-container nuevo-cliente-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('nuevo-cliente-popup');"> <i class="fa fa-times" aria-hidden="true"></i>  </div>

    <div class="form-group col-md-12">
      <h3>
        Nuevo cliente
      </h3>
    </div>


    <input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" >
    
    <div class="form-group col-md-4">
      <label for="code">Código *</label>
      <input type="text" class="form-control checkEmpty" name="code" id="code" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="tipo_persona">Tipo de persona *</label>
      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
        <option value="F" >Física</option>
        <option value="J" >Jurídica</option>
        <option value="D" >DIMEX</option>
        <option value="N" >NITE</option>
        <option value="E" >Extranjero</option>
        <option value="O" >Otro</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="id_number">Número de identificación *</label>
      <input type="number" class="form-control checkEmpty" name="id_number" id="id_number" onchange="getJSONCedula(this.value);">
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
      <input type="number" class="form-control" name="phone" id="phone" >
    </div>
    
    <div></div>
    
    <div class="form-group col-md-4">
      <label for="country">País *</label>
      <select class="form-control checkEmpty" name="country" id="country" >
        <option value="CR" selected>Costa Rica</option>
        @foreach ($countries as $country )
          <option value="{{ $country['country_code'] }}" >{{ $country['country_name'] }}</option>
        @endforeach
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
		  
      $(document).ready(function(){
		    
		  	fillProvincias();
		    toggleApellidos();
		    
		  });
      		  
		</script>
		

  </div>
</div>