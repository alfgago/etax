<?php
      $allow = true;
      $companyId = currentCompanyModel()->id;
      if(
        $companyId == 3965 ||
        $companyId == 3966 ||
        $companyId == 3967 ||
        $companyId == 3968 ||
        $companyId == 3969
      ){
        $allow = false; 
      }
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

    <form id="delete-form-{{ $bill->id }}" class="inline-form" method="POST" action="/facturas-recibidas/confirmar-autorizacion/{{ $bill->id }}" >
      @csrf
      @method('patch')
      <input type="hidden" name="autorizar" value="0">
      <a href="#" title="Rezachar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDelete({{ $bill->id }});">
        Rechazar
      </a>
    </form>
    
    <a href="/facturas-recibidas/download-pdf/{{ $bill->id }}" title="Descargar PDF"class="btn btn-primary btn-agregar m-0" style="background: #d28923; border-color: #d28923; font-size: 0.85em;" download > 
      <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Descargar PDF
    </a>
    
    
@else
  <div style="font-size: 0.9rem;" class="descripcion mb-2">
    Aceptación no disponible.
  </div>
@endif