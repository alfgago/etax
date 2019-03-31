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
      <button type="submit" class="btn btn-primary">{{ __('Iniciar sesión') }}</button>
    </div>

    <div class="form-group col-md-12 button-container text-center">

      <div class="inline-block text-left">
        
        <div>
            No tenés cuenta? 
            @if (Route::has('register'))
                <a class="btn btn-link" href="{{ route('register') }}">
                    {{ __('Registrate aquí') }}
                </a>
            @endif
        </div>
        <div>
          Se te olvidó la contraseña? 
          @if (Route::has('password.request'))
              <a class="btn btn-link" href="{{ route('password.request') }}">
                  {{ __('Recuperala') }}
              </a>
          @endif
         </div>
         
      </div>

    </div>

  </div>

</form>

@endsection