@extends('layouts/app')

@section('title')
    Facturaci&oacute;n:
@endsection
@section('breadcrumb-buttons')

    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar</button>
    <button onclick="back();" class="btn btn-primary">Volver</button>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tabbable verticalForm">
                <div class="row">
                    <div class="col-sm-3">
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <li>
                                <a class="nav-link" aria-selected="false" href="/empresas/editar">Editar perfil de empresa</a>
                            </li>
                            <li class="active">
                                <a class="nav-link" aria-selected="false" href="/empresas/configuracion">Configuración avanzada</a>
                            </li>
                            <li>
                                <a class="nav-link" aria-selected="false" href="/empresas/certificado">Certificado digital</a>
                            </li>
                            <li>
                                <a class="nav-link" aria-selected="false" href="/empresas/equipo">Equipo de trabajo</a>
                            </li>
                            <li>
                                <a class="nav-link active" aria-selected="true" href="/empresas/facturacion">Facturaci&oacute;n</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-9">
                        <div class="tab-content">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="default_invoice_notes">Compañ&iacute;a:</label>
                                    <input type="text" class="form-control" value="{{ $company_name }}" readonly>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="default_invoice_notes">Sucursal:</label>
                                    <input type="text" class="form-control" value="{{ $sucursal->description }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="default_invoice_notes">Provincia:</label>
                                    <input type="text" class="form-control" value="{{ $sucursal->state }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="default_invoice_notes">Cant&oacute;n:</label>
                                    <input type="text" class="form-control" value="{{ $sucursal->city }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="default_invoice_notes">Distrito:</label>
                                    <input type="text" class="form-control" value="{{ $sucursal->district }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="default_invoice_notes">Barrio:</label>
                                    <input type="text" class="form-control" value="{{ $sucursal->neighborhood }}">
                                </div>
                                <div class="form-group col-md-9">
                                    <label for="default_invoice_notes">Direcci&oacute;n:</label>
                                    <input type="text" class="form-control" value="{{ $sucursal->address }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="default_invoice_notes">Tel&eacute;fono:</label>
                                    <input type="text" class="form-control" value="{{ $sucursal->phone }}">
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <form style="display: inline-block;" action="{{route('Company.add_terminal_view', ['id' => $sucursal->id])}}" method="post">
                                @csrf
                                @method('patch')
                                <h3 class="card-title">Terminales</h3>
                                <button class="btn btn-sm btn-primary pull-right m-0" style="margin-left:3em !important;margin-top:1em !important;" type="submit">Agregar</button>
                            </form>
                            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Nombre Interno</th>
                                    <th>Nombre Cliente</th>
                                    <th>&Uacute;ltimo documento</th>
                                    <th>Estado</th>
                                    <th>Editar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ( $terminales->count() )
                                    @foreach($terminales AS $terminal)
                                        <tr>
                                            <td>{{$terminal->internal_description}}</td>
                                            <td>{{$terminal->description}}</td>
                                            <td>{{$terminal->last_document}}</td>
                                            <td>{{$terminal->status}}</td>
                                            <td>
                                                <form style="display: inline-block;" action="{{route('Company.edit_terminal_view', ['id' => $terminal->id])}}" method="post">
                                                    @csrf
                                                    @method('get')
                                                    <button type="submit" class="text-info mr-2" title="Editar Terminal" style="display: inline-block; background: none; border: 0;">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
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
    <style>
        .form-button {
            display: block;
            margin: 0;
            padding: 0.25rem 0.5rem;
            font-size: 0.9rem;
            height: calc(1.9695rem + 2px);
        }
    </style>
@endsection
