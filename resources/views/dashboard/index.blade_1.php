@extends('layouts/app') 

@section('title') Dashboard @endsection 

@section('header-scripts')

<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>

@endsection

@section('content')

<style>
  
  .card.fullh {
    height: 100%;
  }

</style>
<div class="row">
  <div class="col-md-12">
    <div class="row">
      
      <div class="col-lg-4 col-md-12 mb-4">  
        <div class="row">
          <div class=" col-md-12 mb-4">
            @include('dashboard.widgets.liquidacion-periodo', ['titulo' => 'Liquidación del mes', 'mes' => 'Marzo', 'data' => $m])
          </div>
          
          <div class=" col-md-12 mb-4">
            @include('dashboard.widgets.movimiento-periodo', ['titulo' => 'Movimiento del mes actual', 'data' => $m])
          </div>
        </div>
      </div>
      
      <div class="col-lg-8 col-md-12 mb-4">
        @include('dashboard.widgets.grafico-mensual', ['titulo' => 'Resumen de IVA del periodo actual'])
      </div>

      <div class="col-md-4 mb-4">
        @include('dashboard.widgets.ventas-periodo', ['titulo' => 'Ventas del periodo actual', 'data' => $acumulado])
      </div>
      <div class="col-md-4 mb-4">
        @include('dashboard.widgets.compras-periodo', ['titulo' => 'Compras del periodo actual', 'data' => $acumulado])
      </div>

      <div class="col-md-4 mb-4">
          <div class="card o-hidden fullh">

              <div class="card-body">
                  <div class="card-title">Comparativo de liquidación</div>
                  <div class="d-flex justify-content-between mb-2">
                      <div class="flex-grow-1" style="flex:1;">
                          <p class="text-small text-muted m-0">Saldo de IVA del periodo actual</p>
                          <p class="text-22 mb-3 text-muted"><i class="text-success i-Coins"></i> ₡{{ number_format( $acumulado->balance_real, 2) }} </p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong> + ₡{{ number_format( $acumulado->total_invoice_iva, 2) }} </strong> IVA emitido</p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong>- ₡{{ number_format( $acumulado->deductable_iva_real, 2) }}</strong> IVA acreditable</p>
                      </div>
                      <div class="flex-grow-1" style="flex:1;">
                          <p class="text-small text-muted m-0">Saldo de IVA del periodo anterior</p>
                          <p class="text-22 mb-3 text-muted"><i class="text-danger i-Coins" ></i> ₡{{ number_format( $anterior->balance_real, 2) }}</p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong> + ₡{{ number_format( $anterior->total_invoice_iva, 2) }} </strong> IVA emitido</p>
                          <p class="text-12 text-muted m-0 p-2 border-bottom"><strong>- ₡{{ number_format( $anterior->deductable_iva_real, 2) }}</strong> IVA acreditable</p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
        
      
      <div class="col-md-4 mb-4">
        @include('dashboard.widgets.proporcion-porcentajes', ['titulo' => 'Proporción de porcentajes de IVA', 'data' => $acumulado])
      </div>
      
      <div class="col-md-8 mb-4">
        <div class="row">
          <div class="col-md-3 mb-4">
            <div class="card card-icon ">
              <div class="card-body text-center">
                <i class="i-Bar-Chart"></i>
                <p class="text-muted mt-2 mb-2">Prorrata de periodo actual</p>
                <p class="text-primary text-22 line-height-1 m-0"> {{ number_format( $acumulado->prorrata*100, 2) }}% </p>
              </div>
            </div>
          </div>
              
          <div class="col-md-3 mb-4">
            <div class="card card-icon ">
              <div class="card-body text-center">
                <i class="i-Bar-Chart"></i>
                <p class="text-muted mt-2 mb-2">Prorrata del periodo anterior</p>
                <p class="text-primary text-22 line-height-1 m-0"> {{ number_format( $anterior->prorrata*100, 2 ) }}% </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

@endsection @section('footer-scripts')

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
  
  

  $(document).ready(function() {
    
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