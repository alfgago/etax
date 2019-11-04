  <form id="decline-form-{{ $bill->id }}" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/{{ $bill->id }}" >
    @csrf
    @method('patch')
    <input type="hidden" name="respuesta" value="2">
    <a href="#" title="Rechazar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDecline({{ $bill->id }});">
      Rechazar
    </a>
  </form>
