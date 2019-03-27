<div class="card o-hidden fullh ">
    <div class="card-body text-left">
        <div class="card-title">{{ $titulo }}</div>
        <div class="row">
            <div class="col-md-6">
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Ventas</p>
            <p class="text-primary text-16 mb-2">₡{{ number_format( $data->invoices_subtotal, 2 ) }}</p>
        </div>
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Facturas emitidas</p>
            <p class="text-primary text-16 mb-2" >{{ number_format( $data->count_invoices, 0 ) }}</p>
        </div>
                
            </div>
            <div class="col-md-6">
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Gastos e inversiones</p>
            <p class="text-primary text-16 mb-2">₡{{ number_format( $data->bills_subtotal, 2 ) }}</p>
        </div>
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Facturas recibidas</p>
            <p class="text-primary text-16 mb-2">{{ number_format( $data->count_bills, 0 ) }}</p>
        </div>
                
            </div>
        </div>
    </div>
</div>
