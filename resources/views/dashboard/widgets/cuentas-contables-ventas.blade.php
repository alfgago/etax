<div class="card mb-4">
  <div class="card-body text-left">
      <div class="card-title">{{ $titulo }} - Ventas y exportaciones</div>
      <div class="row">
        
        <div class="col-sm-12">
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
                <th>Clientes a crédito - ventas locales con derecho a crédito:</th>
                <td>  ₡{{ number_format( $data->cc_clientes_credito, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Clientes de contado - ventas locales con derecho a crédito:</td>
                <td>  ₡{{ number_format( $data->cc_clientes_contado, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Clientes a crédito  - ventas por exportación con derecho a crédito:</td>
                <td>  ₡{{ number_format( $data->cc_clientes_credito_exp, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Clientes de contado - ventas por exportación con derecho a crédito:</td>
                <td>  ₡{{ number_format( $data->cc_clientes_contado_exp, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Ventas locales de bienes y servicios con derecho a crédito 1%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_1, 0 ) }} </td>
              </tr>
              <tr>
                <th>Ventas locales de bienes y servicios con derecho a crédito 2%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_2, 0 ) }} </td>
              </tr>
              <tr>
                <th>Ventas locales de bienes y servicios con derecho a crédito 13%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_13, 0 ) }} </td>
              </tr>
              <tr>
                <th>Ventas locales de bienes y servicios con derecho a crédito 13%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_4, 0 ) }} </td>
              </tr>
              <tr>
                <th>IVA de facturas emitidas de Enero por ventas locales de bienes y servicios con derecho a crédito 1%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_1_iva, 0 ) }} </td>
              </tr>
              <tr>
                <th>IVA de facturas emitidas de Enero por ventas locales de bienes y servicios con derecho a crédito 2%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_2_iva, 0 ) }} </td>
              </tr>
              <tr>
                <th>IVA de facturas emitidas de Enero por ventas locales de bienes y servicios con derecho a crédito 13%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_13_iva, 0 ) }} </td>
              </tr>
              <tr>
                <th>IVA de facturas emitidas de Enero por ventas locales de bienes y servicios con derecho a crédito 4%</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_4_iva, 0 ) }} </td>
              </tr>
              <tr>
                <th>Ventas por exportación con derecho a crédito</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_exp, 0 ) }} </td>
              </tr>
              <tr>
                <th>Ventas al estado con derecho a crédito</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_estado, 0 ) }} </td>
              </tr>
              <tr>
                <th>Ventas sin derecho a crédito</td>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_ventas_sin_derecho, 0 ) }} </td>
              </tr>
              <tr class="total">
                <th>Total:</th>
                <td>  ₡{{ number_format( $data->cc_clientes_sum, 0 ) }} </td>
                <td>  ₡{{ number_format( $data->cc_ventas_sum, 0 ) }} </td>
              </tr>
            </tbody>
          </table>
        </div>
        
      </div>
  </div>
</div>

