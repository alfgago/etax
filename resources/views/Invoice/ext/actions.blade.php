@if( !$data->trashed()  )

  @if( @$oficialHacienda )
  <a href="/facturas-emitidas/{{ $data->id }}" title="Ver detalle de factura" class="text-info mr-2"> 
    <i class="fa fa-eye" aria-hidden="true"></i>
  </a>
  <form id="anular-form-{{ $data->id }}/anular" class="inline-form" method="POST" action="/facturas-emitidas/{{  $data->id }}" >
    @csrf
    @method('patch')
    <a type="button" class="text-danger mr-2" title="Anular factura" onclick="confirmAnular({{  $data->id }});">
      <i class="fa fa-ban" aria-hidden="true"></i>
    </a>
  </form>
  <a href="#" title="Descargar PDF - Desactivado temporalmente hasta la 1pm." class="text-warning mr-2"> 
    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
  </a>
  <a href="#" title="Reenvir correo electrónico - Desactivado temporalmente hasta la 1pm." class="text-dark mr-2"> 
    <i class="fa fa-share" aria-hidden="true"></i>
  </a>
  @else
  <a href="/facturas-emitidas/{{ $data->id }}/edit" title="Editar factura" class="text-success mr-2"> 
    <i class="fa fa-pencil" aria-hidden="true"></i>
  </a>
  <form id="delete-form-{{  $data->id }}" class="inline-form" method="POST" action="/facturas-emitidas/{{  $data->id }}" >
    @csrf
    @method('delete')
    <a type="button" class="text-danger mr-2" title="Eliminar factura" onclick="confirmDelete({{  $data->id }});">
      <i class="fa fa-trash-o" aria-hidden="true"></i>
    </a>
  </form>
  @endif

@else

  <form id="recover-form-{{ $data->id }}" class="inline-form" method="POST" action="/facturas-emitidas/{{ $data->id }}/restore" >
    @csrf
    @method('patch')
    <a type="button" class="text-success mr-2" title="Restaurar factura" onclick="confirmRecover({{ $data->id }});">
      <i class="fa fa-refresh" aria-hidden="true"></i>
    </a>
  </form>

@endif