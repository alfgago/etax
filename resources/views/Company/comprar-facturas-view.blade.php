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
                            <form method="POST" action="/payment/comprar-facturas" enctype="multipart/form-data">
                                @csrf

                                <div class="form-row" style="position: relative;">
                                    <div style="position: absolute; left: 0; top: 0; font-size: .8rem; display: flex;">
                                        <div style="margin-right: 2rem;">
                                            <label style="width: 100%;"><b>Facturas disponibles actualmente:</b></label>
                                            <div class="dato-facturas">
                                                <div class="barra-limites emitidas" >
                                                    <div class="fill-bar" data-total="{{ $availableInvoices }}" data-fill="{{ number_format( $invoices ) }}"></div>
                                                    <div class="barra-text">{{ number_format( $invoices ) }} de {{ $availableInvoices->monthly_quota }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label style="width: 100%;"><b>Facturas prepago disponibles</b></label>
                                            <div class="dato-facturas">
                                                <div class="barra-limites emitidas" >
                                                    <div class="fill-bar" data-total="{{ $availableInvoices }}" data-fill="{{ number_format( $invoices ) }}"></div>
                                                    <div class="barra-text">{{ currentCompanyModel()->additional_invoices }}</div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group col-md-12" style="margin-top: 4rem;">
                                        <h3>
                                            Comprar Facturas Adicionales
                                        </h3>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="product_id">Seleccione el paquete de facturas que requiere</label>
                                        <select class="form-control col-md-12" name="product_id" id="product_id" >
                                            @foreach($productosEtax as $producto)
                                                <option value="{{ $producto->id}}">{{ $producto->name }}: ${{$producto->price}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="payment_method">Seleccione su m&eacute;todo de pago</label>
                                        <select class="form-control select-search" name="payment_method" id="payment_method" onchange="getPaymentGateway();">
                                            <option value='' selected>-- Seleccione un m&eacute;todo de pago --</option>
                                            @foreach ( $paymentMethods as $paymentMethod )
                                                <option value="{{ $paymentMethod->id }}- {{$paymentMethod->payment_gateway}}" >{{ $paymentMethod->name }} {{ $paymentMethod->last_name }} - {{ $paymentMethod->masked_card }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12" style="white-space: nowrap;">
                                        <h3>
                                            Datos de receptor de la factura de eTax
                                        </h3>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="tipo_persona">Tipo de persona *</label>
                                        <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();">
                                            <option value="F" >Física</option>
                                            <option value="J" >Jurídica</option>
                                            <option value="D" >DIMEX</option>
                                            <option value="N" >NITE</option>
                                            <option value="E" >Extranjero</option>
                                            <option value="O" >Otro</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="id_number">Número de identificación *</label>
                                        <input type="text" class="form-control checkEmpty" name="id_number" id="id_number" value="{{ $company->id_number }}" onchange="getJSONCedula(this.value);" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="first_name">Nombre *</label>
                                        <input type="text" class="form-control checkEmpty" value="{{ $company->name }}" name="first_name" id="first_name" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="last_name">Apellido</label>
                                        <input type="text" class="form-control" name="last_name" value="{{ $company->last_name }}" id="last_name" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="last_name2">Segundo apellido</label>
                                        <input type="text" class="form-control" name="last_name2" value="{{ $company->last_name2 }}" id="last_name2" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="email">Correo electrónico *</label>
                                        <input type="text" class="form-control checkEmpty" name="email" id="email" value="{{ $company->email }}" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="phone">Teléfono</label>
                                        <input type="text" class="form-control" name="phone" id="phone" value="<?php echo ($company->phone) ? $company->phone : '' ?>" required>
                                    </div>

                                    <div></div>

                                    <div class="form-group col-md-4">
                                        <label for="country">País *</label>
                                        <select class="form-control checkEmpty" name="country" id="country">
                                            <option value="CR" selected>Costa Rica</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="state">Provincia</label>
                                        <select class="form-control" name="state" id="state" value="<?php echo ($company->state) ? $company->state : '' ?>" onchange="fillCantones();" required>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="city">Canton</label>
                                        <select class="form-control" name="city" id="city" onchange="fillDistritos();" required>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="district">Distrito</label>
                                        <select class="form-control" name="district" id="district" onchange="fillZip();" required>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="neighborhood">Barrio</label>
                                        <input class="form-control" name="neighborhood" id="neighborhood" value="<?php echo ($company->neighborhood) ? $company->neighborhood : '' ?>" required>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="zip">Código Postal</label>
                                        <input type="text" class="form-control" name="zip" id="zip" readonly >
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="address">Dirección</label>
                                        <input class="form-control" name="address" id="address" value="<?php echo ($company->address) ? $company->address : '' ?>" required>
                                        <input type="text" id="referenceCode" name="referenceCode" value="16" hidden>
                                        <input type="text" id="IpAddress" name="IpAddress" hidden>
                                        <input type="text" id="deviceFingerPrintID" name="deviceFingerPrintID" hidden>
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
    <script>
        $("#deviceFingerPrintID").val(cybs_dfprofiler("tc_cr_011007172","test"));
        $.getJSON('https://api.ipify.org?format=json', function(data){
            $("#IpAddress").val(data.ip);
        });
    $(document).ready(function(){
        fillProvincias();
        toggleApellidos();
        //Revisa si tiene estado, canton y distrito marcados.
        @if( @$company->state )
            $('#state').val( "{{ $company->state }}" );
            fillCantones();
            @if( @$company->city )
                $('#city').val( "{{ $company->city }}" );
                fillDistritos();
                @if( @$company->district )
                    $('#district').val( "{{ $company->district }}" );
                    fillZip();
                @endif
            @endif
        @endif
    });
</script>
<script src="../assets/js/cybs_devicefingerprint.js"></script>
@endsection
