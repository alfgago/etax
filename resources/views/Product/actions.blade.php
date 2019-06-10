@if( !$data->trashed()  )
<a href="/productos/{{ $data->id }}/edit" title="Editar producto" class="text-success mr-2"> 
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>

<form id="delete-form-{{ $data->id }}" class="inline-form" method="POST" action="/productos/{{ $data->id }}" >
  @csrf
  @method('delete')
  <a type="button" class="text-danger mr-2" title="Eliminar producto" style="display: inline-block; background: none; border: 0;" onclick="confirmDelete({{ $data->id }});">
    <i class="fa fa-trash-o" aria-hidden="true"></i>
  </a>
</form>
@else

<form id="recover-form-{{ $data->id }}" class="inline-form" method="POST" action="/productos/{{ $data->id }}/restore" >
  @csrf
  @method('patch')
  <a type="button" class="text-success mr-2" title="Restaurar producto" style="display: inline-block; background: none; border: 0;" onclick="confirmRecover({{ $data->id }});">
    <i class="fa fa-refresh" aria-hidden="true"></i>
  </a>
</form>

@endif
