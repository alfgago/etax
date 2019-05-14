<div class="sidebar-dashboard">
    <div class="card-title">{{ $titulo }}</div>
    <div class="row">
      <?php 
        $company = currentCompanyModel();
        $availableBills = $company->checkCountAvailableBills();
        $availableInvoices = $company->checkCountAvailableInvoices();
      ?>
      <div class="col-md-12 dato-facturas">
          <label><span>Facturas emitidas</span></label>
          @if( $availableInvoices != -1 )
            <div class="barra-limites emitidas" >
              <div class="fill-bar" data-total="{{ $availableInvoices }}" data-fill="{{ number_format( $data->count_invoices ) }}"></div>
              <div class="barra-text">{{ number_format( $data->count_invoices ) }} de {{ $availableInvoices }}</div>
            </div>
          @else
            <div class="barra-limites emitidas" >
              <div class="fill-bar" data-total="99999999" data-fill="{{ number_format( $data->count_invoices ) }}"></div>
              <div class="barra-text">{{ number_format( $data->count_invoices ) }} de &infin;</div>
            </div>
          @endif
      </div>
      
      <div class="col-md-12 dato-facturas mt-2">
          <label><span>Facturas recibidas</span></label>
          @if( $availableBills != -1 )
            <div class="barra-limites recibidas">
              <div class="fill-bar" data-total="{{ $availableBills }}" data-fill="{{ number_format( $data->count_bills ) }}"></div>
              <div class="barra-text">{{ number_format( $data->count_bills ) }} de {{ $availableBills }}</div>
            </div>
          @else
            <div class="barra-limites recibidas">
              <div class="fill-bar" data-total="99999999" data-fill="{{ number_format( $data->count_bills ) }}"></div>
              <div class="barra-text">{{ number_format( $data->count_bills ) }} de &infin;</div>
            </div>
          @endif
      </div>
      
      <div class="col-md-12 dato-facturas">
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
 </div>  
