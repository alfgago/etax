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
        <div class="col-sm-3">
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
                <li class="">
                    <a class="nav-link" aria-selected="false" href="/empresas/comprar-facturas-vista">Comprar facturas</a>
                </li>
            </ul>
        </div>
        <div class="col-sm-9">
          <div class="tab-content">       
						
			<form method="POST" action="{{ route('Company.update_cert', ['id' => $company->id]) }}" enctype="multipart/form-data">
			    
			 @if( @$certificate->key_url )
    		 <div class="alert alert-danger"> 
		         Usted ya subió su llave criptográfica. Cualquier edición en esta pantalla requerirá que lo suba nuevamente.
    		 </div>
			 @endif
			 
			 <div class="alert alert-info"> 
		         ¿No sabe cómo conseguir esta información? Contáctenos via chat, teléfono, o descarge el <a style="text-decoration:underline;" href="https://app.etaxcr.com/assets/files/guias/Manual-ConfiguracionEmpresa.pdf">Manual de configuración de empresa.</a>
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
                    <input type="email" class="form-control" name="user" id="user" value="{{ @$certificate->user }}" required>
                    <div class="description">
                        El formato de este campo debe verse similar a este: cpj-x-xxx-xxxxxx@prod.comprobanteselectronicos.go.cr
                    </div>
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
    				@if( @$certificate->key_url )
            		 <div class="description text-danger"> 
        		         Usted ya subió su llave criptográfica de  ATV. Volver a guardar este formulario, requerirá que suba el archivo nuevamente.
            		 </div>
        			 @endif
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

@section('footer-scripts')

@endsection
