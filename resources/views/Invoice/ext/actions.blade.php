<div class="btn-group" role="group">
  <div class="btn-group" role="group">
    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Acciones
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
      @if($data->hacienda_status == '99')
        <a href="/facturas-emitidas/editar-factura/{{ $data->id }}" title="Ver detalle de factura" class="text-info mr-2 dropdown-item">
          <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Ver detalle de factura</span>
        </a>
        <a href="/facturas-emitidas/download-pdf/{{ $data->id }}" title="Descargar PDF" class="text-warning mr-2 dropdown-item" download > 
          <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span class="toggle-item-text">Ver detalle de factura</span>
        </a>
        <form id="delete-form-{{ $data->id }}" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/eliminar-programada/{{ $data->id }}" >
          @csrf
          @method('delete')
          <a type="button" class="text-danger mr-2" title="Eliminar recurrente" onclick="confirmDeleteProgramada({{ $data->id }});">
            <i class="fa fa-trash" aria-hidden="true"></i> <span class="toggle-item-text">Eliminar recurrente</span>
          </a>
        </form>
      @else
        @if( !$data->trashed()  )
          @if( !@$data->hide_from_taxes )
          
            @if( @$oficialHacienda )
                <a href="/facturas-emitidas/{{ $data->id }}" title="Ver detalle de factura" class="text-info mr-2 dropdown-item">
                    <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Ver detalle de factura</span>
                </a>
                @if(@$company->use_invoicing )
                  @if(in_array($data->document_type, ['01', '08', '09', '04', '03']) && $data->hacienda_status == '03')
                  <a href="/facturas-emitidas/nota-debito/{{ $data->id }}" title="Crear nota de debito" class="text-warning mr-2 dropdown-item">
                      <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Crear nota de debito</span>
                  </a>
                  @endif
                  @if(in_array($data->document_type, ['01', '08', '09', '04']) && $data->hacienda_status == '03')
                  <a href="/facturas-emitidas/nota-credito/{{ $data->id }}" title="Crear nota de credito" class="text-warning mr-2 dropdown-item">
                    <i class="fa fa-ban" aria-hidden="true"></i> <span class="toggle-item-text">Crear nota de credito</span>
                  </a>
                  @endif
                @endif
                <a href="/facturas-emitidas/download-pdf/{{ $data->id }}" title="Descargar PDF" class="text-warning mr-2 dropdown-item" download > 
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span class="toggle-item-text">Descargar PDF</span>
                </a>
                <a href="/facturas-emitidas/download-xml/{{ $data->id }}" title="Descargar XML" class="text-info mr-2 dropdown-item"> 
                  <i class="fa fa-file-text-o" aria-hidden="true"></i> <span class="toggle-item-text">Descargar XML</span>
                </a>
                <a href="/facturas-emitidas/consult/{{ $data->id }}" title="Descargar XML Respuesta Hacienda" class="text-info mr-2 dropdown-item">
                  <i class="fa fa-file-text-o text-success" aria-hidden="true"></i> <span class="toggle-item-text">Descargar Respuesta Hacienda</span>
                </a>
              
                <a href="/facturas-emitidas/reenviar-email/{{ $data->id }}" title="Reenviar correo electrónico" class="text-dark mr-2 dropdown-item"> 
                  <i class="fa fa-share" aria-hidden="true"></i> <span class="toggle-item-text">Reenviar correo electrónico</span>
                </a>
               
            
            @else
              <a href="/facturas-emitidas/{{ $data->id }}/edit" title="Editar factura" class="text-success mr-2 dropdown-item"> 
                <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Editar factura</span>
              </a>
              <form id="delete-form-{{  $data->id }}" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/{{  $data->id }}" >
                @csrf
                @method('delete')
                <a type="button" class="text-danger mr-2" title="Eliminar factura" onclick="confirmDelete({{  $data->id }});">
                  <i class="fa fa-trash-o" aria-hidden="true"></i> <span class="toggle-item-text">Eliminar factura</span>
                </a>
              </form>
            @endif
          
            <form id="hidefromtaxes-form-{{  $data->id }}" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/switch-ocultar/{{ $data->id }}" >
              @csrf
              @method('patch')
              <input type="hidden" name="hide_from_taxes" value="1">
              <a type="button" class="text-info mr-2" title="Ocultar de cálculo de impuestos" onclick="confirmHideFromTaxes({{  $data->id }});">
                 <i style="color:#999;" class="fa fa-eye-slash" aria-hidden="true"></i> <span class="toggle-item-text">Ocultar de cálculos</span>
              </a>
            </form>
          @else
            <form id="hidefromtaxes-form-{{  $data->id }}" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/switch-ocultar/{{ $data->id }}" >
              @csrf
              @method('patch')
              <a type="button" class="text-info mr-2" title="Incluir en cálculo de impuestos" onclick="confirmHideFromTaxes({{  $data->id }});">
                 <i style="color:#999;" class="fa fa-eye" aria-hidden="true"></i> <span class="toggle-item-text">Incluir en cálculos</span>
              </a>
            </form>
          @endif
      
        @else
      
          <form id="recover-form-{{ $data->id }}" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/{{ $data->id }}/restore" >
            @csrf
            @method('patch')
            <a type="button" class="text-success mr-2" title="Restaurar factura" onclick="confirmRecover({{ $data->id }});">
              <i class="fa fa-refresh" aria-hidden="true"></i> <span class="toggle-item-text">Restaurar factura</span>
            </a>
          </form>
      
        @endif
      @endif
    </div>
  </div>
</div>