@extends('layouts/app')

@section('title')
    Pagos pendientes
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
                                    <a class="nav-link" aria-selected="false" href="/payments-methods">M&eacute;todos de pagos</a>
                                </li>
                                <li>
                                    <a class="nav-link" aria-selected="false" href="/payments">Historial de pagos</a>
                                </li>
                                <li>
                                    <a class="nav-link active" aria-selected="true" href="/usuario/perfil">Cargos Pendientes</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-9">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" role="tabpanel">
                                <h3 class="card-title">Pagos Pendientes</h3>
                                <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Descripcion</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Pagar</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if ( $charges > 0 )
                                        @foreach($charges as $charge)
                                            @if($charge)
                                                <tr>
                                                    <td>{{$charge['proof']}}</td>
                                                    <td>${{$charge['amount']}}</td>
                                                    <td><?php echo ($charge['payment_status'] == 1) ? 'Pagado' : 'Pendiente' ?></td>
                                                    <td>{{$charge['created_at']}}</td>
                                                    <td>
                                                        <?php if($charge['payment_status'] == 2){ ?>
                                                            <form id="payment-form" class="inline-form" method="POST" action="/payment/pagar-cargo/{{$charge['chargeTokenId']}}" >
                                                            @csrf
                                                            @method('patch')
                                                                <a type="button" class="text-success mr-2" title="Pagar " style="display: inline-block; background: none; border: 0;"onclick="confirmPayment();">
                                                                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                                                                </a>
                                                            </form>
                                                        <?php } ?>
                                                    </td>
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
<script>
    function confirmPayment() {
        var formId = "#payment-form";
        Swal.fire({
            title: '¿Confirma que realizará el pago?',
            text: "Se utilizará su método de pago por defecto.",
            type: 'info',
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: 'Si, realizar pago'
        }).then((result) => {
            if (result.value) {
                $(formId).submit();
            }
        })

    }
</script>
@endsection
