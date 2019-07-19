@if( !$data->trashed()  )

  @if( @$oficialHacienda )
    <a href="/facturas-recibidas/{{ $data->id }}" title="Ver detalle de factura" class="text-info mr-2"> 
      <i class="fa fa-eye" aria-hidden="true"></i>
    </a>
  @else
  
    <a href="/facturas-recibidas/{{ $data->id }}/edit" title="Editar factura" class="text-success mr-2"> 
      <i class="fa fa-pencil" aria-hidden="true"></i>
    </a>
    
    <form id="delete-form-{{  $data->id }}" class="inline-form" method="POST" action="/facturas-recibidas/{{  $data->id }}" >
      @csrf
      @method('delete')
      <a type="button" class="text-danger mr-2" title="Eliminar factura" onclick="confirmDelete({{  $data->id }});">
        <i class="fa fa-trash-o" aria-hidden="true"></i>
      </a>
    </form>
  @endif
  <form id="envioaceptacion-form-{{  $data->id }}" class="inline-form" method="POST" action="/facturas-recibidas/marcar-para-aceptacion/{{ $data->id }}" >
    @csrf
    @method('patch')
    <a type="button" class="text-info mr-2" title="Enviar a aceptación o rechazo con Hacienda" onclick="condfirmEnvioAceptacion({{  $data->id }});">
       <i class="fa fa-handshake-o" aria-hidden="true"></i>
    </a>
  </form>

@else

  <form id="recover-form-{{ $data->id }}" class="inline-form" method="POST" action="/facturas-recibidas/{{ $data->id }}/restore" >
    @csrf
    @method('patch')
    <a type="button" class="text-success mr-2" title="Restaurar factura" onclick="confirmRecover({{ $data->id }});">
      <i class="fa fa-refresh" aria-hidden="true"></i>
    </a>
  </form>

@endif