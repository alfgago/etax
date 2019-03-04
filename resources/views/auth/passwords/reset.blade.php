@extends('layouts/login') 

@section('title') Recuperar contraseña @endsection 

@section('content')

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                      
                        <div class="form-group col-md-12 text-left">
                          <h3>
                            Recuperar contraseña
                          </h3>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="email">{{ __('Correo electrónico') }}</label>

                            <div>
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="password">{{ __('Contraseña') }}</label>

                            <div>
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="password-confirm">{{ __('Confirmar Contraseña') }}</label>

                            <div>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group col-md-12 mb-0">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reiniciar Contraseña') }}
                                </button>
                        </div>
                    </form>
                
@endsection
