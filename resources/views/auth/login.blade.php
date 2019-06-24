@extends('layouts/login') 

@section('title') Iniciar sesión @endsection 

@section('content')


<form method="POST" action="{{ route('login') }}">
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
        @if ($errors->has('email'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('email') }}</strong>
        </span> 
        @endif
      </div>
    </div>

    <div class="form-group col-md-12">
      <div>
        <input placeholder="{{ __('Contraseña') }}" id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required> 
        @if ($errors->has('password'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('password') }}</strong>
        </span> 
        @endif
        </div>
    </div>

    <div class="form-group col-md-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old( 'remember') ? 'checked' : '' }}>

          <label class="form-check-label" for="remember"> {{ __('Recordarme') }} </label>
        </div>
    </div>
    
    <div class="form-group col-md-12 text-center">
        <div class="description">
          Al iniciar sesión y utilizar eTax en cualquier momento, está confirmando que acepta nuestros <a target="_blank" href="https://etaxcr.com/terminos-y-condiciones">Términos y condiciones</a>
        </div>
    </div>

    <div class="form-group col-md-12 text-center">
      <button type="submit" class="btn btn-primary" onclick="trackClickEvent( 'IniciarSesion' );">{{ __('Iniciar sesión') }}</button>
    </div>

    <div class="form-group col-md-12 button-container text-center">

      <div class="inline-block text-center">
        
        <div class="login-secondary-btn-cont">
            <span class="loginbtn-label">¿No tiene cuenta?</span>
            @if (Route::has('register'))
                <a class="btn btn-link" onclick="trackClickEvent( 'EnlaceRegister' );" href="{{ route('register') }}">
                    Regístrese aquí
                </a>
            @endif
        </div>
        <div class="login-secondary-btn-cont">
          <span class="loginbtn-label">¿Se le olvidó la contraseña? </span>
          @if (Route::has('password.request'))
              <a class="btn btn-link" onclick="trackClickEvent( 'EnlacePassword' );" href="{{ route('password.request') }}">
                  Recupérela
              </a>
          @endif
         </div>
         
      </div>

    </div>

  </div>

</form>

@endsection