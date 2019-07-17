@if( $bill->is_code_validated)

  <form id="accept-form-{{ $bill->id }}" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/{{ $bill->id }}" >
    @csrf
    @method('patch')
    <input type="hidden" name="respuesta" value="1">
    <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAccept({{ $bill->id }});">
      Aceptar
    </a>
  </form>
  
  <form id="decline-form-{{ $bill->id }}" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/{{ $bill->id }}" >
    @csrf
    @method('patch')
    <input type="hidden" name="respuesta" value="2">
    <a href="#" title="Rechazar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDecline({{ $bill->id }});">
      Rechazar
    </a>
  </form>

@else

    <button link="/facturas-recibidas/validar/{{ $bill->id }}" titulo="Verificación Compra" class="btn btn-primary m-0 verificar_compra" data-toggle="modal" data-target="#modal_estandar">Requiere validación</a>
     <script> 
          $(".verificar_compra").click(function(){
            var link = $(this).attr("link");
            var titulo = $(this).attr("titulo");
            $("#titulo_modal_estandar").html(titulo);
            $.ajax({
               type:'GET',
               url:link,
               success:function(data){
                  
                    $("#body_modal_estandar").html(data);

               }

            });
          });
      </script>

@endif