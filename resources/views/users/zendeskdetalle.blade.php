@extends('layouts/app')

@section('title')
Historial de Consultas
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
                        <a class="nav-link" aria-selected="false" href="/usuario/zendesk">General</a>
                    </li>
                -->                    
                    <li>
                        <a class="nav-link active" aria-selected="true" href="/usuario/ver_consultas">Ver consultas</a>
                    </li>
                    <li>
                        <a class="nav-link" aria-selected="false" href="/usuario/crear-ticket">Agregar Consulta</a>
                    </li>
                </ul>
            </div>
            <div class="col-9">
                <h3 class="card-title">Detalle de Consulta</h3>
                <div class="row">
                    <div class="form-group col-md-11">
                        <label>Consulta:</label>
                        <H6><?php echo $tickets[0]->subject;  ?></H6>
                    </div>
                    <div class="form-group col-md-11">
                        <label>Respuesta:</label>
                        <H6><?php echo $tickets[0]->description; ?></H6>
                    </div>
                    <div class="form-group col-md-11">
                        <label>Creado:</label>
                        <H6><?php
                            $fecha = substr($tickets[0]->created_at, 0, 10);  
                            $hora = substr($tickets[0]->created_at, 11, 8);  
                            echo $fecha . ' a las ' . $hora; 
                        ?></H6>
                    </div>
                    <div class="form-group col-md-11">
                        <label>Actualizado:</label>
                        <H6><?php
                            $fecha = substr($tickets[0]->updated_at, 0, 10);  
                            $hora = substr($tickets[0]->updated_at, 11, 8);  
                            echo $fecha . ' a las ' . $hora; 
                        ?></H6>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 offset-6">
                <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <li class="active">
                        <a class="nav-link" aria-selected="false" href="/usuario/ver_consultas">Volver</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-scripts')

@endsection