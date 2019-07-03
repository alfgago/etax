@if( $bill->is_code_validated)
  {{ $bill->id }}
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

  <a href="/facturas-recibidas/validaciones" title="Validar código" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" >
    Requiere validación de código
  </a>

@endif