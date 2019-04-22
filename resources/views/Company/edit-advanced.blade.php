@extends('layouts/app')

@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection

@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración avanzada</button>
@endsection

@section('content')

<div class="row">
  <div class="col-md-12">
  	<div class="tabbable verticalForm">
    	<div class="row">
        <div class="col-3">
            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/editar">Editar perfil de empresa</a>
                </li>
                <li class="active">
                    <a class="nav-link active" aria-selected="true" href="/empresas/configuracion">Configuración avanzada</a>
                </li>
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/certificado">Certificado digital</a>
                </li>
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/equipo">Equipo de trabajo</a>
                </li>
            </ul>
        </div>
        <div class="col-9">
          <div class="tab-content">       
						
						<form method="POST" action="{{ route('Company.update_config', ['id' => $company->id]) }}">
						
						  @csrf
						  @method('patch') 
						  
						  <div class="form-row">
						  	
						    <div class="form-group col-md-12">
						      <h3>
						        Prorrata
						      </h3>
						    </div>
						    
						    <div class="form-group col-md-6">
						      <label for="first_prorrata_type">Método de cálculo de prorrata operativa inicial</label>
						      <select class="form-control" name="first_prorrata_type" id="first_prorrata_type" required>
						        <option value="1" {{ @$company->first_prorrata_type == 1 ? 'selected' : '' }}>Registro de facturas del 2018</option>
						        <option value="2" {{ @$company->first_prorrata_type == 2 ? 'selected' : '' }}>Registro totales de factura</option>
						        <option value="3" {{ @$company->first_prorrata_type == 3 ? 'selected' : '' }}>Registro manual</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-6">
						      <label for="first_prorrata">Digite su prorrata inicial</label>
						      <input type="number" class="form-control" name="first_prorrata" id="first_prorrata" min="1" max="100" value="{{ @$company->first_prorrata ? $company->first_prorrata : 100 }}">
						    </div>
						    
						    <div class="form-group col-md-12">
						      <h3>
						        Facturación
						      </h3>
						    </div>
						    
						    <div class="form-group col-md-6">
						      <label for="use_invoicing">¿Desea emitir facturas electrónicas con eTax?</label>
						      <select class="form-control" name="use_invoicing" id="use_invoicing" required>
						        <option value="1" {{ @$company->use_invoicing ? 'selected' : '' }}>Sí</option>
						        <option value="0" {{ !(@$company->use_invoicing) ? 'selected' : '' }}>No</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-6">
						      <label for="last_document">Último documento emitido</label>
						      <input type="text" class="form-control" name="last_document" id="last_document" value="{{ @$company->last_document }}" required>
						      <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
						    </div>
						    
						    <div class="form-group col-md-12">
						      <label for="default_vat_code">Tipo de IVA por defecto</label>
						      <select class="form-control" id="default_vat_code" name="default_vat_code" >
						        @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
						          <option value="{{ $tipo['codigo'] }}" porcentaje="{{ $tipo['porcentaje'] }}" {{ @$company->default_vat_code == $tipo['codigo']  ? 'selected' : '' }}>{{ $tipo['nombre'] }}</option>
						        @endforeach
						      </select>
						    </div>
						    
						    <div class="form-group col-md-6">
						      <label for="default_currency">Tipo de moneda por defecto</label>
						      <select class="form-control" name="default_currency" id="default_currency" required>
                    <option value="crc" {{ @$company->default_currency == 'crc' ? 'selected' : '' }}>CRC</option>
                    <option value="usd" {{ @$company->default_currency == 'usd' ? 'selected' : '' }}>USD</option>
                  </select>
						    </div>
						    
						     <div class="form-group col-md-12">
						      <label for="default_invoice_notes">Notas por defecto</label>
						      <textarea class="form-control" name="default_invoice_notes" id="default_invoice_notes" >{{ @$company->default_invoice_notes }}</textarea>
						    </div>
						    
						    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>          
						    
						  </div>
						  
						</form>

          </div>
        </div>
      </div>
    </div>
  </div>  
</div>       

@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración</button>
@endsection 

@section('footer-scripts')

@endsection