@extends('layouts/app')

@section('title')
    Editar metodo de Pago
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-9 col-lg-12 col-md-12">

            <form method="POST" action="/payment/payment-token-update" class="tarjeta">

                @csrf
                @method('patch')

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                            Actualizaci&oacute;n del m&eacute;todo de pago
                        </h3>
                    </div>
                    @include( 'Payment.form', ['payment' => $payment] )

                </div>

                <button id="btn-submit" type="submit" class="hidden">Actualizar</button>

            </form>

        </div>
    </div>
@endsection

@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Actualizar</button>
@endsection

@section('footer-scripts')



@endsection
