@extends('layouts/app')

@section('title') 
    App Ivalex
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    
    <h3>
      Acumulado año anterior
    </h3>
    
    <div class="row">
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Billing"></i>
                  <p class="text-muted mt-2 mb-2">Facturas emitidas</p>
                  <p class="text-primary text-24 line-height-1 m-0">{{ round( $calculosAcumulados->count_emitidas, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Financial"></i>
                  <p class="text-muted mt-2 mb-2">Total de ventas</p>
                  <p class="text-primary text-24 line-height-1 m-0">₡{{ round( $calculosAcumulados->subtotal_emitido, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Billing"></i>
                  <p class="text-muted mt-2 mb-2">Facturas recibidas</p>
                  <p class="text-primary text-24 line-height-1 m-0">{{ round( $calculosAcumulados->count_recibidas, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Coins"></i>
                  <p class="text-muted mt-2 mb-2">Total de gastos</p>
                  <p class="text-primary text-24 line-height-1 m-0">₡{{ round( $calculosAcumulados->subtotal_recibido, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Bar-Chart"></i>
                  <p class="text-muted mt-2 mb-2">Prorrata</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->prorrata*100, 2) }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-Bag"></i>
                  <p class="text-muted mt-2 mb-2">IVA deducible</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->iva_deducible, 2) }} </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-Bag"></i>
                  <p class="text-muted mt-2 mb-2">IVA no deducible</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->iva_no_deducible, 2) }} </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-2"></i>
                  <p class="text-muted mt-2 mb-2">Liquidación del mes</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->liquidacion, 2) }} </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 1%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->ratio1, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 2%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->ratio2, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 13%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->ratio3, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 4%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAcumulados->ratio4, 2)*100 }}% </p>
              </div>
          </div>
      </div>

    </div> 
  </div>  
  
    <div class="col-md-12">
    
    <h3>
      Enero
    </h3>
    
    <div class="row">
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Billing"></i>
                  <p class="text-muted mt-2 mb-2">Facturas emitidas</p>
                  <p class="text-primary text-24 line-height-1 m-0">{{ round( $calculosAnterior->count_emitidas, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Financial"></i>
                  <p class="text-muted mt-2 mb-2">Total de ventas</p>
                  <p class="text-primary text-24 line-height-1 m-0">₡{{ round( $calculosAnterior->subtotal_emitido, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Billing"></i>
                  <p class="text-muted mt-2 mb-2">Facturas recibidas</p>
                  <p class="text-primary text-24 line-height-1 m-0">{{ round( $calculosAnterior->count_recibidas, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Coins"></i>
                  <p class="text-muted mt-2 mb-2">Total de gastos</p>
                  <p class="text-primary text-24 line-height-1 m-0">₡{{ round( $calculosAnterior->subtotal_recibido, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Bar-Chart"></i>
                  <p class="text-muted mt-2 mb-2">Prorrata</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAnterior->prorrata*100, 2) }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-Bag"></i>
                  <p class="text-muted mt-2 mb-2">IVA deducible</p>
                  <p class="text-primary text-24 line-height-1 m-0">  {{ round( $calculosAnterior->iva_deducible_anterior, 2) }}
                    <small style="color:{{ $calculosAnterior->prorrata < $calculosAcumulados->prorrata ? 'green' : 'red' }};">( {{ round( $calculosAnterior->iva_deducible, 2) }}  )</small>
                  </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-Bag"></i>
                  <p class="text-muted mt-2 mb-2">IVA no deducible</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAnterior->iva_no_deducible, 2) }} </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-2"></i>
                  <p class="text-muted mt-2 mb-2">Liquidación del mes</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAnterior->liquidacion_anterior, 2) - 40 }}
                    <small style="color:{{ $calculosAnterior->prorrata < $calculosAcumulados->prorrata ? 'green' : 'red' }};">( {{ round( $calculosAnterior->liquidacion, 2) - 40 }} )</small>
                  </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 1%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAnterior->ratio1, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 2%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAnterior->ratio2, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 13%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAnterior->ratio3, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 4%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculosAnterior->ratio4, 2)*100 }}% </p>
              </div>
          </div>
      </div>

    </div> 
  </div>  
  
    <div class="col-md-12">
    
    <h3>
      Febrero
    </h3>
    
    <div class="row">
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Billing"></i>
                  <p class="text-muted mt-2 mb-2">Facturas emitidas</p>
                  <p class="text-primary text-24 line-height-1 m-0">{{ round( $calculos->count_emitidas, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Financial"></i>
                  <p class="text-muted mt-2 mb-2">Total de ventas</p>
                  <p class="text-primary text-24 line-height-1 m-0">₡{{ round( $calculos->subtotal_emitido, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Billing"></i>
                  <p class="text-muted mt-2 mb-2">Facturas recibidas</p>
                  <p class="text-primary text-24 line-height-1 m-0">{{ round( $calculos->count_recibidas, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Coins"></i>
                  <p class="text-muted mt-2 mb-2">Total de gastos</p>
                  <p class="text-primary text-24 line-height-1 m-0">₡{{ round( $calculos->subtotal_recibido, 2) }}</p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Bar-Chart"></i>
                  <p class="text-muted mt-2 mb-2">Prorrata</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculos->prorrata*100, 2) }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-Bag"></i>
                  <p class="text-muted mt-2 mb-2">IVA deducible</p>
                  <p class="text-primary text-24 line-height-1 m-0">  {{ round( $calculos->iva_deducible_anterior, 2) }}
                    <small style="color:{{ $calculos->prorrata < $calculosAcumulados->prorrata ? 'green' : 'red' }};" >( {{ round( $calculos->iva_deducible, 2) }} )</small>
                  </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-Bag"></i>
                  <p class="text-muted mt-2 mb-2">IVA no deducible</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculos->iva_no_deducible, 2) }} </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Money-2"></i>
                  <p class="text-muted mt-2 mb-2">Liquidación del mes</p>
                  <p class="text-primary text-24 line-height-1 m-0">{{ round( $calculos->liquidacion_anterior, 2) }}
                    <small style="color:{{ $calculos->prorrata < $calculosAcumulados->prorrata ? 'green' : 'red' }};">(  {{ round( $calculos->liquidacion, 2) }}   )</small>
                  </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 1%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculos->ratio1, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 2%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculos->ratio2, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 13%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculos->ratio3, 2)*100 }}% </p>
              </div>
          </div>
      </div>
      
      <div class="col-md-3">
          <div class="card card-icon mb-4">
              <div class="card-body text-center">
                  <i class="i-Pie-Chart-2"></i>
                  <p class="text-muted mt-2 mb-2">Ratio de ventas al 4%</p>
                  <p class="text-primary text-24 line-height-1 m-0"> {{ round( $calculos->ratio4, 2)*100 }}% </p>
              </div>
          </div>
      </div>

    </div> 
  </div>  
  
</div>

@endsection




<style>
.dash {
    display: flex;
    flex-wrap: wrap;
    max-width: 900px;
}

.dash > div {
    margin-bottom: 30px;
    font-size: 35px;
    color: green;
    font-weight: 300;
    background: #fff;
    width: 250px;
    padding: 15px;
    margin-right: 30px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    border-radius: 5px;
}

.dash h3 {
    font-size: 17px;
    color: #999;
    font-weight: bold;
}

 small {
  font-size: .8rem !important;
}
  
</style>


