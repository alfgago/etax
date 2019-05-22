@extends('layouts/app')

@section('title')
Historial de Consultas
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
                            <a class="nav-link active" aria-selected="true" href="/usuario/zendesk">General</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/crear_ticket">Agregar Consulta</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Ver consultas</a>
                        </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">
                        <div class="tab-pane fade show active" role="tabpanel">
                            <h3 class="card-title">Agregar ticket</h3>
                            <div class="row">
                                <div class="col-md-8 col-sm-8">                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl4">Usuario:</label>
                                            <label for="inputTestl4"> {{$user_id}} </label>
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