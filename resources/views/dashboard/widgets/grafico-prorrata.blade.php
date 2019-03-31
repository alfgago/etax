
<div class="widget ">
    <div class="card-title"> {{ $titulo }} </div>
    <div id="echartGauge" style="height: 250px;"></div>
    <div class="row comparacion-prorratas">
      <div class="col-lg-12 dif">
        <div><label>Diferencia en IVA por pagar:</label> <span>₡{{ number_format( abs($acumulado->balance_real - $acumulado->balance), 0) }}</span></div>
      </div>
      <div class="col-lg-6 ope">
        <div><label>Prorrata operativa:</label> <span>{{ number_format( $acumulado->last_prorrata*100, 2) }}%</span></div>
      </div>
      <div class="col-lg-6 est">
        <div><label>Prorrata estimada:</label> <span>{{ number_format( $acumulado->prorrata*100, 2) }}%</span></div>
      </div>
    </div>
    
</div>

<?php

  $signo = ( $acumulado->prorrata - $acumulado->last_prorrata ) < 0 ? ' - ' : ' + ';

?>

<script>

  $(document).ready(function() {
    
    // Chart in Dashboard version 1
      var echartElemGauge = document.getElementById('echartGauge');
      if (echartElemGauge) {
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
              center : ['50%', '80%'],
              radius : 180,
              pointer: {
                color : 'black',
                length: '70%'
              },
              axisLine: {            
                show: true,        
                lineStyle: {       
                    color: [[100/6*1/100, '#E75D2F'], [100/6*2/100, '#F47E67'], [100/6*3/100, '#EFA89C'], [100/6*4/100, '#B2CCDC'], [100/6*5/100, '#6D7ABA'], [100/6*6/100, '#2B52A3']], 
                    width: '30'
                }
              },
              axisTick: { 
                show: false
              },
              axisLabel: {           
                show: true,
                formatter: function(v){
                    switch (v+''){
                        case '0': return '-40%';
                        case '50': return '0%';
                        case '100': return '+40%';
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
                formatter:'{{ number_format( abs ($acumulado->last_prorrata - $acumulado->prorrata )*100 , 2 ) }}%',
                textStyle: {  
                    color: '#000',
                    fontSize : 18,
                    fontWeight : 600
                }
              },
              splitLine: {           
                show: false
              },
              data:[{value: {{ $acumulado->prorrata*100 - $acumulado->last_prorrata*100 + 50 }} }]
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