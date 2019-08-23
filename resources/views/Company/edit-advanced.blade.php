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
        <div class="col-sm-3">
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
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/comprar-facturas-vista">Comprar facturas</a>
                </li>
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
						    
						    <div class="form-group col-md-6">
						      <label for="first_prorrata_type">Método de cálculo de prorrata operativa inicial</label>
						      <select class="form-control" name="first_prorrata_type" id="first_prorrata_type" onchange="toggleTipoProrrata();" required>
						        <option value="1" {{ @$company->first_prorrata_type == 1 ? 'selected' : '' }}>Registro manual</option>
						        <option value="2" {{ @$company->first_prorrata_type == 2 ? 'selected' : '' }}>Ingreso de totales por código</option>
						        <option value="3" {{ @$company->first_prorrata_type == 3 ? 'selected' : '' }}>Ingreso de facturas del 2018</option>
						      </select>
						    </div>
						    
						    <div class="form-group col-md-6 hidden toggle-types type-1">
						      <label for="first_prorrata">Digite su prorrata operativa</label>
						      <input type="number" class="form-control" name="first_prorrata" id="first_prorrata" step="0.01" min="0" max="99.99" value="{{ @$company->first_prorrata ? $company->first_prorrata : 0 }}" required>
						    </div>
						    
						    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
						      <label for="operative_ratio1">Digite su proporción de ventas al 1%</label>
						      <input type="number" class="form-control" name="operative_ratio1" id="operative_ratio1" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio1 ? $company->operative_ratio1 : 0 }}" required>
						    </div>
						    
						    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
						      <label for="operative_ratio2">Digite su proporción de ventas al 2%</label>
						      <input type="number" class="form-control" name="operative_ratio2" id="operative_ratio2" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio2 ? $company->operative_ratio2 : 0 }}" required>
						    </div>
						    
						    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
						      <label for="operative_ratio3">Digite su proporción de ventas al 13%</label>
						      <input type="number" class="form-control" name="operative_ratio3" id="operative_ratio3" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio3 ? $company->operative_ratio3 : 0 }}" required>
						    </div>
						    
						    <div class="form-group col-md-6 hidden toggle-types type-1 proporciones">
						      <label for="operative_ratio4">Digite su proporción de ventas al 4%</label>
						      <input type="number" class="form-control" name="operative_ratio4" id="operative_ratio4" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio4 ? $company->operative_ratio4 : 0 }}" required>
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
						      <a class="btn btn-primary form-button" href="/empresas/set-prorrata-2018-facturas">Ingrese sus facturas 2018</a>
						    </div>
						    
						    <div class="form-group col-md-6">
						      <label for="saldo_favor_2018">Ingrese su saldo a favor acumulado de periodos anteriores</label>
						      <input type="numeric" class="form-control" name="saldo_favor_2018" id="saldo_favor_2018" step="0.01" value="{{ @$company->saldo_favor_2018 ? $company->saldo_favor_2018 : 0 }}">
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
								      <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="{{ @$tipo['hidden'] ? 'hidden' : '' }} {{ @$tipo['hideMasiva'] ? 'hidden' : '' }}" {{ @$company->default_vat_code == $tipo['code']  ? 'selected' : '' }}>{{ $tipo['name'] }}</option>
								    @endforeach
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
						      <textarea class="form-control" name="default_invoice_notes" id="default_invoice_notes"  maxlength="190" >{{ @$company->default_invoice_notes }}</textarea>
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
										  <small class="descripcion">Hasta 5000 líneas por archivo.</small>
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
        /*$(document).ready(function(){
            $('#saldo_favor_2018').on('keyup',function(){
                $(this).manageCommas();
            });
            //then sanatize on leave
            // if sanitizing needed on form submission time,
            //then comment beloc function here and call in in form submit function.
            $('#saldo_favor_2018').on('focus',function(){
                $(this).santizeCommas();
            });
        });

        String.prototype.addComma = function() {
            return this.replace(/(.)(?=(.{3})+$)/g,"$1,").replace(',.', '.');
        }
        //Jquery global extension method
        $.fn.manageCommas = function () {
            return this.each(function () {
                $(this).val($(this).val().replace(/(,|)/g,'').addComma());
            });
        }

        $.fn.santizeCommas = function() {
            return $(this).val($(this).val().replace(/(,| )/g,''));
        }*/
    </script>

	<script>
		
		function toggleTipoProrrata() {
		  var metodo = $("#first_prorrata_type").val();
		  $( ".toggle-types" ).hide();
		  $( ".type-"+metodo ).show();
		}
		
		$(document).ready(function(){
		  
		  toggleTipoProrrata();
		  
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
