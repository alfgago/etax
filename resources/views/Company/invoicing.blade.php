@extends('layouts/app')

@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection
@section('breadcrumb-buttons')

    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar</button>


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
                            <form method="POST" action="{{ route('Company.update_config', ['id' => $company->id]) }}">
                                @csrf
                                @method('patch')
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <h3>
                                            Facturación
                                        </h3>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="use_invoicing">¿Desea emitir facturas electrónicas con eTax?</label>
                                        <select class="form-control" name="use_invoicing" id="use_invoicing" required>
                                            <option value="1" {{ @$company->use_invoicing ? 'selected' : '' }}>Sí</option>
                                            <option value="0" {{ !(@$company->use_invoicing) ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="last_document">Último documento emitido</label>
                                        <input type="text" class="form-control" name="last_document" id="last_document" value="{{ @$company->last_document }}" required>
                                        <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="default_vat_code">Tipo de IVA por defecto</label>
                                        <select class="form-control" id="default_vat_code" name="default_vat_code" >
                                            @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
                                                <option value="{{ $tipo['codigo'] }}" porcentaje="{{ $tipo['porcentaje'] }}" {{ @$company->default_vat_code == $tipo['codigo']  ? 'selected' : '' }}>{{ $tipo['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="default_currency">Tipo de moneda por defecto</label>
                                        <select class="form-control" name="default_currency" id="default_currency" required>
                                            <option value="crc" {{ @$company->default_currency == 'crc' ? 'selected' : '' }}>CRC</option>
                                            <option value="usd" {{ @$company->default_currency == 'usd' ? 'selected' : '' }}>USD</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="default_invoice_notes">Notas por defecto</label>
                                        <textarea class="form-control" name="default_invoice_notes" id="default_invoice_notes" >{{ @$company->default_invoice_notes }}</textarea>
                                    </div>
                                    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-content">
                            <h3 class="card-title">Sucursales</h3>
                            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Descripci&oacute;n</th>
                                    <th>Cant&oacute;n</th>
                                    <th>Distrito</th>
                                    <th>Direcci&oacute;n</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ( $sucursales->count() )
                                    @foreach($sucursales AS $sucursal)
                                        <tr id="sucursal" onclick="verInfo({{$sucursal->id}});">
                                            <td>{{$sucursal->description}}</td>
                                            <td>{{$sucursal->district}}</td>
                                            <td>{{$sucursal->neighborhood}}</td>
                                            <td>{{$sucursal->address}}</td>
                                            <td>
                                                <form style="display: inline-block;" action="{{route('Company.sucursal', ['id' => $sucursal->id])}}" method="post">
                                                    @csrf
                                                    @method('patch')
                                                    <button type="submit" class="text-info mr-2" title="Ver Detalles" style="display: inline-block; background: none; border: 0;cursor:pointer;">
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
