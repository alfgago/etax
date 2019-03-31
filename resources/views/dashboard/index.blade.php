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
  <div class="col-md-8">
    
    <div class="row">
      
      <div class="col-lg-12 mb-4">
        @include('dashboard.widgets.grafico-mensual', ['titulo' => 'Resumen de IVA 2019'])
      </div>
      
      <div class="col-lg-6 mb-4">
        @include('dashboard.widgets.proporcion-porcentajes', ['titulo' => 'Porcentaje de ventas del 2019 por tipo de IVA', 'data' => $acumulado])
      </div>
      
      <div class="col-lg-6 mb-4">
        
        @include('dashboard.widgets.grafico-prorrata', ['titulo' => 'Prorrata operativa vs prorrata estimada', 'data' => $acumulado])
        
      </div>
      
    </div>
    
  </div>
  
  <div class=" col-md-4 mb-4">
    <div class="row">
      
      <div class="col-lg-12 mb-4">
        @include('dashboard.widgets.resumen-periodo', ['titulo' => 'Acumulado 2019', 'data' => $acumulado])
      </div>
      
      <div class="col-lg-12 mb-4">
        @include('dashboard.widgets.resumen-periodo', ['titulo' => 'Marzo 2019', 'data' => $m])
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