@extends('layouts/app')

@section('title') 
  Editar proveedor
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
      <form method="POST" action="/proveedores/{{ $provider->id }}">

        @csrf
        @method('patch') 
        
        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Información de proveedor
            </h3>
          </div>
          @include( 'Provider.form', ['provider' => $provider] )
          
          </div>
        
          <button id="btn-submit" type="submit" class="hidden">Guardar proveedor</button>
      </form>
      
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar proveedor</button>
@endsection 

@section('footer-scripts')


@endsection