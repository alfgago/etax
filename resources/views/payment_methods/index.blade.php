@extends('layouts/app')

@section('title')
    M&eacute;todos de pago
@endsection

@section('breadcrumb-buttons')
    @if($cantidad < 3)
        <a type="submit" class="btn btn-primary" href="/payment/payment-create-view">Ingresar nuevo..</a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <table id="payments-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Tarjeta</th>
                    <th>Nombre</th>
                    <th>Fecha de vencimiento</th>
                    <th></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer-scripts')
    <script>
        $(function() {
            $('#payments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('Payment.data') }}",
                columns: [
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'payment_date', name: 'payment_date' },
                    { data: 'amount', name: 'amount' },
                    { data: 'sale', name: 'sale_id' }
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
    </script>
@endsection
