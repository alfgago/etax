<div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="send_email">Envio:</label>
                     <select name="envio_factura" class="form-control" id="envio_factura">
                          <option value="1">Si</option>
                          <option value="0">No</option>
                  </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="send_date">Factura recurrente:</label>
                     <select name="factura_recurrente" class="form-control" id="select_factura_recurrente">
                          <option value="0">No</option>
                          <option value="1">Si</option>
                  </select>
                  </div>
              </div>
              <div class="form-row" id="div_factura_recurrente">
                 <div class="form-group col-md-6">
                  <label for="subtotal">Recurrencia: </label>
                  <select name="frecuencia" class="form-control" id="frecuencia">
                          <option value="0">Nunca</option>
                          <option value="1">Semanal</option>
                          <option value="2">Quincenal</option>
                          <option value="3">Mensual</option>
                          <option value="4">Bimensual</option>
                          <option value="5">Trimestral</option>
                          <option value="6">Cuatrimestral</option>
                          <option value="7">Semestral</option>
                          <option value="8">Anual</option>
                          <option value="9">Cantidad de días</option>
                  </select>
                <input type="text" name="opciones_recurrencia" id="opciones_recurrencia"  class="form-control">
                </div>
                <div class="form-group col-md-6" id="div_opcciones_frecuencia">
                  
                </div>
              </div>