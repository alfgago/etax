@extends('layouts/app')

@section('title')
Historial de Consultas
@endsection

@section('content')

<div class="col-md-12">
    <div class="tabbable verticalForm">
        <div class="row">
            <div class="col-2">
                <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <!--                     
                    <li class="active">
                        <a class="nav-link" aria-selected="false" href="/usuario/zendesk">General</a>
                    </li>
                     -->
                    <li>
                        <a class="nav-link" aria-selected="false" href="/usuario/ver_consultas">Ver consultas</a>
                    </li>
                    <li>
                        <a class="nav-link active" aria-selected="true" href="/usuario/crear-ticket">Agregar Consulta</a>
                    </li>
                </ul>
            </div>
            <div class="col-10">
                <h3 class="card-title">Agregar Consulta</h3>
                <div class="tab-content p-0">
                    <form method="POST" action="/usuario/crear-request">
                        @csrf   
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <br>
                                <div class="form-group col-md-10">
                                    <label for="type">Tipo de Consulta:</label>
                                    <div class="input-group">
                                        <select id="type" name="type" class="form-control" required>
                                            <option selected value="">Seleccione</option>
                                            <option value="task">Tarea</option>
                                            <option value="incident">Incidente</option>
                                            <option value="problem">Problema</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-10">
                                    <label for="priority">Prioridad:</label>
                                    <div class="input-group">
                                        <select id="priority" name="priority" class="form-control" required>
                                            <option selected value="">Seleccione</option>
                                            <option value="low">Baja</option>
                                            <option value="normal">Normal</option>
                                            <option value="high">Alta</option>
                                            <option value="urgent">Urgente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-10">
                                    <label for="subject">Asunto:</label>
                                    <input type="text" class="form-control" name="subject" id="subject" value="" required>
                                </div>
                                <div class="form-group col-md-10">
                                    <label for="description">Comentario:</label>
                                    <textarea class="form-control" name="description" id="description" style="resize: none;" rows="4" cols="50" required></textarea>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="btn-holder col-md-10 offset-7">
                            <button id="btn-submit" type="submit" class="hidden">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar </button>
@endsection 

@section('footer-scripts')

@endsection