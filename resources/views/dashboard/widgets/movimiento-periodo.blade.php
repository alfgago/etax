<div class="card o-hidden fullh ">
    <div class="card-body text-left">
        <div class="card-title">{{ $titulo }}</div>
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Ventas</p>
            <p class="text-primary text-20 mb-2">₡{{ $data->invoices_subtotal }}</p>
        </div>
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Facturas emitidas</p>
            <p class="text-primary text-20 mb-2 border-bottom" >{{ $data->count_invoices }}</p>
        </div>
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Gastos e inversiones</p>
            <p class="text-primary text-20 mb-2">₡{{ $data->bills_subtotal }}</p>
        </div>
        <div class="content" style="max-width: 100%;">
            <p class="text-muted mt-2 mb-0">Facturas recibidas</p>
            <p class="text-primary text-20 mb-2">{{ $data->count_bills }}</p>
        </div>
    </div>
</div>
