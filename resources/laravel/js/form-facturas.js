  window.calcularSubtotalItem = function(){

    var precio_unitario = parseFloat( $('#precio_unitario').val() );
      precio_unitario = parseFloat(precio_unitario);
    var cantidad = parseFloat( $('#cantidad').val() );
      cantidad = parseFloat(cantidad);
      /*cantidad = parseFloat(cantidad).toFixed(3);
      $('#cantidad').val(cantidad);*/
      
    var porc_iva = parseFloat( $('#porc_iva').val() );
      porc_iva = parseFloat(porc_iva);
    var monto_iva = parseFloat( $('#item_iva_amount').val() );
      monto_iva = parseFloat(monto_iva);

    if( !monto_iva ) {
      monto_iva = 0;
    }

    if( precio_unitario && cantidad ){
      var subtotal = cantidad * precio_unitario;

      var discount = $('#discount').val();
        discount = parseFloat(discount);
      if( !discount ) {
        discount = 0;
        $('#discount').val(0);
      }
      var discount_type = $('#discount_type').val();
      if( discount_type == "01" && discount > 0 ) {
          subtotal = subtotal - ( subtotal * (discount / 100) );
      }else if( discount_type == "02" && discount > 0 ) {
          subtotal = subtotal - discount;
      }

      $('#item_subtotal').val( subtotal.toFixed(5) );
      if( $('#porc_iva').val().length ){
        monto_iva = parseFloat(subtotal * porc_iva / 100);
        $('#item_iva_amount').val( monto_iva.toFixed(5) );
        $('#item_total').val( (subtotal + monto_iva).toFixed(5) );
      }else{
        $('#item_total').val( subtotal );
      }
    }else{
      $('#item_subtotal').val( 0 );
      $('#item_total').val( 0 );
      $('#item_iva_amount').val( 0 );
    }

    calcularMontoExoneracion();

  }

  window.calcularConIvaManual = function(){

    var precio_unitario = parseFloat( $('#precio_unitario').val() );
    var cantidad = parseFloat( $('#cantidad').val() );
    var monto_iva = parseFloat( $('#item_iva_amount').val() );

    if( !monto_iva ) {
      monto_iva = 0;
      $('#item_iva_amount').val(0);
    }

    if( precio_unitario && cantidad ){
      var subtotal = cantidad * precio_unitario;

      var discount = $('#discount').val();
        discount = parseFloat(discount);
      var discount_type = $('#discount_type').val();
      if( discount_type == "01" && discount > 0 ) {
        subtotal = subtotal - ( subtotal * (discount / 100) );
      }else if( discount_type == "02" && discount > 0 ){
        subtotal = subtotal - discount;
      }

      $('#item_subtotal').val( subtotal.toFixed(5));
      if( monto_iva ){
        $('#item_total').val( (subtotal + monto_iva).toFixed(5) );
      }else{
        $('#item_total').val( subtotal.toFixed(5) );
      }
    }else{
      $('#item_subtotal').val( 0 );
      $('#item_total').val( monto_iva );
    }

  }

  window.presetPorcentaje = function(){
    var tipo = $('#tipo_iva').val();
    var porcentaje = $('#tipo_iva :selected').attr('porcentaje');

    if( $('#cliente_exento:checked').length ){
      porcentaje = '0';
    }

    $('#porc_iva').val( porcentaje );
  }

  window.presetTipoIVA = function(){
    if( ! $('#cliente_exento:checked').length ){
      var codigoIVA = $('#tipo_iva :selected').val();
      $('#tipo_producto option').hide();
      var tipoProducto = 0;
      $("#tipo_producto option").each(function(){
          var posibles = $(this).attr('posibles').split(",");
      	if(posibles.includes(codigoIVA)){
          	$(this).show();
          	if( !tipoProducto ){
              tipoProducto = $(this).val();
            }
          }
      });

      toggleCamposExoneracion();

      $('#tipo_producto').val( tipoProducto ).change();
    }else{
      $('#tipo_iva').val( 'B260' );
    }
  }

  window.togglePorcentajeIdentificacionPlena = function(){
    if( ('#field_porc_identificacion_plena').length ){
      var is_identificacion_plena = parseInt( $('#tipo_iva :selected').attr('is_identificacion_plena') );

      if( is_identificacion_plena ){
         $('#field_porc_identificacion_plena').show();
      }else{
        $('#field_porc_identificacion_plena').hide();
      }
    }
  }

  window.toggleRetencion = function(){
    var metodo = $("#medio_pago").val();
    if( metodo == '02' ){
      $("#field-retencion").show();
    }else {
      $("#field-retencion").hide();
    }
  }

  window.agregarEditarItem = function() {
  	var guardar = $('#form-checkbox').is(":checked");

    //Si esta editando, usa lnum y item_id para identificar la fila.
    var lnum = $('#lnum').val() ? $('#lnum').val() : '';
    var item_id = $('#item_id').val() ? $('#item_id').val() : '';

    var numero = parseInt( $('.item-tabla:last-of-type').attr('attr-num') ) + 1;
    var index = parseInt( $('#current-index').val() ) + 1;
    var codigo = $('#codigo').val();
    var nombre = $('#nombre').val();
    var tipo_producto = $('#tipo_producto').val();
    var tipo_producto_text = $('#tipo_producto :selected').text();
    var cantidad = $('#cantidad').val();
    cantidad = parseFloat(cantidad);
    var unidad_medicion = $('#unidad_medicion').val();
    var precio_unitario = $('#precio_unitario').val();
    precio_unitario = parseFloat(precio_unitario);
    var porc_identificacion_plena = $('#porc_identificacion_plena').val();
    var is_identificacion_especifica = $('#is_identificacion_especifica:checked').length;
    var descuento = $('#discount').val();
    descuento = parseFloat(descuento);
    var tipo_descuento = $('#discount_type').val();
    var tipo_iva = $('#tipo_iva').val();
    var tipo_iva_text = $('#tipo_iva :selected').text();
    var porc_iva = $('#porc_iva').val();
    var monto_iva = $('#item_iva_amount').val();
    monto_iva = parseFloat(monto_iva);
    var subtotal = $('#item_subtotal').val();
    subtotal = parseFloat(subtotal);
    var total = $('#item_total').val();
    total = parseFloat(total);
    var typeDocument = $('#typeDocument').val();
    var numeroDocumento = $('#numeroDocumento').val();
    var nombreInstitucion = $('#nombreInstitucion').val();
    var exoneration_date = $('#exoneration_date').val();
    var porcentajeExoneracion = $('#porcentajeExoneracion').val();
    var montoExoneracion = $('#montoExoneracion').val();
    var impuestoNeto = $('#impuestoNeto').val();
    var montoTotalLinea = $('#montoTotalLinea').val();
    var tariff_heading = $('#tariff_heading').val();
    var docType = $('#document_type').val();

    if(guardar){
    	$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: "/productos",
            method: 'post',
            data: {
                code: codigo,
                name: nombre,
                measure_unit: unidad_medicion,
                unit_price: precio_unitario,
                product_category_id: tipo_producto,
                default_iva_type: tipo_iva
            },
            success: function (result) {
            	Swal.fire({
		            type: 'success',
		            title: 'Producto Creado',
		            text: 'El producto se ha agregado correctamente al catalogo.'
		        })
		        console.log(codigo);
		        console.log(nombre);
		        $('#codigo-select').append('<option value="'+codigo+'">'+nombre+'</option>');
		        console.log($('#codigo-select'));
            }

        });
	}


    if( !monto_iva ) {
      monto_iva = 0;
      $('#item_iva_amount').val(0);
    }
    if( !descuento ) {
      descuento = 0;
      $('#discount').val(0);
    }

    if( !precio_unitario ) {
      precio_unitario = 0;
      $('#precio_unitario').val(0);
    }

    if( !cantidad ) {
      cantidad = 1;
      $('#cantidad').val(1);
    }

    if( $( '#document_number').val() == "TOTALES2018" ) {
      codigo = $('#codigo').val( "L" + numero  );
      nombre = $('#nombre').val( "TIPO-" + tipo_iva  );
    }

    if( docType == '09' ) {
      if( tariff_heading.length != 12 ) {
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'La tarifa arancelaria debe contener 12 caracteres.'
        })
        return false;
      }
    }
    var exoneradalinea = $('#exoneradalinea').val();
    var datos = false;
    if(exoneradalinea == 1) {
      if( subtotal && codigo && nombre && precio_unitario && cantidad && tipo_iva && tipo_producto && total > 0 && typeDocument  && numeroDocumento &&   nombreInstitucion  &&  exoneration_date  &&  porcentajeExoneracion   && montoExoneracion){
          datos = true;
      }
    }else{
      if( subtotal && codigo && nombre && precio_unitario && cantidad && tipo_iva && tipo_producto && total > 0 ){
          datos = true;
      }
    }
    //Se asegura de que los campos hayan sido llenados
    if( datos){

      //Crear el ID de la fila.
      var itemExistente = false;
      if( lnum && lnum !== '' ){
        numero = lnum;
        itemExistente = $('#item-tabla-' + numero);
        index = itemExistente.attr('index');
        itemExistente.html("");
      }
      var row_id  = "item-tabla-"+numero;

      var inputFields = "<div class='hidden'>" +
                   "<input type='hidden' class='numero' name='items["+index+"][item_number]' itemname='item_number' value='"+(numero+1)+"'>" +
                   "<input class='item_id' type='hidden' name='items["+index+"][id]' itemname='id' value='"+item_id+"'>" +
                   "<input type='hidden' class='codigo' name='items["+index+"][code]' itemname='code' value='"+codigo+"'>" +
                   "<input type='hidden' class='nombre' name='items["+index+"][name]' itemname='name' value='"+nombre+"'>" +
                   "<input type='hidden' class='tipo_producto' name='items["+index+"][product_type]' itemname='product_type' value='"+tipo_producto+"'>" +
                   "<input type='hidden' class='cantidad' name='items["+index+"][item_count]' itemname='item_count' value='"+cantidad+"'>" +
                   "<input type='hidden' class='unidad_medicion' name='items["+index+"][measure_unit]' itemname='measure_unit' value='"+unidad_medicion+"'>" +
                   "<input type='hidden' class='precio_unitario' name='items["+index+"][unit_price]' itemname='unit_price' value='"+precio_unitario+"'>" +
                   "<input type='hidden' class='tipo_iva' name='items["+index+"][iva_type]' itemname='iva_type' value='"+tipo_iva+"'>" +
                   "<input type='hidden' class='porc_identificacion_plena' name='items["+index+"][porc_identificacion_plena]' itemname='porc_identificacion_plena' value='"+porc_identificacion_plena+"'>" +
                   "<input type='hidden' class='discount_type' name='items["+index+"][discount_type]' itemname='discount_type' value='"+tipo_descuento+"'>" +
                   "<input type='hidden' class='discount' name='items["+index+"][discount]' itemname='discount' value='"+descuento+"'>" +
                   "<input type='hidden' class='subtotal' name='items["+index+"][subtotal]' itemname='subtotal' value='"+subtotal+"'>" +
                   "<input type='hidden' class='porc_iva' name='items["+index+"][iva_percentage]' itemname='iva_percentage' value='"+porc_iva+"'>" +
                   "<input type='hidden' class='monto_iva' name='items["+index+"][iva_amount]' itemname='iva_amount' value='"+monto_iva+"'> " +
                   "<input type='hidden' class='total' name='items["+index+"][total]' itemname='total' value='"+total+"'>" +
                   "<input type='hidden' class='is_identificacion_especifica' name='items["+index+"][is_identificacion_especifica]' itemname='is_identificacion_especifica' value='"+is_identificacion_especifica+"'>" +
                   "<input type='hidden' class='typeDocument' name='items["+index+"][typeDocument]' itemname='typeDocument' value='"+typeDocument+"'>" +
                   "<input type='hidden' class='numeroDocumento' name='items["+index+"][numeroDocumento]' itemname='numeroDocumento' value='"+numeroDocumento+"'>" +
                   "<input type='hidden' class='nombreInstitucion' name='items["+index+"][nombreInstitucion]' itemname='nombreInstitucion' value='"+nombreInstitucion+"'>" +
                   "<input type='hidden' class='exoneration_date' name='items["+index+"][exoneration_date]' itemname='exoneration_date' value='"+exoneration_date+"'>" +
                   "<input type='hidden' class='porcentajeExoneracion' name='items["+index+"][porcentajeExoneracion]' itemname='porcentajeExoneracion' value='"+porcentajeExoneracion+"'>" +
                   "<input type='hidden' class='montoExoneracion' name='items["+index+"][montoExoneracion]' itemname='montoExoneracion' value='"+montoExoneracion+"'>" +
                   "<input type='hidden' class='impuestoNeto' name='items["+index+"][impuestoNeto]' itemname='impuestoNeto' value='"+impuestoNeto+"'>" +
                   "<input type='hidden' class='montoTotalLinea' name='items["+index+"][montoTotalLinea]' itemname='montoTotalLinea' value='"+montoTotalLinea+"'>" +
                   "<input type='hidden' class='tariff_heading' name='items["+index+"][tariff_heading]' itemname='tariff_heading' value='"+tariff_heading+"'>" +
                   "<input type='hidden' class='exoneradalinea' name='items["+index+"][exoneradalinea]' itemname='exoneradalinea' value='"+exoneradalinea+"'>" +
              "</div>";

      //Crea la fila en la tabla
      var htmlCols = "<td><span class='numero-fila'>"+(numero+1)+"</span> </td>";
        htmlCols += "<td>"+codigo + inputFields + " </td>";
        htmlCols += "<td>"+nombre+" </td>";
        htmlCols += "<td>"+cantidad+" </td>";
        htmlCols += "<td>"+unidad_medicion+" </td>";
        htmlCols += "<td>"+ fixComas(precio_unitario) +" </td>";
        htmlCols += "<td>"+tipo_iva_text+" <br> -"+tipo_producto_text+"</td>";
        htmlCols += "<td>"+ fixComas(subtotal) +" </td>";
        htmlCols += "<td>"+ fixComas(monto_iva) +" </td>";
        htmlCols += "<td>"+ fixComas(total) +"   </td>";
        htmlCols += "<td class='acciones'><span class='btn-editar-item text-success mr-2' title='Editar linea' onclick='abrirPopup(\"linea-popup\");cargarFormItem("+index+");'> <i class='fa fa-pencil' aria-hidden='true'></i> </span> <span title='Eliminar linea' onclick='eliminarItem("+index+");' class='btn-eliminar-item text-danger mr-2'> <i class='fa fa-trash-o' aria-hidden='true'></i> </span> </td>";

      if( !itemExistente ) {
        var htmlRow = "<tr class='item-tabla item-index-"+index+"' index='"+index+"' attr-num='"+numero+"' id='"+row_id+"' > " + htmlCols + "</tr>";
        $('#tabla-items-factura tbody').append(htmlRow);
      }else{
        itemExistente.append(htmlCols);
      }

      $('#tabla-items-factura').show();

      //Limpia los datos del formulario
      limpiarFormItem();

      //Recalcula números para asegurar que no haya vacíos
      recalcularNumerosItem();

      //Calcula total de factura
      calcularTotalFactura();

      //Aumenta el indice de filas para evitar cualquier conflicto si hubo eliminados. El index nunca debe cambiar ni repetirse, los números pueden cambiar.
      $('#current-index').val(index);

      //Si estaba editando, quita la clase
      $('.item-factura-form').removeClass('editando');

      cerrarPopup('linea-popup');

      //Fuerza un reset en la ayuda al marcar preguntas.
      /*$('#p1').prop('checked', false);
      $('#p1').change();*/

      if( $('#is-compra').length || docType == '08' ){
        $('#tipo_producto').val('B003').change();
      }else {
        if( $('#default_vat_code').length ){
          $('#tipo_iva').val( $('#default_vat_code').val() ).change();
        }else{
          $('#tipo_iva').val( 'B103' ).change();
        }
      }

    }else{
      /*alert('Debe completar los datos de la linea antes de guardarla');
      return false;*/
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'Por favor, asegúrese que todos los datos sean válidos antes de continuar.'
        })
        return false;
    }

  }

  //Se encarga de limpiar el formulario de "Agregar items"
  window.limpiarFormItem = function(){
    var  typeDocument =   $('#typeDocument').val();
      var  numeroDocumento =   $('#numeroDocumento').val();
      var  exoneration_date =   $('#exoneration_date').val();
      var  porcentajeExoneracion =   $('#porcentajeExoneracion').val();
      var  montoExoneracion =   $('#montoExoneracion').val();
      var  impuestoNeto =   $('#impuestoNeto').val();
      var  montoTotalLinea =  $('#montoTotalLinea').val();
      var  nombreInstitucion =  $('#nombreInstitucion').val();
      $('.item-factura-form input, .item-factura-form select').val('');
      $('.item-factura-form input[type=checkbox]').prop('checked', false);
      $('.otros-factura-form input, .otros-factura-form select').val('');
      $('.otros-factura-form input[type=checkbox]').prop('checked', false);
         $('#exoneradalinea').val(0);
      $('#typeDocument').val(typeDocument);
      $('#numeroDocumento').val(numeroDocumento);
      $('#exoneration_date').val(exoneration_date);
      $('#porcentajeExoneracion').val(porcentajeExoneracion);
      $('#montoTotalLinea').val(montoTotalLinea);
      $('#nombreInstitucion').val(nombreInstitucion);
      
      var docType = $('#document_type').val();
      if( ($('#is-compra').length || docType == '08') && !$('#is-manual').length ){
        $('#tipo_iva').val('B003').change();
      }else {
        if( $('#default_vat_code').length ){
          $('#tipo_iva').val( $('#default_vat_code').val() ).change();
        }else{
          $('#tipo_iva').val( 'B103' ).change();
        }
      }
      
      $('#unidad_medicion').val('Unid');
      $('#cantidad').val(1);
      $('#porc_identificacion_plena').val(13);
      $('#discount_type').val('01');
      $('#discount').val(0);
      $('#tipo_producto').change();
  }
  
  window.agregarNuevaLinea = function() {
      $('#codigo-select').val('seleccionar').change();
      $('#codigo-select-div').show();
      limpiarFormItem();
      abrirPopup('linea-popup');
  }

  //Carga la item para ser editada
  window.cargarFormItem = function( index ) {
    $('.item-factura-form').addClass('editando');

    $('#codigo-div').show();
    $('#form-checkbox').prop("checked", false);
    $('#checkbox-div').hide();
    $('#codigo-select-div').hide();		

    var item = $('.item-index-'+index );
    $('#lnum').val( item.attr('attr-num') );
    $('#item_id').val( item.find('.item_id ').val() );
    $('#codigo').val( item.find('.codigo ').val() );
    $('#nombre').val( item.find('.nombre ').val() );
    $('#tipo_producto').val( item.find('.tipo_producto ').val() ).change();
    $('#discount').val( item.find('.discount ').val() );
    $('#discount_type').val( item.find('.discount_type ').val() );
    $('#cantidad').val( item.find('.cantidad ').val() );
    $('#unidad_medicion').val( item.find('.unidad_medicion ').val() );
    $('#precio_unitario').val( item.find('.precio_unitario ').val() );
    $('#tipo_iva').val( item.find('.tipo_iva ').val() ).change();
    $('#item_subtotal').val( item.find('.subtotal ').val() );
    $('#porc_iva').val( item.find('.porc_iva ').val() );
    $('#item_iva_amount').val( item.find('.monto_iva ').val() );
    $('#porc_identificacion_plena').val( item.find('.porc_identificacion_plena ').val() );

    $('#typeDocument').val( item.find('.typeDocument ').val() );
    $('#numeroDocumento').val( item.find('.numeroDocumento ').val() );
    $('#nombreInstitucion').val( item.find('.nombreInstitucion ').val() );
    $('#porcentajeExoneracion').val( item.find('.porcentajeExoneracion ').val() );
    $('#montoExoneracion').val( item.find('.montoExoneracion ').val() );
    $('#impuestoNeto').val( item.find('.impuestoNeto ').val() );
    $('#montoTotalLinea').val( item.find('.montoTotalLinea ').val() );
    $('#exoneradalinea').val( item.find('.exoneradalinea ').val() );

    if( parseInt(item.find('.is_identificacion_especifica').val()) ) {
      $('#is_identificacion_especifica').prop( 'checked', true );
    }else {
      $('#is_identificacion_especifica').prop( 'checked', false );
    }

    toggleCamposExoneracion();

    togglePorcentajeIdentificacionPlena();
    calcularConIvaManual();
  }

  //Carga la item para ser editada
  window.cargarFormOtros = function( index ) {
    $('.otros-factura-form').addClass('editando');

    var item = $('.otros-index-'+index );
    $('#otros-lnum').val( item.attr('attr-num') );
    $('#otros_id').val( item.find('.item_id ').val() );
    $('#otros-document_type').val( item.find('.otros-document_type ').val() ).change();
    $('#otros-provider_id_number').val( item.find('.otros-provider_id_number ').val() );
    $('#otros-provider_name').val( item.find('.otros-provider_name ').val() );
    $('#otros-description').val( item.find('.otros-description ').val() );
    $('#otros-percentage').val( item.find('.otros-percentage ').val() );
    $('#otros-amount').val( item.find('.otros-amount ').val() );

    toggleCobroTercero();
  }

  //Acción para cancelar la edición
  window.cancelarEdicion = function(){
    $('.item-factura-form').removeClass('editando');
    limpiarFormItem();
  }

  //Elimina la item
  window.eliminarItem = function( index ){
    $('.item-index-'+index ).remove();
    recalcularNumerosItem();
    calcularTotalFactura();
  }

  //Recalcula números para asegurar que no haya vacíos
  window.recalcularNumerosItem = function() {
    var i = 0;
    $( '.item-tabla' ).each( function(){
      $(this).attr( 'attr-num', i );
      $(this).attr( 'id', 'item-tabla-'+i );
      $(this).find( '.numero-fila').text(i+1 );
      $(this).find('.hidden input').each( function(){
    	  if($(this).attr('itemname').length){
    		  var itemname = $(this).attr('itemname');
      		$(this).attr('name', 'items['+i+']['+itemname+']');
        }
      });
      i++;
    });
    var j = 0;
    $( '.otros-tabla' ).each( function(){
      $(this).attr( 'attr-num', j );
      $(this).attr( 'id', 'otros-tabla-'+j );
      $(this).find( '.numero-fila').text(j+1 );
      $(this).find('.hidden input').each( function(){
    	  if($(this).attr('itemname').length){
    		  var itemname = $(this).attr('itemname');
      		$(this).attr('name', 'otros['+j+']['+itemname+']');
        }
      });
      j++;
    });
  }

  //Acción para cancelar la edición
  window.cancelarEdicionOtros = function(){
    $('.otros-factura-form').removeClass('editando');
    limpiarFormItem();
  }

  //Elimina la item
  window.eliminarOtros = function( index ){
    $('.otros-index-'+index ).remove();
    recalcularNumerosItem();
    calcularTotalFactura();
  }

  window.calcularTotalFactura = function() {
    var subtotal = 0;
    var monto_iva = 0;
    var total = 0;
    var iva_devuelto = 0;
    var iva_exonerado = 0;
    var otros_cargos = 0;

    $('.item-tabla').each(function(){
      var s = parseFloat($(this).find('.subtotal').val());
      var m = parseFloat($(this).find('.monto_iva').val());
      var t = parseFloat($(this).find('.total').val());
      var tp = parseFloat($(this).find('.tipo_producto').val());
      var ex = parseFloat($(this).find('.montoExoneracion').val());
      if(!ex){ ex = 0; }
      if ($('#medio_pago').val() === '02' && tp === 12) {
          iva_devuelto += m;
      }
      subtotal += s;
      monto_iva += m;
      total += t;
      iva_exonerado += ex;
    });

    $('.otros-tabla').each(function(){
      var ot = parseFloat($(this).find('.otros-amount').val());

      if(!ot){ ot = 0; }
      otros_cargos += ot;
    });

    $('#subtotal').val(subtotal);
    $('#monto_iva').val(monto_iva);
    $('#total').val(total - iva_devuelto - iva_exonerado + otros_cargos);

    $('#total_iva_devuelto').val(iva_devuelto);
    $('#total_iva_exonerado').val(iva_exonerado);
    $('#total_otros_cargos').val(otros_cargos);

    $('#total_iva_devuelto-cont').hide();
    if(iva_devuelto > 0){
      $('#total_iva_devuelto-cont').show();
    }

    $('#total_iva_exonerado-cont').hide();
    if(iva_exonerado > 0){
      $('#total_iva_exonerado-cont').show();
    }

    $('#total_otros_cargos-cont').hide();
    if(otros_cargos > 0){
      $('#total_otros_cargos-cont').show();
    }
  }

  window.fixComas = function( numero ) {
    numero = parseFloat(numero);
    return numero.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 5});
  }

  window.toggleRetencion = function() {
    var metodo = $("#medio_pago").val();
    if( metodo == '02' ){
      $("#field-retencion").show();
    }else {
      $("#field-retencion").hide();
    }
  }

  window.buscarProducto = function() {
        var id = $('#codigo-select').val();
        if(id == 'nuevoProducto'){
        	$('#codigo-div').show();
        	$('#checkbox-div').show();
        	$('#form-checkbox').prop("checked", true);

        }else{
        	$('#codigo-div').hide();
        	$('#checkbox-div').hide();
        	$('#form-checkbox').prop("checked", false);	
        }

        if(id !== '' && id !== undefined){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "/getproduct",
                method: 'get',
                data: {
                    id: id
                },
                success: function (result) {
                    if(result.name) {
                    	$('#codigo').val(result.code);
                        $('#nombre').val(result.name);
                        $('#unidad_medicion').val(result.measure_unit);
                        $('#precio_unitario').val(result.unit_price);
                        $('#tipo_producto').val(result.product_category_id);
                        $('#tipo_iva').val(result.default_iva_type);

                        $('#precio_unitario').change();
                        $('#tipo_producto').change();
                        $('#tipo_iva').change();
                    }
                }
            });
        }else{
            alert('Debe digitar un código numeral para la búsqueda');
        }
    }

   window.calcularMontoExoneracion = function() {
      var hasExoneracion = false;
      var codigosConExoneracion = ["B181", "S181", "B182", "S182", "B183", "S183", "B184", "S184"];
      var construccion = jQuery("#tipo_producto").val() == 43; //Servicios de construcción e ingenieria al 0% transitorio. Llevan exoneración

      if( codigosConExoneracion.includes( $('#tipo_iva').val() ) || construccion ){
        var porcentajeExonerado = parseFloat($('#porcentajeExoneracion').val());
        
        if(porcentajeExonerado <= 13){
          var porcIva = parseFloat(jQuery("#porc_iva").val());
          if( porcIva < porcentajeExonerado ){
            $('#porcentajeExoneracion').val(porcIva)
            porcentajeExonerado = porcIva;
          }
          porcentajeExonerado = (porcentajeExonerado/porcIva)*100;
        }
        
        if(porcentajeExonerado > 0) {
            var monto_iva_detalle = parseFloat($('#item_iva_amount').val());
            var monto = monto_iva_detalle * (porcentajeExonerado / 100);
            var impNeto = monto_iva_detalle - monto;
            var subTotal = parseFloat($('#item_subtotal').val());
            var montoTotal = parseFloat(subTotal) + parseFloat(impNeto);

            $('#montoExoneracion').val(monto.toFixed(5));
            $('#impuestoNeto').val(impNeto.toFixed(5));
            $('#montoTotalLinea').val(montoTotal.toFixed(5));
        }
        
      }
    }

    window.toggleCamposExoneracion = function() {
        /*var checkExoneracion = $('#checkExoneracion').prop('checked');
        console.log(checkExoneracion);*/

        var hasExoneracion = false;
        var codigosConExoneracion = ["B181", "S181", "B182", "S182", "B183", "S183", "B184", "S184"];
        var construccion = jQuery("#tipo_producto").val() == 43; //Servicios de construcción e ingenieria al 0% transitorio. Llevan exoneración

        if( codigosConExoneracion.includes( $('#tipo_iva').val() ) || construccion ){
          hasExoneracion = true;
        }

        if(hasExoneracion){
            $(".exoneracion-cont").show();
            $('#etiqTotal').text('');
            $('#etiqTotal').text('Total sin exonerar');
            $('#divTypeDocument').attr('hidden', false);
            $('#divNumeroDocumento').attr('hidden', false);
            $('#divNombreInstitucion').attr('hidden', false);
            $('#divPorcentajeExoneracion').attr('hidden', false);
            $('#divMontoExoneracion').attr('hidden', false);
            $('#divMontoTotalLinea').attr('hidden', false);
            $('#divImpuestoNeto').attr('hidden', false);
            $('#exoneradalinea').val(1);
            
            $('.input-fecha').datetimepicker({
              format: 'DD/MM/Y',
              allowInputToggle: true,
              icons : {
                    time: 'fa fa-clock-o',
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-calendar-check-o',
                    clear: 'fa fa-times',
                    close: 'fa fa-calendar-times-o'
              }
            });
        }else{
            $(".exoneracion-cont").hide();
            $('#etiqTotal').text('');
            $('#etiqTotal').text('Monto Total Linea');
            $('#divTypeDocument').attr('hidden', true);
            $('#divNumeroDocumento').attr('hidden', true);
            $('#divNombreInstitucion').attr('hidden', true);
            $('#divPorcentajeExoneracion').attr('hidden', true);
            $('#divMontoExoneracion').attr('hidden', true);
            $('#divMontoTotalLinea').attr('hidden', true);
            $('#divImpuestoNeto').attr('hidden', true);
            $('#exoneradalinea').val(0);
        }
    }


    window.agregarEditarOtros = function() {

      //Si esta editando, usa lnum y item_id para identificar la fila.
      var lnum = $('#otros-lnum').val() ? $('#otros-lnum').val() : '';
      var otros_id = $('#otros_id').val() ? $('#otros_id').val() : '';
  
      var numero = parseInt( $('.otros-tabla:last-of-type').attr('attr-num') ) + 1;
      var index = parseInt( $('#current-index-otros').val() ) + 1;
      var document_type = $('#otros-document_type').val();
      var document_type_string = $('#otros-document_type :selected').text();
      var provider_id_number = $('#otros-provider_id_number').val();
      var provider_name = $('#otros-provider_name').val();
      var description = $('#otros-description').val();
      var percentage = $('#otros-percentage').val();
      var amount = $('#otros-amount').val();
  
      if( !provider_name || !provider_id_number || document_type != "04" ){
        provider_id_number = "-";
        provider_name = "N/A";
      }
  
      if( !percentage ) {
        percentage = 10;
        $('#otros-percentage').val(10);
      }
      if( !amount ) {
        amount = 0;
        $('#otros-amount').val(0);
      }
  
      //Se asegura de que los campos hayan sido llenados
      if( amount && description ){
  
        //Crear el ID de la fila.
        var itemExistente = false;
        if( lnum && lnum !== '' ){
          numero = lnum;
          itemExistente = $('#otros-tabla-' + numero);
          index = itemExistente.attr('index');
          itemExistente.html("");
        }
        var row_id  = "otros-tabla-"+numero;
  
        var inputFields = "<div class='hidden'>" +
                     "<input type='hidden' class='otros-item_number' name='otros["+index+"][item_number]' itemname='item_number' value='"+(numero+1)+"'>" +
                     "<input type='hidden' class='otros_id'  name='otros["+index+"][id]' itemname='id' value='"+otros_id+"'>" +
                     "<input type='hidden' class='otros-document_type' name='otros["+index+"][document_type]' itemname='document_type' value='"+document_type+"'>" +
                     "<input type='hidden' class='otros-provider_id_number' name='otros["+index+"][provider_id_number]' itemname='provider_id_number' value='"+provider_id_number+"'>" +
                     "<input type='hidden' class='otros-provider_name' name='otros["+index+"][provider_name]' itemname='provider_name' value='"+provider_name+"'>" +
                     "<input type='hidden' class='otros-description' name='otros["+index+"][description]' itemname='description' value='"+description+"'>" +
                     "<input type='hidden' class='otros-percentage' name='otros["+index+"][percentage]' itemname='percentage' value='"+percentage+"'>" +
                     "<input type='hidden' class='otros-amount' name='otros["+index+"][amount]' itemname='amount' value='"+amount+"'>" +
                "</div>";
  
        //Crea la fila en la tabla
        var htmlCols = "<td><span class='numero-fila'>"+(numero+1)+"</span> </td>";
          htmlCols += "<td>"+document_type_string + inputFields + " </td>";
          htmlCols += "<td>"+provider_id_number+" "+provider_name+" </td>";
          htmlCols += "<td>"+description+" </td>";
          htmlCols += "<td>"+ fixComas(amount) +" </td>";
          htmlCols += "<td class='acciones'><span class='btn-editar-item text-success mr-2' title='Editar otros cargos' onclick='abrirPopup(\"otros-popup\");cargarFormOtros("+index+");'> <i class='fa fa-pencil' aria-hidden='true'></i> </span> <span title='Eliminar otros cargos' onclick='eliminarOtros("+index+");' class='btn-eliminar-item text-danger mr-2'> <i class='fa fa-trash-o' aria-hidden='true'></i> </span> </td>";
  
        if( !itemExistente ) {
          var htmlRow = "<tr class='otros-tabla otros-index-"+index+"' index='"+index+"' attr-num='"+numero+"' id='"+row_id+"' > " + htmlCols + "</tr>";
          $('#tabla-otroscargos-factura tbody').append(htmlRow);
        }else{
          itemExistente.append(htmlCols);
        }
  
        $('#tabla-otroscargos-factura').show();
  
        //Limpia los datos del formulario
        limpiarFormItem();
  
        //Recalcula números para asegurar que no haya vacíos
        recalcularNumerosItem();
  
        //Calcula total de factura
        calcularTotalFactura();
  
        //Aumenta el indice de filas para evitar cualquier conflicto si hubo eliminados. El index nunca debe cambiar ni repetirse, los números pueden cambiar.
        $('#current-index-otros').val(index);
  
        //Si estaba editando, quita la clase
        $('.otros-factura-form').removeClass('editando');
  
        cerrarPopup('otros-popup');
  
      }else{
          Swal.fire({
              type: 'error',
              title: 'Error',
              text: 'Por favor, asegúrese que todos los datos sean válidos antes de continuar.'
          })
          return false;
      }

  }


    window.toggleCamposExoneracion = function() {
        /*var checkExoneracion = $('#checkExoneracion').prop('checked');
        console.log(checkExoneracion);*/

        var hasExoneracion = false;
        var codigosConExoneracion = ["B181", "S181", "B182", "S182", "B183", "S183", "B184", "S184"];
        var construccion = jQuery("#tipo_producto").val() == 43; //Servicios de construcción e ingenieria al 0% transitorio. Llevan exoneración

        if( codigosConExoneracion.includes( $('#tipo_iva').val() ) || construccion ){
          hasExoneracion = true;
        }

        if(hasExoneracion){
            $(".exoneracion-cont").show();
            $('#etiqTotal').text('');
            $('#etiqTotal').text('Total sin exonerar');
            $('#divTypeDocument').attr('hidden', false);
            $('#divNumeroDocumento').attr('hidden', false);
            $('#divNombreInstitucion').attr('hidden', false);
            $('#divPorcentajeExoneracion').attr('hidden', false);
            $('#divMontoExoneracion').attr('hidden', false);
            $('#divMontoTotalLinea').attr('hidden', false);
            $('#divImpuestoNeto').attr('hidden', false);
            $('#exoneradalinea').val(1);
            
            $('.input-fecha').datetimepicker({
              format: 'DD/MM/Y',
              allowInputToggle: true,
              icons : {
                    time: 'fa fa-clock-o',
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-calendar-check-o',
                    clear: 'fa fa-times',
                    close: 'fa fa-calendar-times-o'
              }
            });
        }else{
            $(".exoneracion-cont").hide();
            $('#etiqTotal').text('');
            $('#etiqTotal').text('Monto Total Linea');
            $('#divTypeDocument').attr('hidden', true);
            $('#divNumeroDocumento').attr('hidden', true);
            $('#divNombreInstitucion').attr('hidden', true);
            $('#divPorcentajeExoneracion').attr('hidden', true);
            $('#divMontoExoneracion').attr('hidden', true);
            $('#divMontoTotalLinea').attr('hidden', true);
            $('#divImpuestoNeto').attr('hidden', true);
            $('#exoneradalinea').val(0);
        }
    }


    window.agregarEditarReferencia = function() {

      //Si esta editando, usa lnum y item_id para identificar la fila.
      var lnum = $('#referencia-lnum').val() ? $('#referencia-lnum').val() : '';
      var referencia_id = $('#referencia_id').val() ? $('#referencia_id').val() : '';
  
      var numero = parseInt( $('.referencia-tabla:last-of-type').attr('attr-num') ) + 1;
      var index = parseInt( $('#current-index-referencia').val() ) + 1;
      var docType = $('#referencia-docType :selected').val();
      var number = $('#referencia-number').val();
      var code = $('#referencia-code :selected').val();
      
      if(!numero){
        numero = 1;
      }
      if(!index){
        index = 1;
      }
      
      var docTypeStr = $('#referencia-docType :selected').text();
      var codeStr = $('#referencia-code :selected').text();
  
  
      //Se asegura de que los campos hayan sido llenados
      if( docType && number && code ){
  
        //Crear el ID de la fila.
        var itemExistente = false;
        if( lnum && lnum !== '' ){
          numero = lnum;
          itemExistente = $('#referencia-tabla-' + numero);
          index = itemExistente.attr('index');
          itemExistente.html("");
        }
        var row_id  = "referencia-tabla-"+numero;
  
        var inputFields = "<div class='hidden'>" +
                     "<input type='hidden' class='referencia-item_number' name='referenceData["+index+"][item_number]' itemname='item_number' value='"+(numero+1)+"'>" +
                     "<input type='hidden' class='referencia_id'  name='referenceData["+index+"][id]' itemname='id' value='"+referencia_id+"'>" +
                     "<input type='hidden' class='referencia-docType' name='referenceData["+index+"][docType]' itemname='docType' value='"+docType+"'>" +
                     "<input type='hidden' class='referencia-number' name='referenceData["+index+"][number]' itemname='number' value='"+number+"'>" +
                     "<input type='hidden' class='referencia-code' name='referenceData["+index+"][code]' itemname='code' value='"+code+"'>" +
                "</div>";
  
        //Crea la fila en la tabla
        var htmlCols = "<td><span class='numero-fila'>"+(numero+1)+"</span> </td>";
          htmlCols += "<td>"+docTypeStr + inputFields + " </td>";
          htmlCols += "<td>"+codeStr+" </td>";
          htmlCols += "<td>"+number+" </td>";
          htmlCols += "<td class='acciones'><span class='btn-editar-item text-success mr-2' title='Editar referencia' onclick='abrirPopup(\"referencia-popup\");cargarFormReferencia("+index+");'> <i class='fa fa-pencil' aria-hidden='true'></i> </span> <span title='Eliminar referencia cargos' onclick='eliminarOtros("+index+");' class='btn-eliminar-item text-danger mr-2'> <i class='fa fa-trash-o' aria-hidden='true'></i> </span> </td>";
  
        if( !itemExistente ) {
          var htmlRow = "<tr class='referencia-tabla referencia-index-"+index+"' index='"+index+"' attr-num='"+numero+"' id='"+row_id+"' > " + htmlCols + "</tr>";
          $('#tabla-referencia-factura tbody').append(htmlRow);
        }else{
          itemExistente.append(htmlCols);
        }
  
        $('#tabla-referencia-factura').show();
  
        //Aumenta el indice de filas para evitar cualquier conflicto si hubo eliminados. El index nunca debe cambiar ni repetirse, los números pueden cambiar.
        $('#current-index-referencia').val(index);
  
        //Si estaba editando, quita la clase
        $('.referencia-factura-form').removeClass('editando');
  
        cerrarPopup('referencia-popup');
  
      }else{
          Swal.fire({
              type: 'error',
              title: 'Error',
              text: 'Por favor, asegúrese que todos los datos sean válidos antes de continuar.'
          })
          return false;
      }

  }
  
  //Carga la referencia para ser editada
  window.cargarFormReferencia = function( index ) {
    $('.referencia-factura-form').addClass('editando');

    var item = $('.referencia-index-'+index );
    $('#referencia-lnum').val( item.attr('attr-num') );
    $('#referencia').val( item.find('.item_id ').val() );
    $('#referencia-docType').val( item.find('.referencia-docType ').val() );
    $('#referencia-number').val( item.find('.referencia-number ').val() );
    $('#referencia-code').val( item.find('.referencia-code ').val() );
  }

  //Acción para cancelar la edición
  window.cancelarEdicionReferencia = function(){
    $('.referencia-factura-form').removeClass('editando');
  }

  //Elimina la referencia
  window.eliminarReferencia = function( index ){
    $('.referencia-index-'+index ).remove();
  }

$( document ).ready(function() {

  if( $("#tabla-items-factura").length ) {

    $('#cantidad, #precio_unitario, #discount, #discount_type').on('keyup', function(){
      calcularSubtotalItem();
    });

    $('#cantidad, #precio_unitario, #discount, #discount_type').on('change', function(){
      calcularSubtotalItem();
    });

    $('#tipo_iva').on('change', function(){
      presetTipoIVA();
      presetPorcentaje();
      calcularSubtotalItem();
      togglePorcentajeIdentificacionPlena();
      if( $('#tipo_iva').val() ){
        if( $('#tipo_iva').val().charAt(0) == 'S' ) {  
          $('#unidad_medicion').val('Sp');
        } else{ 
          $('#unidad_medicion').val('Unid'); 
        }
      }
      
    });

    $('#tipo_producto').on('change', function(){
      toggleCamposExoneracion();
    });

    $('#item_iva_amount').on('change', function(){
      calcularConIvaManual();
    });

    $('#item_iva_amount.not-fec').on('click', function(){
      alert('Puede cambiar el monto de IVA manualmente, pero se recomienda utilizar el monto calculado automáticamente.');
    });

    $('#cliente_exento').on('change', function(){

      if( $('#cliente_exento:checked').length ){
        $('#tipo_iva').val('S260');
        $('#tipo_iva').prop('readonly', true);
      }else{
        presetTipoIVA();
        presetPorcentaje();
        calcularSubtotalItem();
        $('#tipo_iva').prop('readonly', false);
      }

      calcularSubtotalItem();
      calcularTotalFactura();

    });

    if($('#tipo_compra').length){
      $('#tipo_compra').on('change', function(){
      	if( $('#tipo_compra').val() == 'import' ){
      		jQuery('#tipo_iva option').addClass('hidden')
      		jQuery('#tipo_iva option:contains("Importaciones de ser")').removeClass('hidden');
      		jQuery('#tipo_iva').val('S023').change();
        }else{
      		jQuery('#tipo_iva option').addClass('hidden')
      		jQuery('#tipo_iva option:contains("Compras loc")').removeClass('hidden');
      		jQuery('#tipo_iva').val('S003').change();
        }
      });
    }

    $('.inputs-fecha').datetimepicker({
          format: 'DD/MM/Y',
          allowInputToggle: true,
          icons : {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check-o',
                clear: 'fa fa-times',
                close: 'fa fa-calendar-times-o'
          }
    });

    $('.inputs-hora').datetimepicker({
          format: 'h:mm A',
          allowInputToggle: true,
          icons : {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check-o',
                clear: 'fa fa-times',
                close: 'fa fa-calendar-times-o'
          }
    });

  }else{

    if( $("#tipo_producto").length && $("#tipo_iva").length ) {
      $('#tipo_iva').on('change', function(){
        presetTipoIVA();
        if( $('#tipo_iva').val().charAt(0) == 'S' ) {  $('#unidad_medicion').val('Sp') } else{ $('#unidad_medicion').val('Unid') }
      });
    }

  }

});
