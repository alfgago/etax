@extends('layouts/app')
@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection
@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Comprar</button>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tabbable verticalForm">
                <div class="row">
                    <div class="col-sm-3">
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <li class="active">
                                <a class="nav-link" aria-selected="false" href="/empresas/editar">Editar perfil de empresa</a>
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
                                <a class="nav-link active" aria-selected="true" href="/empresas/comprar-facturas-vista">Comprar facturas</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-9">
                        <div class="tab-content">
                            <form method="POST" action="/empresas/seleccionar-cliente" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <div class="form-row">
                                    <div class="form-group col-md-7">
                                        <h3>
                                            Comprar Facturas Adicionales
                                        </h3>
                                    </div>
                                    <div class="form-group col-md-5 dato-facturas" style="margin-top: 3%;">
                                        <div class="barra-limites emitidas" >
                                            <?php   ?>
                                            <div class="fill-bar" data-total="{{ $availableInvoices }}" data-fill="{{ number_format( $invoices ) }}"></div>
                                            <div class="barra-text">{{ number_format( $invoices ) }} de {{ $availableInvoices->monthly_quota }}</div>
                                        </div>
                                    </div>
                                    <p>Seleccione el paquete de facturas que requiere</p>
                                    <?php
                                    $first = 1;
                                    $checked = 'checked';
                                    $cnt = 1;
                                    ?>
                                    @foreach($productosEtax as $producto)
                                    <?php $checked = ($cnt == 1) ? 'checked="checked"' : ''; ?>
                                        <div class="form-group col-md-12">
                                            <label for="id_number">
                                                <input type="radio" name="product" id="product" value="{{$producto}}" <?php echo $checked ?> onchange="getRadioValue(this.value);"> {{ $producto->name }} -- Precio del paquete ${{$producto->price}}
                                            </label>
                                        </div>
                                        <?php
                                        $cnt++;
                                        ?>
                                    @endforeach
                                    <br>
                                    <br>
                                    <br>
                                    <div class="form-group col-md-12">
                                        <label for="tipo_persona">Seleccione su m&eacute;todo de pago</label>
                                        <select class="form-control select-search" name="payment_method" id="payment_method" >
                                            <option value='' selected>-- Seleccione un m&eacute;todo de pago --</option>
                                            @foreach ( $paymentmethods as $paymentmethod )
                                                <option value="{{ $paymentmethod->id }}" >{{ $paymentmethod->name }} {{ $paymentmethod->last_name }} - {{ $paymentmethod->masked_card }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Comprar</button>
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

@endsection
