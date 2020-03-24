@extends('layouts/app')

@section('title') 
    Mapeo de Variables - QuickBooks
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
      <?php 
        $company = $qb->company; 
        $qbConditions = $qb->conditions_json;
        $qbMethods = $qb->payment_methods_json;
        if( isset($qb->taxes_json) ) {
          $qbTipoIva = $qb->taxes_json['tipo_iva'];
          $qbTipoProducto = $qb->taxes_json['tipo_producto'];
        }
      ?>
      <form method="POST" action="/quickbooks/guardar-variables/{{$qb->id}}">
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
                  <?php 
                    $selectedCondition = @$qbConditions['default'];
                  ?>
                  <td>Valor por defecto</td>
                  <td>
                    <select id="condicion_venta" name="sale_condition[default]" class="form-control" required>
                        <option value="01" {{ $selectedCondition == "01" ? 'selected' : '' }}>Contado</option>
                        <option value="02" {{ $selectedCondition == "02" ? 'selected' : '' }}>Crédito</option>
                        <option value="03" {{ $selectedCondition == "03" ? 'selected' : '' }}>Consignación</option>
                        <option value="04" {{ $selectedCondition == "04" ? 'selected' : '' }}>Apartado</option>
                        <option value="05" {{ $selectedCondition == "05" ? 'selected' : '' }}>Arrendamiento con opción de compra</option>
                        <option value="06" {{ $selectedCondition == "06" ? 'selected' : '' }}>Arrendamiento en función financiera</option>
                        <option value="99" {{ $selectedCondition == "99" ? 'selected' : '' }}>Otros</option>
                    </select>
                  </td>
                </tr>
                @foreach($terms as $term)
                  <tr class="item-tabla item-index-{{ $loop->index }}">
                    <?php 
                      $condId = $term->Id;
                      $selectedCondition = @$qbConditions[$condId];
                    ?>
                    <td>{{ $term->Name }}</td>
                    <td>
                      <select id="condicion_venta" name="sale_condition[{{$term->Id}}]" class="form-control" required> 
                        <option value="01" {{ $selectedCondition == "01" ? 'selected' : '' }}>Contado</option>
                        <option value="02" {{ $selectedCondition == "02" ? 'selected' : '' }}>Crédito</option>
                        <option value="03" {{ $selectedCondition == "03" ? 'selected' : '' }}>Consignación</option>
                        <option value="04" {{ $selectedCondition == "04" ? 'selected' : '' }}>Apartado</option>
                        <option value="05" {{ $selectedCondition == "05" ? 'selected' : '' }}>Arrendamiento con opción de compra</option>
                        <option value="06" {{ $selectedCondition == "06" ? 'selected' : '' }}>Arrendamiento en función financiera</option>
                        <option value="99" {{ $selectedCondition == "99" ? 'selected' : '' }}>Otros</option>
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
                  <?php 
                    $selectedCondition = @$qbMethods['default'];
                  ?>
                  <td>
                    <select id="medio_pago" name="payment_type[default]" class="form-control" required>
                      <option value="01" {{ $selectedCondition == "01" ? 'selected' : '' }}>Efectivo</option>
                      <option value="02" {{ $selectedCondition == "02" ? 'selected' : '' }}>Tarjeta</option>
                      <option value="03" {{ $selectedCondition == "03" ? 'selected' : '' }}>Cheque</option>
                      <option value="04" {{ $selectedCondition == "04" ? 'selected' : '' }}>Transferencia-Depósito Bancario</option>
                      <option value="05" {{ $selectedCondition == "05" ? 'selected' : '' }}>Recaudado por terceros</option>
                      <option value="99" {{ $selectedCondition == "99" ? 'selected' : '' }}>Otros</option>
                    </select>
                  </td>
                </tr>
                @foreach($paymentMethods as $method)
                  <tr class="item-tabla item-index-{{ $loop->index }}">
                    <?php 
                      $condId = $method->Id;
                      $selectedCondition = @$qbMethods[$condId];
                    ?>
                    <td>{{ $method->Name }}</td>
                    <td>
                      <select id="medio_pago" name="payment_type[{{$method->Id}}]" class="form-control" required>
                        <option value="01" {{ $selectedCondition == "01" ? 'selected' : '' }}>Efectivo</option>
                        <option value="02" {{ $selectedCondition == "02" ? 'selected' : '' }}>Tarjeta</option>
                        <option value="03" {{ $selectedCondition == "03" ? 'selected' : '' }}>Cheque</option>
                        <option value="04" {{ $selectedCondition == "04" ? 'selected' : '' }}>Transferencia-Depósito Bancario</option>
                        <option value="05" {{ $selectedCondition == "05" ? 'selected' : '' }}>Recaudado por terceros</option>
                        <option value="99" {{ $selectedCondition == "99" ? 'selected' : '' }}>Otros</option>
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
                    <select class="form-control select-search tipo_iva" name="tipo_iva[default]" id="tipo_iva">
                      <?php
                        $selectedTipoIva = @$qbTipoIva['default'];
                        $selectedProdType = @$qbTipoProducto['default'];
                      ?>
                      @if(@$company->repercutidos[0]->id)
                        @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select" 
                             {{ $selectedTipoIva == $tipo['code'] ? 'selected' : '' }}>{{ $tipo['name'] }}</option>
                        @endforeach
                      @else
                        @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                         @if(@$document_type == '09')
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ $tipo['code'] !== 'B150' ? 'hidden' : '' }}"
                            {{ $tipo['code'] == $selectedTipoIva ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                         @else
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select"
                            {{ $tipo['code'] == $selectedTipoIva ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                         @endif
                        @endforeach
                      @endif
                    </select>
                  </td>
                  <td>
                    <select class="form-control" id="tipo_producto" name="tipo_producto[default]" >
                      @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $tipo )
                        <option value="{{ $tipo['id'] }}" codigo="{{ $tipo['invoice_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" 
                        {{ $selectedProdType == $tipo['id'] ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                      @endforeach
                    </select>
                  </td>
                </tr>
                @foreach($taxRates as $tax)
                  <tr class="item-tabla item-index-{{ $loop->index }}">
                      <?php
                        $condId = $tax->Id;
                        $selectedTipoIva = @$qbTipoIva[$condId];
                        $selectedProdType = @$qbTipoProducto[$condId];
                      ?>
                    <td>{{ $tax->Name }}</td>
                    <td>
                      <select class="form-control select-search tipo_iva" id="tipo_iva" name="tipo_iva[{{$tax->Id}}]">
                        @if(@$company->repercutidos[0]->id)
                          @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                              <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select"  
                              {{ $selectedTipoIva == $tipo['code'] ? 'selected' : '' }}>{{ $tipo['name'] }}</option>
                          @endforeach
                        @else
                          @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                           @if(@$document_type == '09')
                              <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ $tipo['code'] !== 'B150' ? 'hidden' : '' }}" 
                              {{ $tipo['code'] == $selectedTipoIva ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                           @else
                              <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select" 
                              {{ $tipo['code'] == $selectedTipoIva ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
                           @endif
                          @endforeach
                        @endif
                      </select>
                    </td>
                    <td>
                      <select class="form-control" id="tipo_producto" name="tipo_producto[{{$tax->Id}}]" >
                        @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $tipo )
                          <option value="{{ $tipo['id'] }}" codigo="{{ $tipo['invoice_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" 
                          {{ $selectedProdType == $tipo['id'] ? 'selected' : '' }} >{{ $tipo['name'] }}</option>
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
