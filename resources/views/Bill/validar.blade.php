
<div class="row form-container">
  <div class="col-md-12">
    <b>Combrobante: </b>{{ $data['bills']->document_number }} <br>
    <b>Emisor: </b>{{ @$data['bills']->provider->fullname }} <br>
    <b>Moneda: </b>{{ $data['bills']->currency }} <br>
    <b>Subtotal: </b>{{ number_format( $data['bills']->subtotal, 2 ) }} <br>
    <b>Monto IVA: </b>{{ number_format( $data['bills']->iva_amount, 2 ) }} <br>
    <b>Total: </b>{{ number_format( $data['bills']->total, 2 ) }} 
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
      <div class="form-group col-md-12">
          <b>Categoría de declaración:</b>
          <select class="form-control" name="category_product" id="category_product" placeholder="Seleccione una categoría de producto" required>

              @foreach($data['categoria_productos'] as $categoria_productos)
                  <option value="{{@$categoria_productos->id}}" posibles="{{@$categoria_productos->open_codes}}">{{@$categoria_productos->name}}</option>
              @endforeach
          </select>
      </div>
    </div>
      <div class="form-row">
      <div class="form-group col-md-12">
          <b>Código eTax:</b>
          <select class="form-control" name="codigo_etax" id="codigo_etax" placeholder="Seleccione un código eTax" required >
              @foreach($data['codigos_etax'] as $codigos_etax)
                  <option value="{{@$codigos_etax->code}}" identificacion="{{@$codigos_etax->is_identificacion_plena}}">{{@$codigos_etax->name}}</option>
              @endforeach
          </select>
          <input hidden  readonly id="impuesto_identificacion_plena" name="impuesto_identificacion_plena" value="0" required>
      </div>
    </div>
      <div class="form-row">
      <div  class="form-group col-md-12 hidden" id="identificacion_plena"  >
       <b>Seleccione % de impuesto al que saldrá la compra:</b>
          <select class="form-control" name="impuesto_identificacion_plena_select" id="impuesto_identificacion_plena_select" >
              <option value="1" >1%</option>
              <option value="2" >2%</option>
              <option value="4" >4%</option>
              <option selected value="13" >13%</option>
          </select>
      </div>      
    </div>                        
        <button id="btn-submit" type="submit" class="btn btn-dark">Confirmar</button>
    </form>
  </div>
</div>

<script>
  
$(document).ready(function(){

    $("#category_product").change(function(){
      var posibles = $('#category_product :selected').attr('posibles');
      var arrPosibles = posibles.split(",");
      var tipo;
      $('#codigo_etax option').hide();
      for( tipo of arrPosibles ) {
        $('#codigo_etax option[value='+tipo+']').show();
      }
    });

    $("#codigo_etax").change(function(){
      var identificacion = $('#codigo_etax :selected').attr('identificacion');
      if(identificacion == 1){
          $("#impuesto_identificacion_plena").val('1');
          $("#identificacion_plena").removeClass("hidden");
          $("#impuesto_identificacion_plena").attr("required");
          $("#impuesto_identificacion_plena_select").val('1');
      }else{
          $("#identificacion_plena").addClass("hidden");
          $("#impuesto_identificacion_plena").val("0");
          $("#impuesto_identificacion_plena").removeAttr("required");
      }
    });

    $("#impuesto_identificacion_plena_select").change(function(){
      var valor = $('#impuesto_identificacion_plena_select').val();
      $("#impuesto_identificacion_plena").val(valor);
    });

});
      
</script>