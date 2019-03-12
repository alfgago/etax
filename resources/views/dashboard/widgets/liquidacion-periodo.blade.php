<div class="card fullh">
  <div class="card-body text-left">
      <div class="card-title">{{ $titulo }}</div>
      <p class="text-small text-muted m-0">Saldo de IVA de {{ $mes }}</p>
      <p class="text-20 mb-3 text-muted"><i class="text-success i-Coins"></i> ₡{{ $data->balance_real }} </p>
      <p class="text-12 text-muted m-0 p-2 border-bottom"><strong> + ₡{{ $data->total_invoice_iva }} </strong> IVA emitido</p>
      <p class="text-12 text-muted m-0 p-2 border-bottom"><strong>- ₡{{ $data->deductable_iva_real }}</strong> IVA deducible</p>
  </div>
</div>