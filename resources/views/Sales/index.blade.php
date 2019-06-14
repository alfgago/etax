@extends('layouts/app')

@section('title')
    Compras productos eTax
@endsection

@section('breadcrumb-buttons')
    <a type="submit" class="btn btn-primary" href="/sale/sales-new-view">Nueva compra..</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <table id="sales-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Recurrencia</th>
                    <th>etax_products</th>
                    <th>Status</th>
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
            $('#sales-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('Payment.data') }}",
                columns: [
                    { data: 'recurrency', name: 'recurrency' },
                    { data: 'etax_products', name: 'name' },
                    { data: 'status', name: 'last_name' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
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
