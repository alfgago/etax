<style>
  
  .ope th {
    min-width: 100px;
}

.ope tr {
    border-bottom: 1px solid #ccc;
}

.ope tr:last-of-type {
    border: 0;
}

.ope table {
    margin: auto;
}
  
</style>

<div class="widget">
    <div class="card-title"> {{ $titulo }} </div>
    <div class="row comparacion-prorratas m-0">
      <div class="col-lg-12 ope">
        <table style="margin-left: 0 !important; margin-bottom: 0.5rem;">
          
          <tr>
            <td></td>
            <th>Operativos</th>
            <th>Real estimado</th>
          </tr>
          
          <tr>
            <th>Prorrata</td>
            <th> <span>{{ number_format( $acumulado->company->operative_prorrata, 2) }}%</span> </th>
            <th> {{ number_format( $acumulado->prorrata*100, 2) }}% </th>
          </tr>
          
          <tr>
            <th>Ventas 1%</td>
            <th> <span>{{ number_format( $acumulado->company->operative_ratio1, 2) }}%</span> </th>
            <th> {{ number_format( $acumulado->ratio1*100, 2) }}% </th>
          </tr>
          
          <tr>
            <th>Ventas 2%</td>
            <th> <span>{{ number_format( $acumulado->company->operative_ratio2, 2) }}%</span> </th>
            <th> {{ number_format( $acumulado->ratio2*100, 2) }}% </th>
          </tr>
          
          <tr>
            <th>Ventas 13%</td>
            <th> <span>{{ number_format( $acumulado->company->operative_ratio3, 2) }}%</span> </th>
            <th> {{ number_format( $acumulado->ratio3*100, 2) }}% </th>
          </tr>
          
          <tr>
            <th>Ventas 4%</td>
            <th> <span>{{ number_format( $acumulado->company->operative_ratio4, 2) }}%</span> </th>
            <th> {{ number_format( $acumulado->ratio4*100, 2) }}% </th>
          </tr>
          
        </table>
      </div>
      <div class="col-lg-12 dif text-left" style=" border-top: 2px solid #ddd; padding-top: .5rem; border-bottom: 0; ">
        <div class="text-left">
          <label>
            Liquidación de IVA <br> estimada a fin de año:
            <span class="helper helper-liquidacion-estimada" def="helper-liquidacion-estimada">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
          </label> 
          <span>₡{{ number_format( abs($acumulado->balance_operativo - $acumulado->balance_estimado), 0) }}</span>
        </div>
      </div>
    </div>
    
</div>