  window.calcularSubtotalItem = function(){

    var precio_unitario = parseFloat( $('#precio_unitario').val() );
    var cantidad = parseInt( $('#cantidad').val() );
    var porc_iva = parseFloat( $('#porc_iva').val() );
    var monto_iva = parseFloat( $('#item_iva_amount').val() );
    
    if( !monto_iva ) {
      monto_iva = 0;
    }
    
    if( precio_unitario && cantidad ){
      var subtotal = cantidad * precio_unitario;
      
      var discount = parseFloat( $('#discount').val() );
      if( !discount ) {
        $('#discount').val(0);
      }
      var discount_type = $('#discount_type').val();
      if( discount_type == "01" && discount > 0 ) {
        subtotal = subtotal - ( subtotal * (discount / 100) );
      }else {
        subtotal = subtotal - discount;
      }
      
      $('#item_subtotal').val( subtotal );
      if( $('#porc_iva').val().length ){
        monto_iva = subtotal * porc_iva / 100;
        $('#item_iva_amount').val( monto_iva );
        $('#item_total').val( subtotal + monto_iva );
      }else{
        $('#item_total').val( subtotal );
      }
    }else{
      $('#item_subtotal').val( 0 );
      $('#item_total').val( 0 );
      $('#item_iva_amount').val( 0 );
    }

  }

  window.calcularConIvaManual = function(){
    
    var precio_unitario = parseFloat( $('#precio_unitario').val() );
    var cantidad = parseInt( $('#cantidad').val() );
    var monto_iva = parseFloat( $('#item_iva_amount').val() );
    
    if( !monto_iva ) {
      monto_iva = 0;
      $('#item_iva_amount').val(0);
    }
    
    if( precio_unitario && cantidad ){
      var subtotal = cantidad * precio_unitario;
      
      var discount = parseFloat( $('#discount').val() );
      var discount_type = $('#discount_type').val();
      if( discount_type == "01" && discount > 0 ) {
        subtotal = subtotal - ( subtotal * (discount / 100) );
      }else {
        subtotal = subtotal - discount;
      }
      
      $('#item_subtotal').val( subtotal );
      if( monto_iva ){
        $('#item_total').val( subtotal + monto_iva );
      }else{
        $('#item_total').val( subtotal );
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
      var tipoIVA = $('#tipo_producto :selected').attr('codigo');
      $('#tipo_iva').val( tipoIVA );
    }else{
      $('#tipo_iva').val( '260' );
    }
  }

  window.togglePorcentajeIdentificacionPlena = function(){
    if( ('#field_porc_identificacion_plena').length ){
      var tipo_iva = parseFloat( $('#tipo_iva').val() );
      
      if( tipo_iva >= 40 && tipo_iva <= 74 ){
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
    
    //Si esta editando, usa lnum y item_id para identificar la fila.
    var lnum = $('#lnum').val() ? $('#lnum').val() : '';
    var item_id = $('#item_id').val() ? $('#item_id').val() : '';
    
    var numero = parseInt( $('.item-tabla:last-of-type').attr('attr-num') ) + 1;
    var index = parseInt( $('#current-index').val() ) + 1;
    var codigo = $('#codigo').val();
    var nombre = $('#nombre').val();
    var tipo_producto = $('#tipo_producto').val();
    var cantidad = $('#cantidad').val();
    var unidad_medicion = $('#unidad_medicion').val();
    var precio_unitario = $('#precio_unitario').val();
    var porc_identificacion_plena = $('#porc_identificacion_plena').val();
    var is_identificacion_especifica = $('#is_identificacion_especifica:checked').length;
    var descuento = $('#discount').val();
    var tipo_descuento = $('#discount_type').val();
    var tipo_iva = $('#tipo_iva').val();
    var tipo_iva_text = $('#tipo_iva :selected').text();
    var porc_iva = $('#porc_iva').val();
    var monto_iva = $('#item_iva_amount').val();
    var subtotal = $('#item_subtotal').val();
    var total = $('#item_total').val();
    var typeDocument = $('#typeDocument').val();
    var numeroDocumento = $('#numeroDocumento').val();
    var nombreInstitucion = $('#nombreInstitucion').val();
    var porcentajeExoneracion = $('#porcentajeExoneracion').val();
    var montoExoneracion = $('#montoExoneracion').val();
    var impuestoNeto = $('#impuestoNeto').val();
    var montoTotalLinea = $('#montoTotalLinea').val();

    if( !monto_iva ) {
      monto_iva = 0;
      $('#item_iva_amount').val(0);
    }
    
    if( $( '#document_number').val() == "TOTALES2018" ) {
      codigo = $('#codigo').val( "L" + numero  );
      nombre = $('#nombre').val( "TIPO-" + tipo_iva  );
    }
    
    //Se asegura de que los campos hayan sido llenados
    if( subtotal && codigo && nombre && precio_unitario && cantidad && tipo_iva){
      
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
                   "<input type='hidden' class='numero' name='items["+index+"][item_number]' value='"+(numero+1)+"'>" +
                   "<input class='item_id' type='hidden' name='items["+index+"][id]' value='"+item_id+"'>" +
                   "<input type='hidden' class='codigo' name='items["+index+"][code]' value='"+codigo+"'>" +
                   "<input type='hidden' class='nombre' name='items["+index+"][name]' value='"+nombre+"'>" +
                   "<input type='hidden' class='tipo_producto' name='items["+index+"][product_type]' value='"+tipo_producto+"'>" +
                   "<input type='hidden' class='cantidad' name='items["+index+"][item_count]' value='"+cantidad+"'>" +
                   "<input type='hidden' class='unidad_medicion' name='items["+index+"][measure_unit]' value='"+unidad_medicion+"'>" +
                   "<input type='hidden' class='precio_unitario' name='items["+index+"][unit_price]' value='"+precio_unitario+"'>" +
                   "<input type='hidden' class='tipo_iva' name='items["+index+"][iva_type]' value='"+tipo_iva+"'>" +
                   "<input type='hidden' class='porc_identificacion_plena' name='items["+index+"][porc_identificacion_plena]' value='"+porc_identificacion_plena+"'>" +
                   "<input type='hidden' class='discount_type' name='items["+index+"][discount_type]' value='"+tipo_descuento+"'>" +
                   "<input type='hidden' class='discount' name='items["+index+"][discount]' value='"+descuento+"'>" +
                   "<input type='hidden' class='subtotal' name='items["+index+"][subtotal]' value='"+subtotal+"'>" +
                   "<input type='hidden' class='porc_iva' name='items["+index+"][iva_percentage]' value='"+porc_iva+"'>" +
                   "<input type='hidden' class='monto_iva' name='items["+index+"][iva_amount]' value='"+monto_iva+"'> " +
                   "<input type='hidden' class='total' name='items["+index+"][total]' value='"+total+"'>" +
                   "<input type='hidden' class='is_identificacion_especifica' name='items["+index+"][is_identificacion_especifica]' value='"+is_identificacion_especifica+"'>" +
                   "<input type='hidden' class='typeDocument' name='items["+index+"][typeDocument]' value='"+typeDocument+"'>" +
                   "<input type='hidden' class='numeroDocumento' name='items["+index+"][numeroDocumento]' value='"+numeroDocumento+"'>" +
                   "<input type='hidden' class='nombreInstitucion' name='items["+index+"][nombreInstitucion]' value='"+nombreInstitucion+"'>" +
                   "<input type='hidden' class='porcentajeExoneracion' name='items["+index+"][porcentajeExoneracion]' value='"+porcentajeExoneracion+"'>" +
                   "<input type='hidden' class='montoExoneracion' name='items["+index+"][montoExoneracion]' value='"+montoExoneracion+"'>" +
                   "<input type='hidden' class='impuestoNeto' name='items["+index+"][impuestoNeto]' value='"+impuestoNeto+"'>" +
                   "<input type='hidden' class='montoTotalLinea' name='items["+index+"][montoTotalLinea]' value='"+montoTotalLinea+"'>" +
              "</div>"
                   ;

      //Crea la fila en la tabla
      var htmlCols = "<td><span class='numero-fila'>"+(numero+1)+"</span> </td>";
        htmlCols += "<td>"+codigo + inputFields + " </td>";
        htmlCols += "<td>"+nombre+" </td>";
        htmlCols += "<td>"+cantidad+" </td>";
        htmlCols += "<td>"+unidad_medicion+" </td>";
        htmlCols += "<td>"+ fixComas(precio_unitario) +" </td>";
        htmlCols += "<td>"+tipo_iva_text+"  </td>";
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
      $('#p1').prop('checked', false);
      $('#p1').change();
      
    }else{
      alert('Debe completar los datos de la linea antes de guardarla');
    }
    
  }
  
  //Se encarga de limpiar el formulario de "Agregar items"
  window.limpiarFormItem = function(){
      $('.item-factura-form input, .item-factura-form select').val('');
      $('.item-factura-form input[type=checkbox]').prop('checked', false);
      
      $('#tipo_producto').val(1).change();
      $('#unidad_medicion').val('Unid');
      $('#cantidad').val(1);
      $('#porc_identificacion_plena').val(1);
      $('#discount_type').val('01');
      $('#discount').val(0);
      $('#tipo_producto').change();
  }

  //Carga la item para ser editada
  window.cargarFormItem = function( index ) {
    $('.item-factura-form').addClass('editando');
    
    var item = $('.item-index-'+index );
    $('#lnum').val( item.attr('attr-num') );
    $('#item_id').val( item.find('.item_id ').val() );
    $('#codigo').val( item.find('.codigo ').val() );
    $('#nombre').val( item.find('.nombre ').val() );
    $('#tipo_producto').val( item.find('.tipo_producto ').val() );
    $('#discount').val( item.find('.discount ').val() );
    $('#discount_type').val( item.find('.discount_type ').val() );
    $('#cantidad').val( item.find('.cantidad ').val() );
    $('#unidad_medicion').val( item.find('.unidad_medicion ').val() );
    $('#precio_unitario').val( item.find('.precio_unitario ').val() );
    $('#tipo_iva').val( item.find('.tipo_iva ').val() );
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

    
    if( parseInt(item.find('.is_identificacion_especifica').val()) ) {
      $('#is_identificacion_especifica').prop( 'checked', true );
    }else {
      $('#is_identificacion_especifica').prop( 'checked', false );
    }
    
    togglePorcentajeIdentificacionPlena();
    calcularConIvaManual();
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
    $i = 0;
    $( '.item-tabla' ).each( function(){
      $(this).attr( 'attr-num', $i );   
      $(this).attr( 'id', 'item-tabla-'+$i );   
      $(this).find( '.numero-fila').text($i+1 );
      $i++;        
    });
  }

  window.calcularTotalFactura = function() {
    var subtotal = 0;
    var monto_iva = 0;
    var total = 0;
    $('.item-tabla').each(function(){
      var s = parseFloat($(this).find('.subtotal').val());
      var m = parseFloat($(this).find('.monto_iva').val());
      var t = parseFloat($(this).find('.total').val());
      subtotal += s;
      monto_iva += m;	
      total += t;	
    });
    
    $('#subtotal').val(subtotal);
    $('#monto_iva').val(monto_iva);
    $('#total').val(total);
    
  }
  
  window.fixComas = function( numero ) {
    numero = parseFloat(numero);
    return numero.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2});
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
        var id = $('#codigo').val();
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
                        $('#nombre').val(result.name);
                        $('#unidad_medicion').val(result.measure_unit);
                        $('#precio_unitario').val(result.unit_price);
                        $('#tipo_producto').val(result.product_category_id);
                        $('#tipo_iva').val(result.default_iva_type);

                        $('#precio_unitario').change();
                        $('#tipo_iva').change();
                    }
                }
            });
        }else{
            alert('Debe digitar un código numeral para la búsqueda');
        }
    }
    
   window.calcularMontoExoneracion = function() {
        var porcentajeExonerado = $('#porcentajeExoneracion').val();
        if(porcentajeExonerado > 0) {
            var monto_iva_detalle = $('#item_iva_amount').val();
            var monto = monto_iva_detalle * (porcentajeExonerado / 100);
            var impNeto = monto_iva_detalle - monto;
            var subTotal = $('#item_subtotal').val();
            var montoTotal = parseFloat(subTotal) + parseFloat(impNeto);

            $('#montoExoneracion').val(monto);
            $('#impuestoNeto').val(impNeto);
            $('#montoTotalLinea').val(montoTotal);

        }
    }
    
    window.mostrarCamposExoneracion = function() {
        var checkExoneracion = $('#checkExoneracion').prop('checked');
        console.log(checkExoneracion);
        if(checkExoneracion === true){
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
        }
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
      presetPorcentaje();
      calcularSubtotalItem();
      togglePorcentajeIdentificacionPlena();
    });
  
    $('#tipo_producto').on('change', function(){
      
      if( $(this).val() == 2 ) {  $('#unidad_medicion').val('Sp') } else{ $('#unidad_medicion').val('Unid') }
      
      presetTipoIVA();
      presetPorcentaje();
      calcularSubtotalItem();
      togglePorcentajeIdentificacionPlena();
    });
    
    $('#item_iva_amount').on('change', function(){
      calcularConIvaManual();
    });
    
    $('#item_iva_amount').on('click', function(){
      alert('Puede cambiar el monto de IVA manualmente, pero se recomienda utilizar el monto calculado automáticamente.');
    });
    
    $('#cliente_exento').on('change', function(){
  
      if( $('#cliente_exento:checked').length ){
        $('#tipo_iva').val('260');
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
    
  }
  
});
