@extends('layouts/app')

@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar información</button>
@endsection 

@section('content')

<div class="row">
  <div class="col-md-12">
  	<div class="tabbable verticalForm">
    	<div class="row">
        <div class="col-3">
            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <li class="active">
                    <a class="nav-link active" aria-selected="true" href="/empresas/editar">Editar perfil de empresa</a>
                </li>
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/configuracion">Configuración avanzada</a>
                </li>
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/certificado">Certificado digital</a>
                </li>
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/equipo">Equipo de trabajo</a>
                </li>
            </ul>
        </div>
        <div class="col-9">
          <div class="tab-content">       
						
						<form method="POST" action="{{ route('Company.update', ['id' => $company->id]) }}">
						
						  @csrf
						  @method('patch') 
						  
						  <div class="form-row">
						  	
						    <div class="form-group col-md-12">
						      <h3>
						        Editar perfil de empresa
						      </h3>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="tipo_persona">Tipo de persona *</label>
						      <select class="form-control" name="type" id="type" required onclick="toggleApellidos();">
						        <option value="1" {{ @$company->tipo_persona == 1 ? 'selected' : '' }} >Física</option>
						        <option value="2" {{ @$company->tipo_persona == 2 ? 'selected' : '' }}>Jurídica</option>
						        <option value="3" {{ @$company->tipo_persona == 3 ? 'selected' : '' }}>DIMEX</option>
						        <option value="4" {{ @$company->tipo_persona == 5 ? 'selected' : '' }}>NITE</option>
						        <option value="5" {{ @$company->tipo_persona == 4 ? 'selected' : '' }}>Extranjero</option>
						        <option value="6" {{ @$company->tipo_persona == 6 ? 'selected' : '' }}>Otro</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="id_number">Número de identificación *</label>
						      <input type="text" class="form-control" name="id_number" id="id_number" value="{{ @$company->id_number }}" required>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="business_name">Razón Social *</label>
						      <input type="text" class="form-control" name="business_name" id="business_name" value="{{ @$company->business_name }}" required>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="first_name">Nombre comercial *</label>
						      <input type="text" class="form-control" name="name" id="name" value="{{ @$company->name }}" required>
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
						      <input type="text" class="form-control" name="email" id="email" value="{{ @$company->email }}" required>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="phone">Teléfono</label>
						      <input type="text" class="form-control" name="phone" id="phone" value="{{ @$company->phone }}" >
						    </div>
						    
						    <div class="form-group col-md-12">
						      <label for="activities">Actividades comerciales *</label> {{ @$company->activities }}
						      <select class="form-control select2-tags" name="activities" id="activities" required multiple>
						        <option value="1" selected>Actividad 1</option>
						        <option value="2" >Actividad 2</option>
						        <option value="3" >Actividad 3</option>
						        <option value="4" >Actividad 4</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="country">País *</label>
						      <select class="form-control" name="country" id="country" value="{{ @$company->country }}" required >
						        <option value="CR" selected>Costa Rica</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="state">Provincia</label>
						      <select class="form-control" name="state" id="state" value="{{ @$company->state }}" onchange="fillCantones();">
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="city">Canton</label>
						      <select class="form-control" name="city" id="city" value="{{ @$company->city }}" onchange="fillDistritos();">
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="district">Distrito</label>
						      <select class="form-control" name="district" id="district" value="{{ @$company->district }}" onchange="fillZip();" >
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="neighborhood">Barrio</label>
						      <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$company->neighborhood }}" >
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="zip">Zip</label>
						      <input type="text" class="form-control" name="zip" id="zip" value="{{ @$company->zip }}" readonly >
						    </div>
						    
						    <div class="form-group col-md-12">
						      <label for="address">Dirección</label>
						      <textarea class="form-control" name="address" id="address" >{{ @$company->address }}</textarea>
						    </div>
						    
						    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>          
						    
						  </div>
						  
						</form>

          </div>
        </div>
      </div>
    </div>
  </div>  
</div>       

@endsection

@section('footer-scripts')
	
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
	    @if( @$company->state )
	    	$('#state').val( {{ $company->state }} );
	    	fillCantones();
	    	@if( @$company->city )
		    	$('#city').val( {{ $company->city }} );
		    	fillDistritos();
		    	@if( @$company->district )
			    	$('#district').val( {{ $company->district }} );
			    	fillZip();
			    @endif
		    @endif
	    @endif
	    
	  });
	  
	</script>

@endsection