
<div class="widget">
    <div class="card-title"> {{ $titulo }} </div>
    <div class="row comparacion-prorratas m-0">
      <div class="col-lg-12 ope text-left">
        <div>
          <label>
            Prorrata operativa:
            <span class="helper helper-prorrata-operativa" def="helper-prorrata-operativa">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
          </label> <span>{{ number_format( $acumulado->prorrata_operativa*100, 2) }}%</span>
        </div>
      </div>
      <div class="col-lg-12 est text-left">
        <div>
          <label>
            Prorrata estimada:
            <span class="helper helper-prorrata-estimada" def="helper-prorrata-estimada">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
          </label> 
          <span>{{ number_format( $acumulado->prorrata*100, 2) }}%</span>
        </div>
      </div>
      <div class="col-lg-12 dif text-left" style=" border-top: 2px solid #ddd; padding-top: .5rem; border-bottom: 0; ">
        <div class="text-left">
          <label>
            Liquidación de IVA <br> estimada a fin de año:
            <span class="helper helper-liquidacion-estimada" def="helper-liquidacion-estimada">  <i class="fa fa-question-circle" aria-hidden="true"></i> </span> 
          </label> 
          <span>₡{{ number_format( abs($acumulado->balance_operativo - $acumulado->balance_estimado), 0) }}</span>
        </div>
      </div>
    </div>
    
</div>