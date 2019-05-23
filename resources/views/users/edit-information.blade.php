@extends('layouts/app')

@section('title')
    Editar información personal
@endsection

@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración</button>
@endsection 

@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li class="active">
                            <a class="nav-link active" aria-selected="true" href="/usuario/perfil">Editar información personal</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/cambiar-plan">Cambiar plan</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       

                        <div class="tab-pane fade show active" role="tabpanel">
                            <form method="POST" action="/usuario/update-perfil">

                                @csrf
                                @method('patch') 

                                <div class="form-row">

                                    <div class="form-group col-md-12">
        						      <h3>
        						        Editar información personal
        						      </h3>
        						    </div>

                                    <div class="form-group col-md-4">
                                        <label for="first_name">Nombre</label>
                                        <input type="text" name="first_name" class="form-control" value="{{@$user->first_name}}" required>
                                    </div>
                                    
                                    <div class="form-group col-md-4">
                                        <label for="last_name">Primer apellido</label>                                               
                                        <input type="text"name="last_name" class="form-control" value="{{@$user->last_name}}" required>
                                    </div>
                                    
                                    
                                    <div class="form-group col-md-4">
                                        <label for="last_name2">Segundo apellido</label>
                                        <input type="text" name="last_name2" class="form-control" value="{{@$user->last_name2}}" required>
                                    </div>
                                        
                                    <div class="form-group col-md-4">
                                        <label for="id_number">Cédula</label>
                                        <input type="text" name="id_number" class="form-control" value="{{@$user->id_number}}" required>
                                    </div>
                                    
                                    <div class="form-group col-md-4">
        						      <label for="email">Correo electrónico *</label>
        						      <input type="email" class="form-control" name="email" id="email" value="{{ @$user->email }}" required>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="phone">Teléfono</label>
        						      <input type="text" class="form-control" name="phone" id="phone" value="{{ @$user->phone }}" >
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="country">País *</label>
        						      <select class="form-control" name="country" id="country" value="{{ @$user->country }}" required >
        						        <option value="CR" selected>Costa Rica</option>
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="state">Provincia</label>
        						      <select class="form-control" name="state" id="state" value="{{ @$user->state }}" onchange="fillCantones();">
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="city">Canton</label>
        						      <select class="form-control" name="city" id="city" value="{{ @$user->city }}" onchange="fillDistritos();">
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="district">Distrito</label>
        						      <select class="form-control" name="district" id="district" value="{{ @$user->district }}" onchange="fillZip();" >
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="neighborhood">Barrio</label>
        						      <input class="form-control" name="neighborhood" id="neighborhood" value="{{ @$user->neighborhood }}" >
        						      </select>
        						    </div>
        						    
        						    <div class="form-group col-md-4">
        						      <label for="zip">Zip</label>
        						      <input type="text" class="form-control" name="zip" id="zip" value="{{ @$user->zip }}" readonly >
        						    </div>
        						    
        						    <div class="form-group col-md-12">
        						      <label for="address">Dirección</label>
        						      <textarea class="form-control" name="address" id="address" >{{ @$user->address }}</textarea>
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
</div>       

@endsection

@section('footer-scripts')
	
	<script>
	  
	  $(document).ready(function(){
	    
	  	fillProvincias();
	    
	    toggleApellidos();
	    
	    //Revisa si tiene estado, canton y distrito marcados.
	    @if( @$user->state )
	    	$('#state').val( "{{ $user->state }}" );
	    	fillCantones();
	    	@if( @$user->city )
		    	$('#city').val( "{{ $user->city }}" );
		    	fillDistritos();
		    	@if( @$user->district )
			    	$('#district').val( "{{ $user->district }}" );
			    	fillZip();
			    @endif
		    @endif
	    @endif
	    
	  });
	  
	</script>

@endsection