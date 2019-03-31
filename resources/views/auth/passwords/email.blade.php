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

                        <div class="form-group col-md-12 text-center">
                          <h3>
                            Recuperar contraseña
                          </h3>
                        </div>

                        <div class="form-group col-md-12">
                            <div>
                                <input placeholder="{{ __('Correo electrónico') }}" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12 text-center ">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Enviar correo de recuperación') }}
                                </button>
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
                                  Te acordaste de tu contraseña? 
                                  @if (Route::has('login'))
                                      <a class="btn btn-link" href="{{ route('login') }}">
                                          {{ __('Ingresá aquí') }}
                                      </a>
                                  @endif
                                  </div>
                              </div>
                        </div>
                        
                      </div>
                    </form>

@endsection
