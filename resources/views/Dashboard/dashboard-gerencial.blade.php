<div class="col-md-8" id="vistagerencial">
    
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
    
    <div class="col-lg-12 mb-4" id="prorrata">
      @include('Reports.widgets.grafico-prorrata', ['titulo' => 'Prorrata operativa vs prorrata estimada', 'data' => $acumulado])
    </div>
    
    <div class="col-lg-12 mb-4">
      @include('Reports.widgets.proporcion-porcentajes', ['titulo' => "Porcentaje de ventas del $ano por tipo de IVA", 'data' => $acumulado])
    </div>
  </div>
    <?php $hide_tutorial = $user = auth()->user()->hide_tutorial; ?>
    <input type="text" hidden value="{{$hide_tutorial}}" id="hide_tutorial">
    <script>
        var tutorialInicial = '{{$hide_tutorial}}';
        console.log(tutorialInicial);
        if(tutorialInicial == 0) {
            var Gerencial = {
                id: "Gerencial",
                i18n: {
                    nextBtn: "Next",
                    prevBtn: "Previous"
                },
                steps: [
                    {
                        title: "Escritorio Avanzado",
                        content: "El escritorio avanzado incluye información de interés como el gráfico de resumen del IVA. Un gráfico con la evolución mensual del impuesto para su negocio.",
                        target: document.querySelector("#vistagerencial"),
                        placement: "right"
                    },{
                        title: "Prorrata",
                        content: "Así como el comparativo entre la prorrata operativa y la prorrata estimada. La diferencia entre ellas indica si usted tendrá un saldo por pagar o un saldo a favor del impuesto al final del año. \n" +
                            "\n"+
                            "Pulse Finalizar para volver a la vista básica",
                        target: document.querySelector("#prorrata"),
                        placement: "left"
                    }
                ],
                onEnd: function (){
                    jQuery.ajax({
                        url: "/usuario/update-user-tutorial",
                        type: 'PUT',
                        cache: false,
                        data : {
                            tutorialInicial : 1,
                            _token: '{{ csrf_token() }}'
                        },
                        success : function( response ) {
                            console.log(response);
                        },
                        async: true
                    });
                    var vista = "basica";
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
                            $("#input-vista").val("basica");
                        },
                        async: true
                    });

                }
            }
            hopscotch.startTour(Gerencial);
        }
        function actualizarTutorial() {
            jQuery.ajax({
                url: "/usuario/update-user-tutorial",
                type: 'PUT',
                cache: false,
                data : {
                    tutorialInicial : 2,
                    _token: '{{ csrf_token() }}'
                },
                success : function( response ) {
                    console.log(response);
                },
                async: true
            });
        }
    </script>
</div>
