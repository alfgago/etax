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
  $posNeg = ( $acumulado->prorrata - $acumulado->prorrata_operativa ) < 0 ? -1 : 1;
  $texto = ( $acumulado->prorrata - $acumulado->prorrata_operativa ) < 0 ? 'por pagar' : 'por cobrar';
  $signo = ( $acumulado->prorrata - $acumulado->prorrata_operativa ) < 0 ? '-' : '';
  
  $numerador = abs($acumulado->balance_operativo - $acumulado->balance_estimado) ;
  $denumerador = abs($acumulado->balance_operativo);
  $porcentaje = 0;
  if( abs($denumerador) ) {
    $porcentaje = abs($numerador) / abs($denumerador);
  }
  
?>

<div class="widget text-center ">
    <div class="card-title"> <?php echo e($titulo); ?> </div>
    
<?php if( allowTo('reports')  || in_array(8, auth()->user()->permisos())): ?>   

    <div id="echartGauge" style="height: 180px;"></div>
    <div class="row comparacion-prorratas">
      <div class="col-lg-12 dif">
        <div>
          <label>
            Liquidación de IVA estimada <br> <?php echo e($texto); ?> a fin de año:
            <span class="helper helper-resumen-mensual" def="helper-resumen-mensual">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
          </label> 
          <span>₡<?php echo e(number_format( abs($acumulado->balance_operativo - $acumulado->balance_estimado), 0 )); ?></span>
        </div>
      </div>
      <div class="col-lg-12 ope">
        <table>
          
          <tr>
            <td></td>
            <th>Operativos</th>
            <th>Real estimado</th>
          </tr>
          
          <tr>
            <th>Prorrata</td>
            <td> <span><?php echo e(number_format( $operativeData->prorrata_operativa*100, 2)); ?>%</span> </td>
            <td> <?php echo e(number_format( $acumulado->prorrata*100, 2)); ?>% </td>
          </tr>
          
          <tr>
            <th>Ventas 1%</td>
            <td> <span><?php echo e(number_format( $operativeData->operative_ratio1*100, 2)); ?>%</span> </td>
            <td> <?php echo e(number_format( $acumulado->ratio1*100, 2)); ?>% </td>
          </tr>
          
          <tr>
            <th>Ventas 2%</td>
            <td> <span><?php echo e(number_format( $operativeData->operative_ratio2*100, 2)); ?>%</span> </td>
            <td> <?php echo e(number_format( $acumulado->ratio2*100, 2)); ?>% </td>
          </tr>
          
          <tr>
            <th>Ventas 13%</td>
            <td> <span><?php echo e(number_format( $operativeData->operative_ratio3*100, 2)); ?>%</span> </td>
            <td> <?php echo e(number_format( $acumulado->ratio3*100, 2)); ?>% </td>
          </tr>
          
          <tr>
            <th>Ventas 4%</td>
            <td> <span><?php echo e(number_format( $operativeData->operative_ratio4*100, 2)); ?>%</span> </td>
            <td> <?php echo e(number_format( $acumulado->ratio4*100, 2)); ?>% </td>
          </tr>
          
        </table>
      </div>
    </div>
    
<?php else: ?>
  <div class="not-allowed-message">
    Usted actualmente no tiene permisos para ver los reportes.
  </div>
<?php endif; ?>   
    
</div>

<?php if( allowTo('reports') || in_array(8, auth()->user()->permisos()) ): ?>   

<script>

  $(document).ready(function() {
    
    // Chart in Dashboard version 1
      var echartElemGauge = document.getElementById('echartGauge');
      if (echartElemGauge) {
        
        var diff = <?php echo e($porcentaje*100); ?>;
        var rango = 40;
        if( diff > 40 ){
          rango = 100;
        }
        var res = <?php echo e($porcentaje*100); ?> * <?php echo e($posNeg); ?>;
        var ratio = 50 / rango;
        var res = ( parseFloat(res) * ratio ) + 50;
        
        var echartGauge = echarts.init(echartElemGauge);
        echartGauge.setOption({
          toolbox: {
            show : false
          },
          series : [
            {
              type:'gauge',
              startAngle: 180,
              endAngle: 0,
              center : ['50%', '70%'],
              radius : 120,
              pointer: {
                color : 'black',
                length: '65%'
              },
              axisLine: {            
                show: true,        
                lineStyle: {       
                    color: [[100/6*1/100, '#E75D2F'], [100/6*2/100, '#F47E67'], [100/6*3/100, '#EFA89C'], [100/6*4/100, '#B2CCDC'], [100/6*5/100, '#6D7ABA'], [100/6*6/100, '#2B52A3']], 
                    width: '25'
                }
              },
              axisTick: { 
                show: false
              },
              axisLabel: {           
                show: true,
                formatter: function(v){
                    switch (v+''){
                        case '0': return '-'+rango+'%';
                        case '50': return '0%';
                        case '100': return '+'+rango+'%';
                        default: return '';
                    }
                },
                textStyle: {  
                  color: '#000',
                  fontSize : 14,
                  fontWeight : 400
                }
              },
              detail : {
                show : true,
                backgroundColor: 'rgba(0,0,0,0)',
                borderWidth: 0,
                borderColor: '#ccc',
                width: 100,
                height: 40,
                offsetCenter: [0, 30],
                formatter:'<?php echo e($signo . number_format( $porcentaje*100, 2 )); ?>%',
                textStyle: {  
                    color: '#000',
                    fontSize : 18,
                    fontWeight : 600
                }
              },
              splitLine: {           
                show: false
              },
              data:[{value: res }]
            }
          ]
        });
        $(window).on('resize', function() {
          setTimeout(function() {
            echartGauge.resize();
          }, 500);
        });
    }
    
  });

</script>

<?php endif; ?>  <?php /**PATH /var/www/resources/views/Reports/widgets/grafico-prorrata.blade.php ENDPATH**/ ?>