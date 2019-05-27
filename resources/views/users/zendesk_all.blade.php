@extends('layouts/app')

@section('title')
Historial de Consultas
@endsection

@section('breadcrumb-buttons')   
@endsection

@section('content')
<style type="text/css">
    [data-href] {
        cursor: pointer;
    }
</style>
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
            <div class="col-8">
                <h3 class="card-title">Todas las consultas</h3>
                <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Asunto</th>
                            <th>Prioridad</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha creacion</th>
                        </tr>            
                    </thead>
                    <tbody>
                        @if ( $tickets )
                            <?php //var_dump($tickets);die; ?>
                            <?php 
                                $array = (array) $tickets;
                                $count = count($array);
                                //$result = json_decode($array['requests'][0]->id);
                                //echo $result;
                                //var_dump($array['requests'][0]->priority);die;
                                //echo $array['requests'][0]->priority;die;
                            ?>
                            <?php
                            for ($i=0;$i<count($array['requests']);$i++){
                                $id = json_decode($array['requests'][0]->id);
                            ?>
                            <tr data-href="/usuario/zendesk-detalle/{{$id}}">
                                <td>
                                    <?php echo json_decode($array['requests'][$i]->id); ?>
                                </td>
                                <td>
                                    <?php echo $array['requests'][$i]->subject; ?>
                                </td>
                                <td>
                                    <?php echo $array['requests'][$i]->priority; ?>
                                </td>
                                <td>
                                    <?php echo ($array['requests'][$i]->type) ? $array['requests'][$i]->type : 'No definido' ?>
                                </td>
                                <td>
                                    <?php echo ($array['requests'][$i]->status) ? $array['requests'][$i]->status : 'No definido' ?>
                                </td>
                                <td>
                                    <?php echo substr($array['requests'][$i]->updated_at, 0, 10); ?>
                                </td>
                            </tr>
                           <?php } ?>
                        @endif
                    </tbody>
                </table>
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('*[data-href]').on('click', function() {
            window.location = $(this).data("href");
        });
    });
</script>
@endsection

@section('footer-scripts')

@endsection