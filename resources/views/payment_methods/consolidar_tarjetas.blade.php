@extends('layouts/app')

@section('title')
    Consolidar pago por tarjeta
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-9 col-lg-12 col-md-12">

                <table class="table table-striped table-bordered dt-responsive nowrap dataTable no-footer dtr-inline" id="pagos_tarjeta">
                    <tr>
                        <th>Fecha</th>
                        <th>Documento</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <th>{{@$invoice->generated_date}}</th>
                                <th>{{@$invoice->document_number}}</th>
                                <th>{{@$invoice->client_first_name}} {{@$invoice->client_last_name}} {{@$invoice->client_last_name2}}</th>
                                <th>{{@$invoice->total * $invoice->currency_rate}}</th>
                                <th id="estado-{{@$invoice->id}}">
                                    @switch($invoice->confirm_payment_card)
                                        @case(0)
                                            Pendiente
                                            @break
                                        @case(1)
                                            Pagada
                                            @break
                                        @case(2)
                                            No Aplica
                                            @break

                                        @default
                                            Pendiente
                                    @endswitch
                                </th>
                                <th>
                                    <i class="text-success fa fa-check-circle confirmacion_pago" documento="{{@$invoice->id}}" tipo="1"></i>
                                    <i class="text-info fa fa-question-circle confirmacion_pago" documento="{{@$invoice->id}}" tipo="0"></i>
                                    <i class="text-danger fa fa-times-circle confirmacion_pago" documento="{{@$invoice->id}}" tipo="2"></i>
                                </th>
                            </tr>
                        @endforeach
                </table>
        </div>
    </div>
@endsection

@section('footer-scripts')

<script>
    $(function() {
        $('#pagos_tarjeta').DataTable();
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.confirmacion_pago').click(function(){
        var confirmacion = $(this).attr("tipo");
        var documento = $(this).attr("documento");
        $.ajax({
           type:'POST',
           url:'/confirmar-pago-tarjeta',
           data:{confirmacion:confirmacion,
                documento:documento},
           success:function(data){
            if(confirmacion == 0){
                $("#estado-"+documento).html("Pendiente");
            }
            if(confirmacion == 1){
                $("#estado-"+documento).html("Pagada");
            }
            if(confirmacion == 2){
                $("#estado-"+documento).html("No Aplica");
            }
           }
        });
    });
</script>

@endsection