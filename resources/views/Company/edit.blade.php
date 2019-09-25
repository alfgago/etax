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
        <div class="col-sm-3">
            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            	<?php 
				$menu = new App\Menu;
				$items = $menu->menu('menu_empresas');
				foreach ($items as $item) { ?>
					<li>
						<a class="nav-link" aria-selected="false"  style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
					</li>
				<?php } ?>
            </ul>
        </div>
        <div class="col-sm-9">
          <div class="tab-content">       
						
						<form method="POST" action="{{ route('Company.update', ['id' => $company->id]) }}" enctype="multipart/form-data">
						
						  @csrf
						  @method('patch') 
						  
						  <div class="form-row">
						  	
						    <div class="form-group col-md-5">
						      <h3>
						        Editar perfil de empresa
						      </h3>
						    </div>
							  <div class="form-group col-md-7">
								  <div class="">
									  <label for="input_logo" class="logo-input">Logo empresa</label>
									  <label id="logo-name"></label>
									  <input name="input_logo" id="input_logo" style="visibility:hidden;" type="file" multiple="false">
								  </div>
								  
								  <div class="logo-container">
								  @if($company->logo_url)
								  	<img src="{{ \Illuminate\Support\Facades\Storage::temporaryUrl($company->logo_url, now()->addMinutes(1)) }}" style="max-height: 75px">
								  @endif
								  </div>
							  </div>
						    
						    <div class="form-group col-md-4">
						      <label for="tipo_persona">Tipo de persona *</label>
						      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
						        <option value="F" {{ @$company->type == 'F' ? 'selected' : '' }} >Física</option>
						        <option value="J" {{ @$company->type == 'J' ? 'selected' : '' }}>Jurídica</option>
						        <option value="D" {{ @$company->type == 'D' ? 'selected' : '' }}>DIMEX</option>
						        <option value="N" {{ @$company->type == 'N' ? 'selected' : '' }}>NITE</option>
						        <option value="E" {{ @$company->type == 'E' ? 'selected' : '' }}>Extranjero</option>
						        <option value="O" {{ @$company->type == 'O' ? 'selected' : '' }}>Otro</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="id_number">Número de identificación *</label>
						      <input type="number" class="form-control" name="id_number" id="id_number" value="{{ @$company->id_number }}" required onchange="getJSONCedula(this.value);">
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
						      <input type="email" class="form-control" name="email" id="email" value="{{ @$company->email }}" required>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="phone">Teléfono</label>
						      <input type="number" class="form-control" name="phone" id="phone" value="{{ @$company->phone }}" onblur="validatePhoneFormat();">
						    </div>
						    
						    <div class="form-group col-md-12">
                                <label for="tipo_persona">Actividades comerciales *</label>
                                <select class="form-control checkEmpty select2-tags" name="commercial_activities[]" id="commercial_activities" multiple required>
                                    <?php
                                        $listaActividades = explode(",", $company->commercial_activities);
                                    ?>
                                    @foreach ( $actividades as $actividad )
                                        <option value="{{ $actividad['codigo'] }}" {{ (in_array($actividad['codigo'], $listaActividades) !== false) ? 'selected' : '' }}>{{ $actividad['codigo'] }} - {{ $actividad['actividad'] }}</option>
                                    @endforeach
                                </select>
                            </div>
						    
						    <div class="form-group col-md-4">
						      <label for="country">País *</label>
						      <select class="form-control" name="country" id="country" value="{{ @$company->country }}" required >
						        <option value="CR" selected>Costa Rica</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="state">Provincia *</label>
						      <select class="form-control" name="state" id="state" value="{{ @$company->state }}" onchange="fillCantones();">
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="city">Cantón *</label>
						      <select class="form-control" name="city" id="city" value="{{ @$company->city }}" onchange="fillDistritos();">
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="district">Distrito *</label>
						      <select class="form-control" name="district" id="district" value="{{ @$company->district }}" onchange="fillZip();" >
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="neighborhood">Barrio</label>
						      <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$company->neighborhood }}" >
						      </select>
						    </div>
						    
						    <div class="form-group col-md-4">
						      <label for="zip">Código Postal</label>
						      <input type="text" class="form-control" name="zip" id="zip" value="{{ @$company->zip }}" readonly >
						    </div>
						    
						    <div class="form-group col-md-12">
						      <label for="address">Dirección</label>
						      <textarea class="form-control" name="address" id="address" maxlength="250" rows="2" style="resize: none;">{{ @$company->address }}</textarea>
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
	    	$('#state').val( "{{ $company->state }}" );
	    	fillCantones();
	    	@if( @$company->city )
		    	$('#city').val( "{{ $company->city }}" );
		    	fillDistritos();
		    	@if( @$company->district )
			    	$('#district').val( "{{ $company->district }}" );
			    	fillZip();
			    @endif
		    @endif
	    @endif
	    
	  });
	</script>

@endsection
