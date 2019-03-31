<div class="popup" id="importar-popup">
  <div class="popup-container item-cliente-form form-row">
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
		      <option value="xlsx" selected>Excel</option>
		    </select>
		  </div>
		  
		  <div class="form-group col-md-12">
			  <div class="description">
			  	Las columnas requeridas para importación de clientes son: <br>
			  	
			  	<ul class="cols-excel">
			  		<li>Codigo</li>
			  		<li>Nombre</li>
			  		<li>PrimerApellido</li>
			  		<li>TipoPersona</li>
			  		<li>Identificacion</li>
			  		<li>Correo</li>
			  		<li>Pais</li>
			  		<li>Provincia</li>
			  		<li>Canton</li>
			  		<li>Distrito</li>
			  		<li>Barrio</li>
			  		<li>Direccion</li>
			  		<li>AreaTel</li>
			  		<li>Telefono</li>
			  		<li>CorreosCopia</li>
			  		<li>Exento</li>
			  		<li>EmisorReceptor</li>
			  	</ul>
			  	* El orden puede variar, mantener nombres de columnas. Debe utilizar una fila por cada cliente.
			  	<br>
			  	<a href="{{asset('assets/files/PlantillaClientes.xlsx')}}" class="btn btn-link" title="Descargar plantilla" download><i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar plantilla</a>
			  </div>
		  </div>
		  
		  <div class="form-group col-md-12">
		    <label for="archivo">Archivo</label>  
				<div class="">
					<div class="fallback">
				      <input name="archivo" type="file" multiple="false">
				  </div>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importar clientes</button>
			
		</form>
  </div>
</div>