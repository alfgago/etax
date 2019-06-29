<div class="popup" id="importar-mensajes-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-recibidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i>  </div>
			
		<div class="form-group col-md-12">
	    <h3>
	      Importar mensajes de aceptación de otros proveedores
	    </h3>
	  </div>
		
	<form method="POST" action="/facturas-recibidas/importarXMLAceptacion" enctype="multipart/form-data" >
				
			@csrf	
				
			<div class="form-group col-md-12">
		    <div class="descripcion">
		    	Arrastre los archivos XML de Hacienda de aceptación que haya recibido. <br>
		    	* Únicamente para XML en versión 4.3
		    	</div>
		  </div>
		  
		  <div class="form-group col-md-12 hidden">
		    <label for="formato_xml">Formato de los XML</label>
		    <input name="formato_xml" type="hidden" value="43">
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
</div>