@extends('layouts/login') 
@section('title') Registrar cuenta @endsection 

@section('content')

  <form method="POST" action="{{ route('register') }}">
    @csrf
    @honeypot
    
    <div class="form-row">

      <div class="form-group col-md-12 text-center">
        <h3>
          Registrar cuenta
        </h3>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="{{ __('Correo electrónico') }}" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autofocus required> 
          @if ($errors->has('email'))
          <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('email') }}</strong>
          </span> 
          @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="{{ __('Teléfono') }}" id="phone" type="number" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" required autofocus> @if ($errors->has('phone'))
          <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('phone') }}</strong>
            </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">

        <div>
          <input placeholder="{{ __('Nombre') }}" id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" required autofocus> @if ($errors->has('first_name'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('first_name') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="{{ __('Primer apellido') }}" id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" required autofocus> @if ($errors->has('last_name'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('last_name') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="{{ __('Segundo apellido') }}" id="last_name2" type="text" class="form-control{{ $errors->has('last_name2') ? ' is-invalid' : '' }}" name="last_name2" value="{{ old('last_name2') }}" required autofocus> @if ($errors->has('last_name2'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('last_name2') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <div>
          <input placeholder="{{ __('Contraseña') }}" id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required> @if ($errors->has('password'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('password') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12 text-center">
        <div>
          <input placeholder="{{ __('Confirmar Contraseña') }}" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
        </div>
      </div>
      
      <div class="form-group col-md-12 text-center">
        <div class="description">
            Al registrarse y utilizar eTax en cualquier momento, está confirmando que acepta nuestros <a target="_blank" href="https://etaxcr.com/terminos-y-condiciones">Términos y condiciones</a>
        </div>
      </div>
      
      <div class="form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" onclick="trackClickEvent( 'Lead' );">{{ __('Confirmar cuenta') }} </button>
      </div>
      
      <div class="form-group col-md-12 button-container text-center">
        <div class="inline-block text-center">
          <div class="login-secondary-btn-cont">
          <span class="loginbtn-label">¿Ya tiene cuenta? </span>
          @if (Route::has('login'))
              <a class="btn btn-link" href="{{ route('login') }}">
                  {{ __('Ingrese aquí') }}
              </a>
          @endif
          </div>
        </div>
        
      </div>

    </div>

  </form>

@endsection