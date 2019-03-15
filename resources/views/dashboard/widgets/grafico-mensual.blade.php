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
  
  var dataRepercutidos = [];
  var dataSoportados = [];
  var dataDeducibles = [];
  var dataSaldos = [];
  
  

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
        data: [ '{{ $e->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $f->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $m->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $a->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $y->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $j->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $l->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $g->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $s->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $c->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $n->total_invoice_iva }}'.replace(/\,/g,''), 
                '{{ $d->total_invoice_iva }}'.replace(/\,/g,''), 
              ],
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
        data: [ '{{ $e->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $f->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $m->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $a->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $y->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $j->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $l->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $g->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $s->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $c->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $n->total_bill_iva }}'.replace(/\,/g,''), 
                '{{ $d->total_bill_iva }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#77A1ED',
        smooth: true

      }, {
        name: $tituloDeducible,
        data: [ '{{ $e->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $f->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $m->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $a->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $y->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $j->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $l->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $g->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $s->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $c->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $n->deductable_iva_real }}'.replace(/\,/g,''), 
                '{{ $d->deductable_iva_real }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#814bb5',
        smooth: true
      }, {
        name: $tituloSaldo,
        data: [ '{{ $e->balance_real }}'.replace(/\,/g,''), 
                '{{ $f->balance_real }}'.replace(/\,/g,''), 
                '{{ $m->balance_real }}'.replace(/\,/g,''), 
                '{{ $a->balance_real }}'.replace(/\,/g,''), 
                '{{ $y->balance_real }}'.replace(/\,/g,''), 
                '{{ $j->balance_real }}'.replace(/\,/g,''), 
                '{{ $l->balance_real }}'.replace(/\,/g,''), 
                '{{ $g->balance_real }}'.replace(/\,/g,''), 
                '{{ $s->balance_real }}'.replace(/\,/g,''), 
                '{{ $c->balance_real }}'.replace(/\,/g,''), 
                '{{ $n->balance_real }}'.replace(/\,/g,''), 
                '{{ $d->balance_real }}'.replace(/\,/g,''), 
        ],
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