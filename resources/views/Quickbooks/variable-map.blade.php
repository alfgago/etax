@extends('layouts/app')

@section('title') 
    Mapeo de Variables - QuickBooks
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
      <?php $company = currentCompanyModel(); ?>
      <form method="POST" action="/quickbooks/guardar-variables/">

        @csrf

        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Condiciones de venta
            </h3>
          </div>
          
          <div class="form-group col-md-12">
            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
              <thead class="thead-dark">
                <tr>
                  <th>QuickBooks</th>
                  <th>eTax</th>
                </tr>
              </thead>
              <tbody>
                <tr class="item-tabla item-index-0">
                  <td>Valor por defecto</td>
                  <td>
                    <select id="condicion_venta" name="sale_condition" class="form-control" required>
                      <option value="01">Contado</option>
                      <option value="02">Crédito</option>
                      <option value="03">Consignación</option>
                      <option value="04">Apartado</option>
                      <option value="05">Arrendamiento con opción de compra</option>
                      <option value="06">Arrendamiento en función financiera</option>
                      <option value="99">Otros</option>
                    </select>
                  </td>
                </tr>
                @foreach($terms as $term)
                  <tr class="item-tabla item-index-{{ $loop->index }}">
                    <td>{{ $term->Name }}</td>
                    <td>
                      <select id="condicion_venta" name="sale_condition" class="form-control" required>
                        <option value="01">Contado</option>
                        <option value="02">Crédito</option>
                        <option value="03">Consignación</option>
                        <option value="04">Apartado</option>
                        <option value="05">Arrendamiento con opción de compra</option>
                        <option value="06">Arrendamiento en función financiera</option>
                        <option value="99">Otros</option>
                      </select>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="form-group col-md-12">
            <h3>
              Métodos de pago
            </h3>
          </div>
          
          <div class="form-group col-md-12">
            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
              <thead class="thead-dark">
                <tr>
                  <th>QuickBooks</th>
                  <th>eTax</th>
                </tr>
              </thead>
              <tbody>
                <tr class="item-tabla item-index-0">
                  <td>Valor por defecto</td>
                  <td>
                    <select id="medio_pago" name="payment_type" class="form-control" required>
                      <option value="01" selected>Efectivo</option>
                      <option value="02">Tarjeta</option>
                      <option value="03">Cheque</option>
                      <option value="04">Transferencia-Depósito Bancario</option>
                      <option value="05">Recaudado por terceros</option>
                      <option value="99">Otros</option>
                    </select>
                  </td>
                </tr>
                @foreach($paymentMethods as $method)
                  <tr class="item-tabla item-index-{{ $loop->index }}">
                    <td>{{ $method->Name }}</td>
                    <td>
                      <select id="medio_pago" name="payment_type" class="form-control" required>
                        <option value="01" selected>Efectivo</option>
                        <option value="02">Tarjeta</option>
                        <option value="03">Cheque</option>
                        <option value="04">Transferencia-Depósito Bancario</option>
                        <option value="05">Recaudado por terceros</option>
                        <option value="99">Otros</option>
                      </select>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <div class="form-group col-md-12">
            <h3>
              Códigos de impuesto
            </h3>
          </div>
          
          <div class="form-group col-md-12">
            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
              <thead class="thead-dark">
                <tr>
                  <th>QuickBooks</th>
                  <th>Código eTax</th>
                  <th>Categoría Hacienda</th>
                </tr>
              </thead>
              <tbody>
                <tr class="item-tabla item-index-0">
                  <td>Valor por defecto</td>
                  <td>
                    <select class="form-control select-search tipo_iva" id="tipo_iva">
                      <?php
                        $codigoDefecto = $company->default_vat_code;
                        $preselectos = array();
                        foreach($company->repercutidos as $repercutido){
                          $preselectos[] = $repercutido->id;
                        }
                      ?>
                      @if(@$company->repercutidos[0]->id)
                        @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ (in_array($tipo['id'], $preselectos) == false) ? 'hidden' : '' }}" 
                            {{ $tipo['code'] == $codigoDefecto ? 'selected' : '' }}  >{{ $tipo['name'] }}</option>
                        @endforeach
                        <option class="mostrarTodos" value="1">Mostrar Todos</option>
                      @else
                        @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                         @if(@$document_type == '09')
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ $tipo['code'] !== 'B150' ? 'hidden' : '' }}"
                            {{ $tipo['code'] == $codigoDefecto ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                         @else
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select"
                            {{ $tipo['code'] == $codigoDefecto ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                         @endif
                        @endforeach
                      @endif
                    </select>
                  </td>
                  <td>
                    <select class="form-control" id="tipo_producto" >
                      @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $tipo )
                        <option value="{{ $tipo['id'] }}" codigo="{{ $tipo['invoice_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" >{{ $tipo['name'] }}</option>
                      @endforeach
                    </select>
                  </td>
                </tr>
                @foreach($taxRates as $tax)
                  <tr class="item-tabla item-index-{{ $loop->index }}">
                    <td>{{ $tax->Name }}</td>
                    <td>
                      <select class="form-control select-search tipo_iva" id="tipo_iva">
                        @if(@$company->repercutidos[0]->id)
                          @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                              <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ (in_array($tipo['id'], $preselectos) == false) ? 'hidden' : '' }}"  
                            {{ $tipo['code'] == $codigoDefecto ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                          @endforeach
                          <option class="mostrarTodos" value="1">Mostrar Todos</option>
                        @else
                          @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                           @if(@$document_type == '09')
                              <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ $tipo['code'] !== 'B150' ? 'hidden' : '' }}" 
                              {{ $tipo['code'] == $codigoDefecto ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                           @else
                              <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select" 
                              {{ $tipo['code'] == $codigoDefecto ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                           @endif
                          @endforeach
                        @endif
                      </select>
                    </td>
                    <td>
                      <select class="form-control" id="tipo_producto" >
                        @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $tipo )
                          <option value="{{ $tipo['id'] }}" codigo="{{ $tipo['invoice_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" >{{ $tipo['name'] }}</option>
                        @endforeach
                      </select>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        <button id="btn-submit" class="btn btn-primary" type="submit" class="">Guardar variables</button>
        
      </form> 
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar variables</button>
@endsection 

@section('footer-scripts')
<script>
    $( document ).ready(function() {
      $('#tipo_iva').on('select2:selecting', function(e){
        var selectBox = document.getElementById("tipo_iva");
        if(e.params.args.data.id == 1){
           $.each($('.tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $('.mostrarTodos').addClass("hidden");
           e.preventDefault();
        }

      });

    }); 
</script>
@endsection
