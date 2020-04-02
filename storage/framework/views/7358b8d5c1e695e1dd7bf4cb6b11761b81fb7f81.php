<div class="ayuda-cont col-md-12" style="display:none;">
  <div class="form-row ">
    
    <div class="form-group col-md-4 pregs p1-1 end-0">
      <label>¿Es una venta o un autoconsumo?</label>
      <select class="form-control" id="p2" onchange="onChangeAyuda('p2');" >
          <option value="1">Venta local</option>
          <option value="150">Exportación o venta a zona franca</option>
          <option value="2">Autoconsumo</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p2-1">
      <label>¿Es una venta sujeta al IVA?</label>
      <select class="form-control" id="p3" onchange="onChangeAyuda('p3');">
          <option value="1">Sí</option>
          <option value="2">No</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p3-1">
      <label >¿Es una venta exenta?</label>
      <select class="form-control" id="p4" onchange="onChangeAyuda('p4');">
          <option value="1">No</option>
          <option value="2">Sí</option>
      </select>
    </div>
    
    <div class="form-group col-md-8 pregs p4-1">
      <label>Indique la tarifa de la venta</label>
      <select class="form-control" id="p5" onchange="onChangeAyuda('p5');">
          <option value="103">13% - Tarifa general</option>
          <option value="101">1% - Canasta básica</option>
          <option value="102">2% - Educación privada o venta de medicamentos</option>
          <option value="104">4% - Servicios de salud o venta de tiquetes aéreos</option>
          <option value="130">13% - Arrendamiento de inmuebles superiores a 1.5 salarios base</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p4-2">
      <label>¿Da derecho a crédito fiscal?</label>
      <select class="form-control" id="p6" onchange="onChangeAyuda('p6');" >
          <option value="2">No</option>
          <option value="160">Sí (Ventas al estado)</option>
      </select>
    </div>
    
    <div class="form-group col-md-8 pregs p6-2">
      <label>Indique el tipo de venta</label>
      <select class="form-control" id="p7" onchange="onChangeAyuda('p7');" >
          <option value="201">Arrendamiento de inmuebles inferior a 1.5 salarios base</option>
          <option value="201">Venta de servicios de electricidad con consumo inferior a 280Kw</option>
          <option value="201">Venta de servicios de agua con consumo inferior a 30m3</option>
          <option value="200">Otras ventas exentas</option>
          <option value="245">Ventas locales con tarifa transitoria</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p2-2">
      <label>¿Se aplicó crédito fiscal en la compra?</label>
      <select class="form-control" id="p8" onchange="onChangeAyuda('p8');">
          <option value="1">Sí</option>
          <option value="240">No</option>
      </select>
    </div>
    
    <div class="form-group col-md-8 pregs p6-1">
      <label>Indique la tarifa de la venta</label>
      <select class="form-control" id="p8" onchange="onChangeAyuda('p8');">
          <option value="123">13% - Tarifa general</option>
          <option value="121">1% - Canasta básica</option>
          <option value="122">2% - Educación privada o venta de medicamentos</option>
          <option value="124">4% - Servicios de salud o venta de tiquetes aéreos</option>
      </select>
    </div>
    
    <div class="form-group col-md-4 pregs p3-2">
      <label >¿Da derecho a crédito fiscal?</label>
      <select class="form-control" id="p9" onchange="onChangeAyuda('p9');">
          <option value="160">Sí</option>
          <option value="260">No</option>
      </select>
    </div>
    
  </div>
  <div id="recomendacion">
    <b>Recomendación: </b>
    <span></span>
  </div>
</div>

<script>
  
  function toggleAyudaTipoIVa() {
    
    if ( $("#p1:checked").length ) {
      $(".ayuda-cont").show();
      $('.ayuda-cont select').each( function(){
      	var optVal = $(this).find('option').first().val();
      	$(this).val(optVal);
      });
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
  
  
</script><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Invoice/preguntas-ayuda.blade.php ENDPATH**/ ?>