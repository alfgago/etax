<div class="row form-container">
  <div class="col-md-12">
    <div class="row form-container">
      <div class="col-md-6">
        <b>Combrobante: </b>{{ $bill->document_number }} <br>
        <b>Emisor: </b>{{ @$bill->provider->fullname }} <br>
        <b>Moneda: </b>{{ $bill->currency }} <br>
      </div>
      <div class="col-md-6">
        <b>Subtotal: </b>{{ number_format( $bill->subtotal, 2 ) }} <br>
        <b>Monto IVA: </b>{{ number_format( $bill->iva_amount, 2 ) }} <br>
        <b>Total: </b>{{ number_format( $bill->total, 2 ) }} 
      </div>
    </div>
    <hr>
  </div>
  
  <div class="col-md-12">
    <form method="POST" action="/facturas-recibidas/guardar-validar">
      @csrf
      @method('post') 
      <input type="text" name="bill" id="bill" hidden value="{{@$bill->id }}"/>
      <div class="form-row">
        <div class="form-group col-md-12">
            <b>Actividad Comercial:</b>
            <select class="form-control" name="actividad_comercial" id="actividad_comercial" placeholder="Seleccione una actividad Comercial" required >
                
                @foreach($commercial_activities as $commercial)
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
              <th>Tipo IVA</th>
              <th>Categoría</th>
              <th>Identificación plena</th>
            </tr>
          </thead>
          <tbody>
             <tr>
               <th colspan="7">Selección masiva: </th>
               <td>
                  <div class="input-validate-iva">
                    <select class="form-control iva_type_all"  placeholder="Seleccione un código eTax" id="iva_type_all"  >
                      <?php
                        $preselectos = array();
                        foreach($company->soportados as $soportado){
                          $preselectos[] = $soportado->id;
                        }
                      ?>
                      @if(@$company->soportados[0]->id)
                        @foreach ( \App\CodigoIvaSoportado::where('hidden', false)->get() as $tipo )
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="all_tipo_iva_select {{ (in_array($tipo['id'], $preselectos) == false) ? 'hidden' : '' }}"  is_identificacion_plena="{{ $tipo['is_identificacion_plena'] }}>{{ $tipo['name'] }}</option>
                        @endforeach
                        <option class="all_mostrarTodos" value="1">Mostrar Todos</option>
                      @else
                        @foreach ( \App\CodigoIvaSoportado::where('hidden', false)->get() as $tipo )
                        <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="all_tipo_iva_select"  is_identificacion_plena="{{ $tipo['is_identificacion_plena'] }}">{{ $tipo['name'] }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
               </td>
               <td>
                 <div class="input-validate-iva">
                   <select class="form-control product_type_all"  placeholder="Seleccione una categoría de hacienda" >
                      <option value="0">-- Seleccione --</option>
                      @foreach($categoria_productos as $cat)
                         <option value="{{@$cat->id}}" codigo="{{ @$cat->bill_iva_code }}" posibles="{{@$cat->open_codes}}" >{{@$cat->name}}</option>
                      @endforeach
                    </select>
                  </div>
               </td>
               <td>
                 <div class="input-validate-iva">
                    <select class="form-control porc_identificacion_plena_all"  >
                      <option value="0">-- Seleccione --</option>
                      <option value="13" >13%</option>
                      <option value="1" >1%</option>
                      <option value="2" >2%</option>
                      <option value="4" >4%</option>
                    </select>
                  </div>
               </td>
             </tr>
             @foreach ( $bill->items as $item )
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
                    <select class="form-control iva_type" name="items[{{ $loop->index }}][iva_type]" placeholder="Seleccione un código eTax" required >
                        @if(@$company->soportados[0]->id)
                        @foreach ( \App\CodigoIvaSoportado::where('hidden', false)->get() as $tipo )
                            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ (in_array($tipo['id'], $preselectos) == false) ? 'hidden' : '' }}"  >{{ $tipo['name'] }}</option>
                        @endforeach
                        <option class="mostrarTodos" value="1">Mostrar Todos</option>
                      @else
                        @foreach ( \App\CodigoIvaSoportado::where('hidden', false)->get() as $tipo )
                        <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select"  >{{ $tipo['name'] }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-validate-iva">
                    <select curr="{{ $item->product_type }}" class="form-control product_type" name="items[{{ $loop->index }}][product_type]" placeholder="Seleccione una categoría de hacienda" required>
                        @foreach($categoria_productos as $cat)
                            <option value="{{@$cat->id}}" codigo="{{ @$cat->bill_iva_code }}" posibles="{{@$cat->open_codes}}" {{ $item->product_type == @$cat->id ? 'selected' : '' }}>{{@$cat->name}}</option>
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

    $(".product_type_all").change(function(){
        var product_type  = $(this).val(); 
        if(product_type != 0){
        $(".product_type").val(product_type);
      }
    });
    $(".iva_type_all").change(function(){
        var iva_type  = $(this).val(); 
        if(iva_type != 0){
          $(".iva_type").val(iva_type);
        }
    });
    $(".porc_identificacion_plena_all").change(function(){
        var porc_identificacion_plena  = $(this).val(); 
        if(porc_identificacion_plena != 0){
          $(".porc_identificacion_plena").val(porc_identificacion_plena);
        }
    });

    $(".iva_type").change(function(){
      var codigoIVA = $(this).find(':selected').val();
      var parent = $(this).parents('tr');
      parent.find('.product_type option').hide();
      var tipoProducto = 0;
      parent.find(".product_type option").each(function(){
        var posibles = $(this).attr('posibles').split(",");
      	if(posibles.includes(codigoIVA)){
          $(this).show();
          if( !tipoProducto ){
            tipoProducto = $(this).val();
          }
        }
      });
      parent.find('.product_type').val( tipoProducto ).change();
          
      var identificacion = $(this).find(':selected').attr('identificacion');
      if(identificacion == 1){
          parent.find(".porc_identificacion_plena").removeClass("hidden");
          parent.find(".porc_identificacion_plena").attr("required");
      }else{
          parent.find(".porc_identificacion_plena").addClass("hidden");
          parent.find(".porc_identificacion_plena").removeAttr("required");
      }
    });
    
    <?php if( !$bill->is_code_validated ){ ?>
      $(".iva_type").change();
    <?php }else{ ?>
      $('.iva_type').change();
      $(".product_type").each(function(){
        $(this).val($(this).attr('curr')).change();
      });
    <?php } ?>
});
      

$( document ).ready(function() {
      $('.iva_type_all').on('change', function(e){
        if($('.iva_type_all').val() == 1){
           $.each($('.all_tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $('.all_mostrarTodos').addClass("hidden");
           $('.iva_type_all').val("");
        }
      });
    }); 

    $( document ).ready(function() {
      $('.iva_type').on('change', function(e){
        if($('.iva_type').val() == 1){
           $.each($('.tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $('.mostrarTodos').addClass("hidden");
           $('.iva_type').val("");
        }
      });
    }); 

</script>