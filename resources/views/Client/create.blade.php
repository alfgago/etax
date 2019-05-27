@extends('layouts/app')

@section('title') 
  Crear cliente
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
        <form method="POST" action="/clientes">
	
          <div class="form-row">
            <div class="form-group col-md-12">
              <h3>
                Información de cliente
              </h3>
            </div>
            
            @csrf
            @include( 'Client.form' )
            
            </div>
          
            <button id="btn-submit" type="submit" class="hidden">Guardar cliente</button>
            
        </form>
        
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar cliente</button>
@endsection 

@section('footer-scripts')
		
		<script>
		  
		  $(document).ready(function(){
		    
		  	
  	    $("#billing_emails").tagging({
  	      "forbidden-chars":[",",'"',"'","?"],
  	      "forbidden-chars-text": "Caracter inválido: ",
  	      "edit-on-delete": false,
  	      "tag-char": "@"
  	    });
  	    
  	    fillProvincias();
		    
		    
		  });
		  
		</script>

@endsection