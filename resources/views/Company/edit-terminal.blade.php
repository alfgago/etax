@extends('layouts/app')

@section('title')
    Terminal: {{ $terminal->description }}
@endsection
@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Editar</button>
    <button class="btn btn-primary" onclick="back();">Volver</button>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tabbable verticalForm">
                <div class="row">
                    <div class="col-sm-3">
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <li class="active">
                                <a class="nav-link active" aria-selected="true" href="/empresas/editar">Editar perfil de empresa</a>
                            </li>
                            <li>
                                <a class="nav-link" aria-selected="false" href="/empresas/configuracion">Configuración avanzada</a>
                            </li>
                            <li>
                                <a class="nav-link" aria-selected="false" href="/empresas/certificado">Certificado digital</a>
                            </li>
                            <li>
                                <a class="nav-link" aria-selected="false" href="/empresas/equipo">Equipo de trabajo</a>
                            </li>
                            <li>
                                <a class="nav-link" aria-selected="false" href="/empresas/facturacion">Facturaci&oacute;n</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-9">
                        <div class="tab-content">
                            <form method="POST" action="{{ route('Company.edit_terminal')}}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <h3>
                                            Editar Terminal
                                        </h3>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="id_number">Descripci&oacute;n:</label>
                                        <input type="text" class="form-control" name="description" id="description" required value="{{ $terminal->description }}">
                                    </div>
                                    <input type="text" hidden value="{{$terminal->id}}" name="terminal_id">
                                    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Editar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        function back() {
            window.history.back();
        }
    </script>
@endsection
