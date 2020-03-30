<div class="popup" id="importar-recibidas-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-recibidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i>  </div>
			
		<div class="form-group col-md-12">
	    <h3>
	      Importar facturas recibidas
	    </h3>
	  </div>
		
		<div class="form-group col-md-12">
	    <label for="tipo_archivo">Tipo de archivo</label>
	    <select class="form-control" name="tipo_archivo" id="tipo_archivo"  onchange="toggleTiposImportacion()" required>
	      <option value="xlsx">Excel</option>
	      <option value="xml">XML de Hacienda</option>
	    </select>
	  </div>
		
		<form method="POST" action="/facturas-recibidas/importarExcel" enctype="multipart/form-data" class="toggle-xlsx">
			
			<?php echo csrf_field(); ?>
		  
		  <div class="form-group col-md-12">
			  <div class="descripcion">
			  	Las columnas requeridas para importación de facturas son: <br>
			  	
			  	<ul class="cols-excel">
			  		<li>TipoDocumento</li>
			  		<li>ConsecutivoComprobante</li>
			  		<li>Moneda</li>
			  		<li>TipoCambio</li>
			  		<li>FechaEmision</li>
			  		<li>CodigoProveedor</li>
			  		<li>NombreProveedor</li>
			  		<li>TipoIdentificacion</li>
			  		<li>IdentificacionReceptor</li>
			  		<li>CorreoReceptor</li>
			  		<li>CondicionVenta</li>
			  		<li>MetodoPago</li>
			  		<li>NumeroLinea</li>
			  		<li>DetalleProducto</li>
			  		<li>CodigoProducto</li>
			  		<li>Cantidad</li>
			  		<li>UnidadMedicion</li>
			  		<li>PrecioUnitario</li>
			  		<li>SubtotalLinea</li>
			  		<li>MontoDescuento</li>
			  		<li>PorcIdentificacionPlena</li>
			  		<li>MontoIVA</li>
			  		<li>TotalLinea</li>
			  		<li>TotalDocumento</li>
			  		<li>CodigoIVAEtax</li>
			  		<li>ActividadComercial</li>
			  	</ul>
			  	* El orden puede variar, pero debe mantener nombres de columnas. Debe utilizar una fila por cada linea de factura.
			  	* Máximo de 2500 lineas por archivo.
			  	<br>
			  	<a href="<?php echo e(asset('assets/files/PlantillaLineasFacturaRecibida.xlsx')); ?>" class="btn btn-link" title="Descargar plantilla" download><i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar plantilla</a>
			  </div>
		  </div>
		  
		  <div class="form-group col-md-12">
		    <label for="archivo">Archivo</label>  
				<div class="">
					<div class="fallback">
				      <input name="archivo" type="file" multiple="false" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
				  </div>
				  <small class="descripcion">Hasta 2500 líneas por archivo.</small>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importar facturas</button>
	</form>
	
			<div class="form-group col-md-12 toggle-xml">
		      <div class="descripcion">
			    	Arrastre los archivos XML de Hacienda que haya generado desde sistemas de facturación externos. <br>
			    	
			    	* Utilice el formato 4.2 si su factura fue emitida antes del 1 de Julio del 2019.
		    	</div>
		  
					<form method="POST" action="/facturas-recibidas/importarXML" class="dropzone toggle-xml" id="xml-dropzone" enctype="multipart/form-data" >
						<?php echo csrf_field(); ?>		 
					  <div class="form-group col-md-12">
					  	<label for="file"></label>  
							<div class="">
								<div class="fallback">
							      <input name="file" type="file" multiple="true" accept=".xml">
							  </div>
							</div>
						</div>
						
					</form>
		  </div>
  </div>
</div>
<script type="text/javascript">

		Dropzone.autoDiscover = false;
        $(document).ready(function(){

            var baseUrl = "<?php echo e(secure_url('/')); ?>";	
            var token = "<?php echo e(Session::token()); ?>";
			        
            $("#xml-dropzone").dropzone({
                paramName: 'file',
                //url: baseUrl+"/facturas-recibidas/importarXML",
                url: "/facturas-recibidas/importarXML",
                params: {
                    _token: token
                },
                method : "post",
                dictDefaultMessage: "Arrastre sus archivos XML aquí o presione para seleccionarlos.",
                clickable: true,
                error: function (file, response) {
		            $(file.previewElement).find('.dz-error-message').text(response);
		            $(file.previewElement).addClass('dz-error');
                	if(response == 'El documento no le pertenece a su empresa actual.' ){
                    	toastr.error(response+' Archivo: '+file['name']);
                	}
                	if(response == 'Error: El mes de la factura ya fue cerrado.'){
                    	toastr.error(response+' Archivo: '+file['name']);
                	}
                	if(response == 'Se ha detectado un error en el tipo de archivo subido.' ){
                    	toastr.error(response+' Archivo: '+file['name']);
                	}
                },
                success: function(file, response) {
		            $(file.previewElement).addClass('dz-complete');
		            $(file.previewElement).addClass('dz-success ');
                	if(response == 'Se importo un tiquete sin cedula de receptor.'){
		            	$(file.previewElement).addClass('dz-warning');
                    	toastr.warning('Se importo un tiquete sin cédula de receptor. Archivo: '+file['name']);
                	}
                }
            });
        });
</script><?php /**PATH /var/www/resources/views/Bill/import.blade.php ENDPATH**/ ?>