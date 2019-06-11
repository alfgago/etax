<form id="accept-form-{{ $id }}" class="inline-form" method="POST" action="/facturas-recibidas/estado-hacienda/{{ $id }}" >
  @csrf
  @method('patch')
  <input type="hidden" name="status" value="02">
  <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAuthorize({{ $id }});">
    Aceptar
  </a>
</form>

<form id="delete-form-{{ $id }}" class="inline-form" method="POST" action="/facturas-recibidas/estado-hacienda/{{ $id }}" >
  @csrf
  @method('delete')
  <input type="hidden" name="status" value="03">
  <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDelete({{ $id }});">
    Rechazar
  </a>
</form>