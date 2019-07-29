<a href="/clientes/clientes-edit/{{ $data->id }}" title="Editar cliente {{ $data->id }}" class="text-success mr-2">
    <i class="fa fa-pencil" aria-hidden="true"></i>
</a>

<form id="delete-form-{{ $data->id }}" class="inline-form" method="POST" action="/clientes/destroy/{{ $data->id }}" >
    @csrf
    @method('delete')
    <a type="button" class="text-danger mr-2" title="Eliminar cliente"
       style="display: inline-block; background: none;" onclick="confirmDelete({{ $data->id }});">
        <i class="fa fa-trash" aria-hidden="true"></i>
    </a>
</form>
