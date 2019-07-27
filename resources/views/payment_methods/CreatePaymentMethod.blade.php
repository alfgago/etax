@extends('layouts/app')

@section('title')
    Crear metodo de Pago
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="tabbable verticalForm">
                <div class="row">
                    <div class="col-3">
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            @if( !auth()->user()->is_guest )
                                <li>
                                    <a class="nav-link active" aria-selected="true" href="/payments-methods">M&eacute;todos de pagos</a>
                                </li>
                                <li>
                                    <a class="nav-link " aria-selected="true" href="/payments">Historial de pagos</a>
                                </li>
                                <li>
                                    <a class="nav-link" aria-selected="false" href="/payment/pending-charges">Cargos Pendientes</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-9">
                        <div class="tab-content p-0">

                            <div class="tab-pane fade show active" role="tabpanel">

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
                    </div>
                </div>
            </div>
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
