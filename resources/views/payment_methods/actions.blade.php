@if( !$data->default_card )
    <form id="update-form-{{ $data->id }}" class="inline-form" method="POST" action="/payment-methods/payment-method-default-card-change/{{ $data->id }}">
        @csrf
        @method('patch')
        <a type="button" title="Cambiar a defecto" class="text-muted mr-2" onclick="confirmUpdate({{ $data->id }});">
          <i class="fa fa-credit-card" aria-hidden="true"></i>
        </a>
    </form>
@else
    <a href="#" title="Por defecto" class="text-success mr-2">
        <i class="fa fa-credit-card" aria-hidden="true"></i>
    </a>
@endif
<a href="/payment-methods/payment-method-token-update-view/{{ $data->id }}" title="Editar tarjeta {{ $data->last_4digits }}" class="text-success mr-2">
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>
<form id="delete-form-{{ $data->id }}" class="inline-form" method="POST" action="/payment-methods/payment-method-token-delete/{{ $data->id }}" >
  @csrf
  @method('delete')
  <a type="button" class="text-danger mr-2" title="Eliminar M&eacute;todo de Pago" style="display: inline-block; background: none;" onclick="confirmDelete({{ $data->id }});">
    <i class="fa fa-trash" aria-hidden="true"></i>
  </a>
</form>

