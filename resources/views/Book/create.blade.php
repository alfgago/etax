@extends('layouts/app')

@section('title') 
  Crear proveedor
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
        <form method="POST" action="/proveedores">
	
          <div class="form-row">
            <div class="form-group col-md-12">
              <h3>
                Información de proveedor
              </h3>
            </div>
            
            @csrf
            @include( 'Provider.form' )
            
            </div>
          
            <button id="btn-submit" type="submit" class="hidden btn btn-primary">Confirmar proveedor</button>
            
        </form>
        
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar proveedor</button>
@endsection 

@section('footer-scripts')


@endsection