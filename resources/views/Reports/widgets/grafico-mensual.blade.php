<div class="widget ">
  <div class="card-title">{{ $titulo }}</div>
@if( allowTo('reports')  || 1 == 1)     
  <div id="echartBar" style="height: 300px;"></div>
  
@else
  <div class="not-allowed-message">
    Usted actualmente no tiene permisos para ver los reportes.
  </div>
@endif    
  
</div>

@if( allowTo('reports')  || 1 == 1)  
<script>

var echartBar;
var tituloRepercutido = " IVA fact. emitidas ";
var tituloSoportado = " IVA fact. recibidas ";
var tituloDeducible = " IVA acreditable ";
var tituloAsumido = " IVA por ajustar ";
var tituloSaldo = " IVA por pagar ";
var tituloPorCobrar = " IVA por cobrar ";
  
function initBarChart() {
  
  var dataRepercutidos = [];
  var dataSoportados = [];
  var dataDeducibles = [];
  var dataAsumidos = [];
  var dataSaldos = [];
  
  var echartElemBar = document.getElementById('echartBar');
  if (echartElemBar) {
    echartBar = echarts.init(echartElemBar);
    echartBar.setOption({
      legend: {
        borderRadius: 0,
        orient: 'horizontal',
        x: 'left',
        data: [ tituloRepercutido, tituloSoportado, tituloDeducible, tituloAsumido, tituloSaldo, tituloPorCobrar ]
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
        name: tituloRepercutido,
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
        name: tituloSoportado,
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
        name: tituloDeducible,
        data: [ '{{ number_format( $e->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->iva_deducible_operativo, 0 ) }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#814bb5',
        smooth: true
      }, {
        name: tituloAsumido,
        data: [ '{{ number_format( $e->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->iva_no_deducible, 0 ) }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#342686',
        smooth: true
      }, {
        name: tituloSaldo,
        data: [ '{{ number_format( $e->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->iva_por_pagar, 0 ) }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#F1BD45',
        smooth: true
      }, {
        name: tituloPorCobrar,
        data: [ '{{ number_format( $e->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $f->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $m->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $a->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $y->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $j->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $l->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $g->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $s->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $c->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $n->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
                '{{ number_format( $d->iva_por_cobrar, 0 ) }}'.replace(/\,/g,''), 
        ],
        label: {
          show: false,
          color: 'red'
        },
        type: 'bar',
        color: '#CD4488',
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

@endif  