@extends('layouts/app')

@section('title')
    M&eacute;todos de pago
@endsection

@section('breadcrumb-buttons')
    @if($cantidad < 3)
        <a type="submit" class="btn btn-primary" href="/payment-methods/payment-method-create-view">Registrar nuevo método de pago</a>
    @endif
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
                                    <a class="nav-link" aria-selected="false" href="/payments">Historial de pagos</a>
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
                                <h3 class="card-title">Metodos de Pagos</h3>
                                <table id="paymentMethod-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Tarjeta</th>
                                        <th>Nombre</th>
                                        <th>Vencimiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
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
        $(function() {
            $('#paymentMethod-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "/api/paymentsMethods",
                columns: [
                    { data: 'masked_card', name: 'masked_card' },
                    { data: 'name', name: 'name' },
                    { data: 'due_date', name: 'due_date' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ],
                language: {
                    url: "/lang/datatables-es_ES.json",
                },
            });
        });

        function confirmDelete( id ) {
            var formId = "#delete-form-"+id;
            Swal.fire({
                title: '¿Está seguro que desea eliminar el metodo de pago',
                text: "",
                type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Sí, quiero eliminarlo'
            }).then((result) => {
                if (result.value) {
                    $(formId).submit();
                }
            })

        }
        function confirmUpdate( id ) {
            var formId = "#update-form-"+id;
            Swal.fire({
                title: '¿Desea actualizar el metodo de pago?',
                text: "Este metodo de pago sera utilizado por defecto",
                type: 'info',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.value) {
                    $(formId).submit();
                }
            })

        }
    </script>
@endsection

