@if($is_code_validated === 1)
    <form id="accept-form-{{ $id }}" class="inline-form" method="POST" action="/facturas-recibidas/confirmar-autorizacion/{{ $id }}" >
      @csrf
      @method('patch')
      <input type="hidden" name="autorizar" value="1">
      <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAuthorize({{ $id }});">
        Autorizar
      </a>
    </form>

    <form id="delete-form-{{ $id }}" class="inline-form" method="POST" action="/facturas-recibidas/confirmar-autorizacion/{{ $id }}" >
      @csrf
      @method('patch')
      <input type="hidden" name="autorizar" value="0">
      <a href="#" title="Rezachar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDelete({{ $id }});">
        Rechazar
      </a>
    </form>
@else
    <button link="/facturas-recibidas/validar/{{ $id }}" titulo="Verificación Compra" class="btn btn-primary m-0 verificar_compra" data-toggle="modal" data-target="#modal_estandar">Validar</a>
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