@extends('layouts/app')

@section('title')
    Crear metodo de Pago
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-9 col-lg-12 col-md-12">

            <form method="POST" action="/payment-methods/payment-method-create" class="tarjeta">

                @csrf
                @method('post')

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                            Informaci&oacute;n de la tarjeta:</h3>
                    </div>
                    @include( 'payment_methods.formCreate' )

                </div>

                <button id="btn-submit" type="submit" class="hidden">Crear</button>

            </form>

        </div>
    </div>
@endsection

@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Crear</button>
    <button onclick="volver();" class="btn btn-primary">Volver</button>
@endsection

@section('footer-scripts')

<script>
    function volver() {
        window.history.back();
    }
</script>

@endsection
