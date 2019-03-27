<div class="card mb-4">
  <div class="card-body text-left">
      <div class="card-title">{{ $titulo }} - Compras e importaciones</div>
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
                <th>Compras locales de bienes y servicios:</th>
                <td>  ₡{{ number_format( $data->cc_compras, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Importación de bienes y servicios:</th>
                <td>  ₡{{ number_format( $data->cc_importaciones, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de propiedad, planta y equipo:</th>
                <td>  ₡{{ number_format( $data->cc_propiedades, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras sin derecho a crédito:</th>
                <td>  ₡{{ number_format( $data->cc_compras_sin_derecho, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por compras locales:</th>
                <td>  ₡{{ number_format( $data->cc_iva_compras, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por importación:</th>
                <td>  ₡{{ number_format( $data->cc_iva_importaciones, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de propiedad, planta y equipo por compras locales:</th>
                <td>  ₡{{ number_format( $data->cc_iva_propiedades, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Proveedores a crédito:</th>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_proveedores_credito, 0 ) }} </td>
              </tr>
              <tr>
                <th>Proveedores a contado:</th>
                <td>-</td>
                <td>  ₡{{ number_format( $data->cc_proveedores_contado, 0 ) }} </td>
              </tr>
              <tr class="total">
                <th>Total:</th>
                <td>  ₡{{ number_format( $data->cc_compras + $data->cc_importaciones + $data->cc_propiedades + $data->cc_compras_sin_derecho + $data->cc_iva_compras + $data->cc_iva_importaciones + $data->cc_iva_propiedades, 0 ) }} </td>
                <td>  ₡{{ number_format( $data->cc_proveedores_contado + $data->cc_proveedores_credito, 0 ) }} </td>
              </tr>
            </tbody>
            
          </table>
        </div>
        
        
      </div>
  </div>
</div>

