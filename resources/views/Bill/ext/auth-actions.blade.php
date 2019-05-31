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