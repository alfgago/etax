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
                            <div class="form-row">
					  	
    						    <div class="form-group col-md-12">
    						      <h3>
    						        Tabla de cargos pendientes
    						      </h3>
    						    </div>
						        
						        <div class="form-group col-md-12">
						    
                                    <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Descripcion</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Pagar</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if ( $charges )
                                            @foreach($charges as $charge)
                                                @if(@$charge->payment_status == 1)
                                                    <tr>
                                                        <td>{{ @$charge->sale->saleDescription() }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($charge->created_at)->format('d/m/Y') }}</td>
                                                        <td>${{$charge->amount}}</td>
                                                        <td>Pendiente</td>
                                                        <td>
                                                            
                                                            <form id="payment-form" class="inline-form" method="POST" action="/payment/pagar-cargo/{{$charge['id']}}" >
                                                            @csrf
                                                            @method('patch')
                                                                <a type="button" class="text-success mr-2" title="Pagar " style="display: inline-block; background: none; border: 0;"onclick="confirmPayment();">
                                                                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                                                                </a>
                                                            </form>
                                                            
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
