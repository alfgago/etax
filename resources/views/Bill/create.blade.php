@extends('layouts/app')

@section('title') 
  Crear factura recibida
@endsection

@section('content') 

<?php 
    $company = currentCompanyModel();
    $numero_doc = ((int)$company->document_number) + 1;
    $document_number = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
?>

<div class="row form-container">
  <div class="col-md-12">
                          
        <form method="POST" action="/facturas-recibidas">

          @csrf
          
          <input type="hidden" id="current-index" value="0">
          <input type="hidden" id="is-compra" value="1">


          <div class="form-row">
            <div class="col-md">
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-row">
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
                        <option value="CRC" selected>CRC</option>
                        <option value="USD">USD</option>
                      </select>
                    </div>
      
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" name="currency_rate" id="tipo_cambio" value="1.00" required>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="form-row">    
                <div class="form-group col-md-12">
                  <h3>
                    Datos de aceptación
                  </h3>
                </div>
                
                <div class="form-group col-md-3">
                  <label for="xml_schema">XML de factura</label>
                  <select class="form-control" name="xml_schema" id="xml_schema" required>
                    <option value="43" selected>4.3</option>
                    <option value="42">4.2</option>
                  </select>
                </div>
                  
                <div class="form-group col-md-9">
                    <label for="activity_company_verification">Actividad Comercial</label>
                    <div class="input-group">
                      <select id="activity_company_verification" name="activity_company_verification" class="form-control" required>
                          @foreach ( $arrayActividades as $actividad )
                              <option value="{{ $actividad->codigo }}" >{{ $actividad->codigo }} - {{ $actividad->actividad }}</option>
                          @endforeach
                      </select>
                    </div>
                </div>
                  
                <div class="form-group col-md-12 inline-form inline-checkbox">
                  <label for="accept_status">
                    <span>¿Aceptada desde otro proveedor?</span>
                    <input type="checkbox" class="form-control" id="accept_status" name="accept_status" onchange="toggleInfoAceptacion();" checked>
                  </label>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_condition">Condición de acceptación</label>
                    <select class="form-control" name="accept_iva_condition" id="accept_iva_condition">
                      <option value="01" selected>Genera crédito IVA</option>
                      <option value="02">Genera crédito parcial del IVA</option>
                      <option value="03">Bienes de capital</option>
                      <option value="04">Gasto corriente (no genera IVA)</option>
                      <option value="05">Proporcionalidad</option>
                    </select>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_acreditable">IVA acreditable</label>
                    <div class="input-group">
                      <input type="number" id="accept_iva_acreditable" name="accept_iva_acreditable" class="form-control" value="0" />
                    </div>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_gasto">IVA al gasto</label>
                    <div class="input-group">
                      <input type="number" id="accept_iva_gasto" name="accept_iva_gasto" class="form-control" value="0" />
                    </div>
                </div>
    
              </div>
              
              <div class="form-row">    
                <div class="form-group col-md-12">
                  <h3>
                    Detalle
                  </h3>
                </div>
                
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
    
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" >
                </div>
                
                <div class="form-group col-md-12">
                  <div onclick="abrirPopup('linea-popup');" class="btn btn-dark btn-agregar">Agregar línea</div>
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
                  <input type="text" class="form-control" name="document_number" id="document_number" value="" placeholder="" required>
                </div>

                <div class="form-group col-md-6 not-required">
                  <label for="document_key">Clave de factura</label>
                  <input type="text" class="form-control" name="document_key" id="document_key" value="" placeholder="" >
                </div>
                
                <div class="form-group col-md-4">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" class="form-control input-hora" name="hora" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('g:i A') }}">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{ \Carbon\Carbon::parse( now('America/Costa_Rica') )->format('d/m/Y') }}">
                      <span class="input-group-addon">
                        <i class="icon-regular i-Calendar-4"></i>
                      </span>
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
                        <option value="99">Otros</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="payment_type">Método de pago</label>
                    <div class="input-group">
                      <select id="medio_pago" name="payment_type" class="form-control"  required>
                        <option value="01" selected>Efectivo</option>
                        <option value="02">Tarjeta</option>
                        <option value="03">Cheque</option>
                        <option value="04">Transferencia-Depósito Bancario</option>
                        <option value="05">Recaudado por terceros</option>
                        <option value="99">Otros</option>
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
                    <textarea class="form-control" name="description" id="notas" placeholder=""></textarea>
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
          
          @include( 'Bill.form-otros-cargos' )
          @include( 'Bill.form-linea' )
          @include( 'Bill.form-nuevo-proveedor' )

          <button id="btn-submit" type="submit" class="hidden">Guardar factura</button>

        </form>
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar factura</button>
@endsection 

@section('footer-scripts')

<script>
$(document).ready(function(){
  $('#tipo_iva').val('B003').change();
});
$(function () {
    $("#accept_iva_acreditable").keydown(function () {
        // Save old value.
        if (!$(this).val() || parseInt($(this).val()) >= 0)
            $(this).data("old", $(this).val());
    });
    $("#accept_iva_acreditable").keyup(function () {
        // Check correct, else revert back to old value.
        if (!$(this).val() || parseInt($(this).val()) >= 0)
            ;
        else
            $(this).val($(this).data("old"));
    });
    $("#accept_iva_gasto").keydown(function () {
        // Save old value.
        if (!$(this).val() || parseInt($(this).val()) >= 0)
            $(this).data("old", $(this).val());
    });
    $("#accept_iva_gasto").keyup(function () {
        // Check correct, else revert back to old value.
        if (!$(this).val() || parseInt($(this).val()) >= 0)
            ;
        else
            $(this).val($(this).data("old"));
    });
    $("#tipo_cambio").keydown(function () {
        // Save old value.
        if (!$(this).val() || parseInt($(this).val()) >= 0)
            $(this).data("old", $(this).val());
    });
    $("#tipo_cambio").keyup(function () {
        // Check correct, else revert back to old value.
        if (!$(this).val() || parseInt($(this).val()) >= 0)
            ;
        else
            $(this).val($(this).data("old"));
    });
});

</script>


@endsection
