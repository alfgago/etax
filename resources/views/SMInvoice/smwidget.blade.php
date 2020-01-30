
<div class="car mb-4">
  <div class="car-body text-left">
      <h3 class="card-title">Estado de facturación Seguros del Magisterio</h3>
      <div class="row">
        
        <div class="col-sm-12">
          <p class="text-20 text-muted m-0"></p>
          <table class="text-12 text-muted m-0 p-2 ivas-table bigtext smwidget">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
                <th>Aceptadas</th>
                <th>Pendientes</th>
                <th>Rechazadas</th>
              </tr>
            </thead>
            <tbody>
                
              <tr>
                <th>Facturas Excel:</td>
                <td> ₡{{ number_format( $facturasExcel->subtotal, 0 ) }} </td>
                <td> ₡{{ number_format( $facturasExcel->iva, 0 ) }} </td>
                <td> ₡{{ number_format( $facturasExcel->total, 0 ) }} </td>
                <td> {{ $facturasExcel->aceptadas }}</td>
                <td> {{ $facturasExcel->pendientes }}</td>
                <td> {{ $facturasExcel->rechazadas }}</td>
              </tr>
                
              <tr>
                <th>Facturas eTax:</td>
                <td> ₡{{ number_format( $facturasEtax->subtotal, 0 ) }} </td>
                <td> ₡{{ number_format( $facturasEtax->iva, 0 ) }} </td>
                <td> ₡{{ number_format( $facturasEtax->total, 0 ) }} </td>
                <td> {{ $facturasEtax->aceptadas }}</td>
                <td> {{ $facturasEtax->pendientes }}</td>
                <td> {{ $facturasEtax->rechazadas }}</td>
              </tr>
                
              <tr>
                <th>Notas de crédito Excel:</td>
                <td> ₡{{ number_format( -$notasExcel->subtotal, 0 ) }} </td>
                <td> ₡{{ number_format( -$notasExcel->iva, 0 ) }} </td>
                <td> ₡{{ number_format( -$notasExcel->total, 0 ) }} </td>
                <td> {{ $notasExcel->aceptadas }}</td>
                <td> {{ $notasExcel->pendientes }}</td>
                <td> {{ $notasExcel->rechazadas }}</td>
              </tr>
                
              <tr>
                <th>Notas de crédito etax:</td>
                <td> ₡{{ number_format( -$notasEtax->subtotal, 0 ) }} </td>
                <td> ₡{{ number_format( -$notasEtax->iva, 0 ) }} </td>
                <td> ₡{{ number_format( -$notasEtax->total, 0 ) }} </td>
                <td> {{ $notasEtax->aceptadas }}</td>
                <td> {{ $notasEtax->pendientes }}</td>
                <td> {{ $notasEtax->rechazadas }}</td>
              </tr>
              
              <tr>
                <th>Totales:</td>
                <td> ₡{{ number_format( $facturasExcel->subtotal + $facturasEtax->subtotal - $notasExcel->subtotal - $notasEtax->subtotal, 0 ) }} </td>
                <td> ₡{{ number_format( $facturasExcel->iva + $facturasEtax->iva - $notasExcel->iva - $notasEtax->iva, 0 ) }} </td>
                <td> ₡{{ number_format( $facturasExcel->total + $facturasEtax->total - $notasExcel->total - $notasEtax->total, 0 ) }} </td>
                <td> {{ $facturasExcel->aceptadas + $facturasEtax->aceptadas + $notasExcel->aceptadas + $notasEtax->aceptadas }} </td>
                <td> {{ $facturasExcel->pendientes + $facturasEtax->pendientes + $notasExcel->pendientes + $notasEtax->pendientes }} </td>
                <td> {{ $facturasExcel->rechazadas + $facturasEtax->rechazadas + $notasExcel->rechazadas + $notasEtax->rechazadas }} </td>
              </tr>
              
            </tbody>
          </table>
        </div>
        
      </div>
  </div>
</div>

