<div class="popup" id="importar-recibidas-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-recibidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i>  </div>
		<form method="POST" action="/facturas-recibidas/importar" enctype="multipart/form-data">
			
			@csrf
			
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
		  
		  <div class="form-group col-md-12 toggle-xlsx">
			  <div class="descripcion">
			  	Las columnas requeridas para importación de facturas son: <br>
			  	
			  	<ul class="cols-excel">
			  		<li>TipoDocumento</li>
			  		<li>ConsecutivoComprobante</li>
			  		<li>IdMoneda</li>
			  		<li>TipoCambio</li>
			  		<li>FechaEmision</li>
			  		<li>CodigoProveedor</li>
			  		<li>NombreProveedor</li>
			  		<li>TipoIdentificacion</li>
			  		<li>IdentificacionReceptor</li>
			  		<li>CondicionVenta</li>
			  		<li>MetodoPago</li>
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
			  		<li>CodigoEtax</li>
			  	</ul>
			  	* El orden puede variar, pero debe mantener nombres de columnas. Debe utilizar una fila por cada linea de factura.
			  	* Máximo de 2500 lineas por archivo.
			  	<br>
			  	<a href="{{asset('assets/files/PlantillaLineasFacturaRecibida.xlsx')}}" class="btn btn-link" title="Descargar plantilla" download><i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar plantilla</a>
			  </div>
		  </div>
		  
		  <div class="form-group col-md-12 toggle-xlsx">
		    <label for="archivo">Archivo</label>  
				<div class="">
					<div class="fallback">
				      <input name="archivo" type="file" multiple="false">
				  </div>
				</div>
			</div>
			
			
			<div class="form-group col-md-12 toggle-xml">
		    <div class="descripcion">
		    	Arrastre los archivos XML de Hacienda que haya generado desde sistemas de facturación externos. <br>
		    	
		    	* Utilice el formato 4.2 si su factura fue emitida antes del 1 de Julio del 2018.
		    	</div>
		  </div>
		  
		  <div class="form-group col-md-12 toggle-xml">
		    <label for="formato_xml">Formato de XML</label>
		    <select class="form-control" name="formato_xml" id="formato_xml">
		      <option value="4.2">4.2</option>
		      <option value="4.3">4.3</option>
		    </select>
			</div>
		  
		  <div class="form-group col-md-12 toggle-xml">
		    <label for="xmls[]">Archivos</label>  
				<div class="">
					<div class="fallback">
				      <input name="xmls[]" type="file" multiple="false">
				  </div>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importar facturas</button>
			
		</form>
  </div>
</div>