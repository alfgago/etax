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
<?php

  $signo = ( $acumulado->prorrata - $acumulado->prorrata_operativa ) < 0 ? ' - ' : ' + ';
  $texto = ( $acumulado->prorrata - $acumulado->prorrata_operativa ) < 0 ? 'por pagar' : 'por cobrar';
  $porcentaje = abs($acumulado->balance_operativo) - abs($acumulado->balance_operativo - $acumulado->balance_estimado) ;
  
?>
<div class="widget">
    <div class="card-title"> {{ $titulo }} </div>
    
@if( allowTo('reports')  || in_array(8, auth()->user()->permisos())) 
    
    <div class="row comparacion-prorratas m-0">
      <div class="col-lg-12 dif text-left" style="">
        <div class="text-left">
          <label>
            Liquidación de IVA estimada <br> {{$texto}} a fin de año:
            <span class="helper helper-liquidacion-estimada" def="helper-liquidacion-estimada">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
          </label> 
          <span>₡{{ number_format( abs($acumulado->balance_operativo - $acumulado->balance_estimado), 0) }}</span>
        </div>
      </div>
      
      <div class="col-lg-12 ope">
        <table style="margin-left: 0 !important; margin-bottom: 0.5rem;">
          
          <tr>
            <td></td>
            <th>Operativos</th>
            <th>Real estimado</th>
          </tr>
          
         <tr>
            <th>Prorrata</td>
            <td> <span>{{ number_format( $operativeData->prorrata_operativa*100, 2) }}%</span> </td>
            <td> {{ number_format( $acumulado->prorrata*100, 2) }}% </td>
          </tr>
          
          <tr>
            <th>Ventas 1%</td>
            <td> <span>{{ number_format( $operativeData->operative_ratio1*100, 2) }}%</span> </td>
            <td> {{ number_format( $acumulado->ratio1*100, 2) }}% </td>
          </tr>
          
          <tr>
            <th>Ventas 2%</td>
            <td> <span>{{ number_format( $operativeData->operative_ratio2*100, 2) }}%</span> </td>
            <td> {{ number_format( $acumulado->ratio2*100, 2) }}% </td>
          </tr>
          
          <tr>
            <th>Ventas 13%</td>
            <td> <span>{{ number_format( $operativeData->operative_ratio3*100, 2) }}%</span> </td>
            <td> {{ number_format( $acumulado->ratio3*100, 2) }}% </td>
          </tr>
          
          <tr>
            <th>Ventas 4%</td>
            <td> <span>{{ number_format( $operativeData->operative_ratio4*100, 2) }}%</span> </td>
            <td> {{ number_format( $acumulado->ratio4*100, 2) }}% </td>
          </tr>
          
        </table>
      </div>
    </div>
    
@else
  <div class="not-allowed-message">
    Usted actualmente no tiene permisos para ver los reportes.
  </div>
@endif   
    
</div>