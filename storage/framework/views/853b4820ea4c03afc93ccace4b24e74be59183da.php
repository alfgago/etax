<div class="sidebar-dashboard">
    <div class="card-title"><?php echo e($titulo); ?></div>
    
<?php if( allowTo('reports') || in_array(8, auth()->user()->permisos())): ?>     
    
    <div class="row">
        
        <div class="col-md-12 dato-iva dato-compras compras">
            <label><span>Total de ventas</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->invoices_subtotal, 0 )); ?></div>
        </div>
        
        <div class="col-md-12 dato-iva dato-ventas ventas">
            <label><span>Total de compras</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->bills_subtotal, 0 )); ?></div>
        </div>
        
        <?php if( $data->balance_operativo > 0): ?>
          <div class="col-md-12 dato-iva saldo dato-saldo">
            <label><span>IVA por pagar</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->balance_operativo, 0 )); ?></div>
          </div>
        <?php endif; ?>
        
        <?php if( $data->balance_operativo < 0): ?>
          <div class="col-md-12 dato-iva cobrar dato-cobrar">
            <label><span>IVA por cobrar</span></label>
            <div class="dato">₡<?php echo e(number_format( abs($data->balance_operativo), 0 )); ?></div>
          </div>
        <?php endif; ?>
        
        <div class="col-md-12 dato-iva emitido">
            <label><span>IVA de facturas emitidas</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->total_invoice_iva, 0 )); ?></div>
        </div>
        <div class="col-md-12 dato-iva recibido">
            <label><span>IVA de facturas recibidas</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->total_bill_iva, 0 )); ?></div>
        </div>
        <div class="col-md-12 dato-iva acreditable">
            <label><span>IVA acreditable</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->iva_deducible_operativo, 0 )); ?></div>
        </div>
        
        <div class="col-md-12 dato-iva asumido">
            <label><span>IVA por ajustar</span></label>
            <div class="dato">₡<?php echo e(number_format( ( $data->iva_no_deducible - $data->iva_no_acreditable_identificacion_plena), 0 )); ?></div>
        </div>
        
        <?php if( $data->iva_no_acreditable_identificacion_plena > 0): ?>
        <div class="col-md-12 dato-iva gastado">
            <label><span style="">Gasto por IVA no acreditable</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->iva_no_acreditable_identificacion_plena, 0 )); ?></div>
        </div>
        <?php endif; ?>
        
       
        <?php if( $data->iva_retenido > 0): ?>
        <div class="col-md-12 dato-iva retenido">
            <label><span style="">IVA retenido por tarjetas</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->iva_retenido, 0 )); ?></div>
        </div>
        <?php endif; ?>  
    
        <?php if( $data->iva_devuelto > 0 ): ?>
        <div class="col-md-12 dato-iva anterior">
            <label><span style="">IVA devuelto</span></label>
            <div class="dato">₡<?php echo e(number_format( $data->iva_devuelto, 0 )); ?></div>
        </div>
        <?php endif; ?>  
        
        
        <?php if( $data->month != 0): ?>
            <?php if( $data->saldo_favor_anterior > 0 ): ?>
            <div class="col-md-12 dato-iva anterior">
                <label><span style="">IVA a favor periodo anterior</span></label>
                <div class="dato">₡<?php echo e(number_format( $data->saldo_favor_anterior, 0 )); ?></div>
            </div>
            <?php endif; ?>  
        <?php endif; ?>
    </div>
    
 <?php else: ?>
  <div class="not-allowed-message">
    Usted actualmente no tiene permisos para ver los reportes.
  </div>
<?php endif; ?>     
    
 </div>  
 <?php /**PATH /var/www/resources/views/Reports/widgets/resumen-periodo.blade.php ENDPATH**/ ?>