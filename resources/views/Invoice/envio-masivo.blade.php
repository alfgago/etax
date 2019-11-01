<div class="popup" id="enviar-emitidas-popup">
  <div class="popup-container item-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('enviar-emitidas-popup');"> <i class="fa fa-times" aria-hidden="true"></i> </div>
			
		
		<div class="form-group col-md-12">
	    <h3>
	      Envio masivo por Excel
	    </h3>
	  </div>
		
		<form method="POST" action="/facturas-emitidas/enviarExcel" enctype="multipart/form-data" class="toggle-xlsx">
			
			@csrf
		
		  <div class="form-group col-md-12">
			  <div class="descripcion">
			  	Las columnas requeridas para importación de facturas son: <br>
			  	
			  	<ul class="columnas-excel">
			  		<li>cedula empresa*</li>
					 <li>tipo documento*</li>
					 <li>consecutivo*</li>
					 <li>descripcion</li>
					 <li>fecha emision</li>
					 <li>fecha vencimiento</li>
					 <li>codigo actividad</li>
					 <li>nombre emisor</li>
					 <li>tipo identificacion emisor</li>
					 <li>identificacion emisor</li>
					 <li>provincia emisor</li>
					 <li>canton emisor</li>
					 <li>distrito emisor</li>
					 <li>direccion emisor</li>
					 <li>correo emisor</li>
					 <li>nombre receptor*</li>
					 <li>tipo identificacion receptor*</li>
					 <li>identificacion receptor*</li>
					 <li>provincia receptor*</li>
					 <li>canton receptor*</li>
					 <li>distrito receptor*</li>
					 <li>direccion receptor*</li>
					 <li>correo receptor*</li>
					 <li>condicion venta</li>
					 <li>plazo credito</li>
					 <li>medio pago</li>
					 <li>tipo linea*</li>
					 <li>numero linea*</li>
					 <li>cantidad</li>
					 <li>unidad medida</li>
					 <li>detalle</li>
					 <li>precio unitario</li>
					 <li>monto total</li>
					 <li>monto descuento</li>
					 <li>naturaleza descuento</li>
					 <li>subtotal</li>
					 <li>codigo impuesto</li>
					 <li>codigo tarifa</li>
					 <li>tarifa impuesto</li>
					 <li>monto impuesto</li>
					 <li>exento</li>
					 <li>tipo documento exoneracion</li>
					 <li>numero documento exoneracion</li>
					 <li>nombre institucion exoneracion</li>
					 <li>fecha emision exoneracion</li>
					 <li>porcentaje exoneracion</li>
					 <li>monto exoneracion</li>
					 <li>monto total linea</li>
					 <li>tipo cargo</li>
					 <li>identidad tercero</li>
					 <li>nombre tercero</li>
					 <li>detalle cargo</li>
					 <li>porcentaje cartago</li>
					 <li>monto cargo</li>
					 <li>codigo moneda</li>
					 <li>tipo cambio</li>
					 <li>total serv gravados</li>
					 <li>total serv exentos</li>
					 <li>total serv exonerados</li>
					 <li>total mercancias gravadas</li>
					 <li>total mercancias exentas</li>
					 <li>total mercancias exonerada</li>
					 <li>total gravado</li>
					 <li>total exento</li>
					 <li>total exonerado</li>
					 <li>total venta</li>
					 <li>total descuentos</li>
					 <li>total venta neta</li>
					 <li>total impuesto</li>
					 <li>total otros cargos</li>
					 <li>total comprobante</li>
					 <li>tipo documento referencia</li>
					 <li>numero documento referencia</li>
					 <li>fecha emision referencia</li>
					 <li>codigo nota</li>
					 <li>razon nota</li>
			  	</ul>
			  	* El orden puede variar, mantener nombres de columnas. Debe utilizar una fila por cada linea de factura.
			  	* Máximo de 2500 lineas por archivo.
			  	<br>
			  	<a href="{{asset('assets/files/PlantillaEnvioMasivo.xlsx')}}" class="btn btn-link" title="Descargar plantilla" download><i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar plantilla</a>
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
		
	
  </div>
</div>
