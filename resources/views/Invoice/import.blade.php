<div class="popup" id="importar-emitidas-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-emitidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i> </div>
			
		
		<div class="form-group col-md-12">
	    <h3>
	      Importar facturas emitidas
	    </h3>
	  </div>
		
		<div class="form-group col-md-12">
	    <label for="tipo_archivo">Tipo de archivo</label>
	    <select class="form-control" name="tipo_archivo" id="tipo_archivo" onchange="toggleTiposImportacion()" required>
	      <option value="xlsx">Excel</option>
	      <option value="xml">XML de Hacienda</option>
	    </select>
	  </div>
		  
		<form method="POST" action="/facturas-emitidas/importarExcel" enctype="multipart/form-data" class="toggle-xlsx">
			
			@csrf
		
		  <div class="form-group col-md-12">
			  <div class="descripcion">
			  	Las columnas requeridas para importación de facturas son: <br>
			  	
			  	<ul class="cols-excel">
			  		<li>TipoDocumento</li>
			  		<li>ConsecutivoComprobante</li>
			  		<li>Moneda</li>
			  		<li>TipoCambio</li>
			  		<li>FechaEmision</li>
			  		<li>CodigoCliente</li>
			  		<li>NombreCliente</li>
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
			  		<li>MontoIVA</li>
			  		<li>TotalLinea</li>
			  		<li>TotalDocumento</li>
			  		<li>CodigoIVAEtax</li>
			  		<li>ActividadComercial</li>
			  	</ul>
			  	* El orden puede variar, mantener nombres de columnas. Debe utilizar una fila por cada linea de factura.
			  	* Máximo de 5000 lineas por archivo.
			  	<br>
			  	<a href="{{asset('assets/files/PlantillaLineasFacturaEmitida.xlsx')}}" class="btn btn-link" title="Descargar plantilla" download><i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar plantilla</a>
			  </div>
		  </div>
		  
		  <div class="form-group col-md-12">
		    <label for="archivo">Archivo</label>  
				<div class="">
					<div class="fallback">
				      <input name="archivo" type="file" multiple="false" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
				  </div>
				  <small class="descripcion">Hasta 5000 líneas por archivo.</small>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importar facturas</button>
		</form>	
			
	<form method="POST" action="/facturas-emitidas/importarXML" enctype="multipart/form-data" class="toggle-xml">
			
			@csrf
			
			<div class="form-group col-md-12 toggle-xml">
		    <div class="descripcion">
		    	Arrastre los archivos XML de Hacienda que haya generado desde sistemas de facturación externos. <br>
		    	
		    	* Utilice el formato 4.2 si su factura fue emitida antes del 1 de Julio del 2019.
		    	</div>
		  </div>
		  
		  <div class="form-group col-md-12">
		    <label for="formato_xml">Formato de XML</label>
		    <select class="form-control" name="formato_xml" id="formato_xml">
		      <option value="4.2">4.2</option>
		      <option value="4.3">4.3</option>
		    </select>
			</div>
		  
		  <div class="form-group col-md-12">
		    <label for="xmls[]">Archivos</label>  
				<div class="">
					<div class="fallback">
				      <input name="xmls[]" type="file" multiple="true" accept=".xml">
				  </div>
				  <small class="descripcion">Puede subir hasta 10 archivos XML por intento.</small>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importar facturas</button>
			
		</form>
  </div>
</div>
