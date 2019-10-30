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

  <a link="/facturas-recibidas/validar/{{ $bill->id }}" titulo="Verificación Compra" class="btn btn-dark btn-agregar" style="color:#fff; border-color: #333; background: #333; font-size: 0.85em; " onclick="validarPopup(this);" data-toggle="modal" data-target="#modal_estandar">Revalidar</a>

@else

  <a link="/facturas-recibidas/validar/{{ $bill->id }}" titulo="Verificación Compra" class="btn btn-dark btn-agregar m-0 " style="color:#fff; border-color: #333; background: #333; font-size: 0.85em;" onclick="validarPopup(this);" data-toggle="modal" data-target="#modal_estandar">Requiere validación</a>

  <form id="decline-form-{{ $bill->id }}" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/{{ $bill->id }}" >
    @csrf
    @method('patch')
    <input type="hidden" name="respuesta" value="2">
    <a href="#" title="Rechazar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDecline({{ $bill->id }});">
      Rechazar
    </a>
  </form>

@endif

  <a style="margin-left: .5rem;" href="/facturas-recibidas/download-pdf/{{ $bill->id }}" title="Descargar PDF" class="text-warning mr-2" download > 
    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
  </a>