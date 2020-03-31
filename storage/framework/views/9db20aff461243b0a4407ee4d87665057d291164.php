<div class="popup" id="otros-popup">
  <div class="popup-container otros-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('otros-popup');"> <i class="fa fa-times" aria-hidden="true"></i> </div>

    <div class="form-group col-md-12">
      <h3>
        Línea de otros cargos
      </h3>
    </div>
                
    <input type="hidden" class="form-control" id="otros-lnum" value="">
    <input type="hidden" class="form-control" id="otros_id" value="">
    
    <div class="form-group col-md-12">
        <label for="otros-document_type">Tipo de otros cargos</label>
        <select class="form-control" id="otros-document_type" onclick="toggleCobroTercero();" >
        	<option value="01">Contribución parafiscal</option>
        	<option value="02">Timbre de la Cruz Roja</option>
        	<option value="03">Timbre de Benemérito Cuerpo de Bomberos de Costa Rica</option>
        	<option value="04">Cobro de un tercero</option>
        	<option value="05">Costos de Exportación</option>
        	<option value="06">Impuesto de Servicio 10%</option>
        	<option value="07">Timbre de Colegios Profesionales</option>
        	<option value="99">Otros Cargos</option>
        </select>
    </div>
    
    <div class="form-group col-md-6 show-tipo-06">
        <label for="otros-provider_id_number">Número de cédula</label>
        <input type="text" class="form-control" value="" id="otros-provider_id_number" maxlength="12">
    </div>
    
    <div class="form-group col-md-6 show-tipo-06">
        <label for="otros-provider_name">Nombre del receptor</label>
        <input type="text" class="form-control" value="" id="otros-provider_name" maxlength="100">
    </div>
    
    <div class="form-group col-md-12">
        <label for="otros-description">Detalle</label>
        <textarea type="text" class="form-control" value="" id="otros-description" maxlength="100" style="height: 4rem;"></textarea>
    </div>
    
    <div class="form-group col-md-12 hidden">
      <label for="otros-percentage">Porcentaje</label>
      <input type="number" min="0" class="form-control" id="otros-percentage" value="10" number >
    </div>
    
    <div class="form-group col-md-12">
      <label for="otros-amount">Monto del cargo</label>
      <input type="number" min="0" class="form-control" id="otros-amount" value="0" number >
    </div>

    <div class="form-group col-md-12">
      <div class="botones-agregar">
        <div onclick="agregarEditarOtros();" class="btn btn-dark m-1 ml-0">Confirmar linea</div>
        <div onclick="cerrarPopup('otros-popup');cancelarEdicionOtros();" class="btn btn-danger m-1">Cancelar</div>
      </div>
      <div class="botones-editar">
        <div onclick="cerrarPopup('otros-popup');agregarEditarOtros();" class="btn btn-dark m-1 ml-0">Confirmar edición</div>
        <div onclick="cerrarPopup('otros-popup');cancelarEdicionOtros();" class="btn btn-danger m-1">Cancelar</div>
      </div>
    </div>

  </div>
</div>

<script>
	function toggleCobroTercero(){
		if( $("#otros-document_type" ).val() == '04'){
			$('.show-tipo-06').show();
		}else{
			$('.show-tipo-06').hide();
		}
	}
</script>

<style>
	.show-tipo-06 {
		display: none;
	}
</style><?php /**PATH /var/www/resources/views/Invoice/form-otros-cargos.blade.php ENDPATH**/ ?>