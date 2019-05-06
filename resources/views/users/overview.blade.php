@extends('layouts/app')

@section('title')
Información general del perfil
@endsection

@section('breadcrumb-buttons')   
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">         

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li class="active">
                            <a class="nav-link active" aria-selected="true" href="/usuario/overview">Visión general</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/general">Editar información personal</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Seguridad</a>
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
                            <h3 class="card-title">Visión general</h3>

                            <div class="row">
                                <div class="col-md-8 col-sm-8">                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl4">Nombre :</label>
                                            <label for="inputTestl4"> {{Auth::user()->first_name}} </label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputText">Primer apellido :</label>
                                            <label for="inputText">   {{Auth::user()->last_name}} </label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">Segundo apellido :</label>
                                            <label for="inputTestl5">  {{Auth::user()->last_name2}} </label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputText1">Cedula :</label>
                                            <label for="inputText1"> {{Auth::user()->id_number }}</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">Telefono :</label>
                                            <label for="inputTestl5"> {{Auth::user()->phone}}</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputText1">Celular :</label>
                                            <label for="inputText1">  {{Auth::user()->celular}}</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">Email :</label>
                                            <label for="inputTestl5"> {{Auth::user()->email}}</label>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label for="Direccion">Direccion :</label>
                                        <label for="Direccion"> {{Auth::user()->address}}</label>
                                    </div>                                    
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="inputTestl4">Estado :</label>
                                            <label for="inputTestl4">  {{Auth::user()->state }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection

@section('footer-scripts')

@endsection