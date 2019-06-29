<form id="accept-form-{{ $id }}" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/{{ $id }}" >
  @csrf
  @method('patch')
  <input type="hidden" name="respuesta" value="1">
  <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAccept({{ $id }});">
    Aceptar
  </a>
</form>

<form id="decline-form-{{ $id }}" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/{{ $id }}" >
  @csrf
  @method('patch')
  <input type="hidden" name="respuesta" value="2">
  <a href="#" title="Rechazar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDecline({{ $id }});">
    Rechazar
  </a>
</form>