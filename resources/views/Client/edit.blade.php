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
          
            <button type="submit" class="btn btn-primary">Confirmar edición</button>
          
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
@endsection

@section('footer-scripts')


@endsection