@extends('layouts/login') 

@section('title') Iniciar sesión @endsection 

@section('content')


<form method="POST" action="/gosocket/validar-cuenta">
  @csrf
  
  <div class="form-row">

    <div class="form-group col-md-12 text-center">
      <h3>
        Iniciar sesión
      </h3>
    </div>

    <div class="form-group col-md-12">
      <div>
        <input placeholder="{{ __('Correo electrónico') }}" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus> 
      </div>
    </div>

    <div class="form-group col-md-12">
      <div>
        <input placeholder="{{ __('Contraseña') }}" id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required> 

        <input placeholder="{{ __('Contraseña') }}" id="token" type="token" class="hidden" value="{{ $token }}" name="token" required> 
      </div>
    </div>

    
    <div class="form-group col-md-12 text-center">
        <div class="description">
          Al iniciar sesión y utilizar eTax en cualquier momento, está confirmando que acepta nuestros <a target="_blank" href="https://etaxcr.com/terminos-y-condiciones">Términos y condiciones</a>
        </div>
    </div>

    <div class="form-group col-md-12 text-center">
      <button type="submit" class="btn btn-primary" >Iniciar sesión</button>
    </div>

    
  </div>

</form>

@endsection