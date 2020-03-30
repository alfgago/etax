<div class="popup" id="importar-sm-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-emitidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i> </div>
			
	  <div class="form-group col-md-12">
		    <div class="form-group col-md-12">
		      <h3>
		        Importación de Excel para envíos SM
		      </h3>
		    </div>
		    
			<form method="POST" action="/sm/importar-excel" enctype="multipart/form-data" class=" mt-3">
										
			  <?php echo csrf_field(); ?>
	    	  <div class="form-group col-md-12">
			    <label for="fileType">Tipo de documento para enviar</label>
			    <select class="form-control" name="fileType" id="fileType" required>
			      <option value="01">Facturas</option>
			      <option value="03">Notas de crédito</option>
			    </select>
			  </div>
				<div class="form-group col-md-12">
			    <label for="archivo">Excel SM Seguros para envio masivo</label>  
					<div style="margin-bottom: 1rem; width: 400px;">
						
						<div class="fallback">
					      <input name="archivo" type="file" multiple="false" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
					  </div>
					  <small class="descripcion">Hasta 5000 líneas por archivo.</small>
					</div>
				</div>
					<button type="submit" class="btn btn-primary">Importar y enviar facturas</button>
			</form>
	  </div>
  </div>
</div>
<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/SMInvoice/import.blade.php ENDPATH**/ ?>