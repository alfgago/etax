<div class="widget ">
  <div class="card-title">{{ $titulo }}</div>
  <div id="echartBar" style="height: 300px;"></div>
</div>

<script>

function initBarChart() {
  var $tituloRepercutido = " IVA fact. emitidas ";
  var $tituloSoportado = " IVA fact. recibidas ";
  var $tituloDeducible = " IVA acreditable ";
  var $tituloAsumido = " IVA por ajustar ";
  var $tituloSaldo = " IVA por pagar ";
  
  var dataRepercutidos = [];
  var dataSoportados = [];
  var dataDeducibles = [];
  var dataAsumidos = [];
  var dataSaldos = [];
  
  

  var echartElemBar = document.getElementById('echartBar');
  if (echartElemBar) {
    var echartBar = echarts.init(echartElemBar);
    echartBar.setOption({
      legend: {
        borderRadius: 0,
        orient: 'horizontal',
        x: 'left',
        data: [ $tituloRepercutido, $tituloSoportado, $tituloDeducible, $tituloAsumido, $tituloSaldo ]
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
        data: [ '{{ number_format( $e->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->total_invoice_iva, 0 ) }}'.replace(/\,/g,''), 
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
        data: [ '{{ number_format( $e->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->total_bill_iva, 0 ) }}'.replace(/\,/g,''), 
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
        data: [ '{{ number_format( $e->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->deductable_iva_real, 0 ) }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#814bb5',
        smooth: true
      }, {
        name: $tituloAsumido,
        data: [ '{{ number_format( $e->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->non_deductable_iva, 0 ) }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#342686',
        smooth: true
      }, {
        name: $tituloSaldo,
        data: [ '{{ number_format( $e->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->balance_real, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->balance_real, 0 ) }}'.replace(/\,/g,''), 
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