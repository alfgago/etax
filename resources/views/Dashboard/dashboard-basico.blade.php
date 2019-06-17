<div class="col-md-8">
    
  <div class="row">
    
    <div class="col-lg-12">
      
      <div class="card-title">Enlaces rápidos</div>
      <div class="quicklinks">
        <a class="btn btn-primary" href="/facturas-emitidas/emitir-factura">Emitir facturas</a>
        <a class="btn btn-primary" href="#" onclick="abrirPopup('importar-emitidas-popup');">Importar facturas de venta</a>
        <a class="btn btn-primary" href="#" onclick="abrirPopup('importar-recibidas-popup');">Importar facturas de compra</a>
        <a class="btn btn-primary" href="/cierres">Cierres de mes</a>
        <a class="btn btn-primary" href="/reportes">Generar presentación de IVA</a>
      </div>
      
    </div>
    
    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.resumen-basico', ['titulo' => "Resumen de $nombreMes $ano", 'data' => $dataMes])
    </div>
    
    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.resumen-facturacion', ['titulo' => "Facturación $nombreMes $ano", 'data' => $dataMes])
    </div>
    
    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.proporcion-porcentajes', ['titulo' => "Proporción anual de ventas por tipo de IVA", 'data' => $acumulado])
    </div>
    
    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.grafico-prorrata-basico', ['titulo' => 'Prorrata operativa vs estimada', 'data' => $acumulado])
    </div>

  </div>
  
</div>

<div class=" col-md-4 mb-4">
  <div class="row">
    
    
    <div class="col-lg-12 mb-4 manuales-etax">
      <div class="sidebar-dashboard">
        <div class="card-title">Manuales eTax</div>
        <ul>
    			<li>	
    			  <a download href="/assets/files/guias/Manual-ConfiguracionEmpresa.pdf">Descargar manual de configuración de empresa</a>
    			</li>
    			<li>	
    				<a download href="/assets/files/guias/Manual-IntroducirVentas.pdf">Descargar manual de introducción de ventas</a>
    			</li>
    			<li>	
    				<a download href="/assets/files/guias/Manual-GenerarFactura.pdf">Descargar manual de facturación electrónica</a>
    			</li>
    			<li>	
    				<a download href="/assets/files/guias/Manual-IntroducirCompras.pdf">Descargar manual de introducción de compras</a>
    			</li>
    			<li>	
    				<a download href="/assets/files/guias/Manual-Escritorio.pdf">Descargar manual de escritorio</a>
    			</li>
    			<li>	
    				<a download href="/assets/files/guias/Manual-CodigosVentasEtax.pdf">Descargar manual de códigos eTax de ventas.</a>
    			</li>
    			<li>	
    				<a download href="/assets/files/guias/Manual-CodigosComprasEtax.pdf">Descargar manual de códigos eTax de compras.</a>
    			</li>
    		</ul>
        
      </div>
    </div>
    
    <div class="col-lg-12 mb-4">
      <div class="sidebar-dashboard">
        <div class="card-title">Notificaciones</div>
    
        <div class="notificaciones-container">
          <div class="notificacion"><span class="fecha">17/06/2019</span> Facturación habilitada en eTax.</div>
        </div>
        
      </div>
    </div>
    
    <div class="col-lg-12 mb-4">
      <div class="sidebar-dashboard">
        <div class="card-title">Empresa</div>
    
        <div class="info-empresa">
          <?php $empresa = currentCompanyModel(); ?>
          <div class="dato-empresa">
            {{ $empresa->name.' '.$empresa->last_name.' '.$empresa->last_name2 }}
          </div>
          <div class="dato-empresa">
            {{ $empresa->id_number }} {{ $empresa->business_name ? " - ".$empresa->business_name : ''}}
          </div>
          <div class="dato-empresa">
            <b>Plan actual:</b> {{ getCurrentSubscription()->product->plan->getName() }}
          </div>
          <div class="dato-empresa">
            <b>Facturación electrónica:</b> Habilitada
          </div>
          <div class="dato-empresa mt-3">  
            <a class="btn btn-secondary btn-sm" href="/empresas/configuracion">Configurar datos</a>
          </div>
        </div>
        
      </div>
    </div>
  
  </div> 
 
</div>