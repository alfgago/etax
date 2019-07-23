<div class="row form-container">
  <div class="col-md-12">
    <div class="row form-container">
      <div class="col-md-6">
        <b>Combrobante: </b>{{ $data['bills']->document_number }} <br>
        <b>Emisor: </b>{{ @$data['bills']->provider->fullname }} <br>
        <b>Moneda: </b>{{ $data['bills']->currency }} <br>
      </div>
      <div class="col-md-6">
        <b>Subtotal: </b>{{ number_format( $data['bills']->subtotal, 2 ) }} <br>
        <b>Monto IVA: </b>{{ number_format( $data['bills']->iva_amount, 2 ) }} <br>
        <b>Total: </b>{{ number_format( $data['bills']->total, 2 ) }} 
      </div>
    </div>
    <hr>
  </div>
  <div class="col-md-12">
    <form method="POST" action="/facturas-recibidas/guardar-validar">
      @csrf
      @method('post') 
      <input type="text" name="bill" id="bill" hidden value="{{@$data['bills']->id }}"/>
      <div class="form-row">
        <div class="form-group col-md-12">
            <b>Actividad Comercial:</b>
            <select class="form-control" name="actividad_comercial" id="actividad_comercial" placeholder="Seleccione una actividad Comercial" required >
                
                @foreach($data['commercial_activities'] as $commercial)
                    <option value="{{@$commercial->codigo}}">{{@$commercial->actividad}}</option>
                @endforeach
                
                <option value="0">No asignar actividad comercial</option>
                
            </select>
        </div>
      </div>
      <div class="form-row">
        <table id="dataTable" class="table table-striped table-bordered validate-table" cellspacing="0" width="100%" >
          <thead class="thead-dark">
            <tr>
              <th>Nombre</th>
              <th>Cant.</th>
              <th>Unidad</th>
              <th>Precio unitario</th>
              <th>Subtotal</th>
              <th>IVA</th>
              <th>Total</th>
              <th>Categoría</th>
              <th>Tipo IVA</th>
              <th>Identificación plena</th>
            </tr>
          </thead>
          <tbody>
             @foreach ( $data['bills']->items as $item )
             <tr class="item-tabla item-index-{{ $loop->index }}" index="{{ $loop->index }}" attr-num="{{ $loop->index }}" id="item-tabla-{{ $loop->index }}">
                <td>
                  <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{@$item->id}}">
                  {{ $item->name }}
                </td>
                <td>{{ $item->item_count }}</td>
                <td>{{ \App\Variables::getUnidadMedicionName($item->measure_unit) }}</td>
                <td>{{ number_format($item->unit_price,2) }} </td>
                <td>{{ number_format($item->subtotal,2) }}</td>
                <td>{{ number_format($item->iva_amount,2) }}</td>
                <td>{{ number_format($item->total,2) }} </td>
                <td>
                  <div class="input-validate-iva">
                    <select class="form-control product_type" name="items[{{ $loop->index }}][product_type]" placeholder="Seleccione una categoría de hacienda" required>
                        @foreach($data['categoria_productos'] as $categoria_productos)
                            <option value="{{@$categoria_productos->id}}" codigo="{{ $categoria_productos->bill_iva_code }}" posibles="{{@$categoria_productos->open_codes}}" {{ $item->product_type == @$categoria_productos->id ? 'selected' : '' }}>{{@$categoria_productos->name}}</option>
                        @endforeach
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-validate-iva">
                    <select class="form-control iva_type" name="items[{{ $loop->index }}][iva_type]" placeholder="Seleccione un código eTax" required >
                        @foreach($data['codigos_etax'] as $codigos_etax)
                            <option value="{{@$codigos_etax->code}}" identificacion="{{@$codigos_etax->is_identificacion_plena}}" {{ $item->iva_type == @$codigos_etax->code ? 'selected' : '' }}>{{@$codigos_etax->name}}</option>
                        @endforeach
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-validate-iva">
                    <select class="form-control porc_identificacion_plena" name="items[{{ $loop->index }}][porc_identificacion_plena]" >
                        <option value="13" {{ $item->porc_identificacion_plena == 13 ? 'selected' : '' }}>13%</option>
                        <option value="1" {{ $item->porc_identificacion_plena == 1 ? 'selected' : '' }}>1%</option>
                        <option value="2" {{ $item->porc_identificacion_plena == 2 ? 'selected' : '' }}>2%</option>
                        <option value="4" {{ $item->porc_identificacion_plena == 4 ? 'selected' : '' }}>4%</option>
                    </select>
                  </div>
                </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>                      
      <button id="btn-submit" type="submit" class="btn btn-primary">Confirmar validación</button>
    </form>
  </div>
</div>

<script>
  
$(document).ready(function(){

    $(".product_type").change(function(){
      var parent = $(this).parents('tr');
      var posibles = $(this).find(':selected').attr('posibles');
      var arrPosibles = posibles.split(",");
      var currTipo = parent.find('.iva_type').val();
      var isAvailable = false;
      var tipo;
      parent.find('.iva_type option').hide();
      for( tipo of arrPosibles ) {
        parent.find('.iva_type option[value='+tipo+']').show();
        if(currTipo == tipo){ isAvailable = true; }
      }
      if(!isAvailable){
        var tipoIVA = $(this).find(':selected').attr('codigo');
        parent.find('.iva_type').val( tipoIVA ).change();
      }
    });

    $(".iva_type").change(function(){
      var parent = $(this).parents('tr');
      var identificacion = $(this).find('.iva_type :selected').attr('identificacion');
      if(identificacion == 1){
          $(this).find(".porc_identificacion_plena").val('1');
          $(this).find(".porc_identificacion_plena").removeClass("hidden");
          $(this).find(".porc_identificacion_plena").attr("required");
          $(this).find(".porc_identificacion_plena").val('1');
      }else{
          $(this).find(".porc_identificacion_plena").addClass("hidden");
          $(this).find(".porc_identificacion_plena").val("0");
          $(this).find(".porc_identificacion_plena").removeAttr("required");
      }
    });
    
    $(".product_type").change();

});
      
</script>