<div class="card ">
          <div class="card-body">
            <div class="card-title">{{ $titulo }}</div>
            <div id="echartBar" style="height: 300px;"></div>
          </div>
        </div>

<script>

function initBarChart() {
  var $tituloRepercutido = "IVA emitido ";
  var $tituloSoportado = "IVA recibido ";
  var $tituloDeducible = "IVA deducible ";
  var $tituloSaldo = "Saldo de IVA ";

  var echartElemBar = document.getElementById('echartBar');
  if (echartElemBar) {
    var echartBar = echarts.init(echartElemBar);
    echartBar.setOption({
      legend: {
        borderRadius: 0,
        orient: 'horizontal',
        x: 'left',
        data: [$tituloRepercutido, $tituloSoportado, $tituloDeducible, $tituloSaldo]
      },
      grid: {
        left: '8px',
        right: '8px',
        bottom: '0',
        containLabel: true
      },
      tooltip: {
        show: true,
        backgroundColor: 'rgba(0, 0, 0, .8)'
      },
      xAxis: [{
        type: 'category',
        data: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'],
        axisTick: {
          alignWithLabel: true
        },
        splitLine: {
          show: false
        },
        axisLine: {
          show: true
        }
      }],
      yAxis: [{
        type: 'value',
        axisLabel: {
          formatter: '₡{value}'
        },
        axisLine: {
          show: false
        },
        splitLine: {
          show: true,
          interval: 'auto'
        }
      }],

      series: [{
        name: $tituloRepercutido,
        data: [{{ $e->total_invoice_iva }}, {{ $f->total_invoice_iva }}, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        barGap: 0,
        color: '#274FAB',
        smooth: true

      }, {
        name: $tituloSoportado,
        data: [{{ $e->total_bill_iva }}, {{ $f->total_bill_iva }}, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#77A1ED',
        smooth: true

      }, {
        name: $tituloDeducible,
        data: [{{ $e->deductable_iva_real }}, {{ $f->deductable_iva_real }}, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#814bb5',
        smooth: true
      }, {
        name: $tituloSaldo,
        data: [{{ $e->balance_real }}, {{ $f->balance_real }}, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#F1BD45',
        smooth: true
      }]
    });
    $(window).on('resize', function() {
      setTimeout(function() {
        echartBar.resize();
      }, 500);
    });
  }
}

  initBarChart();


</script>