<div class="popup" id="referencia-popup">
  <div class="popup-container referencia-factura-form form-row">
  	<div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('referencia-popup');"> <i class="fa fa-times" aria-hidden="true"></i> </div>

    <div class="form-group col-md-12">
      <h3>
        Línea de referencia
      </h3>
    </div>
                
    <input type="hidden" class="form-control" id="referencia-lnum" value="">
    <input type="hidden" class="form-control" id="referencia_id" value="">
    
    <div class="form-group col-md-12">
        <label for="ref-docType">Tipos de referencia</label>
        <select class="form-control" id="referencia-docType" >
        	<option value="01">01 - Factura electrónica</option>
        	<option value="02">02 - Nota de débito</option>
        	<option value="03">03 - Nota de crédito</option>
        	<option value="04">04 - Tiquete electrónico</option>
        	<option value="05">05 - Nota de despacho</option>
        	<option value="06">06 - Contrato</option>
        	<option value="07">07 - Procedimiento</option>
        	<option value="08">08 - Comprobante emitido en contingencia</option>
        	<option value="09">09 - Devolución de mercaderia</option>
        	<option value="10">10 - Sustituye factura rechazada por Hacienda</option>
        	<option value="11">11 - Sustituye factura rechazada por el receptor del comprobante</option>
        	<option value="12">12 - Sustituye factura de exportación</option>
        	<option value="13">13 - Facturación de mes vencido</option>
        	<option value="99">99 - Otros</option>
        </select>
    </div>
    
    <div class="form-group col-md-12">
        <label for="referencia-number">Número</label>
        <input type="text" class="form-control" value="" id="referencia-number" maxlength="12">
    </div>
    
    
    <div class="form-group col-md-12">
        <label for="ref-code">Código</label>
        <select class="form-control" id="referencia-code" >
        	<option value="01">01 - Anula documento de referencia</option>
        	<option value="02">02 - Corrige texto de documento</option>
        	<option value="03">03 - Corrige monto</option>
        	<option value="04">04 - Referencia a otro documento</option>
        	<option value="05">05 - Sustituye comprobante provisional por contingencia</option>
        	<option value="99">99 - Otros</option>
        </select>
    </div>

    <div class="form-group col-md-12">
      <div class="botones-agregar">
        <div onclick="agregarEditarReferencia();" class="btn btn-dark m-1 ml-0">Confirmar linea</div>
        <div onclick="cerrarPopup('referencia-popup');cancelarEdicionReferencia();" class="btn btn-danger m-1">Cancelar</div>
      </div>
      <div class="botones-editar">
        <div onclick="cerrarPopup('referencia-popup');agregarEditarReferencia();" class="btn btn-dark m-1 ml-0">Confirmar edición</div>
        <div onclick="cerrarPopup('referencia-popup');cancelarEdicionReferencia();" class="btn btn-danger m-1">Cancelar</div>
      </div>
    </div>

  </div>
</div>
