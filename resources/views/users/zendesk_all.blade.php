@extends('layouts/app')

@section('title')
Consultas
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
                            <?php $count = sizeof($tickets['tickets']); ?>
                            @for ($i=0; $i<$count;$i++)
                            <?php $id = $tickets['tickets'][$i]['id']; ?>
                            <tr data-href="/usuario/zendesk-detalle/{{$id}}">
                                <td>
                                    <?php echo $tickets['tickets'][$i]['id'] ?>
                                </td>
                                <td>
                                    <?php echo $tickets['tickets'][$i]['subject'] ?>
                                </td>
                                <td>
                                    <?php echo $tickets['tickets'][$i]['priority'] ?>
                                </td>
                                <td>
                                    <?php echo ($tickets['tickets'][$i]['type']) ? $tickets['tickets'][$i]['type'] : 'No definido' ?>
                                </td>
                                <td>
                                    <?php echo ($tickets['tickets'][$i]['status']) ? $tickets['tickets'][$i]['status'] : 'No definido' ?>
                                </td>
                                <td>
                                    <?php echo substr($tickets['tickets'][$id-1]['updated_at'], 0, 10); ?>
                                </td>
                            </tr>
                            @endfor
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