@extends('layouts/app')

@section('title')
Consultas
@endsection

@section('breadcrumb-buttons')   
@endsection

@section('content')

<div class="col-md-12">
    <div class="tabbable verticalForm">
        <div class="row">
            <div class="col-2">
                <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <!-- 
                    <li class="active">
                        <a class="nav-link active" aria-selected="true" href="/usuario/zendesk">General</a>
                    </li>
                     -->
                    <li>
                        <a class="nav-link" aria-selected="false" href="/usuario/ver_consultas">Ver consultas</a>
                    </li>
                    <li>
                        <a class="nav-link" aria-selected="false" href="/usuario/crear-ticket">Agregar Consulta</a>
                    </li>
                </ul>
            </div>
            <div class="col-10">
                <h3 class="card-title">Datos Generales</h3>
                <div class="row">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <div class="col-md-12">                                    
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="inputTestl4">Id Consultas:</label>
                                </div>
                                <div class="form-group col-md-10">
                                    <label for="inputTestl4">{{$submitter_id}}</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="inputTestl4">Id de Grupo:</label>
                                </div>
                                <div class="form-group col-md-10">
                                    <label for="inputTestl4"> {{$group_id}}</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="inputTestl4">Id Organizacion:</label>
                                </div>
                                <div class="form-group col-md-10">
                                    <label for="inputTestl4"> {{$organization_id}}</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="inputTestl4">Total consultas:</label>
                                </div>
                                <div class="form-group col-md-10">
                                    <label for="inputTestl4"> {{$total}}</label>
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