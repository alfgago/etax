@extends('layouts/login') 
@section('title') Registrar cuenta @endsection 

@section('content')

  <form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-row">

      <div class="form-group col-md-12 text-left">
        <h3>
          Registrar cuenta
        </h3>
      </div>

      <div class="form-group col-md-12">
        <label for="email">{{ __('Correo electrónico') }}</label>

        <div>
          <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required> @if ($errors->has('email'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <label for="first_name">{{ __('Nombre') }}</label>

        <div>
          <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" required autofocus> @if ($errors->has('first_name'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('first_name') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <label for="last_name">{{ __('Primer apellido') }}</label>

        <div>
          <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" required autofocus> @if ($errors->has('last_name'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('last_name') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <label for="last_name2">{{ __('Segundo apellido') }}</label>

        <div>
          <input id="last_name2" type="text" class="form-control{{ $errors->has('last_name2') ? ' is-invalid' : '' }}" name="last_name2" value="{{ old('last_name2') }}" required autofocus> @if ($errors->has('last_name2'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('last_name2') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <label for="password">{{ __('Contraseña') }}</label>

        <div>
          <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required> @if ($errors->has('password'))
          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $errors->first('password') }}</strong>
                                      </span> @endif
        </div>
      </div>

      <div class="form-group col-md-12">
        <label for="password-confirm">{{ __('Confirmar Contraseña') }}</label>

        <div>
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
        </div>
      </div>

      <div class="form-group col-md-12 mb-0 button-container">
          <button type="submit" class="btn btn-primary">{{ __('Confirmar cuenta') }} </button>
      </div>
      
      <div class="form-group col-md-12 mb-0 button-container">
        
          <span>
          Ya tenés cuenta? 
          @if (Route::has('login'))
              <a class="btn btn-link" href="{{ route('login') }}">
                  {{ __('Ingresá aquí') }}
              </a>
          @endif
          </span>
        
      </div>

    </div>

  </form>

@endsection