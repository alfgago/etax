<div class="card fullh">
  <div class="card-body text-left">
      <div class="card-title">{{ $titulo }}</div>
      <div class="row">
        <div class="col-md-6">
          <p class="text-small text-muted m-0">Saldo de IVA de {{ $mes }}</p>
          <p class="text-16 mb-3 text-muted"><i class="text-success i-Coins"></i> ₡{{ number_format( $data->balance_real, 2 ) }} </p>
          
        </div>
        <div class="col-md-6">
          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong> + ₡{{ number_format( $data->total_invoice_iva, 2 ) }} </strong> IVA emitido</p>
          <p class="text-12 text-muted m-0 p-2 "><strong>- ₡{{ number_format( $data->deductable_iva_real, 2 ) }}</strong> IVA acreditable</p>
          
        </div>
      </div>
        
  
  </div>
</div>