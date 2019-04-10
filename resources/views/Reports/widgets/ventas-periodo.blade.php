<div class="card o-hidden ">
    <div class="card-body">
        <div class="card-title">{{ $titulo }}</div>
        <span class="text-26 text-muted">₡{{ number_format( $data->invoices_subtotal, 2 ) }}</span>
        <p class="text-small text-muted m-0"></p>
        <div class="d-flex justify-content-between mt-4">
            <div class="flex-grow-1" style="flex:;">
                &nbsp;
            </div>
            <div class="flex-grow-1" style="flex:;">
                <span class="text-small">IVA emitido</span>
                <h5 class="m-0 font-weight-bold text-muted">₡{{ number_format( $data->total_invoice_iva, 2 ) }}</h5>
            </div>
            <div class="flex-grow-1" style="flex:;">
                <span class="text-small">Total de facturas emitidas</span>
                <h5 class="m-0 font-weight-bold text-muted">{{ number_format( $data->count_invoices, 0 ) }}</h5>
            </div>
        </div>
    </div>
</div>