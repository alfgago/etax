@extends('layouts/app')

@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection

@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar certificado</button>
@endsection

@section('content')

<div class="row">
  <div class="col-md-12">
  	<div class="tabbable verticalForm">
    	<div class="row">
        <div class="col-3">
            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/editar">Editar perfil de empresa</a>
                </li>
                <li>
                    <a class="nav-link " aria-selected="false" href="/empresas/configuracion">Configuración avanzada</a>
                </li>
                <li class="active">
                    <a class="nav-link active" aria-selected="true" href="/empresas/certificado">Certificado digital</a>
                </li>
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/equipo">Equipo de trabajo</a>
                </li>
            </ul>
        </div>
        <div class="col-9">
          <div class="tab-content">       
						
			<form method="POST" action="{{ route('Company.update_cert', ['id' => $company->id]) }}" enctype="multipart/form-data">
			    
    		 <div class="alert alert-success"> 
    		     @if( @$certificate->key_url )
    		     
    		         Usted ya subió su certificado ATV. Cualquier edición en esta pantalla requerirá que lo suba nuevamente.
    		     
    		     @endif
    		 </div>
			
			  @csrf
			  @method('patch') 
			  
			  <div class="form-row">
			  	
			    <div class="form-group col-md-12">
			      <h3>
			        Certificado digital
			      </h3>
			    </div>
						    
                <div class="form-group col-md-6">
                    <label for="user">Usuario ATV</label>
                    <input type="text" class="form-control" name="user" id="user" value="{{ @$certificate->user }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="password">Contraseña ATV</label>
                    <input type="password" class="form-control" name="password" id="password" value="{{ @$certificate->password }}" required>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="cert">Llave criptográfica</label>
                    <div class="fallback">
    				    <input name="cert" type="file" multiple="false">
    				</div>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="pin">PIN de llave criptográfica</label>
                    <input type="text" class="form-control" name="pin" id="pin" value="{{ @$certificate->pin }}" required>
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

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar certificado</button>
@endsection 

@section('footer-scripts')

@endsection