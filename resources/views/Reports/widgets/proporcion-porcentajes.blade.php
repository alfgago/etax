
<div class="widget text-center ">
    <div class="card-title"> {{ $titulo }} </div>
    <div id="echartPie" style="height: 250px;"></div>
</div>

<script>

  $(document).ready(function() {
    
    // Chart in Dashboard version 1
      var echartElemPie = document.getElementById('echartPie');
      if (echartElemPie) {
        var echartPie = echarts.init(echartElemPie);
        echartPie.setOption({
          color: ['#274FAB', '#F1BD45', '#77A1ED', '#E065A3', '#F47E67', '#6A6AD8'],
          tooltip: {
            show: true,
            backgroundColor: 'rgba(0, 0, 0, .8)'
          },

          xAxis: [{

            axisLine: {
              show: false
            },
            splitLine: {
              show: false
            }
          }],
          yAxis: [{

            axisLine: {
              show: false
            },
            splitLine: {
              show: false
            }
          }],

          series: [{
            name: '',
            type: 'pie',
            radius: ['50%', '70%'],
            data: [
            @if($acumulado->fake_ratio1)
              {
                value: '{{ number_format( $acumulado->fake_ratio1*100, 2) }}',
                name: 'Ventas al 1%'
              }, 
            @endif
            @if($acumulado->fake_ratio2)
              {
                value: '{{ number_format( $acumulado->fake_ratio2*100, 2) }}',
                name: 'Ventas al 2%'
              }, 
            @endif
            @if($acumulado->fake_ratio3)
              {
                value: '{{ number_format( $acumulado->fake_ratio3*100, 2) }}',
                name: 'Ventas al 13%'
              }, 
            @endif
            @if($acumulado->fake_ratio4)
              {
                value: '{{ number_format( $acumulado->fake_ratio4*100, 2) }}',
                name: 'Ventas al 4%'
              }, 
            @endif
            @if($acumulado->fake_ratio_exento_sin_credito)
              {
                value: '{{ number_format( $acumulado->fake_ratio_exento_sin_credito*100, 2) }}',
                name: 'Exentas sin \n derecho a credito'
              }, 
            @endif
            @if($acumulado->fake_ratio_exento_con_credito)
              {
                value: '{{ number_format( $acumulado->fake_ratio_exento_con_credito*100, 2) }}',
                name: 'Exentas con \n derecho a credito'
              }, 
            @endif
            ],
            itemStyle: {
              emphasis: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
              }
            }
          }]
        });
        $(window).on('resize', function() {
          setTimeout(function() {
            echartPie.resize();
          }, 500);
        });
    }
    
  });

</script>