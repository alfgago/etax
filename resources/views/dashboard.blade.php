@extends('layouts/app') @section('title') Dashboard eTAX @endsection @section('content')

<style>
  
  .card.fullh {
    height: 100%;
  }

</style>
<div class="row">
  <div class="col-md-12">
    <div class="row">
      
      
      
      
      <div class="col-lg-2 col-md-12 mb-4">
            <div class="card fullh">
              <div class="card-body text-left">
                  <div class="card-title">Liquidación del mes</div>
                  <p class="text-small text-muted m-0">Saldo de IVA de febrero</p>
                  <p class="text-20 mb-3 text-muted"><i class="text-success i-Coins"></i> ₡{{ $f->balance_real }} </p>
                  <p class="text-12 text-muted m-0 p-2 border-bottom"><strong> + ₡{{ $f->total_invoice_iva }} </strong> IVA emitido</p>
                  <p class="text-12 text-muted m-0 p-2 border-bottom"><strong>- ₡{{ $f->deductable_iva_real }}</strong> IVA deducible</p>
              </div>
            </div>
      </div>
      
      <div class="col-lg-2 col-md-12 mb-4">
            <div class="card o-hidden fullh ">
                <div class="card-body text-left">
                    <div class="card-title">Movimiento del mes actual</div>
                    <div class="content" style="max-width: 100%;">
                        <p class="text-muted mt-2 mb-0">Ventas</p>
                        <p class="text-primary text-20 mb-2">₡{{ $f->invoices_subtotal }}</p>
                    </div>
                    <div class="content" style="max-width: 100%;">
                        <p class="text-muted mt-2 mb-0">Facturas emitidas</p>
                        <p class="text-primary text-20 mb-2 border-bottom" >{{ $f->count_invoices }}</p>
                    </div>
                    <div class="content" style="max-width: 100%;">
                        <p class="text-muted mt-2 mb-0">Gastos e inversiones</p>
                        <p class="text-primary text-20 mb-2">₡{{ $f->bills_subtotal }}</p>
                    </div>
                    <div class="content" style="max-width: 100%;">
                        <p class="text-muted mt-2 mb-0">Facturas recibidas</p>
                        <p class="text-primary text-20 mb-2">{{ $f->count_bills }}</p>
                    </div>
                </div>
            </div>
      </div>
      
      <div class="col-lg-8 col-md-12 mb-4">
        <div class="card ">
          <div class="card-body">
            <div class="card-title">Resumen de IVA del periodo actual</div>
            <div id="echartBar" style="height: 300px;"></div>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
          <div class="card o-hidden ">

              <div class="card-body">
                  <div class="card-title">Ventas del periodo actual</div>
                  <span class="text-26 text-muted">₡{{ $acumulado->invoices_subtotal }}</span>
                  <p class="text-small text-muted m-0"></p>
                  <div id="chart-invoices" style="height: 65px;"></div>
                  <div class="d-flex justify-content-between mt-4">
                      <div class="flex-grow-1" style="flex:;">
                          &nbsp;
                      </div>
                      <div class="flex-grow-1" style="flex:;">
                          <span class="text-small">IVA emitido</span>
                          <h5 class="m-0 font-weight-bold text-muted">₡{{ $acumulado->total_invoice_iva }}</h5>
                      </div>
                      <div class="flex-grow-1" style="flex:;">
                          <span class="text-small">Total de facturas emitidas</span>
                          <h5 class="m-0 font-weight-bold text-muted">{{ $acumulado->count_invoices }}</h5>
                      </div>
                  </div>
              </div>

          </div>
      </div>
      <div class="col-md-4 mb-4">

          <div class="card o-hidden ">

              <div class="card-body">
                  <div class="card-title">Compras del periodo actual</div>
                  <span class="text-26 text-muted">₡{{ $acumulado->bills_subtotal }}</span>
                  <p class="text-small text-muted m-0"></p>
                  <div id="chart-bills" style="height: 65px;"></div>
                  <div class="d-flex justify-content-between mt-4">
                      <div class="flex-grow-1" style="flex:;">
                          &nbsp;
                      </div>
                      <div class="flex-grow-1" style="flex:;">
                          <span class="text-small">IVA Deducible</span>
                          <h5 class="m-0 font-weight-bold text-muted">₡{{ $acumulado->deductable_iva_real }}</h5>
                      </div>
                      <div class="flex-grow-1" style="flex:;" >
                          <span class="text-small">Total de facturas recibidas</span>
                          <h5 class="m-0 font-weight-bold text-muted">{{ $acumulado->count_bills }}</h5>
                      </div>
                  </div>
              </div>

          </div>

      </div>

      <div class="col-md-4 mb-4">
          <div class="card o-hidden fullh">

              <div class="card-body">
                  <div class="card-title">Comparativo de liquidación</div>
                  <div class="d-flex justify-content-between mb-2">
                      <div class="flex-grow-1" style="flex:1;">
                          <p class="text-small text-muted m-0">Saldo de IVA del periodo actual</p>
                          <p class="text-22 mb-3 text-muted"><i class="text-success i-Coins"></i> ₡{{ $acumulado->balance_real }} </p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong> + ₡{{ $acumulado->total_invoice_iva }} </strong> IVA emitido</p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong>- ₡{{ $acumulado->deductable_iva_real }}</strong> IVA deducible</p>
                      </div>
                      <div class="flex-grow-1" style="flex:1;">
                          <p class="text-small text-muted m-0">Saldo de IVA del periodo anterior</p>
                          <p class="text-22 mb-3 text-muted"><i class="text-danger i-Coins" ></i> ₡{{ $anterior->balance_real }}</p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong> + ₡{{ $anterior->total_invoice_iva }} </strong> IVA emitido</p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong>- ₡{{ $anterior->deductable_iva_real }}</strong> IVA deducible</p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
        
      
      <div class="col-md-4 mb-4">
        <div class="card ">
          <div class="card-body">
            <div class="card-title">Proporción de porcentajes de IVA</div>
            <div id="echartPie" style="height: 300px;"></div>
          </div>
        </div>
      </div>
      
      <div class="col-md-8 mb-4">
        <div class="row">
      <div class="col-md-3 mb-4">
        <div class="card card-icon ">
          <div class="card-body text-center">
            <i class="i-Bar-Chart"></i>
            <p class="text-muted mt-2 mb-2">Prorrata Acumulada</p>
            <p class="text-primary text-22 line-height-1 m-0"> {{ $acumulado->prorrata*100 }}% </p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card card-icon ">
          <div class="card-body text-center">
            <i class="i-Bar-Chart"></i>
            <p class="text-muted mt-2 mb-2">Prorrata Febrero</p>
            <p class="text-primary text-22 line-height-1 m-0"> {{ $f->prorrata*100 }}% </p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card card-icon ">
          <div class="card-body text-center">
            <i class="i-Bar-Chart"></i>
            <p class="text-muted mt-2 mb-2">Prorrata Enero</p>
            <p class="text-primary text-22 line-height-1 m-0"> {{ $e->prorrata*100 }}% </p>
          </div>
        </div>
      </div>
          
          <div class="col-md-3 mb-4">
        <div class="card card-icon ">
          <div class="card-body text-center">
            <i class="i-Bar-Chart"></i>
            <p class="text-muted mt-2 mb-2">Prorrata Periodo anterior</p>
            <p class="text-primary text-22 line-height-1 m-0"> {{ $anterior->prorrata*100 }}% </p>
          </div>
        </div>
      </div>
          
        </div>
      </div>

      <div class="col-lg-12 col-md-12">
          <div class="card o-hidden mb-4">
              <div class="card-body">
                  <div class="card-title">Detalle de débito fiscal</div>

                  <div class="table-responsive">

                      <table id="" class="table dataTable-collapse text-center ivas-table">
                          <thead>
                              <tr class="first-header">
                                  <th colspan="1">Concepto</th>
                                  <th colspan="2">Acumulado</th>
                                  <th colspan="2">Enero</th>
                                  <th colspan="2">Febrero</th>
                                  <th colspan="2">Marzo</th>
                                  <th colspan="2">Abrril</th>
                                  <th colspan="2">Mayo</th>
                                  <th colspan="2">Junio</th>
                                  <th colspan="2">Julio</th>
                                  <th colspan="2">Agosto</th>
                                  <th colspan="2">Setiembre</th>
                                  <th colspan="2">Octubre</th>
                                  <th colspan="2">Noviembre</th>
                                  <th colspan="2">Diciembre</th>
                              </tr>
                              <tr class="second-header">
                                  <th class="concepto">Tipo de IVA</th>
                                  <th class="acum">Base</th>
                                  <th class="acum">IVA</th>
                                  <th class="ene">Base</th>
                                  <th class="ene">IVA</th>
                                  <th class="feb">Base</th>
                                  <th class="feb">IVA</th>
                                  <th class="mar">Base</th>
                                  <th class="mar">IVA</th>
                                  <th class="abr">Base</th>
                                  <th class="abr">IVA</th>
                                  <th class="may">Base</th>
                                  <th class="may">IVA</th>
                                  <th class="jun">Base</th>
                                  <th class="jun">IVA</th>
                                  <th class="jul">Base</th>
                                  <th class="jul">IVA</th>
                                  <th class="ago">Base</th>
                                  <th class="ago">IVA</th>
                                  <th class="set">Base</th>
                                  <th class="set">IVA</th>
                                  <th class="oct">Base</th>
                                  <th class="oct">IVA</th>
                                  <th class="nov">Base</th>
                                  <th class="nov">IVA</th>
                                  <th class="dic">Base</th>
                                  <th class="dic">IVA</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
                                <?php 
                                    $bVar = "b".$tipo['codigo'];
                                    $iVar = "i".$tipo['codigo'];
                                ?>
                                <tr class="r-{{ $tipo['codigo'] }}">
                                    <th>{{ $tipo['nombre'] }}</th>
                                    <td>{{ $acumulado->$bVar ? $acumulado->$bVar : '-' }}</td>
                                    <td>{{ $acumulado->$iVar ? $acumulado->$iVar : '-' }}</td>
                                    <td>{{ $e->$bVar ? $e->$bVar : '-' }}</td>
                                    <td>{{ $e->$iVar ? $e->$iVar : '-' }}</td>
                                    <td>{{ $f->$bVar ? $f->$bVar : '-' }}</td>
                                    <td>{{ $f->$iVar ? $f->$iVar : '-' }}</td>
                                    <td>{{ $m->$bVar ? $m->$bVar : '-' }}</td>
                                    <td>{{ $m->$iVar ? $m->$iVar : '-' }}</td>
                                    <td>{{ $a->$bVar ? $a->$bVar : '-' }}</td>
                                    <td>{{ $a->$iVar ? $a->$iVar : '-' }}</td>
                                    <td>{{ $y->$bVar ? $y->$bVar : '-' }}</td>
                                    <td>{{ $y->$iVar ? $y->$iVar : '-' }}</td>
                                    <td>{{ $j->$bVar ? $j->$bVar : '-' }}</td>
                                    <td>{{ $j->$iVar ? $j->$iVar : '-' }}</td>
                                    <td>{{ $l->$bVar ? $l->$bVar : '-' }}</td>
                                    <td>{{ $l->$iVar ? $l->$iVar : '-' }}</td>
                                    <td>{{ $g->$bVar ? $g->$bVar : '-' }}</td>
                                    <td>{{ $g->$iVar ? $g->$iVar : '-' }}</td>
                                    <td>{{ $s->$bVar ? $s->$bVar : '-' }}</td>
                                    <td>{{ $s->$iVar ? $s->$iVar : '-' }}</td>
                                    <td>{{ $c->$bVar ? $c->$bVar : '-' }}</td>
                                    <td>{{ $c->$iVar ? $c->$iVar : '-' }}</td>
                                    <td>{{ $n->$bVar ? $n->$bVar : '-' }}</td>
                                    <td>{{ $n->$iVar ? $n->$iVar : '-' }}</td>
                                    <td>{{ $d->$bVar ? $d->$bVar : '-' }}</td>
                                    <td>{{ $d->$iVar ? $d->$iVar : '-' }}</td>
                                </tr>
                              @endforeach

                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    
      <div class="col-lg-12 col-md-12">
          <div class="card o-hidden mb-4">
              <div class="card-body">
                  <div class="card-title">Detalle de crédito fiscal</div>

                  <div class="table-responsive">

                      <table id="" class="table dataTable-collapse text-center ivas-table">
                          <thead>
                              <tr class="first-header">
                                  <th colspan="1">Concepto</th>
                                  <th colspan="2">Acumulado</th>
                                  <th colspan="2">Enero</th>
                                  <th colspan="2">Febrero</th>
                                  <th colspan="2">Marzo</th>
                                  <th colspan="2">Abrril</th>
                                  <th colspan="2">Mayo</th>
                                  <th colspan="2">Junio</th>
                                  <th colspan="2">Julio</th>
                                  <th colspan="2">Agosto</th>
                                  <th colspan="2">Setiembre</th>
                                  <th colspan="2">Octubre</th>
                                  <th colspan="2">Noviembre</th>
                                  <th colspan="2">Diciembre</th>
                              </tr>
                              <tr class="second-header">
                                  <th class="concepto">Tipo de IVA</th>
                                  <th class="acum">Base</th>
                                  <th class="acum">IVA</th>
                                  <th class="ene">Base</th>
                                  <th class="ene">IVA</th>
                                  <th class="feb">Base</th>
                                  <th class="feb">IVA</th>
                                  <th class="mar">Base</th>
                                  <th class="mar">IVA</th>
                                  <th class="abr">Base</th>
                                  <th class="abr">IVA</th>
                                  <th class="may">Base</th>
                                  <th class="may">IVA</th>
                                  <th class="jun">Base</th>
                                  <th class="jun">IVA</th>
                                  <th class="jul">Base</th>
                                  <th class="jul">IVA</th>
                                  <th class="ago">Base</th>
                                  <th class="ago">IVA</th>
                                  <th class="set">Base</th>
                                  <th class="set">IVA</th>
                                  <th class="oct">Base</th>
                                  <th class="oct">IVA</th>
                                  <th class="nov">Base</th>
                                  <th class="nov">IVA</th>
                                  <th class="dic">Base</th>
                                  <th class="dic">IVA</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ( \App\Variables::tiposIVASoportados() as $tipo )
                                <?php 
                                    $bVar = "b".$tipo['codigo'];
                                    $iVar = "i".$tipo['codigo'];
                                ?>
                                <tr class="r-{{ $tipo['codigo'] }}">
                                    <th>{{ $tipo['nombre'] }}</th>
                                    <td>{{ $acumulado->$bVar ? $acumulado->$bVar : '-' }}</td>
                                    <td>{{ $acumulado->$iVar ? $acumulado->$iVar : '-' }}</td>
                                    <td>{{ $e->$bVar ? $e->$bVar : '-' }}</td>
                                    <td>{{ $e->$iVar ? $e->$iVar : '-' }}</td>
                                    <td>{{ $f->$bVar ? $f->$bVar : '-' }}</td>
                                    <td>{{ $f->$iVar ? $f->$iVar : '-' }}</td>
                                    <td>{{ $m->$bVar ? $m->$bVar : '-' }}</td>
                                    <td>{{ $m->$iVar ? $m->$iVar : '-' }}</td>
                                    <td>{{ $a->$bVar ? $a->$bVar : '-' }}</td>
                                    <td>{{ $a->$iVar ? $a->$iVar : '-' }}</td>
                                    <td>{{ $y->$bVar ? $y->$bVar : '-' }}</td>
                                    <td>{{ $y->$iVar ? $y->$iVar : '-' }}</td>
                                    <td>{{ $j->$bVar ? $j->$bVar : '-' }}</td>
                                    <td>{{ $j->$iVar ? $j->$iVar : '-' }}</td>
                                    <td>{{ $l->$bVar ? $l->$bVar : '-' }}</td>
                                    <td>{{ $l->$iVar ? $l->$iVar : '-' }}</td>
                                    <td>{{ $g->$bVar ? $g->$bVar : '-' }}</td>
                                    <td>{{ $g->$iVar ? $g->$iVar : '-' }}</td>
                                    <td>{{ $s->$bVar ? $s->$bVar : '-' }}</td>
                                    <td>{{ $s->$iVar ? $s->$iVar : '-' }}</td>
                                    <td>{{ $c->$bVar ? $c->$bVar : '-' }}</td>
                                    <td>{{ $c->$iVar ? $c->$iVar : '-' }}</td>
                                    <td>{{ $n->$bVar ? $n->$bVar : '-' }}</td>
                                    <td>{{ $n->$iVar ? $n->$iVar : '-' }}</td>
                                    <td>{{ $d->$bVar ? $d->$bVar : '-' }}</td>
                                    <td>{{ $d->$iVar ? $d->$iVar : '-' }}</td>
                                </tr>
                              @endforeach

                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

@endsection @section('footer-scripts')
<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>

<script>
  'use strict';

  var _extends = Object.assign || function(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  
  function initBarChart(){
    var $tituloRepercutido = "IVA emitido";
    var $tituloSoportado = "IVA recibido";
    var $tituloDeducible = "IVA deducible";
    var $tituloSaldo = "Saldo de IVA";
    
    var echartElemBar = document.getElementById('echartBar');
    if (echartElemBar) {
      var echartBar = echarts.init(echartElemBar);
      echartBar.setOption({
        legend: {
          borderRadius: 0,
          orient: 'horizontal',
          x: 'left',
          data: [$tituloRepercutido, $tituloSoportado, $tituloSaldo]
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

  $(document).ready(function() {
    // Chart in Dashboard version 1
    
    initBarChart();
    
    // Chart in Dashboard version 1
      var echartElemPie = document.getElementById('echartPie');
      if (echartElemPie) {
        var echartPie = echarts.init(echartElemPie);
        echartPie.setOption({
          color: ['#274FAB', '#F1BD45', '#77A1ED', '#E065A3', '#6A6AD8'],
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
            @if($acumulado->ratio1)
              {
                value: '{{ $acumulado->ratio1*100 }}',
                name: 'Ventas al 1%'
              }, 
            @endif
            @if($acumulado->ratio2)
              {
                value: '{{ $acumulado->ratio2*100 }}',
                name: 'Ventas al 2%'
              }, 
            @endif
            @if($acumulado->ratio3)
              {
                value: '{{ $acumulado->ratio3*100 }}',
                name: 'Ventas al 13%'
              }, 
            @endif
            @if($acumulado->ratio4)
              {
                value: '{{ $acumulado->ratio4*100 }}',
                name: 'Ventas al 4%'
              }, 
            @endif
            @if($acumulado->ratio_ex)
              {
                value: '{{ $acumulado->ratio_ex*100 }}',
                name: 'No deducible'
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

    // Chart in Dashboard version 1
    var chartBills = document.getElementById('chart-bills');
    if (chartBills) {
      var echartBills = echarts.init(chartBills);
      echartBills.setOption(_extends({}, echartOptions.defaultOptions, {
          grid: echartOptions.gridAlignLeft,
          series: [_extends({
                data: [30, 40, 20, 50, 40, 80, 90]
            }, echartOptions.smoothLine, {
                markArea: {
                    label: {
                        show: true
                    }
                },
                areaStyle: {
                    color: 'rgba(102, 51, 153, .2)',
                    origin: 'start'
                },
                lineStyle: {
                    color: '#663399'
                },
                itemStyle: {
                    color: '#663399'
                }
            })]
      }));
      $(window).on('resize', function () {
          setTimeout(function () {
              echartBills.resize();
          }, 500);
      });
    }
    
    // Chart in Dashboard version 1
    var chartInvoices = document.getElementById('chart-invoices');
    if (chartInvoices) {
      var echartInvoices = echarts.init(chartInvoices);
      echartInvoices.setOption(_extends({}, echartOptions.defaultOptions, {
          grid: echartOptions.gridAlignLeft,
          series: [_extends({
                data: [30, 40, 20, 50, 40, 80, 90]
            }, echartOptions.smoothLine, {
                markArea: {
                    label: {
                        show: true
                    }
                },
                areaStyle: {
                    color: 'rgba(102, 51, 153, .2)',
                    origin: 'start'
                },
                lineStyle: {
                    color: '#663399'
                },
                itemStyle: {
                    color: '#663399'
                }
            })]
      }));
      $(window).on('resize', function () {
          setTimeout(function () {
              echartInvoices.resize();
          }, 500);
      });
    }
    
  });
</script>

<style>
  small {
    font-size: .8rem !important;
  }
</style>
@endsection