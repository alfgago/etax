<div class="car mb-4" >
  <div class="car-body text-left">
      <h3 class="card-title">{{ $titulo }} - Compras e importaciones</h3>
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
                <th>Compras locales de bienes y servicios al 1%:</th>
                <td>  ₡{{ number_format( $data->book->cc_compras1, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de bienes y servicios al 2%:</th>
                <td>  ₡{{ number_format( $data->book->cc_compras2, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de bienes y servicios al 13%:</th>
                <td>  ₡{{ number_format( $data->book->cc_compras3, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de bienes y servicios al 4%:</th>
                <td>  ₡{{ number_format( $data->book->cc_compras4, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Importación de bienes y servicios al 1%:</th>
                <td>  ₡{{ number_format( $data->book->cc_importaciones1, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Importación de bienes y servicios al 2%:</th>
                <td>  ₡{{ number_format( $data->book->cc_importaciones2, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Importación de bienes y servicios al 13%:</th>
                <td>  ₡{{ number_format( $data->book->cc_importaciones3, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Importación de bienes y servicios al 4%:</th>
                <td>  ₡{{ number_format( $data->book->cc_importaciones4, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de propiedad, planta y equipo al 1%:</th>
                <td>  ₡{{ number_format( $data->book->cc_propiedades1, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de propiedad, planta y equipo al 2%:</th>
                <td>  ₡{{ number_format( $data->book->cc_propiedades2, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de propiedad, planta y equipo al 13%:</th>
                <td>  ₡{{ number_format( $data->book->cc_propiedades3, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras locales de propiedad, planta y equipo al 4%:</th>
                <td>  ₡{{ number_format( $data->book->cc_propiedades4, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras exentas:</th>
                <td>  ₡{{ number_format( $data->book->cc_compras_exentas, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Compras sin derecho a crédito:</th>
                <td>  ₡{{ number_format( $data->book->cc_compras_sin_derecho, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por compras locales al 1%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_compras1, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por compras locales al 2%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_compras2, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por compras locales al 13%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_compras3, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por compras locales al 4%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_compras4, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por importación al 1%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_importaciones1, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por importación al 2%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_importaciones2, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por importación al 13%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_importaciones3, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de bienes y servicios por importación al 4%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_importaciones4, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de propiedad, planta y equipo por compras locales al 1%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_propiedades1, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de propiedad, planta y equipo por compras locales al 2%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_propiedades2, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de propiedad, planta y equipo por compras locales al 13%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_propiedades3, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>IVA de facturas recibidas de propiedad, planta y equipo por compras locales al 4%:</th>
                <td>  ₡{{ number_format( $data->book->cc_iva_propiedades4, 0 ) }} </td>
                <td>-</td>
              </tr>
              <tr>
                <th>Proveedores a crédito:</th>
                <td>-</td>
                <td>  ₡{{ number_format( $data->book->cc_proveedores_credito, 0 ) }} </td>
              </tr>
              <tr>
                <th>Proveedores a contado:</th>
                <td>-</td>
                <td>  ₡{{ number_format( $data->book->cc_proveedores_contado, 0 ) }} </td>
              </tr>
              <tr class="total">
                <th>Total:</th>
                <?php
                  $debe = $data->book->cc_compras_sum;
                  
                  $haber = $data->book->cc_proveedores_contado + $data->book->cc_proveedores_credito;
                ?>
                <td>  ₡{{ number_format( $debe, 0 ) }} </td>
                <td>  ₡{{ number_format( $haber, 0 ) }} </td>
              </tr>
            </tbody>
            
          </table>
        </div>
        
        
      </div>
  </div>
</div>

