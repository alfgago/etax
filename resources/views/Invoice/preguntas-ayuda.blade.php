<div class="ayuda-cont col-md-12" style="display:none;">
  <div class="form-row ">
    
    <div class="form-group col-md-4 pregs p1-1 end-0">
      <label>¿Es una venta o un autoconsumo?</label>
      <select class="form-control" id="p2" onchange="onChangeAyuda('p2');" >
          <option value="1">Venta</option>
          <option value="2">Autoconsumo</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p2-1" >
      <label>¿Es venta local o exportación?</label>
      <select class="form-control" id="p3" onchange="onChangeAyuda('p3');">
          <option value="1">Venta local</option>
          <option value="150">Exportación</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p3-1">
      <label>¿Es al estado?</label>
      <select class="form-control" id="p4" onchange="onChangeAyuda('p4');">
          <option value="1">No</option>
          <option value="2">Sí</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p4-1">
      <label>¿Es a una institución con exención especial?</label>
      <select class="form-control" id="p5" onchange="onChangeAyuda('p5');">
          <option value="1">No</option>
          <option value="2">Sí</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p5-1">
      <label >¿Es un producto de canasta básica?</label>
      <select class="form-control" id="p6" onchange="onChangeAyuda('p6');">
          <option value="1">No</option>
          <option value="101">Sí</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p6-1">
      <label style="padding-top:1.3em;">¿Tiene tarifa diferenciada?</label>
      <select class="form-control" id="p7" onchange="onChangeAyuda('p7');">
          <option value="103">No</option>
          <option value="2">Sí</option>
      </select>
    </div>
    
    <div class="form-group col-md-8 pregs p7-2">
      <label>Indique el sector de actividad</label>
      <select class="form-control" id="p8" onchange="onChangeAyuda('p8');">
          <option value="104">Servicios médicos</option>
          <option value="102">Servicios de educación</option>
          <option value="201">Arrendamiento de inmuebles inferiores a 1.5 salarios base</option>
          <option value="130">Arrendamiento de inmuebles superiores a 1.5 salarios base</option>
          <option value="201">Servicios de electricidad y agua</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p2-2">
      <label>Indique la tarifa o si no pagó IVA</label>
      <select class="form-control" id="p9" onchange="onChangeAyuda('p9');" >
          <option value="123">13%</option>
          <option value="121">1%</option>
          <option value="122">2%</option>
          <option value="124">4%</option>
          <option value="240">No se pagó IVA en la compra</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p4-2">
      <label>¿Tiene derecho a crédito?</label>
      <select class="form-control" id="p10" onchange="onChangeAyuda('p10');" >
          <option value="160">Sí</option>
          <option value="260">No</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p5-2">
      <label>¿Tiene derecho a crédito?</label>
      <select class="form-control" id="p11" onchange="onChangeAyuda('p11');" >
          <option value="250">Sí</option>
          <option value="200">No</option>
      </select>
    </div>
    
  </div>
  <div id="recomendacion">
    <b>Recomendación: </b>
    <span></span>
  </div>
</div>

<style>
  
  .ayuda-cont {
    background: #f5f5f5;
    padding: 10px 15px !important;
    padding-bottom: 0 !important;
    border: 1px solid #999;
    margin-top: -.5rem;
    margin-bottom: .5rem;
  }
  
  .ayuda-cont .form-group label {
      font-size: 11px;
      line-height: 1.3;
  }
  
  .pregs {
    display: none;
    margin-bottom: .5rem;
  }
  
  #recomendacion {
    margin-bottom: .5rem;
    padding: 5px 15px;
    font-size: 10px;
    background: #fff;
    color: #2b53d5;
    border: dotted 1px;
  }
  
</style>


<script>
  
  function toggleAyudaTipoIVa() {
    
    if ( $("#p1:checked").length ) {
      $(".ayuda-cont").show();
    } else {
       $(".ayuda-cont").hide(); 
    }
    
    clearAyuda();
    
  }
  
  function onChangeAyuda ( id ) {
    
    $( '.pregs' ).hide();
    var respuesta = $('#'+id).val();
    if( respuesta != '1' && respuesta != '2' ) {
      $("#tipo_iva").val(respuesta);
      $("#tipo_iva").change();
      $("#porc_iva").change();
      $("#recomendacion span").text( $("#tipo_iva :selected").text() );
    }else {
      $("#recomendacion span").text( '' );
      var toggleClass = '.' + id + "-" + respuesta;
      $( toggleClass + ' select' ).change();
    }
    $( toggleClass ).show();
    togglePrev( id );
    
  }
  
  function togglePrev ( id ) {
    var prevId = $('#'+id).parent().attr('class').split(' ').pop().split('-')[0];
    $( '#'+id ).parent().show();
    
    if( prevId != 'end' ) {
      togglePrev( prevId );
    }
  }
  
  function clearAyuda() {
    
    $( '#p2' ).change();
    
  }
  
  
</script>