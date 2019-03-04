  window.calcularSubtotalItem = function(){

    var precio_unitario = parseFloat( $('#precio_unitario').val() );
    var cantidad = parseInt( $('#cantidad').val() );
    var porc_iva = parseFloat( $('#porc_iva').val() );
    
    if( precio_unitario && cantidad ){
      var subtotal = cantidad * precio_unitario;
      $('#item_subtotal').val( subtotal );
      if( porc_iva ){
        $('#item_total').val( subtotal + ( subtotal * (porc_iva/100) ) );
      }else{
        $('#item_subtotal').val( subtotal );
        $('#item_total').val( subtotal );
      }
    }else{
      $('#item_subtotal').val( 0 );
      $('#item_total').val( 0 );
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
    var descuento = '';
    var razon_descuento = '';
    var tipo_iva = $('#tipo_iva').val();
    var porc_iva = $('#porc_iva').val();
    var subtotal = $('#item_subtotal').val();
    var total = $('#item_total').val();
    
    //Se asegura de que los campos hayan sido llenados
    if( subtotal && codigo && nombre && precio_unitario && cantidad ){
      
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
        htmlCols += "<td>"+precio_unitario+" <input type='hidden' class='precio_unitario' name='items["+index+"][unit_price]' value='"+precio_unitario+"'></td>";
        htmlCols += "<td>"+tipo_iva+" <input type='hidden' class='tipo_iva' name='items["+index+"][iva_type]' value='"+tipo_iva+"'></td>";
        htmlCols += "<td>"+subtotal+" <input class='subtotal' type='hidden' name='items["+index+"][subtotal]' value='"+subtotal+"'></td>";
        htmlCols += "<td>"+porc_iva+" <input class='porc_iva' type='hidden' name='items["+index+"][iva_percentage]' value='"+porc_iva+"'></td>";
        htmlCols += "<td>"+total+" <input class='total' type='hidden' name='items["+index+"][total]' value='"+total+"'></td>";
        htmlCols += "<td class='acciones'><span class='btn-editar-item text-success mr-2' title='Editar linea' onclick='cargarFormItem("+index+");'><i class='nav-icon i-Pen-2'></i> </span> <span title='Eliminar linea' onclick='eliminarItem("+index+");' class='btn-eliminar-item text-danger mr-2'><i class='nav-icon i-Close-Window'></i> </span> </td>";

      
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
    }else{
      alert('Debe completar los datos de la item antes de guardarla');
    }
    
  }
  
  //Se encarga de limpiar el formulario de "Agregar items"
  window.limpiarFormItem = function(){
      $('.item-factura-form input, .item-factura-form select').val('');
      $('#tipo_producto').val('Bienes generales').change();
      $('#unidad_medicion').val(1);
      $('#cantidad').val(1);
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
    
    calcularSubtotalItem();
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
      var m = parseFloat($(this).find('.porc_iva').val()) / 100;
      var t = parseFloat($(this).find('.total').val());
      subtotal += s;
      monto_iva += s*m;	
      total += t;	
    });
    
    $('#subtotal').val(subtotal);
    $('#monto_iva').val(monto_iva);
    $('#total').val(total);
    
  }

$( document ).ready(function() {

  $('#cantidad, #precio_unitario').on('keyup', function(){
    calcularSubtotalItem();
  });

  $('#tipo_iva').on('change', function(){
    presetPorcentaje();
    calcularSubtotalItem();
  });

  $('#tipo_producto').on('change', function(){
    presetTipoIVA();
    presetPorcentaje();
    calcularSubtotalItem();
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


  $(".input-fecha").pickadate({
    monthsFull: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    weekdaysFull: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    weekdaysShort: ['D', 'L', 'K', 'M', 'J', 'V', 'S'],
    formatSubmit: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
    today: 'Hoy',
    clear: 'Limpiar',
    close: 'Cerrar',
    labelMonthNext: 'Siguiente',
    labelMonthPrev: 'Anterior',
    labelMonthSelect: 'Elegir mes',
    labelYearSelect: 'Elegir año',
  })

  $('.input-hora').pickatime({
    interval: 1,
    clear: 'Limpiar'
  })


});