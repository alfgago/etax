@extends('layouts/app')

@section('title')
    Comprar contabilidades
@endsection

@section('breadcrumb-buttons')
    @if( auth()->user()->isContador() )
        @can('admin')
            <a class="btn btn-primary" href="/usuario/empresas">Empresas</a>
        @endcan
    @endif
@endsection
@section('content')
<div class="row">

    <div class="col-md-12">

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                         <?php 
                        $menu = new App\Menu;
                        $items = $menu->menu('menu_perfil');
                        foreach ($items as $item) { ?>
                            <li>
                                <a class="nav-link @if($item->link == '/usuario/seguridad') active @endif" aria-selected="false"  style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
                            </li>
                        <?php } ?>

                        @if( auth()->user()->isContador() )
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                        @endif
                        @if( auth()->user()->isInfluencers())
                         <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/wallet">Billetera</a>
                           </li>
                        @endif
                    </ul>
                </div>
                <div class="col-9">


                        <div class="tab-content">
                            <form method="POST" action="/payment/comprar-contabilidades" enctype="multipart/form-data">
                                @csrf

                                @method('post')
                                <div class="form-row" style="position: relative;">
                                    <div class="form-group col-md-12" >
                                        <h3>
                                            Comprar contabilidades adicionales
                                        </h3>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="product_id">Seleccione la cantidad de contabilidades que requiere</label>
                                        <input type="number" class="form-control col-md-12" name="contabilidades" id="contabilidades" value="1"  onchange="calcularPrecioContabilidades()"/>
                                        <input type="number" class="form-control d-none" name="recurrency" id="recurrency" value="{{@$sale->recurrency}}" />
                                        <input type="number" class="form-control d-none" name="price_code" id="price_code" value="{{@$sale->price}}" />
                                        <input type="number" class="form-control d-none" name="diff" id="diff" value="{{@$diff}}" />
                                        <label for="payment_method">Seleccione su m&eacute;todo de pago</label>
                                        <select class="form-control select-search" name="payment_method" id="payment_method" required>
                                            <option value='' selected>-- Seleccione un m&eacute;todo de pago --</option>
                                            @foreach ( $paymentMethods as $paymentMethod )
                                                <option value="{{ $paymentMethod->id }}" >{{ $paymentMethod->name }} {{ $paymentMethod->last_name }} - {{ $paymentMethod->masked_card }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <h4>En este momento cuentas con <span id="cantidad_disponibles_contabilidades">{{@$sale->num_companies}}</span> contabilidades disponibles</h4>
                                        <p>Estas comprando <span id="cantidad_contabilidades_requeridad">1 contabilidad</span></p>
                                        <p>Monto a pagar $<span id="precio_pago"></span></p>
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
                                        <select class="form-control" name="state" id="state" onchange="fillCantones();" required>
                                            <option value="0" selected>Seleccione una opcion</option>
                                            <option value="1" >San José</option>
                                            <option value="2" >Alajuela</option>
                                            <option value="3" >Cartago</option>
                                            <option value="4" >Heredia</option>
                                            <option value="5" >Guanacaste</option>
                                            <option value="6" >Puntarenas</option>
                                            <option value="7" >Limón</option>
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
                                        <input class="form-control" name="neighborhood" id="neighborhood">
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="zip">Código Postal</label>
                                        <input type="text" class="form-control" name="zip" id="zip" readonly >
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="address">Dirección</label>
                                        <input class="form-control" name="address" id="address">
                                    </div>
                                    <input type="text" hidden id="IpAddress" name="IpAddress">
                                    <input type="text" hidden id="deviceFingerPrintID" name="deviceFingerPrintID">

                                    <button id="btn-submit" type="submit" class="btn btn-primary">Comprar</button>
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

<script type="text/javascript">
    calcularPrecioContabilidades();

    function calcularPrecioContabilidades() {
          var cantidad = parseFloat($('#contabilidades').val());
          var texto = cantidad+" contabilidades";
          if(cantidad == 1){
            texto = cantidad+" contabilidad";
          }
          var price_code = parseFloat($('#price_code').val());
          var existentes = parseFloat($('#cantidad_disponibles_contabilidades').html());
          cantidad = cantidad + existentes;
          existentes = existentes ;
          var recurrency = $('#recurrency').val();
          var diff = $('#diff').val();
          var procesadas = 0;
          var total = 0;
          var total_extras = 0;
          var precio_25 = 8;
          var precio_10 = 10;
          if(price_code != 0){
             precio_25 = price_code;
             precio_10 = price_code;
          }
          if(cantidad > 25){
            procesadas = (cantidad - 25 );
            if(existentes > 25){
                procesadas = (cantidad - existentes );
            }
              if(procesadas > 0){
                  total_extras =  procesadas * precio_25;
                  cantidad = 25;
                }
          }
          if(cantidad > 10){
              procesadas = (cantidad - existentes);
              if(procesadas > 0){
                  total_extras +=  procesadas * precio_10;
                  cantidad = 10;
                }
          }
          if(recurrency == 1){
            total_extras = total_extras / 31 * diff;
            total = total_extras;
          }
          if(recurrency == 6){
            total_extras = total_extras / 133 * diff;
            total = total_extras * 6;
          }
          if(recurrency == 12){
            total_extras = total_extras / 366 * diff;
            total = total_extras * 12;
          }
          var precioFinal = parseFloat(total).toFixed(2);
          $("#precio_pago").html(precioFinal);
          $("#cantidad_contabilidades_requeridad").html(texto);

    }

</script>
<script src="../assets/js/cybs_devicefingerprint.js" type="text/javascript"></script>
<script>
    $("#deviceFingerPrintID").val(cybs_dfprofiler("tc_cr_011007172","test"));
    $.getJSON('https://api.ipify.org?format=json', function(data){
        $("#IpAddress").val(data.ip);
    });
</script>
@endsection
