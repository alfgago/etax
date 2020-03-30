<div class="popup" id="importar-aceptacion-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-recibidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i>  </div>
			
		<div class="form-group col-md-12">
	    <h3>
	      Importar facturas recibidas para aceptación o rechazo
	    </h3>
	  </div>
		
	<form method="POST" action="/facturas-recibidas/importarXML" enctype="multipart/form-data" >
				
			<?php echo csrf_field(); ?>	
				
			<div class="form-group col-md-12">
		    <div class="descripcion">
		    	Arrastre los archivos XML de Hacienda que le hayan sido emitidos. <br>
		    	
		    	* Utilice el formato 4.2 si su factura fue emitida antes del 1 de Julio del 2018.
		    	</div>
		  </div>
		  
		  <div class="form-group col-md-12">
		    <label for="formato_xml">Formato de los XML</label>
		    <select class="form-control" name="formato_xml" id="formato_xml">
		      <option value="43">4.3</option>
		      <option value="42">4.2</option>
		    </select>
			</div>
		  
		  <div class="form-group col-md-12">
		    <label for="xmls[]">Archivos</label>  
				<div class="">
					<div class="fallback">
				      <input name="xmls[]" type="file" multiple="true" accept=".xml">
				  </div>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importar XML</button>
			
		</form>
  </div>
</div><?php /**PATH /var/www/resources/views/Bill/import-accepts.blade.php ENDPATH**/ ?>