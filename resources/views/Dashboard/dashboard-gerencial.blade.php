<div class="col-md-8">
    
  <div class="row">
    
    <div class="col-lg-12 mb-4">
      @include('Reports.widgets.grafico-mensual', ['titulo' => "Resumen de IVA $ano"])
    </div>
    
    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.resumen-periodo', ['titulo' => "$nombreMes $ano", 'data' => $dataMes])
    </div>
    
    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.resumen-periodo', ['titulo' => "Acumulado $ano", 'data' => $acumulado])
    </div>

  </div>
  
</div>

<div class=" col-md-4 mb-4">
  <div class="row">
    
    <div class="col-lg-12 mb-4">
      @include('Reports.widgets.grafico-prorrata', ['titulo' => 'Prorrata operativa vs prorrata estimada', 'data' => $acumulado])
    </div>
    
    <div class="col-lg-12 mb-4">
      @include('Reports.widgets.proporcion-porcentajes', ['titulo' => "Porcentaje de ventas del $ano por tipo de IVA", 'data' => $acumulado])
    </div>
  </div> 
 
</div>