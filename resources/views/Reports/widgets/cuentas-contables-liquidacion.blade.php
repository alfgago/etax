<div class="car mb-4">
  <div class="car-body text-left">
      <h3 class="card-title">{{ $titulo }} - Liquidación anual de IVA</h3>
      <div class="row">
        <?php 
          $ajustar = $data->book->cc_ajuste_ppp + $data->book->cc_ajuste_bs;
          $diff = $data->prorrata_operativa - $data->prorrata;
          if( $data->iva_deducible_operativo >= $data->iva_deducible_estimado ){
            $hacienda = $data->iva_deducible_operativo - $data->iva_deducible_estimado;
            $gasto = abs($hacienda + $ajustar);
            $total = $gasto;
          }else{
            $hacienda = $data->iva_deducible_estimado - $data->iva_deducible_operativo;
            $gasto = abs($hacienda - $ajustar);
            $total = $ajustar;
          }
        ?>
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
                <th>Gasto por IVA:</td>
                <td>  ₡{{ number_format( $gasto, 0 ) }} </td>
                <td>-</td>
              </tr>
              
              @if( $data->iva_deducible_operativo >= $data->iva_deducible_estimado )
              <?php
              ?>
              <tr>
                <th>IVA por pagar a Hacienda</td>
                <td>-</td>
                <td>  ₡{{ number_format( $hacienda, 0 ) }} </td>
              </tr>
              @endif
              
              @if( $data->iva_deducible_operativo < $data->iva_deducible_estimado )
              <tr>
                <th>IVA por cobrar a Hacienda</td>
                <td>  ₡{{ number_format( $hacienda, 0 ) }} </td>
                <td>-</td>
              </tr>
              @endif
              
              <tr>
                <th>IVA por ajustar:</td>
                <td>-</td>
                <td>  ₡{{ number_format( $ajustar, 0 ) }} </td>
              </tr>
              
              <tr class="total">
                <th>Total:</th>
                <td>  ₡{{ number_format($total, 0  ) }} </td>
                <td>  ₡{{ number_format($total, 0  ) }} </td>
              </tr>
              
            </tbody>
          </table>
        </div>
        
      </div>
  </div>
</div>

