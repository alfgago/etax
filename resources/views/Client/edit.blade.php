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