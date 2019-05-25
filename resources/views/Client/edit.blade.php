@extends('layouts/app')

@section('title') 
  Editar cliente
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
        <form method="POST" action="/clientes/{{ $client->id }}">
	
          @csrf
          @method('patch') 
          
          <div class="form-row">
            <div class="form-group col-md-12">
              <h3>
                Información de cliente
              </h3>
            </div>
            @include( 'Client.form', ['client' => $client] )
            
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
		    
		  	fillProvincias();
		  	
  	    $("#billing_emails").tagging({
  	      "forbidden-chars":[",",'"',"'","?"],
  	      "forbidden-chars-text": "Caracter inválido: ",
  	      "edit-on-delete": false,
  	      "tag-char": "@"
  	    });
		    
		    toggleApellidos();
		    
		    //Revisa si tiene estado, canton y distrito marcados.
		    @if( @$client->state )
		    	$('#state').val( {{ $client->state }} );
		    	fillCantones();
		    	@if( @$client->city )
			    	$('#city').val( {{ $client->city }} );
			    	fillDistritos();
			    	@if( @$client->district )
				    	$('#district').val( {{ $client->district }} );
				    	fillZip();
				    @endif
			    @endif
		    @endif
		    
		  });
		  
		</script>

@endsection