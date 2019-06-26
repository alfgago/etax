@extends('layouts/app')

@section('title')
    Seleccion de cliente
@endsection

@section('slug', 'wizard')

@section('header-scripts')

@endsection
@section('content')
    <div class="wizard-container">
        <div class="wizard-popup">
            <div class="titulo-bienvenida">
                <h2>Selecci&oacute;n de cliente</h2>
            </div>
            <div class="form-container">
                <form method="POST" action="/empresas/comprar-facturas" class="wizard-form" enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="form-group col-md-12">
                        <h3 class="mt-0">
                            Actualice la informaci&oacute;n
                        </h3>
                    </div>
                    <div class="step-section step1 is-active">
                        <div class="form-row">
                            <div class="form-group col-md-12" style="white-space: nowrap;">
                                Datos del receptor de la factura de eTax
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
                                <input type="text" class="form-control checkEmpty" name="id_number" id="id_number" onchange="getJSONCedula(this.value);" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="first_name">Nombre *</label>
                                <input type="text" class="form-control checkEmpty" value="{{ $user->first_name }}" name="first_name" id="first_name" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="last_name">Apellido</label>
                                <input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" id="last_name" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="last_name2">Segundo apellido</label>
                                <input type="text" class="form-control" name="last_name2" value="{{ $user->last_name2 }}" id="last_name2" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="email">Correo electrónico *</label>
                                <input type="text" class="form-control checkEmpty" name="email" id="email" value="{{ $user->email }}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="phone">Teléfono</label>
                                <input type="text" class="form-control" name="phone" id="phone" value="<?php echo ($user->phone) ? $user->phone : '' ?>" required>
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
                                <select class="form-control" name="state" id="state" onchange="fillCantones();" required>
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
                                <input class="form-control" name="neighborhood" id="neighborhood" required>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="zip">Zip</label>
                                <input type="text" class="form-control" name="zip" id="zip" readonly >
                            </div>
                            <div class="form-group col-md-9">
                                <label for="address">Dirección</label>
                                <input class="form-control" name="address" id="address" required>
                            </div>
                            <div class="form-group"></div>
                            <input type="text" hidden value=" {{$product->id}}" name="product_id">
                            <input type="text" hidden value=" {{$product->name}}" name="product_name">
                            <input type="text" hidden value=" {{$product->price}}" name="product_price">
                            <input type="text" hidden value=" {{$payment_method}}" name="payment_method">
                            <div class="btn-holder">
                                <button type="submit" id="btn-submit" class="btn btn-primary btn-next" onclick="CambiarNombre();">Continuar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.css" />
    <script src="../assets/js/cybs_devicefingerprint.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            fillProvincias();
        });
    </script>
@endsection
