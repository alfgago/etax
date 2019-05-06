@extends('layouts/app')

@section('title')
Change Password
@endsection

@section('breadcrumb-buttons')
<button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar contraseña</button>    
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">

        @if(session('success'))

        <div role="alert" class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{session('success')}}

        </div>
        @endif

        @if(session('error'))

        <div role="alert" class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{session('error')}}

        </div>
        @endif        

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/overview">Visión general</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/general">Editar información personal</a>
                        </li>
                        <li class="active">
                            <a class="nav-link active" aria-selected="true" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/planes">Mis Planes Suscritos</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       

                        <div class="tab-pane fade show active" role="tabpanel">

                            <h3 class="card-title">Seguridad</h3>

                            <form method="POST" action="{{ route('User.update_password', ['id' => Auth::user()->id]) }}">

                                @csrf
                                @method('patch') 

                                <div class="form-row">

                                    <div class="col-md-8 col-sm-8">

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputTestl4">Old Password</label>
                                                <input type="password" placeholder="Old Password" name="old_password" class="form-control">

                                                @if ($errors->has('old_password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('old_password') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputTestl5">New Password</label>
                                                <input type="password" placeholder="New Password" name="password" class="form-control">

                                                @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputTestl5">Confirm Password</label>
                                                <input type="password" placeholder="Confirm Password" name="confirm_password" class="form-control">

                                                @if ($errors->has('confirm_password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('confirm_password') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-8 col-sm-8 d-flex flex-wrap">                                        
                                        <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>          
                                    </div>

                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection

@section('breadcrumb-buttons')
<button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración</button>
@endsection 

@section('footer-scripts')

@endsection