
<div class="widget card-porcentajes">
    <div class="card-title"> {{ $titulo }} 
      <span class="helper helper-proporcion" def="helper-proporcion">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
    </div>
    @if( $data->count_invoices  )
      <div id="echartPie" style="height: 12.5rem; max-width: 25rem;"></div>
    @else
      <div class="descripcion">La empresa aún no registra ventas durante el año. Empiece ingresando sus facturas de venta o emitiéndolas por medio de eTax</div>
    @endif
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
          legend: {
              orient: 'vertical',
              right: 0,
              top: 0,
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
            radius: ['45%', '65%'],
            center : ['30%', '40%'],
            labelLine: {
                normal: {
                    show: false
                }
            },
            label: {
                normal: {
                    show: false,
                    position: 'center'
                },
            },
            data: [
            
              {
                value: '{{ number_format( $acumulado->fake_ratio1*100, 2) }}',
                name: 'Ventas al 1%'
              }, 
            
              {
                value: '{{ number_format( $acumulado->fake_ratio2*100, 2) }}',
                name: 'Ventas al 2%'
              }, 
            
              {
                value: '{{ number_format( $acumulado->fake_ratio3*100, 2) }}',
                name: 'Ventas al 13%'
              }, 
            
              {
                value: '{{ number_format( $acumulado->fake_ratio4*100, 2) }}',
                name: 'Ventas al 4%'
              }, 
            
              {
                value: '{{ number_format( $acumulado->fake_ratio_exento_sin_credito*100, 2) }}',
                name: 'Exentas sin \n derecho a credito'
              }, 
            
              {
                value: '{{ number_format( $acumulado->fake_ratio_exento_con_credito*100, 2) }}',
                name: 'Exentas con \n derecho a credito'
              }, 
            
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