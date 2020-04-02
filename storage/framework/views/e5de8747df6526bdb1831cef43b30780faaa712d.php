<div class="car mb-4">
  <div class="car-body text-left">
      <h3 class="card-title"><?php echo e($titulo); ?> - Ajuste de periodificación</h3>
      <div class="row">
        
        <div class="col-sm-12">
          <p class="text-20 text-muted m-0"></p>
          <table class="text-12 text-muted m-0 p-2 ivas-table bigtext">
            <thead>
              <tr>
                <th>Descripción</th>
                <th>Debe</th>
                <th>Haber</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th>IVA por facturas emitidas al 1%:</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_iva_emitido_1, 0 )); ?> </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA por facturas emitidas al 2%:</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_iva_emitido_2, 0 )); ?> </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA por facturas emitidas al 13%:</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_iva_emitido_3, 0 )); ?> </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA por facturas emitidas al 4%:</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_iva_emitido_4, 0 )); ?> </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Gasto por IVA no acreditable:</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_gasto_no_acreditable, 0 )); ?> </td>
                <td>-</td>
              </tr>
              
              <tr>
                <th>IVA Devuelto:</td>
                <td> -</td>
                <td> ₡<?php echo e(number_format( $data->book->calculos->iva_devuelto, 0 )); ?></td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 1% de bienes y servicios</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_bs_1, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 2% de bienes y servicios</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_bs_2, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 13% de bienes y servicios</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_bs_3, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 4% de bienes y servicios</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_bs_4, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 1% de propiedad, planta y equipo</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_ppp_1, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 2% de propiedad, planta y equipo</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_ppp_2, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 13% de propiedad, planta y equipo</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_ppp_3, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por fact. recibidas al 4% de propiedad, planta y equipo</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_ppp_4, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA de facturas recibidas con recaudación a nivel Mayorista, Fábrica o Aduana:</th>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_iva_restaurantes, 0 )); ?> </td>
              </tr>
              
              <tr>
                <th>IVA por ajustar de bienes y servicios:</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_ajuste_bs, 0 )); ?></td>
                <td>-</td>
              </tr>
              
              <tr>
                <th>IVA por ajustar de propiedad, planta y equipo:</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_ajuste_ppp, 0 )); ?> </td>
                <td>-</td>
              </tr>
              
              <tr>
                <th>IVA a favor de periodos anteriores:</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->saldo_favor_anterior, 0 )); ?> </td>
              </tr>
              
              <?php if( $data->book->cc_por_pagar > 0): ?>
              <tr>
                <th>IVA por pagar a Hacienda</td>
                <td>-</td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_por_pagar, 0 )); ?> </td>
              </tr>
              <?php endif; ?>
              
              <?php if( $data->book->cc_por_pagar < 0): ?>
              <tr>
                <th>IVA por cobrar a Hacienda</td>
                <td>  ₡<?php echo e(number_format( abs( $data->book->cc_por_pagar ) , 0 )); ?> </td>
                <td>-</td>
              </tr>
              <?php endif; ?>
              
              <tr class="total">
                <th>Total:</th>
                <td>  ₡<?php echo e(number_format( $data->book->cc_sum1, 0 )); ?> </td>
                <td>  ₡<?php echo e(number_format( $data->book->cc_sum2, 0 )); ?> </td>
              </tr>
            </tbody>
          </table>
        </div>
        
      </div>
  </div>
</div>

<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Reports/widgets/cuentas-contables-ajustes.blade.php ENDPATH**/ ?>