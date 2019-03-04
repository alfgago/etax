@extends('layouts/login') 

@section('title') Recuperar contraseña @endsection 

@section('content')

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                      
                      <div class="form-row">

                        <div class="form-group col-md-12 text-left">
                          <h3>
                            Recuperar contraseña
                          </h3>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="email">{{ __('Correo electrónico') }}</label>

                            <div>
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12 mb-0">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Enviar correo de recuperación') }}
                                </button>
                        </div>
                        
                        <div class="form-group col-md-12 mb-0 button-container">
                            <span>
                                No tenés cuenta? 
                                @if (Route::has('register'))
                                    <a class="btn btn-link" href="{{ route('register') }}">
                                        {{ __('Registrate aquí') }}
                                    </a>
                                @endif
                                </span>
                            <span>
                              || Te acordaste de tu contraseña? 
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
