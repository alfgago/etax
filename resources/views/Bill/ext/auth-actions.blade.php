<?php
      $allow = true;
      $cedula = currentCompanyModel()->id_number;
      if( 
        $cedula == "3101018968" ||
        $cedula == "3101011989" ||
        $cedula == "3101166930" ||
        $cedula == "3007684555" ||
        $cedula == "3130052102" ||
        $cedula == "3007791551" ||
        $cedula == "31017024290" )
      {
        $allow = false; 
      }
      
      $mh = $bill->haciendaResponse;
      $printMH = false;
      if( isset($mh) ){
        $printMH = $mh->mensaje == 1 ? "Aceptada" : "Rechazada";
      }
      
      $user = auth()->user();
?>
      @if($allow)
      
    <form id="accept-form-{{ $bill->id }}" class="inline-form" method="POST" action="/facturas-recibidas/confirmar-autorizacion/{{ $bill->id }}" >
      @csrf
      @method('patch')
      
      @if($bill->company_id == 3965)
      <div class="input-validate-iva" style="display: inline-block;">
       <select class="form-control hidden" name="regiones[{{ $bill->id }}]" placeholder="Seleccione la región" required style="font-size: 0.85em; padding: 5.75px !important; line-height: 1;">
          <option value="01" {{$bill->sucursal == '01' ? 'selected': ''}}>01 : San José</option>
          <option value="02" {{$bill->sucursal == '02' ? 'selected': ''}}>02 : Guápiles</option>
        </select>
      </div>
      @endif      
      <input type="hidden" name="autorizar" value="1">
      <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAuthorize({{ $bill->id }});">
        Autorizar
      </a>
    </form>

    <form id="reject-form-{{ $bill->id }}" class="inline-form" method="POST" action="/facturas-recibidas/confirmar-autorizacion/{{ $bill->id }}" >
      @csrf
      @method('patch')
      <input type="hidden" name="autorizar" value="0">
      <a href="#" title="Rezachar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmReject({{ $bill->id }});">
        Rechazar
      </a>
    </form>
    
    <a href="/facturas-recibidas/download-pdf/{{ $bill->id }}" title="Descargar PDF"class="btn btn-primary btn-agregar m-0" style="background: #d28923; border-color: #d28923; font-size: 0.85em;" download > 
      <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Descargar PDF
    </a>

@else
  <div style="font-size: 0.9rem;" class="descripcion mb-2">
    Correo: {{ $bill->email_reception ?? "No indica"  }}
    <br>
    
    <a href="/facturas-recibidas/download-pdf/{{ $bill->id }}" title="Descargar PDF"class="btn btn-primary btn-agregar m-0" style="background: #d28923; border-color: #d28923; font-size: 0.75em;margin-top: .25rem !important;" download > 
      <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Descargar PDF
    </a>
    
    <a href="/facturas-recibidas/download-xml/{{ $bill->id }}" title="Descargar XML"class="btn btn-success btn-agregar m-0" style="background: #2379d2; border-color: #2379d2; font-size: 0.75em; margin-top: .25rem !important;"  > 
      <i class="fa fa-file-text-o" aria-hidden="true"></i> Descargar XML
    </a>

      @if( $user->email=='darivera@corbana.co.cr' || $user->email=='corbana@etaxcr.com' || $user->email=='johrojas@corbana.co.cr' || $user->email=='majimenez@corbana.co.cr'
       || $user->email=='ysanchez@corbana.co.cr' || $user->email=='aorias@corbana.co.cr' || $user->email=='jsalazar@corbana.co.cr' || $user->email=='jmontero@corbana.co.cr')
    <form id="delete-form-{{  $bill->id }}" class="inline-form" method="POST" action="/facturas-recibidas/{{  $bill->id }}" >
      @csrf
      @method('delete')
      <a href="#" title="Eliminar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.75em; margin-top: .25rem !important; color:#fff !important;" onclick="confirmDelete({{ $bill->id }});">
        <i class="fa fa-trash-o" aria-hidden="true"></i> Eliminar factura
      </a>
    </form>
    @endif
    
  </div>
@endif

<br>
@if( $printMH )
  <a href="/facturas-recibidas/download-mh/{{ $bill->id }}" title="Descargar MH"class="btn btn-primary btn-agregar m-0" style="background: #2379d2; border-color: #2379d2; font-size: 0.75em; margin-top: .25rem !important;"  > 
    <i class="fa fa-file-text-o" aria-hidden="true"></i> Descargar Respuesta ({{ $printMH }})
  </a>
@else
  MH no recibido
@endif
