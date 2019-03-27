<div class="popup" id="importar-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-popup');"> <i class="nav-icon i-Close"></i>  </div>
		<form method="POST" action="/clientes/importar" enctype="multipart/form-data">
			
			@csrf
			
			<div class="form-group col-md-12">
		    <h3>
		      Importar clientes
		    </h3>
		  </div>
			
			<div class="form-group col-md-12">
		    <label for="tipo_archivo">Tipo de archivo</label>
		    <select class="form-control" name="tipo_archivo" id="tipo_archivo" required>
		      <option value="xlsx">Excel</option>
		      <option value="xml">XML de Hacienda</option>
		    </select>
		  </div>
		  
		  <div class="form-group col-md-12">
			  <div class="description">
			  	Las columnas requeridas para importación de clientes son: <br>
			  	Codigo, TipoPersona, Identificacion, Nombre, PrimerApellido, SegundoApellido, Correo, CorreosCopia, Pais, Provincia, Canton, Distrito, Barrio, Direccion, AreaTel, Telefono, Exento, y EmisorReceptor.
			  	<a href="#">Descargar plantilla</a>
			  </div>
		  </div>
		  
		  <div class="form-group col-md-12">
		    <label for="archivo">Archivo</label>  
				<div class="dropzone">
					<div class="fallback">
				      <input name="archivo" type="file" multiple="false">
				  </div>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importar clientes</button>
			
		</form>
  </div>
</div>