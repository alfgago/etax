  window.calcularSubtotalItem = function(){

    var precio_unitario = parseFloat( $('#precio_unitario').val() );
    var cantidad = parseInt( $('#cantidad').val() );
    var porc_iva = parseFloat( $('#porc_iva').val() );
    var monto_iva = parseFloat( $('#item_iva_amount').val() );
    
    if( precio_unitario && cantidad ){
      var subtotal = cantidad * precio_unitario;
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
    
    if( precio_unitario && cantidad ){
      var subtotal = cantidad * precio_unitario;
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
    var descuento = '';
    var razon_descuento = '';
    var tipo_iva = $('#tipo_iva').val();
    var tipo_iva_text = $('#tipo_iva :selected').text();
    var porc_iva = $('#porc_iva').val();
    var monto_iva = $('#item_iva_amount').val();
    var subtotal = $('#item_subtotal').val();
    var total = $('#item_total').val();
    
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
      
      //Crea la fila en la tabla
      var htmlCols = "<td><span class='numero-fila'>"+(numero+1)+"</span><input type='hidden' class='numero' name='items["+index+"][item_number]' value='"+(numero+1)+"'> <input class='item_id' type='hidden' name='items["+index+"][id]' value='"+item_id+"'> </td>";
        htmlCols += "<td>"+codigo+" <input type='hidden' class='codigo' name='items["+index+"][code]' value='"+codigo+"'></td>";
        htmlCols += "<td>"+nombre+" <input type='hidden' class='nombre' name='items["+index+"][name]' value='"+nombre+"'></td>";
        htmlCols += "<td>"+tipo_producto+" <input type='hidden' class='tipo_producto' name='items["+index+"][product_type]' value='"+tipo_producto+"'></td>";
        htmlCols += "<td>"+cantidad+" <input type='hidden' class='cantidad' name='items["+index+"][item_count]' value='"+cantidad+"'></td>";
        htmlCols += "<td>"+unidad_medicion+" <input type='hidden' class='unidad_medicion' name='items["+index+"][measure_unit]' value='"+unidad_medicion+"'></td>";
        htmlCols += "<td>"+ fixComas(precio_unitario) +" <input type='hidden' class='precio_unitario' name='items["+index+"][unit_price]' value='"+precio_unitario+"'></td>";
        htmlCols += "<td>"+tipo_iva_text+" <input type='hidden' class='tipo_iva' name='items["+index+"][iva_type]' value='"+tipo_iva+"'> <input type='hidden' class='porc_identificacion_plena' name='items["+index+"][porc_identificacion_plena]' value='"+porc_identificacion_plena+"'></td>";
        htmlCols += "<td>"+ fixComas(subtotal) +" <input class='subtotal' type='hidden' name='items["+index+"][subtotal]' value='"+subtotal+"'></td>";
        htmlCols += "<td>"+ fixComas(monto_iva) +" <input class='porc_iva' type='hidden' name='items["+index+"][iva_percentage]' value='"+porc_iva+"'> <input class='monto_iva' type='hidden' name='items["+index+"][iva_amount]' value='"+monto_iva+"'> </td>";
        htmlCols += "<td>"+ fixComas(total) +" <input class='total' type='hidden' name='items["+index+"][total]' value='"+total+"'> <input class='is_identificacion_especifica' type='hidden' name='items["+index+"][is_identificacion_especifica]' value='"+is_identificacion_especifica+"'> </td>";
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
      $('#unidad_medicion').val(1);
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
    $('#cantidad').val( item.find('.cantidad ').val() );
    $('#unidad_medicion').val( item.find('.unidad_medicion ').val() );
    $('#precio_unitario').val( item.find('.precio_unitario ').val() );
    $('#tipo_iva').val( item.find('.tipo_iva ').val() );
    $('#item_subtotal').val( item.find('.subtotal ').val() );
    $('#porc_iva').val( item.find('.porc_iva ').val() );
    $('#item_iva_amount').val( item.find('.monto_iva ').val() );
    $('#porc_identificacion_plena').val( item.find('.porc_identificacion_plena ').val() );
    
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
    return numero.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
  }
  
  window.toggleRetencion = function() {
    var metodo = $("#medio_pago").val();
    if( metodo == '02' ){
      $("#field-retencion").show();
    }else {
      $("#field-retencion").hide();
    }
  }

$( document ).ready(function() {

  if( $("#tabla-items-factura").length ) {
  
    $('#cantidad, #precio_unitario').on('keyup', function(){
      calcularSubtotalItem();
    });
  
    $('#tipo_iva').on('change', function(){
      presetPorcentaje();
      calcularSubtotalItem();
      togglePorcentajeIdentificacionPlena();
    });
  
    $('#tipo_producto').on('change', function(){
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