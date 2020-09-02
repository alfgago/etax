@extends('layouts/app')

@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection

@section('breadcrumb-buttons')

	<button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración avanzada</button>

@endsection

@section('content')
<style>
	.operative-data-tabs {
    display: flex;
}

.data-tab {
    margin-right: .25rem;
    padding: .25rem .5rem;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    cursor: pointer;
    transition: .5s ease all;
}

.data-tab:hover {
    background: #f5f5f5;
}

.data-tab.is-active {
    background: #e5e5e5;
}

.form-row.data-fields {
    margin: 0;
    border: 2px solid #e5e5e5;
    padding: .25rem;
}

.form-row.data-fields:not(.is-active) {
    display: none !important;
}
</style>
<div class="row">
  <div class="col-md-12">
  	<div class="tabbable verticalForm">
    	<div class="row">
        <div class="col-sm-3">
            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

            	<?php 
				$menu = new App\Menu;
				$items = $menu->menu('menu_empresas');
				foreach ($items as $item) { ?>
					<li>
						<a class="nav-link @if($item->link == '/empresas/configuracion') active @endif" aria-selected="false"  style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
					</li>
				<?php } ?>
                
            </ul>
        </div>
        <div class="col-sm-9">
          <div class="tab-content">       
						
						<form method="POST" action="{{ route('Company.update_config', ['id' => $company->id]) }}">
						
						  @csrf
						  @method('patch') 
						  
						  <div class="form-row">
						  	
						    <div class="form-group col-md-12">
						      <h3>
						        Datos de periodos anteriores
						      </h3>
						    </div>
						    
						    <?php 
						    	$today = Carbon\Carbon::now();
            					$anoActual = $today->year;
						    ?>
						    
						    <div class="form-group col-md-12">
							    <div class="operative-data-tabs">
							    @for ($i = 2019; $i <= $anoActual; $i++)
							    	<div class="data-tab data-tab-{{$i}} {{ $i == $anoActual ? 'is-active' : '' }}" onclick="toggleDatafields({{$i}});">
							    		{{$i}}
							    	</div>
							    @endfor
							    </div>
							    
							    @for ($i = 2019; $i <= $anoActual; $i++)
							    	<?php
							    		$operativeData = $company->getOperativeData($i);
							    	?>
								    <div class="form-row data-fields data-{{$i}} {{ $i == $anoActual ? 'is-active' : '' }}">
									    <div class="form-group col-md-6">
									      <label for="method">Método de cálculo de prorrata operativa inicial</label>
									      <select class="form-control" name="operative[{{ $i }}][method]" id="method" onchange="toggleTipoProrrata({{$i}});" required>
									        <option value="1" {{ @$operativeData->method == 1 ? 'selected' : '' }}>Registro manual</option>
									        @if($i == 2019)<option value="2" {{ @$operativeData->method == 2 ? 'selected' : '' }}>Ingreso de totales por código</option>@endif
									        <option value="3" {{ @$operativeData->method == 3 ? 'selected' : '' }}>Ingreso de facturas periodo anterior</option>
									      </select>
									    </div>
									    
									    <div class="form-group col-md-6 hidden toggle-types type-1">
									      <label for="prorrata_operativa">Digite su prorrata operativa</label>
									      <input type="number" class="form-control" name="operative[{{ $i }}][prorrata_operativa]"  step="0.01" min="0" max="99.99" value="{{ (@$operativeData->prorrata_operativa ?? 0.99)*100}}" required>
									    </div>
									    
									    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
									      <label for="operative_ratio1">Digite su proporción de ventas al 1%</label>
									      <input type="number" class="form-control" name="operative[{{ $i }}][operative_ratio1]"  step="0.01" min="0" max="100" value="{{ (@$operativeData->operative_ratio1 ?? 0)*100 }}" required>
									    </div>
									    
									    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
									      <label for="operative_ratio2">Digite su proporción de ventas al 2%</label>
									      <input type="number" class="form-control" name="operative[{{ $i }}][operative_ratio2]"  step="0.01" min="0" max="100" value="{{ (@$operativeData->operative_ratio2 ?? 0)*100 }}" required>
									    </div>
									    
									    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
									      <label for="operative_ratio3">Digite su proporción de ventas al 13%</label>
									      <input type="number" class="form-control" name="operative[{{ $i }}][operative_ratio3]"  step="0.01" min="0" max="100" value="{{ (@$operativeData->operative_ratio3 ?? 1)*100 }}" required>
									    </div>
									    
									    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
									      <label for="operative_ratio4">Digite su proporción de ventas al 4%</label>
									      <input type="number" class="form-control" name="operative[{{ $i }}][operative_ratio4]"  step="0.01" min="0" max="100" value="{{ (@$operativeData->operative_ratio4 ?? 0)*100 }}" required>
									    </div>
									    
									    <div class="form-group col-md-12 hidden toggle-types type-1">
									    	<div id="validate-ratios-text" class="text-danger hidden" >La sumatoria de proporciones debe ser igual a 100.</div>
									    </div>
									    
									    <div class="form-group col-md-6 hidden toggle-types type-2">
									      <label for="">&nbsp;</label>
									     	<a class="btn btn-primary form-button" href="/editar-totales-2018">Ingrese sus totales por código</a>
									    </div>
									    
									    <div class="form-group col-md-6 hidden toggle-types type-3">
									      <label for="">&nbsp;</label>
									      <a class="btn btn-primary form-button" href="/facturas-emitidas">Ingrese sus facturas {{ $i-1 }}</a>
									    </div>
									    
									    <div class="form-group col-md-6">
									      <label for="previous_balance">Ingrese su saldo a favor acumulado de periodos anteriores</label>
									      <input type="numeric" class="form-control" name="operative[{{ $i }}][previous_balance]"  step="0.01" value="{{ @$operativeData->previous_balance ?? 0 }}">
									    </div>
									</div>
								@endfor
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
						      <input type="text" class="form-control" name="last_document" id="last_document" value="{{ $company->last_document === null ? '00100001010000000000' : @$company->last_document  }}" required>
						      <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
						    </div>
							  <div class="form-group col-md-6">
								  <label for="last_document_rec">Último documento emitido de aceptacion</label>
								  <input type="text" class="form-control" name="last_document_rec" id="last_document_rec" value="{{ $company->last_document_rec === null ?  '00100001050000000000' : @$company->last_document_rec }}" required>
								  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
							  </div>
							  <div class="form-group col-md-6">
								  <label for="last_document_note">Último documento emitio nota de credito</label>
								  <input type="text" class="form-control" name="last_document_note" id="last_document_note" value="{{ $company->last_document_note === null ? '00100001030000000000' : @$company->last_document_note }}" required>
								  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
							  </div>

							  <div class="form-group col-md-6">
								  <label for="last_document_ticket">Último documento emitio tiquete electronico</label>
								  <input type="text" class="form-control" name="last_document_ticket" id="last_document_ticket" value="{{ $company->last_document_ticket === null ? '00100001040000000000' : @$company->last_document_ticket }}" required>
								  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
							  </div>

							  <div class="form-group col-md-6">
								  <label for="last_document_ticket">Último documento emitio nota de debito</label>
								  <input type="text" class="form-control" name="last_document_debit_note" id="last_document_debit_note" value="{{ $company->last_document_debit_note === null ? '00100001020000000000' : @$company->last_document_debit_note }}" required>
								  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
							  </div>
							  <div class="form-group col-md-6">
								  <label for="last_document_ticket">Último documento emitio factura electronica de compra</label>
								  <input type="text" class="form-control" name="last_document_invoice_pur" id="last_document_invoice_pur" value="{{ $company->last_document_invoice_pur === null ? '00100001080000000000' : @$company->last_document_invoice_pur }}" required>
								  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
							  </div>
							  <div class="form-group col-md-6">
								  <label for="last_document_ticket">Último documento emitio factura electronica de exportacion</label>
								  <input type="text" class="form-control" name="last_document_invoice_exp" id="last_document_invoice_exp" value="{{ $company->last_document_invoice_exp === null ? '00100001090000000000' : @$company->last_document_invoice_exp }}" required>
								  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
							  </div>
							  
								<div class="form-group col-md-12">
								  <label for="default_category_producto_code">Categoría de declaración por defecto</label>
								  <select class="form-control" id="default_category_producto_code" name="default_category_producto_code">
								    @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $category )
								      <option value="{{ $category['id'] }}" posibles="{{ $category['open_codes'] }}" {{ @$company->default_product_category == $category['id']  ? 'selected' : '' }}>{{ $category['name'] }}</option>
								    @endforeach
								  </select>
								</div>  
								
								<div class="form-group col-md-12">
								  <label for="default_vat_code">Tipo de IVA por defecto</label>
								  <select class="form-control" id="default_vat_code" name="default_vat_code">
								    @foreach ( \App\CodigoIvaRepercutido::all() as $tipo )
								      <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="{{ @$tipo['hidden'] ? 'hidden' : '' }}" {{ @$company->default_vat_code == $tipo['code']  ? 'selected' : '' }}>{{ $tipo['name'] }}</option>
								    @endforeach
								  </select>
								</div> 

								<div class="form-group col-md-12">
	                                <label for="tipo_persona">Preseleción de codigos IVA Repercutidos</label>
	                                <select class="form-control checkEmpty select2-tags" name="preselected_vat_code[]" id="preselected_vat_code" multiple required>
	              						<option value="1" {{@$company->repercutidos[0]->id ? '' : 'selected'}}>Utilizar todos los codigos</option>
	              						<?php
	              						$preselectos = array();
	              						foreach($company->repercutidos as $repercutido){
	              							$preselectos[] = $repercutido->id;
	              						}
                                    	?>
	                                    @foreach ( \App\CodigoIvaRepercutido::all() as $tipo )
	                                    	@if(@$tipo['hidden'])
	                                    	@else
	                                        <option id="preselected-option-{{$tipo['id']}}" value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class=""  {{ (in_array($tipo['id'], $preselectos) !== false) ? 'selected' : '' }}  >{{ $tipo['name'] }}</option>
	                                        @endif
	                                    @endforeach
	                                </select>
	                            </div>

	                            <div class="form-group col-md-12">
	                                <label for="tipo_persona">Preseleción de codigos IVA Soportados</label>
	                                <select class="form-control checkEmpty select2-tags" name="preselected_sop_code[]" id="preselected_sop_code" multiple required>
	              						<option value="1" {{@$company->soportados[0]->id ? '' : 'selected'}}>Utilizar todos los codigos</option>
	              						<?php
	              						$presoportados = array();
	              						foreach($company->soportados as $soportado){
	              							$presoportados[] = $soportado->id;
	              						}
                                    	?>
	                                    @foreach ( \App\CodigoIvaSoportado::where('hidden', false)->get() as $tipo )
	                                        <option id="presoported-option-{{$tipo['id']}}" value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class=""  {{ (in_array($tipo['id'], $presoportados) !== false) ? 'selected' : '' }}  >{{ $tipo['name'] }}</option>
	                                    @endforeach
	                                </select>
	                            </div>

						    <div class="form-group col-md-6">
						      <label for="auto_accept_email">Aceptar facturas por correo automáticamente</label>
						      <select class="form-control" name="auto_accept_email" id="auto_accept_email" required>
                                <option value="1" {{ @$company->auto_accept_email == 1 ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ @$company->auto_accept_email == 0 ? 'selected' : '' }}>No</option>
                              </select>
						    </div>

						    <div class="form-group col-md-6">
						      <label for="default_currency">Tipo de moneda por defecto</label>
						      <select class="form-control" name="default_currency" id="default_currency" required>
                                <option value="CRC" {{ @$company->default_currency == 'CRC' ? 'selected' : '' }}>CRC</option>
                                <option value="USD" {{ @$company->default_currency == 'USD' ? 'selected' : '' }}>USD</option>
                              </select>
						    </div>
			    
						    <div class="form-group col-md-6">
						      <label for="card_retention">% Retención Tarjetas</label>
						      <select class="form-control" id="card_retention" name="card_retention" >
				                    <option value="6" {{ @$company->card_retention == 6 ? 'selected' : '' }}>6%</option>
				                    <option value="0" {{ @$company->card_retention == 0 ? 'selected' : '' }}>0%</option>
				                    <option value="1" {{ @$company->card_retention == 1 ? 'selected' : '' }}>1%</option>
				                    <option value="2" {{ @$company->card_retention == 2 ? 'selected' : '' }}>2%</option>
				                    <option value="3" {{ @$company->card_retention == 3 ? 'selected' : '' }}>3%</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-12">
						      <label for="default_invoice_notes">Notas por defecto</label>
						      <textarea class="form-control" name="default_invoice_notes" id="default_invoice_notes" rows="6"  maxlength="190" >{{ @$company->default_invoice_notes }}</textarea>
						      <div class="description">Este campo aparecerá tanto en XML como en PDF de las facturas enviadas. Máximo de 190 caracteres.</div>
						    </div>
						    
						    <div class="form-group col-md-12">
						      <label for="payment_notes">Información de pago</label>
						      <textarea class="form-control" name="payment_notes" id="payment_notes" rows="6"  maxlength="2000" >{{ @$company->payment_notes }}</textarea>
						      <div class="description">Este campo aparecerá únicamente en el PDF de las facturas enviadas. Máximo de 2000 caracteres.</div>
						    </div>
						    
						    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>          
						    
						  </div>
						  
						</form>
		
							@if(currentCompanyModel()->id==1110 || currentCompanyModel()->id==437)
						    <div class="form-group col-md-12">
						      <h3>
						        Importación de Excel para facturación SM Seguros
						      </h3>
						    </div>
						    
								<form method="POST" action="/facturas-emitidas/importarExcelSM" enctype="multipart/form-data" class="toggle-xlsx mt-3">
															
								  @csrf
									<div class="form-group col-md-12">
								    <label for="archivo">Excel SM Seguros para envio masivo</label>  
										<div class="">
											<div class="fallback">
										      <input name="archivo" type="file" multiple="false" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
										  </div>
										  <small class="descripcion">Hasta 2500 líneas por archivo.</small>
										</div>
										<button type="submit" class="btn btn-primary">Importar facturas</button>
									</div>
								</form>
							@endif	

          </div>
        </div>
      </div>
    </div>
  </div>  
</div>       

@endsection

@section('footer-scripts')
    <script>
		function toggleTipoProrrata(ano = null) {
		  var metodo = $(".data-"+ano+" #method").val();
		  $( ".data-"+ano+" .toggle-types" ).hide();
		  $( ".data-"+ano+" .type-"+metodo ).show();
		}
		
		function toggleDatafields(ano){
			jQuery(".data-fields").removeClass('is-active');
			jQuery(".data-" + ano).addClass('is-active');
			toggleTipoProrrata(ano);
		}
		
		$(document).ready(function(){
		  var anoActual = "{{$anoActual}}";
		  toggleTipoProrrata(anoActual);
		  
	    $("#default_category_producto_code").change(function(){
	      var posibles = $('#default_category_producto_code :selected').attr('posibles');
	      var arrPosibles = posibles.split(",");
	      var tipo;
	      $('#default_vat_code option').hide();
	      for( tipo of arrPosibles ) {
	        $('#default_vat_code option[value='+tipo+']').show();
	      }
	    });

		});
		
	</script>
	<style>
		.form-button {
		    display: block;
		    margin: 0;
		    padding: 0.25rem 0.5rem;
		    font-size: 0.9rem;
		    height: calc(1.9695rem + 2px);
		}
	</style>

@endsection
