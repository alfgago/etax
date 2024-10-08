<div class="col-md-8">

  <div class="row">

    <div class="col-lg-12">
        <div id=""></div>
      <div class="card-title" id="escritorio">Enlaces rápidos</div>
      <div class="quicklinks">
        <?php     
        $menu = new App\Menu;
        $items = $menu->menu('menu_dashboard');
        foreach ($items as $item) { ?>
            <a class="btn btn-primary" style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
        <?php } ?>

          <div id="content">
              <p></p>
          </div>
      </div>

    </div>


    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.resumen-periodo', ['titulo' => "$nombreMes $ano", 'data' => $dataMes])
    </div>

    <div class="col-lg-6 mb-4">
      @include('Reports.widgets.resumen-periodo', ['titulo' => ($ano == 2019 ? "Acumulado Jul. 2019 - Dic. 2019" : "Acumulado anual $ano"), 'data' => $acumulado])
    </div>
    
    @if( currentCompany() == '1110' )
      <div class="col-lg-12 mb-4" id="smwidget">
        
      </div>
      <script>
        $(document).ready(function(){
          var ano = $("#input-ano").val();
          var mes = $("#input-mes").val();
          $('#smwidget').load("/sm/widget/"+ano+"/"+mes);
        });
      </script>
    @endif

    <div class="col-lg-6 mb-4" id="proporcion">
      @include('Reports.widgets.proporcion-porcentajes', ['titulo' => "Proporción anual de ventas por tipo de IVA", 'data' => $acumulado])
    </div>

    <div class="col-lg-6 mb-4" id="prorrata">
      @include('Reports.widgets.grafico-prorrata', ['titulo' => 'Prorrata operativa vs prorrata estimada', 'data' => $acumulado])
    </div>

  </div>

</div>

<div class=" col-md-4 mb-4">
  <div class="row">

    <div class="col-lg-12 mb-4">
      <div class="sidebar-dashboard">
        <div class="card-title" id="empresa">Empresa</div>

        <div class="info-empresa">
          <?php 
              $empresa = currentCompanyModel(); 
              $hide_tutorial = $user = auth()->user()->hide_tutorial;
              $atv = $empresa->atv;
          ?>
          <div class="dato-empresa">
            {{ $empresa->name.' '.$empresa->last_name.' '.$empresa->last_name2 }}
          </div>
          <div class="dato-empresa">
            {{ $empresa->id_number }} {{ $empresa->business_name ? " - ".$empresa->business_name : ''}}
          </div>
          <div class="dato-empresa">
            <b>Plan actual:</b> {{ getCurrentSubscription()->plan->getName() }}
          </div>
          <div class="dato-empresa">
            <b>Empresas disponibles:</b> {{ getCurrentSubscription()->plan->num_companies }}
          </div>
          <div class="dato-empresa">
            <b>Facturación electrónica:</b> Habilitada
          </div>
          @if( $empresa->atv_validation && $empresa->use_invoicing && $atv->getDueDate() )
          <div class="dato-empresa">
            <b>Vencimiento Certificado ATV:</b> <span style='color:{{ $atv->isVencido() ? "red" : "green" }}'>{{ $atv->getDueDate()->format('d/m/Y')  }}</span>
          </div>
          @endif
          <div class="dato-empresa mt-3">
            <a class="btn btn-secondary btn-sm" href="/empresas/configuracion">Configurar datos</a>
          </div>
        </div>

      </div>
    </div>

    <div class="col-lg-12 mb-4" id="facturas">
      @include('Reports.widgets.resumen-facturacion', ['titulo' => "Facturación $nombreMes $ano", 'data' => $dataMes])
    </div>


    <div class="col-lg-12 mb-4 manuales-etax">
      <div class="sidebar-dashboard">
        <div class="card-title" id="manuales">Manuales eTax</div>
        <ul>
    			<li>
    			  <a href="https://etaxcr.com/knowledge-base/que-son-los-codigos-de-etax/">Manual de códigos en eTax</a>
    			</li>
    			<li>
    				<a href="https://etaxcr.com/manual-de-usuario/">Manual general de eTax</a>
    			</li>
    			<li>
    				<a href="https://etaxcr.com/blog/">Blog de eTax</a>
    			</li>
    			<li>
    				<a href="https://etaxcr.com/soporte/">Base de conocimiento</a>
    			</li>
    		</ul>

      </div>
    </div>

  </div>

 <input type="text" hidden value="{{$hide_tutorial}}" id="hide_tutorial">
</div>



    <div class="col-lg-12 mb-4 pb-4">
      @include('Reports.widgets.grafico-mensual', ['titulo' => "Resumen de IVA $ano"])
    </div>


<script>
    function tutorialBurbujas(){
        /*var tour = {
            id: "tour",
            i18n: {
                nextBtn: "Siguiente",
                prevBtn: "Anterior",
                doneBtn: "Finalizar",
            },
            steps: [
                {
                    title: "Vista básica",
                    content: "El escritorio en versión básica le permite revisar en minutos la información más relevante para su negocio. Su escritorio se alimentará conforme incluya información de ventas y compras. Para conocer más sobre cada uno de los elementos, posicione el cursor sobre los signos de pregunta.",
                    target: document.querySelector("#escritorio"),
                    placement: "right",
                    delay:500
                },{
                    title: "Menú de Ventas",
                    content: "eTax funciona con base en la información de compras y ventas de su negocio. Para ingresar la información usted puede: \n" +
                        "\tRegistrar una factura manual \n" +
                        "\tImportar a través de un excel \n" +
                        "Importar a través de un XML\n" +
                        "\n" +
                        "Posiciónese sobre el botón de ventas para ver las opciones. \n" +
                        "\n" +
                        "También puede reenviar sus XML’s al correo facturas@etaxcr.com y autorizarlas desde el sistema.",
                    target: document.querySelector("#ventas"),
                    placement: "right",
                    showPrevButton:true
                },{
                    title: "Menú de Compras",
                    content: "Lo mismo sucede en el caso de las compras. Puede registrar facturas, importar (en XML o excel) o re-enviar a través de facturas@etaxcr.com y autorizarlas aquí.",
                    target: document.querySelector("#compras"),
                    placement: "right",
                    showPrevButton:true
                },{
                    title: "Facturación",
                    content: "Si usted utiliza eTax para facturar, en Facturación encuentra la posibilidad de realizar los diferentes tipos de documentos y de aceptar las facturas recibidas ante el Ministerio de Hacienda.",
                    target: document.querySelector("#facturacion"),
                    placement: "right",
                    showPrevButton:true
                },{
                    title: "Cierres de Mes",
                    content: "Previo a descargar su declaración de IVA mensual, usted tendrá que realizar el cierre de mes. ",
                    target: document.querySelector("#cierresmes"),
                    placement: "right",
                    showPrevButton:true
                },{
                    title: "Reportes",
                    content: " Ingrese a Reportes para conocer múltiple información sobre su negocio y el IVA. Cada uno de ellos incluye una explicación sobre su contenido.",
                    target: document.querySelector("#reportes"),
                    placement: "right",
                    showPrevButton:true
                },{
                    title: "Clientes",
                    content: "Usted puede incluir la información de sus clientes. Así los tendrá disponibles al momento de realizar una venta.",
                    target: document.querySelector("#clientes"),
                    placement: "right",
                    showPrevButton:true
                },{
                    title: "Proveedores",
                    content: "Usted puede incluir la información de sus proveedores. Así los tendrá disponibles al momento de registrar una compra.",
                    target: document.querySelector("#proveedores"),
                    placement: "right",
                    showPrevButton:true
                },{
                    title: "Productos",
                    content: "Usted puede incluir la información de sus productos. Así los tendrá disponibles al momento de realizar una venta.",
                    target: document.querySelector("#productos"),
                    placement: "right",
                    showPrevButton:true,
                },{
                    title: "Vista Básica",
                    content: "Además del escritorio en vista simple, tambien cuenta con una opción de vista avanzada. Pulse 'Siguiente' para ver la Vista Gerencial y completar el tutorial",
                    target: document.querySelector("#vistabasica"),
                    placement: "left",
                    showPrevButton:true
                }
            ],
            multipage:true,
            onEnd: function (){
                var vista = "gerencial";
                var mes = $("#input-mes").val();
                var ano = $("#input-ano").val();

                jQuery.ajax({
                    url: "/reportes/reporte-dashboard",
                    type: 'post',
                    cache: false,
                    data : {
                        mes : mes,
                        ano : ano,
                        vista : vista,
                        _token: '{{ csrf_token() }}'
                    },
                    success : function( response ) {
                        $('#reporte-container').html(response);
                        initHelpers();
                        $("#input-vista").val("gerencial");
                    },
                    async: true
                });
                jQuery.ajax({
                    url: "/usuario/update-user-tutorial",
                    type: 'post',
                    cache: false,
                    data : {
                        tutorialInicial : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success : function( response ) {
                        console.log(response);
                    },
                    async: true
                });
            }
        }
        hopscotch.startTour(tour);*/
    }
    function actualizarTutorial() {
        console.log('here');
        jQuery.ajax({
            url: "/usuario/update-user-tutorial",
            type: 'post',
            cache: false,
            data : {
                tutorialInicial : 2,
                _token: '{{ csrf_token() }}'
            },
            success : function( response ) {
                console.log(response);
                tutorialBurbujas();
            },
            async: true
        });
    }
    $( document ).ready(function(){
        var tutorialInicial = '{{$hide_tutorial}}';
        if(tutorialInicial == 0){
            tutorialBurbujas();
        }
    });
</script>
