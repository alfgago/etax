<div class="sidebar-dashboard">
    <div class="card-title">{{ $titulo }}</div>
@if( allowTo('reports')  || in_array(8, auth()->user()->permisos()))      
    
    <div class="row">
      <?php 
        $company = currentCompanyModel();
        $countAvailableBills    = $company->getCountAvailableBills();
        $countPurchasedInvoices = $company->getCountPurchasedInvoices();
        $availableInvoices      = $company->getAvailableInvoices( $data->year, $data->month );
      ?>
      <div class="col-md-12 dato-facturas">
          <label><span>Facturas de venta procesadas</span></label>
          @if( $availableInvoices )
            <div class="barra-limites emitidas" >
              <div class="fill-bar" data-total="{{ $availableInvoices }}" data-fill="{{ $data->count_invoices }}"></div>
              <div class="barra-text">{{ number_format( $availableInvoices->current_month_sent ) }} de {{ number_format($availableInvoices->monthly_quota)  }}</div>
            </div>
          @else
            <div class="barra-limites emitidas" >
              <div class="fill-bar" data-total="99999999" data-fill="{{ number_format( $data->count_invoices ) }}"></div>
              <div class="barra-text">{{ number_format( $data->count_invoices ) }} de &infin;</div>
            </div>
          @endif

      </div>
      
      <div class="col-md-12 dato-facturas mt-2">
          <label><span>Facturas de compra procesadas</span></label>
          @if( $countAvailableBills != -1 )
            <div class="barra-limites recibidas">
              <div class="fill-bar" data-total="{{ $countAvailableBills }}" data-fill="{{ $data->count_bills }}"></div>
              <div class="barra-text">{{ number_format( $data->count_bills ) }} de {{ number_format($countAvailableBills) }}</div>
            </div>
          @else
            <div class="barra-limites recibidas">
              <div class="fill-bar" data-total="99999999" data-fill="{{ $data->count_bills }}"></div>
              <div class="barra-text">{{ number_format( $data->count_bills ) }} de &infin;</div>
            </div>
          @endif
      </div>
      <div class="col-md-12 dato-facturas mt-2">
          @if( $countPurchasedInvoices > 0 )
            <label><span>Facturas prepago disponibles</span></label>
            <div> {{ number_format($countPurchasedInvoices) }} </div>
          @endif
      </div>
      <div class="col-md-12 dato-facturas hidden">
        <a class="btn btn-secondary btn-sm" href="#">Comprar facturas</a>
      </div>
      
      
      <script>
        $('.fill-bar').each( function(){
        	var total = $(this).attr('data-total');
        	var fill = $(this).attr('data-fill');
        	var porc = (fill / total) * 100;
        	$(this).width( parseFloat(porc) + '%' );
        });
      </script>
      
      <style>
        


      </style>
        
    </div>
    
 @else
  <div class="not-allowed-message">
    Usted actualmente no tiene permisos para ver los reportes.
  </div>
@endif   
    
    
 </div>  
