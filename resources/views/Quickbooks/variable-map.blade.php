@extends('layouts/app')

@section('title') 
    Mapeo de Variables - QuickBooks
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
        
      <form method="POST" action="/productos">

        @csrf

        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Mapeo de Variables - QuickBooks
            </h3>
          </div>

          <?php $company = currentCompanyModel(); ?>
          
            <div class="form-group col-md-12">
                <label>Tipo</label>
                <select type="text" class="form-control" id="select-tipo">
                    <option value="1">Condición de venta</option>
                    <option value="2">Método de pago</option>
                    <option value="3">Código de impuestos</option>
                </select>
            </div>
            
            <div class="form-group col-md-12">
                <label>Variable QuickBooks</label>
                <select type="text" class="form-control" id="select-tipo">
                    <option value="0" selected>-- Seleccione una variable de QuickBooks --</option>
                    <option>Condición de venta</option>
                    <option>Método de pago</option>
                    <option>Código de impuestos</option>
                </select>
            </div>
            
            <div class="form-group col-md-12">
                <label>Variable eTax</label>
                <select type="text" class="form-control" id="select-tipo">
                    <option value="0" selected>-- Seleccione una variable de eTax --</option>
                    <option>Condición de venta</option>
                    <option>Método de pago</option>
                    <option>Código de impuestos</option>
                </select>
            </div>
                
            <div class="form-group col-md-12">
              <div onclick="agregarNuevaLinea();" class="btn btn-dark btn-agregar">Agregar variable</div>
            </div>
          
          <div class="form-row" id="tabla-items-factura" style="width: 100%;">  
            <div class="form-group col-md-12">
              <h3>
                Variables mapeadas
              </h3>
            </div>
  
            <div class="form-group col-md-12">
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>Tipo</th>
                    <th>QuickBooks</th>
                    <th>eTax</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                    <tr class="item-tabla item-index-{{ @$loop->index }}" index="{{ @$loop->index }}" attr-num="{{ @$loop->index }}" id="item-tabla-{{ @$loop->index }}">
                        <td>Condición de venta</td>
                        <td>Ejemplo Condición QB1</td>
                        <td>Crédito</td>
                        <td class='acciones'>
                            <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ @$loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                        </td>
                    </tr>
                    <tr class="item-tabla item-index-{{ @$loop->index }}" index="{{ @$loop->index }}" attr-num="{{ @$loop->index }}" id="item-tabla-{{ @$loop->index }}">
                        <td>Método de pago</td>
                        <td>Ejemplo Método QB1</td>
                        <td>Transferencia</td>
                        <td class='acciones'>
                            <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ @$loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                        </td>
                    </tr>
                    <tr class="item-tabla item-index-{{ @$loop->index }}" index="{{ @$loop->index }}" attr-num="{{ @$loop->index }}" id="item-tabla-{{ @$loop->index }}">
                        <td>Método de pago</td>
                        <td>Ejemplo Método QB2</td>
                        <td>Cheque</td>
                        <td class='acciones'>
                            <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ @$loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                        </td>
                    </tr>
                    <tr class="item-tabla item-index-{{ @$loop->index }}" index="{{ @$loop->index }}" attr-num="{{ @$loop->index }}" id="item-tabla-{{ @$loop->index }}">
                        <td>Código de impuesto</td>
                        <td>Ejemplo Código QB1</td>
                        <td>S103 - Ventas locales de servicios con derecho a crédito al 13%</td>
                        <td class='acciones'>
                            <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ @$loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                        </td>
                    </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <button id="btn-submit" type="submit" class="hidden">Guardar</button>
        
      </form> 
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar variables</button>
@endsection 

@section('footer-scripts')
  <script>
    
  </script>
@endsection
