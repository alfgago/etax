<div class="col-md-8">
    
  <div class="row">
    
    <div class="col-lg-12">
      
      <div class="card-title" id="header">Enlaces rápidos</div>
      <div class="quicklinks">
        <a class="btn btn-primary" href="/facturas-emitidas/emitir-factura">Emitir facturas</a>
        <a class="btn btn-primary" href="#" onclick="abrirPopup('importar-emitidas-popup');">Importar facturas de venta</a>
        <a class="btn btn-primary" href="#" onclick="abrirPopup('importar-recibidas-popup');">Importar facturas de compra</a>
        <a class="btn btn-primary" href="/cierres">Cierres de mes</a>
        <a class="btn btn-primary" href="/reportes">Generar presentación de IVA</a>
          <div id="content">
              <p></p>
          </div>
      </div>
      
    </div>
    
    <div class="col-lg-6 mb-4" id="reporteMes">
      @include('Reports.widgets.resumen-basico', ['titulo' => "Resumen de $nombreMes $ano", 'data' => $dataMes])
    </div>
    
    <div class="col-lg-6 mb-4" id="facturas">
      @include('Reports.widgets.resumen-facturacion', ['titulo' => "Facturación $nombreMes $ano", 'data' => $dataMes])
    </div>
    
    <div class="col-lg-6 mb-4" id="proporcion">
      @include('Reports.widgets.proporcion-porcentajes', ['titulo' => "Proporción anual de ventas por tipo de IVA", 'data' => $acumulado])
    </div>
    
    <div class="col-lg-6 mb-4" id="prorrata">
      @include('Reports.widgets.grafico-prorrata-basico', ['titulo' => 'Prorrata operativa vs estimada', 'data' => $acumulado])
    </div>

  </div>
  
</div>

<div class=" col-md-4 mb-4">
  <div class="row">
    
    
    <div class="col-lg-12 mb-4 manuales-etax">
      <div class="sidebar-dashboard">
        <div class="card-title" id="manuales">Manuales eTax</div>
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
        <div class="card-title" id="empresa">Empresa</div>
    
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
<script>
var tour = {
    id: "tour",
        i18n: {
            nextBtn: "Next",
            prevBtn: "Previous"
        },
        steps: [
            {
                title: "Escritorio",
                content: "El escritorio en versión básica le permite revisar en minutos la información más relevante para su negocio. Su escritorio se alimentará conforme incluya información de ventas y compras. Para conocer más sobre cada uno de los elementos, posicione el cursor sobre los signos de pregunta.",
                target: document.querySelector("#escritorio"),
                placement: "right"
            },{
                title: "Vista básica",
                content: "Además del escritorio en vista simple, cuenta con una opción de vista avanzada, selecciónela en el accionable para acceder a ella",
                target: document.querySelector("#vistabasica"),
                placement: "left"
            },{
                title: "Vista Gerencial",
                content: "El escritorio avanzado incluye información de interés como el gráfico de resumen del IVA. Un gráfico con la evolución mensual del impuesto para su negocio. ",
                target: document.querySelector("#vistagerencial"),
                placement: "right"
            },{
                title: "Enlaces Rápidos",
                content: "Estos le llevarán a las tareas más comunes",
                target: document.querySelector("#header"),
                placement: "right"
            },{
                title: "PDF",
                content: "Aprenda paso a paso con nuestras guías",
                target: document.querySelector("#manuales"),
                placement: "left"
            },{
                title: "Reportes por mes",
                content: "Tenga siempre a mano la informacion detallada y concisa de sus operaciones mensuales",
                target: document.querySelector("#reporteMes"),
                placement: "top"
            },
            {
                title: "Facturacion",
                content: "Controle sus facturas emitidas y recibidas",
                target: document.querySelector("#facturas"),
                placement: "left"
            },
            {
                title: "Proporcion",
                content: "Analice de forma visual cual es su estado",
                target: document.querySelector("#proporcion"),
                placement: "top"
            },
            {
                title: "Prorrata",
                content: "El detalle de su prorrata a simple vista",
                target: document.querySelector("#prorrata"),
                placement: "left"
            },
            {
                title: "Empresa",
                content: "Configure sus datos",
                target: document.querySelector("#empresa"),
                placement: "left"
            }
        ]
    }
    hopscotch.startTour(tour);
</script>
