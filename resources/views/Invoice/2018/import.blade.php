<div class="popup" id="importar-emitidas-2018-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-emitidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i> </div>
		<form method="POST" action="/facturas-emitidas/importar-2018" enctype="multipart/form-data">
			
			@csrf
			
			<div class="form-group col-md-12">
		    <h3>
		      Importar facturas para prorrata 2018
		    </h3>
		  </div>
			
			<div class="form-group col-md-12 hidden">
		    <label for="tipo_archivo">Tipo de archivo</label>
		    <select class="form-control" name="tipo_archivo" id="tipo_archivo" onchange="toggleTiposImportacion()" required>
		      <option value="xlsx">Excel</option>
		    </select>
		  </div>
		  
		  <div class="form-group col-md-12">
			  <div class="descripcion">
			  	Las columnas requeridas para importación de facturas son: <br>
			  	
			  	<ul class="cols-excel">
			  		<li>TipoDocumento</li>
			  		<li>ConsecutivoComprobante</li>
			  		<li>FechaEmision</li>
			  		<li>SubtotalLinea</li>
			  		<li>MontoIVA</li>
			  		<li>TotalLinea</li>
			  		<li>CodigoEtax</li>
			  	</ul>
			  	* El orden puede variar, mantener nombres de columnas. Debe utilizar una fila por cada linea de factura.
			  	* Máximo de 5000 lineas por archivo.
			  	<br>
			  	<a href="{{asset('assets/files/PlantillaLineasEmitidas2018.xlsx')}}" class="btn btn-link" title="Descargar plantilla" download><i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar plantilla</a>
			  </div>
		  </div>
			
			<button type="submit" class="btn btn-primary">Importar facturas</button>
			
		</form>
  </div>
</div>