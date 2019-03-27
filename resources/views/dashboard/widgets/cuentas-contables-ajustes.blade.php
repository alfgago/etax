<div class="card mb-4">
  <div class="card-body text-left">
      <div class="card-title">{{ $titulo }} - Ajuste de periodificación</div>
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
                <th>IVA en facturas emitidas al 1%:</td>
                <td>  ₡{{ number_format( $data->cc_iva_emitido_1, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA en facturas emitidas al 2%:</td>
                <td>  ₡{{ number_format( $data->cc_iva_emitido_2, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA en facturas emitidas al 13%:</td>
                <td>  ₡{{ number_format( $data->cc_iva_emitido_3, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA en facturas emitidas al 4%:</td>
                <td>  ₡{{ number_format( $data->cc_iva_emitido_4, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA asumido no acreditable por facturas recibidas:</td>
                <td>  ₡{{ number_format( $data->cc_no_acreditable, 0 ) }} </td>
                <td>-</td>
              </tr>
              
              <tr>
                <th>IVA acreditable al 1%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_deducible_1, 0 ) }} </td>
              </tr>
              
              <tr>
                <th>IVA acreditable al 2%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_deducible_2, 0 ) }} </td>
              </tr>
              
              <tr>
                <th>IVA acreditable al 13%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_deducible_3, 0 ) }} </td>
              </tr>
              
              <tr>
                <th>IVA acreditable al 4%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_deducible_4, 0 ) }} </td>
              </tr>
              
              <tr>
                <th>IVA por pagar a Hacienda</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ajuste_hacienda, 0 ) }} </td>
              </tr>
              
              <tr class="total">
                <th>Total:</th>
                <td>  ₡{{ number_format( $data->cc_sum1, 0 ) }} </td>
                <td>  ₡{{ number_format( $data->cc_sum2, 0 ) }} </td>
              </tr>
            </tbody>
          </table>
        </div>
        
      </div>
  </div>
</div>

