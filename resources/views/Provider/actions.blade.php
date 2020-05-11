
<a href="/proveedores/{{ $data->id }}/edit" title="Editar proveedor {{ $data->code }}" class="text-success mr-2">
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>

<form id="delete-form-{{ $data->id }}" class="inline-form" method="POST" action="/proveedores/{{ $data->id }}" >
  @csrf
  @method('delete')
  <a type="button" class="text-danger mr-2" title="Eliminar proveedor {{ $data->code }}"
     style="display: inline-block; background: none;" onclick="confirmDelete({{ $data->id }});">
    <i class="fa fa-trash" aria-hidden="true"></i>
  </a>
</form>

