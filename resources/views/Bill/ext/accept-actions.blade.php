<form class="inline-form" method="POST" action="#" >
  @csrf
  @method('patch')
  <input type="hidden" name="id">
  <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;">
    Aceptar
  </a>
  <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;">
    Rechazar
  </a>
</form>