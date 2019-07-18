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

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li class="active">
                            <a class="nav-link" aria-selected="true" href="/usuario/perfil">Editar información personal</a>
                        </li>
                        <li class="active">
                            <a class="nav-link active" aria-selected="true" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/cambiar-plan">Cambiar plan</a>
                        </li>
                        @if( auth()->user()->isContador() )
                            <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                            </li>
                        @endif
                        
                         <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/wallet">Billetera</a>
                           </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content">       

                        <div class="tab-pane fade show active" role="tabpanel">

                            <h3 class="card-title">Seguridad</h3>

                            <form method="POST" action="{{ route('User.update_password', ['id' => Auth::user()->id]) }}">

                                @csrf
                                @method('patch') 

                                <div class="form-row">

                                    <div class="col-md-8 col-sm-8">

                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputTestl4">Contraseña anterior</label>
                                                <input type="password" name="old_password" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputTestl5">Nueva contraseña</label>
                                                <input type="password" name="password" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputTestl5">Repita la contraseña</label>
                                                <input type="password" name="confirm_password" class="form-control" required>
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
