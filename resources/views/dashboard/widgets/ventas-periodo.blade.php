<div class="card o-hidden ">
    <div class="card-body">
        <div class="card-title">{{ $titulo }}</div>
        <span class="text-26 text-muted">₡{{ $data->invoices_subtotal }}</span>
        <p class="text-small text-muted m-0"></p>
        <div id="chart-invoices" style="height: 65px;"></div>
        <div class="d-flex justify-content-between mt-4">
            <div class="flex-grow-1" style="flex:;">
                &nbsp;
            </div>
            <div class="flex-grow-1" style="flex:;">
                <span class="text-small">IVA emitido</span>
                <h5 class="m-0 font-weight-bold text-muted">₡{{ $data->total_invoice_iva }}</h5>
            </div>
            <div class="flex-grow-1" style="flex:;">
                <span class="text-small">Total de facturas emitidas</span>
                <h5 class="m-0 font-weight-bold text-muted">{{ $data->count_invoices }}</h5>
            </div>
        </div>
    </div>
</div>