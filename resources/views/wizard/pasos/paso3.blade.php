<div class="form-group col-md-12">
  <h3>
    Datos de facturación
  </h3>
</div>

<div class="form-group col-md-6">
  <label for="use_invoicing">¿Desea emitir facturas electrónicas con eTax?</label>
  <select class="form-control checkEmpty" name="use_invoicing" id="use_invoicing" required>
    <option value="1" @if(!in_array(8, auth()->user()->permisos()) ) selected @endif >Sí</option>
    <option value="0" @if(in_array(8, auth()->user()->permisos()) ) selected @endif >No</option>
  </select>
</div>

<div class="form-group col-md-6">
  <label for="last_document" >Último documento emitido</label>
  <input type="text" class="form-control" name="last_document" id="last_document" value="{{ @$company->last_document }}" >
  <div class="description">Si utilizaba otro sistema de facturación antes de eTax, por favor digite el último número de documento emitido.</div>
</div>

<div class="form-group col-md-12">
  <label for="default_vat_code">Tipo de IVA por defecto</label>
  <select class="form-control select-search" id="default_vat_code" name="default_vat_code">
    @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
      <option value="{{ $tipo['code'] }}" attr-iva="{{ $tipo['percentage'] }}" porcentaje="{{ $tipo['percentage'] }}" {{ $tipo['code'] == 'B103' ? 'selected' : ''}} >{{ $tipo['name'] }}</option>
    @endforeach
  </select>
  <div class="description">Seleccione el tipo de venta y tarifa por defecto que desea utilizar en su facturación.</div>
</div>  

<div class="form-group col-md-12">
  <label for="default_category_producto_code">Categoría de declaración por defecto</label>
  <select class="form-control" id="default_category_producto_code" name="default_category_producto_code">
    @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $category )
      <option value="{{ $category['id'] }}" posibles="{{ $category['open_codes'] }}" >{{ $category['name'] }}</option>
    @endforeach
  </select>
  <div class="description">Seleccione la categoría de declaración por defecto donde irán sus ventas, y facilite el proceso de automatización de su declaración.</div>
</div>  

<div class="form-group col-md-6">
  <label for="card_retention">% Retención Tarjetas</label>
  <select class="form-control" id="card_retention" name="card_retention" >
    <option value="0" {{ @$company->card_retention == 0 ? 'selected' : '' }}>0%</option>
    <option value="3" {{ @$company->card_retention == 3 ? 'selected' : '' }}>3%</option>
    <option value="6" {{ @$company->card_retention == 6 ? 'selected' : '' }}>6%</option>
  </select>
</div>

    
<div class="form-group col-md-6">
  <label for="default_currency">Tipo de moneda por defecto</label>
  <select class="form-control" name="default_currency" id="default_currency" >
    <option value="crc" selected>CRC</option>
    <option value="usd" >USD</option>
  </select>
</div>

<div class="form-group col-md-12">
  <label for="default_invoice_notes">Notas por defecto</label>
  <textarea class="form-control" name="default_invoice_notes" id="default_invoice_notes" maxlength="190" rows="2" style="resize: none;">{{ @$company->default_invoice_notes }}</textarea>
</div>

<div class="btn-holder">
  <button type="button" class="btn btn-primary btn-prev" onclick="toggleStep('step2');">Paso anterior</button>
  <button type="button" class="btn btn-primary btn-next" onclick="toggleStep('step5');">Siguiente paso</button>
</div>

<script>
  
$(document).ready(function(){

    $("#default_vat_code").change(function(){
      filterCategoriasHacienda();
    });

    function filterCategoriasHacienda(){
        var codigoIVA = $('#default_vat_code :selected').val();
        $('#default_category_producto_code option').hide();
        var tipoProducto = 0;
        $("#default_category_producto_code option").each(function(){
          var posibles = $(this).attr('posibles').split(",");
        	if(posibles.includes(codigoIVA)){
            	$(this).show();
            	if( !tipoProducto ){
                tipoProducto = $(this).val();
              }
            }
        });
        $('#default_category_producto_code').val( tipoProducto ).change();
    }
  
    filterCategoriasHacienda();

});
      
</script>

<style>
.wizard-popup .select2-container {
    z-index: 999999;
}
</style>