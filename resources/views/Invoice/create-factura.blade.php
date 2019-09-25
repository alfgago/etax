@extends('layouts/app')

<?php 
  $tipoHacienda = "FE";
  $titulo = "Factura electrónica";
  if($document_type == "01"){
    $tipoHacienda = "FE";
    $titulo = "Factura electrónica";
  }else if($document_type == "04"){
    $tipoHacienda = "TE";
    $titulo = "Tiquete electrónico";
  }else if($document_type == "08"){
      $tipoHacienda = "FEC";
      $titulo = "Factura electrónica de compra";
  }else if($document_type == "09"){
      $tipoHacienda = "FEE";
      $titulo = "Factura electrónica de exportación";
  }else if($document_type == "02"){
      $tipoHacienda = "ND";
      $titulo = "Nota de débito";
  }
if(!isset($document_type)){
    $document_type = '01';
}

$company = currentCompanyModel();

?>
@section('title') 
  Enviar {{ $titulo }}
@endsection

@section('content') 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-emitidas/send">

          @csrf
          
          <input type="hidden" id="current-index" value="0">
          <input type="hidden" class="form-control" id="default_product_category" value="{{$company->default_product_category}}">
          <input type="hidden" class="form-control" id="default_vat_code" value="{{$company->default_vat_code}}">

          <div class="form-row">
            <div class="col-md">
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-row">
                    @if( $document_type != "08"  )
                    <div class="form-group col-md-12">
                      <h3>
                        Cliente
                      </h3>
                      <div onclick="abrirPopup('nuevo-cliente-popup');" class="btn btn-agregar btn-agregar-cliente">Nuevo cliente</div>
                    </div>  
                    
                    <div class="form-group col-md-12 with-button">
                      <label for="cliente">Seleccione el cliente</label>
                      
                      @if( count(currentCompanyModel()->clients) < 4000 )
                        <select class="form-control select-search" name="client_id" id="client_id" placeholder="" @if(@$document_type !== '04') required @endif>
                          <option value='' selected>-- Seleccione un cliente --</option>
                          @foreach ( currentCompanyModel()->clients as $cliente )
                            @if( @$cliente->canInvoice($document_type) )
                              <option value="{{ $cliente->id }}" >{{ $cliente->toString() }}</option>
                            @endif
                          @endforeach
                        </select>
                      @else
                        <select class="form-control select-search-many" name="client_id" id="client_id" placeholder="" required>
                          <option value='' selected>-- Seleccione un cliente --</option>
                        </select>
                        <script>
                          <?php 
                            $clientesJson = json_encode(currentCompanyModel()->clientsForSelect2()); 
                          ?>
                          var data = '<?php echo $clientesJson ?>';
                          var jsonData = JSON.parse(data);
                          $(document).ready(function() { 

                            $('.select-search-many').select2({
                              data: jsonData
                            });
                            
                          });
                        </script>
                      @endif
                      
                      @if($document_type == "04")
                        <div class="description">El cliente no es obligatorio para los tiquetes electrónicos.</div>
                      @endif
                    </div>
                    @else
                      <div class="form-group col-md-12">
                        <h3>
                          Proveedor
                        </h3>
                        <div onclick="abrirPopup('nuevo-proveedor-popup');" class="btn btn-agregar btn-agregar-cliente">Nuevo proveedor</div>
                      </div>

                      <div class="form-group col-md-12 with-button">
                        <label for="provider_id">Seleccione el proveedor</label>
                        <select class="form-control select-search" name="provider_id" id="provider_id" placeholder="" required>
                          <option value='' selected>-- Seleccione un proveedor --</option>
                          @foreach ( $company->providers as $proveedor )
                            <option value="{{ $proveedor->id }}" >{{ $proveedor->id_number }} - {{ $proveedor->first_name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group col-md-12">
                        <h3>
                          Cliente
                        </h3>
                      </div>
                      <div class="form-group col-md-12">
                        <label for="actual">Empresa actual</label>
                        <input disabled readonly class="form-control" type="text" value="{{ $company->id_number . ' - ' . $company->name.' '.$company->last_name.' '.$company->last_name2 }}">
                      </div>
                    @endif
                    <div class="form-group col-md-12">
                      <label for="send_email">Enviar copia a:</label>
                      <input type="email" class="form-control" name="send_email" id="send_email" value="">
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <h3>
                        Moneda
                      </h3>
                    </div>
      
                    <div class="form-group col-md-4">
                      <label for="currency">Divisa</label>
                      <select class="form-control" name="currency" id="moneda" required>
                        <option value="CRC" data-rate="1" {{$company->default_currency == 'CRC' ? 'selected' : ''}}>CRC</option>
                        <option value="USD" data-rate="{{$rate}}" {{$company->default_currency == 'USD' ? 'selected' : ''}}>USD</option>
                      </select>
                    </div>
      
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" data-rates="{{$rate}}" name="currency_rate" id="tipo_cambio" value="{{$company->default_currency == 'USD' ? $rate : '1.00'}}"required>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="form-row">    
                <div class="form-group col-md-12">
                  <h3>
                    Detalle
                  </h3>
                </div>
                
                @if( @$document_type == '08' )
                  <div class="form-group col-md-12">
                    <label for="tipo_compra">Tipo de compra</label>
                    <select class="form-control" id="tipo_compra" name="tipo_compra">
                        <option value="local">Compra local</option>
                        <option value="import">Importación de servicios</option>
                    </select>
                  </div>
                @endif
                
                 <div class="form-group col-md-4">
                  <label for="subtotal">Subtotal </label>
                  <input type="text" class="form-control" name="subtotal" id="subtotal" placeholder="" readonly="true" required>
                </div>
    
                <div class="form-group col-md-4">
                  <label for="iva_amount">Monto IVA </label>
                  <input type="text" class="form-control" name="iva_amount" id="monto_iva" placeholder="" readonly="true" required>
                </div>

                <div class="form-group col-md-4 hidden" id="total_iva_devuelto-cont">
                  <label for="total">IVA Devuelto</label>
                  <input type="text" class="form-control total" name="total_iva_devuelto" id="total_iva_devuelto" placeholder="" readonly="true" required>
                </div>

                <div class="form-group col-md-4 hidden" id="total_iva_exonerado-cont">
                  <label for="total">IVA Exonerado</label>
                  <input type="text" class="form-control total" name="total_iva_exonerado" id="total_iva_exonerado" placeholder="" readonly="true" required>
                </div>

                <div class="form-group col-md-4 hidden" id="total_otros_cargos-cont">
                  <label for="total">Otros cargos</label>
                  <input type="text" class="form-control total" name="total_otros_cargos" id="total_otros_cargos" placeholder="" readonly="true" required>
                </div>
    
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" required>
                </div>
                
                <div class="form-group col-md-12">
                  <div onclick="abrirPopup('linea-popup');" class="btn btn-dark btn-agregar">Agregar línea</div>
                  <div onclick="abrirPopup('otros-popup');" class="btn btn-dark btn-agregar btn-otroscargos">Agregar otros cargos</div>
                </div>
    
              </div>
              
            </div>
            
            <div class="col-md offset-md-1">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <h3>
                    Datos generales
                  </h3>
                </div>
                  
                  <div class="form-group col-md-6">
                    <label for="document_number">Número de documento</label>
                    <input type="text" class="form-control" name="document_number" id="document_number" value="{{$document_number}}" required readonly="readonly">
                  </div>
  
                  <div class="form-group col-md-6 not-required">
                    <label for="document_key">Clave de factura</label>
                    <input type="text" class="form-control" name="document_key" id="document_key" value="{{$document_key}}" required readonly="readonly">
                  </div>

                  <div class="form-group col-md-4 hidden">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4 hidden">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" class="form-control input-hora" name="hora" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('g:i A') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->addDays(3)->format('d/m/Y') }}" maxlength="10">
                      <span class="input-group-addon">
                        <i class="icon-regular i-Calendar-4"></i>
                      </span>
                    </div>
                  </div>

                  <div class="form-group col-md-12">
                      <label for="payment_type">Actividad Comercial</label>
                      <div class="input-group">
                          <select id="commercial_activity" name="commercial_activity" class="form-control" required>
                              @foreach ( $arrayActividades as $actividad )
                                  <option value="{{ $actividad->codigo }}">{{ $actividad->codigo }} - {{ $actividad->actividad }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  
                  <div class="form-group col-md-6">
                    <label for="sale_condition">Condición de venta</label>
                    <div class="input-group">
                      <select id="condicion_venta" name="sale_condition" class="form-control" required>
                        <option selected value="01">Contado</option>
                        <option value="02">Crédito</option>
                        <option value="03">Consignación</option>
                        <option value="04">Apartado</option>
                        <option value="05">Arrendamiento con opción de compra</option>
                        <option value="06">Arrendamiento en función financiera</option>
                        <option value="07">Servicios prestados al Estado a crédito</option>
                        <option value="08">Pago del servicios prestado al Estado</option>
                        <option value="99">Otros</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="payment_type">Método de pago</label>
                    <div class="input-group">
                      <select id="medio_pago" name="payment_type" class="form-control" required onchange="calcularTotalFactura();">
                        <option value="01" selected>Efectivo</option>
                        <option value="02">Tarjeta</option>
                        <option value="03">Cheque</option>
                        <option value="04">Transferencia-Depósito Bancario</option>
                        <option value="05">Recaudado por terceros</option>
                        <option value="99">Otros</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group col-md-12" id="field-retencion" style="display:none; !important">
                    <label for="retention_percent">Porcentaje de retención</label>
                    <div class="input-group">
                      <select id="retention_percent" name="retention_percent" class="form-control" required>
                        <option value="6" selected>6%</option>
                        <option value="3">3%</option>
                        <option value="0" >Sin retención</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-6 not-required">
                    <label for="other_reference">Referencia</label>
                    <input type="text" class="form-control" name="other_reference" id="referencia" value="" >
                  </div>

                  <div class="form-group col-md-6 not-required">
                    <label for="buy_order">Orden de compra</label>
                    <input type="text" class="form-control" name="buy_order" id="orden_compra" value="" >
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Notas</label>
                    <textarea class="form-control" name="notas" id="notas"  maxlength="200" placeholder="" rows="2" style="resize: none;"> {{ @currentCompanyModel()->default_invoice_notes }}  </textarea>
                  </div>

              </div>
              
            </div>
          </div>

          <div class="form-row" id="tabla-items-factura" style="display: none;">  

            <div class="form-group col-md-12">
              <h3>
                Líneas de factura
              </h3>
            </div>
            
            <div class="form-group col-md-12" >
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Cant.</th>
                    <th>Unidad</th>
                    <th>Precio unitario</th>
                    <th>Tipo IVA</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="form-row" id="tabla-otroscargos-factura" style="display: none;">  

            <div class="form-group col-md-12">
              <h3>
                Otros cargos
              </h3>
            </div>
            
            <div class="form-group col-md-12" >
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Receptor</th>
                    <th>Detalle</th>
                    <th>Monto del cargo</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                
                </tbody>
              </table>
            </div>
          </div>
          
        @include( 'Invoice.form-otros-cargos' )
        @include('Invoice.form-linea')
        @if($document_type != "08")
          @include('Invoice.form-nuevo-cliente')
        @else
          @include('Bill.form-nuevo-proveedor')
        @endif
        
            <input type="text" hidden value="{{ $document_type }}" name="document_type" id="document_type">
          <div class="btn-holder hidden">
           
            <button id="btn-submit" type="submit" class="btn btn-primary">Enviar factura electrónica</button>
          </div>

      </form>
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button id='btn-submit-fe' onclick="$('#btn-submit').click();" class="btn btn-primary">Enviar factura electrónica</button>
@endsection

@section('footer-scripts')

<script>
$(document).ready(function(){
  @if( @$document_type != '08' )
    if( $('#default_vat_code').length ){
      $('#tipo_iva').val( $('#default_vat_code').val() ).change();
    }else{
      $('#tipo_iva').val( 'B103' ).change();
    }
  @else
    $('#tipo_iva').val( 'B003' ).change();
  @endif

  @if (@$document_type == '09')
    $('#tipo_iva').val( 'B150' ).change();
  @endif

  $('#moneda').change(function() {
    if ($(this).val() == 'USD') {
      $('#tipo_cambio').val($('#tipo_cambio').data('rates'))
    } else {
      $('#tipo_cambio').val('1.00')
    }
  });
});

function toggleRetencion() {
  var metodo = $("#medio_pago").val();
  if( metodo == '02' ){
    $("#field-retencion").show();
  }else {
    $("#field-retencion").hide();
  }
}

</script>

@endsection
