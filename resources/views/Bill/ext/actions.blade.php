<div class="actions-flex">
@if( !$data->trashed()  )

  @if( !@$data->hide_from_taxes )
  
    @if( @$oficialHacienda )
      <a href="/facturas-recibidas/{{ $data->id }}" title="Ver detalle de factura" class="text-info mr-2"> 
        <i class="fa fa-info" aria-hidden="true"></i>
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
    
    @if( !(@$data->accept_status == 1 && @$data->hacienda_status == '03') )
      <form id="envioaceptacion-form-{{  $data->id }}" class="inline-form" method="POST" action="/facturas-recibidas/marcar-para-aceptacion/{{ $data->id }}" >
        @csrf
        @method('patch')
        <a type="button" class="text-info mr-2" title="Enviar a aceptación o rechazo con Hacienda" onclick="confirmEnvioAceptacion({{  $data->id }});">
           <i class="fa fa-handshake-o" aria-hidden="true"></i>
        </a>
      </form>
    @endif
    
    <a href="/facturas-recibidas/download-pdf/{{ $data->id }}" title="Descargar PDF" class="text-warning mr-2" download > 
      <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
    </a>
    
    <a href="/facturas-recibidas/download-xml/{{ $data->id }}" title="Descargar XML" class="text-info mr-2"> 
      <i class="fa fa-file-text-o" aria-hidden="true"></i>
    </a>
    
    <a link="/facturas-recibidas/validar/{{ $data->id }}" titulo="Verificación Compra" class="text-success mr-2" onclick="validarPopup(this);" data-toggle="modal" data-target="#modal_estandar"><i class="fa fa-check" aria-hidden="true"></i></a>
  
    <form id="hidefromtaxes-form-{{  $data->id }}" class="inline-form" method="POST" action="/facturas-recibidas/switch-ocultar/{{ $data->id }}" >
      @csrf
      @method('patch')
      <input type="hidden" name="hide_from_taxes" value="1">
      <a type="button" class="text-info mr-2" title="Ocultar de cálculo de impuestos" onclick="confirmHideFromTaxes({{  $data->id }});">
         <i style="color:#999;" class="fa fa-eye-slash" aria-hidden="true"></i>
      </a>
    </form>
  @else
    <form id="hidefromtaxes-form-{{  $data->id }}" class="inline-form" method="POST" action="/facturas-recibidas/switch-ocultar/{{ $data->id }}" >
      @csrf
      @method('patch')
      <a type="button" class="text-info mr-2" title="Incluir en cálculo de impuestos" onclick="confirmHideFromTaxes({{  $data->id }});">
         <i style="color:#999;" class="fa fa-eye" aria-hidden="true"></i>
      </a>
    </form>
  @endif

@else

  <form id="recover-form-{{ $data->id }}" class="inline-form" method="POST" action="/facturas-recibidas/{{ $data->id }}/restore" >
    @csrf
    @method('patch')
    <a type="button" class="text-success mr-2" title="Restaurar factura" onclick="confirmRecover({{ $data->id }});">
      <i class="fa fa-refresh" aria-hidden="true"></i>
    </a>
  </form>

@endif
</div>