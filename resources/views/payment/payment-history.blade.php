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
                             <?php 
                              $menu = new App\Menu;
                              $items = $menu->menu('menu_gestion_pagos');
                              foreach ($items as $item) { ?>
                                <li>
                                    <a class="nav-link @if($item->link == '/payments') active @endif" aria-selected="true"  style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
                                </li>
                              <?php } ?>
                        </ul>
                    </div>
                    <div class="col-9">
                        <div class="tab-content p-0">

                            <div class="tab-pane fade show active" role="tabpanel">

                                <h3 class="card-title">Historial de Pagos</h3>

                                <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Descripcion</th>
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
                                                    <td>{{ @$payment->sale->saleDescription() }}</td>
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
