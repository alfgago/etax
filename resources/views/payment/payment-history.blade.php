@extends('layouts/app')

@section('title')
    Pagos
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
                                    <a class="nav-link" aria-selected="true" href="/payments-methods">M&eacute;todos de pagos</a>
                                </li>
                                <li>
                                    <a class="nav-link active" aria-selected="true" href="/payments">Historial de pagos</a>
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

                                <h3 class="card-title">Historial de Pagos</h3>

                                <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if ( $payments->count() )
                                        @foreach($payments as $payment)
                                            @if($payment)
                                                <tr>
                                                    <td>{{$payment->payment_date}}</td>
                                                    <td>{{$payment->amount}}</td>
                                                    <td>{{$payment->getStatusString() }}</td>
                                                </tr>
                                            @endif
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
    </div>

@endsection

@section('footer-scripts')

    <script>

        function confirmDelete( id ) {
            var formId = "#delete-form-"+id;
            Swal.fire({
                title: '¿Está seguro que desea desactivar la empresa?',
                text: "Los datos de la empresa serán guardados durante 12 meses. Si desea recuperarlos o transferirlos a otra cuentas, contacte a soporte.",
                type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Sí, quiero desactivarla'
            }).then((result) => {
                if (result.value) {
                    $(formId).submit();
                }
            })

        }

    </script>


@endsection
