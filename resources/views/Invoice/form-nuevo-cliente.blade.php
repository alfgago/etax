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
      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();" onchange="cambiarDireccion();">
      @if(@$document_type == '01' || $document_type == '08')
        <option value="F" >Física</option>
        <option value="J" >Jurídica</option>
        <option value="D" >DIMEX</option>
        <option value="N" >NITE</option>
      @elseif(@$document_type == '04')
        <option value="F" >Física</option>
        <option value="J" >Jurídica</option>
        <option value="D" >DIMEX</option>
        <option value="N" >NITE</option>
        <option value="E" >Extranjero</option>
        <option value="O" >Otro</option>
        @else
        <option value="E" >Extranjero</option>
        <option value="O" >Otro</option>
      @endif
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="id_number">Número de identificación *</label>
      <input type="text" class="form-control @if(@$document_type !== '04') checkEmpty @endif" name="id_number" id="id_number" onchange="getJSONCedula(this.value);" maxlength="20">
    </div>
    
    <div class="form-group col-md-4">
      <label for="first_name">Nombre *</label>
      <input type="text" class="form-control @if(@$document_type !== '04') checkEmpty @endif" name="first_name" id="first_name" maxlength="80">
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name">Apellido</label>
      <input type="text" class="form-control" name="last_name" id="last_name" maxlength="30">
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name2">Segundo apellido</label>
      <input type="text" class="form-control" name="last_name2" id="last_name2" maxlength="30">
    </div>
    
    <div class="form-group col-md-4">
      <label for="email">Correo electrónico *</label>
      <input type="text" class="form-control @if(@$document_type !== '04') checkEmpty @endif" name="email" id="email" onblur="validateEmail();" maxlength="160">
    </div>
    
    <div class="form-group col-md-4">
      <label for="phone">Teléfono</label>
      <input type="text" class="form-control" name="phone" id="phone" maxlength="20">
    </div>
    
    <div></div>
    
    <div class="form-group col-md-4">
      <label for="country">País *</label>
      <select class="form-control @if(@$document_type !== '04') checkEmpty @endif" name="country" id="country" >
        @if(@$document_type != '09')
            <option value="CR" selected>Costa Rica</option>
        @else
            <option value="US" selected>United States</option>
        @endif
        @foreach ($countries as $country )
          <option value="{{ $country['country_code'] }}" >{{ $country['country_name'] }}</option>
        @endforeach
      </select>
    </div>
    
    @if(@$document_type != '09')
      <div class="form-group col-md-4" id="divState">
        <label for="state">Provincia *</label>
        <select class="form-control @if(@$document_type !== '04') checkEmpty @endif" name="state" id="state" onchange="fillCantones();">
        </select>
      </div>

      <div class="form-group col-md-4" id="divCity">
        <label for="city">Canton *</label>
        <select class="form-control @if(@$document_type !== '04') checkEmpty @endif" name="city" id="city" onchange="fillDistritos();">
        </select>
      </div>

      <div class="form-group col-md-4" id="divDistrict">
        <label for="district">Distrito *</label>
        <select class="form-control @if(@$document_type !== '04') checkEmpty @endif" name="district" id="district" onchange="fillZip();" >
        </select>
      </div>

      <div class="form-group col-md-4" id="divNeighborhood">
        <label for="neighborhood">Barrio</label>
        <input class="form-control" name="neighborhood" id="neighborhood" maxlength="150">
        </select>
      </div>

      <div class="form-group col-md-4" id="divZip">
        <label for="zip">Zip</label>
        <input type="text" class="form-control" name="zip" id="zip" readonly >
      </div>

      <div class="form-group col-md-12" id="divAddress">
        <label for="address">Dirección</label>
        <textarea class="form-control" name="address" id="address" maxlength="250"></textarea>
      </div>
    @else
      <div class="form-group col-md-12">
          <label for="address">Otras señas extranjero</label>
          <textarea class="form-control" name="foreign_address" id="foreign_address" maxlength="300">{{ @$client->foreign_address }}</textarea>
      </div>
    @endif

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
		<style>
            .error {
                border:1px solid red;
            }
        </style>
		<script>
		  
        $(document).ready(function(){
		    
		  	fillProvincias();
		    toggleApellidos();
		    
		  });
          $("#id_number").keyup(function() {
              $("#id_number").val(this.value.match(/[0-9]*/));
          });
          $("#phone").keyup(function() {
              $("#phone").val(this.value.match(/[0-9]*/));
          });
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

        function validateEmail() {
            var email = $('#email').val();
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(re.test(String(email).toLowerCase()) != true){
                $('#email').addClass('error');
                Swal.fire({
                    type: 'error',
                    title: 'Info:',
                    text: 'La direccion de correo electronico no coincide con ningun formato de correo'
                });
            }else{
                $('#email').removeClass('error');
            }
            //return re.test(String(email).toLowerCase());
        }
		</script>
  </div>
</div>
