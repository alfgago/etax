@extends('layouts/app')

@section('title') 
  Crear proveedor
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        
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
          
            <button type="submit" class="btn btn-primary">Confirmar proveedor</button>
          
            @if ($errors->any())
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            @endif
            
        </form>
        
      </div>  
    </div>  
  </div>  
</div>
@endsection

@section('footer-scripts')


@endsection