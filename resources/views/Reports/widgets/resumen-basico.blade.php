<div class="sidebar-dashboard">
    <div class="card-title">
      {{ $titulo }} 
      <span class="helper helper-resumen-mensual" def="helper-resumen-mensual">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
    </div>
    
    
    <div class="row">
        
      @if( $data->balance_operativo != 0)
        
        @if( $data->balance_operativo > 0)
          <div class="col-md-12 dato-iva saldo dato-saldo">
            <label>
              <span>IVA por pagar
                <span class="helper helper-iva-por-pagar" def="helper-iva-por-pagar">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span>
              </span>
            </label>
            <div class="dato">₡{{ number_format( $data->balance_operativo, 0 ) }}</div>
          </div>
        @endif
        
        @if( $data->balance_operativo < 0)
          <div class="col-md-12 dato-iva cobrar dato-cobrar">
            <label>
              <span>IVA por cobrar
                <span class="helper helper-iva-por-cobrar" def="helper-iva-por-cobrar">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span>
              </span>
            </label>
            <div class="dato">₡{{ number_format( abs($data->balance_operativo), 0 ) }}</div>
          </div>
        @endif
    
      @else
      
        <div class="col-md-12 dato-iva saldo dato-saldo">
          <label>
            <span>IVA por pagar
              <span class="helper helper-iva-por-pagar" def="helper-iva-por-pagar">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span>
            </span>
          </label>
          <div class="dato">₡{{ number_format( $data->balance_operativo, 0 ) }}</div>
        </div>
      
      @endif
        
      <div class="col-md-12 dato-iva dato-compras compras">
          <label><span>Total de ventas</span></label>
          <div class="dato">₡{{ number_format( $data->invoices_subtotal, 0 ) }}</div>
      </div>
      
      <div class="col-md-12 dato-iva dato-ventas ventas">
          <label><span>Total de compras</span></label>
          <div class="dato">₡{{ number_format( $data->bills_subtotal, 0 ) }}</div>
      </div>
      
      <div class="col-md-12 dato-iva emitido">
            <label>
              <span>IVA de facturas emitidas
                <span class="helper helper-iva-emitidas" def="helper-iva-emitidas">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span>
              </span>
            </label>
            <div class="dato">₡{{ number_format( $data->total_invoice_iva, 0 ) }}</div>
        </div>
        
        <div class="col-md-12 dato-iva recibido">
            <label>
              <span>IVA de facturas recibidas
                <span class="helper helper-iva-recibidas" def="helper-iva-recibidas">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span>
              </span>
            </label>
            <div class="dato">₡{{ number_format( $data->total_bill_iva, 0 ) }}</div>
        </div>
        
    </div>
    
 </div>  
