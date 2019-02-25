/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 41);
/******/ })
/************************************************************************/
/******/ ({

/***/ 41:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(42);


/***/ }),

/***/ 42:
/***/ (function(module, exports) {

window.calcularSubtotalLinea = function () {

  var precio_unitario = parseFloat($('#precio_unitario').val());
  var cantidad = parseInt($('#cantidad').val());
  var porc_iva = parseFloat($('#porc_iva').val());

  if (precio_unitario && cantidad) {
    var subtotal = cantidad * precio_unitario;
    $('#linea_subtotal').val(subtotal);
    if (porc_iva) {
      $('#linea_total').val(subtotal + subtotal * (porc_iva / 100));
    } else {
      $('#linea_subtotal').val(subtotal);
      $('#linea_total').val(subtotal);
    }
  } else {
    $('#linea_subtotal').val(0);
    $('#linea_total').val(0);
  }
};

window.presetPorcentaje = function () {
  var tipo = $('#tipo_iva').val();
  var porcentaje = $('#tipo_iva :selected').attr('porcentaje');

  if ($('#cliente_exento:checked').length) {
    porcentaje = '0';
  }

  $('#porc_iva').val(porcentaje);
};

window.presetTipoIVA = function () {
  if (!$('#cliente_exento:checked').length) {
    var tipoIVA = $('#tipo_producto :selected').attr('codigo');
    $('#tipo_iva').val(tipoIVA);
  } else {
    $('#tipo_iva').val('260');
  }
};

window.agregarEditarLinea = function () {

  //Si esta editando, usa lnum y linea_id para identificar la fila.
  var lnum = $('#lnum').val() ? $('#lnum').val() : '';
  var linea_id = $('#linea_id').val() ? $('#linea_id').val() : '';

  var numero = parseInt($('.linea-tabla:last-of-type').attr('attr-num')) + 1;
  var index = parseInt($('#current-index').val()) + 1;
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
  var subtotal = $('#linea_subtotal').val();
  var total = $('#linea_total').val();

  //Se asegura de que los campos hayan sido llenados
  if (subtotal && codigo && nombre && precio_unitario && cantidad) {

    //Crear el ID de la fila.
    var lineaExistente = false;
    if (lnum && lnum !== '') {
      numero = lnum;
      lineaExistente = $('#linea-tabla-' + numero);
      index = lineaExistente.attr('index');
      lineaExistente.html("");
    }
    var row_id = "linea-tabla-" + numero;

    //Crea la fila en la tabla
    var htmlCols = "<td><span class='numero-fila'>" + (numero + 1) + "</span><input type='hidden' class='numero' name='lineas[" + index + "][numero]' value='" + (numero + 1) + "'> <input class='linea_id' type='hidden' name='lineas[" + index + "][id]' value='" + linea_id + "'> </td>";
    htmlCols += "<td>" + codigo + " <input type='hidden' class='codigo' name='lineas[" + index + "][codigo]' value='" + codigo + "'></td>";
    htmlCols += "<td>" + nombre + " <input type='hidden' class='nombre' name='lineas[" + index + "][nombre]' value='" + nombre + "'></td>";
    htmlCols += "<td>" + tipo_producto + " <input type='hidden' class='tipo_producto' name='lineas[" + index + "][tipo_producto]' value='" + tipo_producto + "'></td>";
    htmlCols += "<td>" + cantidad + " <input type='hidden' class='cantidad' name='lineas[" + index + "][cantidad]' value='" + cantidad + "'></td>";
    htmlCols += "<td>" + unidad_medicion + " <input type='hidden' class='unidad_medicion' name='lineas[" + index + "][unidad_medicion]' value='" + unidad_medicion + "'></td>";
    htmlCols += "<td>" + precio_unitario + " <input type='hidden' class='precio_unitario' name='lineas[" + index + "][precio_unitario]' value='" + precio_unitario + "'></td>";
    htmlCols += "<td>" + tipo_iva + " <input type='hidden' class='tipo_iva' name='lineas[" + index + "][tipo_iva]' value='" + tipo_iva + "'></td>";
    htmlCols += "<td>" + subtotal + " <input class='subtotal' type='hidden' name='lineas[" + index + "][subtotal]' value='" + subtotal + "'></td>";
    htmlCols += "<td>" + porc_iva + " <input class='porc_iva' type='hidden' name='lineas[" + index + "][porc_iva]' value='" + porc_iva + "'></td>";
    htmlCols += "<td>" + total + " <input class='total' type='hidden' name='lineas[" + index + "][total]' value='" + total + "'></td>";
    htmlCols += "<td class='acciones'><span class='btn-editar-linea text-success mr-2' title='Editar linea' onclick='cargarFormLinea(" + index + ");'><i class='nav-icon i-Pen-2'></i> </span> <span title='Eliminar linea' onclick='eliminarLinea(" + index + ");' class='btn-eliminar-linea text-danger mr-2'><i class='nav-icon i-Close-Window'></i> </span> </td>";

    if (!lineaExistente) {
      var htmlRow = "<tr class='linea-tabla linea-index-" + index + "' index='" + index + "' attr-num='" + numero + "' id='" + row_id + "' > " + htmlCols + "</tr>";
      $('#tabla-lineas-factura tbody').append(htmlRow);
    } else {
      lineaExistente.append(htmlCols);
    }

    $('#tabla-lineas-factura').show();

    //Limpia los datos del formulario
    limpiarFormLinea();

    //Recalcula números para asegurar que no haya vacíos
    recalcularNumerosLinea();

    //Calcula total de factura
    calcularTotalFactura();

    //Aumenta el indice de filas para evitar cualquier conflicto si hubo eliminados. El index nunca debe cambiar ni repetirse, los números pueden cambiar.
    $('#current-index').val(index);

    //Si estaba editando, quita la clase
    $('.linea-factura-form').removeClass('editando');
  } else {
    alert('Debe completar los datos de la linea antes de guardarla');
  }
};

//Se encarga de limpiar el formulario de "Agregar lineas"
window.limpiarFormLinea = function () {
  $('.linea-factura-form input, .linea-factura-form select').val('');
  $('#tipo_producto').val('Bienes generales').change();
  $('#unidad_medicion').val(1);
  $('#cantidad').val(1);
};

//Carga la linea para ser editada
window.cargarFormLinea = function (index) {

  $('.linea-factura-form').addClass('editando');

  var linea = $('.linea-index-' + index);
  $('#lnum').val(linea.attr('attr-num'));
  $('#linea_id').val(linea.find('.linea_id ').val());
  $('#codigo').val(linea.find('.codigo ').val());
  $('#nombre').val(linea.find('.nombre ').val());
  $('#tipo_producto').val(linea.find('.tipo_producto ').val());
  $('#cantidad').val(linea.find('.cantidad ').val());
  $('#unidad_medicion').val(linea.find('.unidad_medicion ').val());
  $('#precio_unitario').val(linea.find('.precio_unitario ').val());
  $('#tipo_iva').val(linea.find('.tipo_iva ').val());
  $('#linea_subtotal').val(linea.find('.subtotal ').val());
  $('#porc_iva').val(linea.find('.porc_iva ').val());

  calcularSubtotalLinea();
};

//Acción para cancelar la edición
window.cancelarEdicion = function () {
  $('.linea-factura-form').removeClass('editando');
  limpiarFormLinea();
};

//Elimina la linea
window.eliminarLinea = function (index) {
  $('.linea-index-' + index).remove();
  recalcularNumerosLinea();
  calcularTotalFactura();
};

//Recalcula números para asegurar que no haya vacíos
window.recalcularNumerosLinea = function () {
  $i = 0;
  $('.linea-tabla').each(function () {
    $(this).attr('attr-num', $i);
    $(this).attr('id', 'linea-tabla-' + $i);
    $(this).find('.numero-fila').text($i + 1);
    $i++;
  });
};

window.calcularTotalFactura = function () {
  var subtotal = 0;
  var monto_iva = 0;
  var total = 0;
  $('.linea-tabla').each(function () {
    var s = parseFloat($(this).find('.subtotal').val());
    var m = parseFloat($(this).find('.porc_iva').val()) / 100;
    var t = parseFloat($(this).find('.total').val());
    subtotal += s;
    monto_iva += s * m;
    total += t;
  });

  $('#subtotal').val(subtotal);
  $('#monto_iva').val(monto_iva);
  $('#total').val(total);
};

$(document).ready(function () {

  $('#cantidad, #precio_unitario').on('keyup', function () {
    calcularSubtotalLinea();
  });

  $('#tipo_iva').on('change', function () {
    presetPorcentaje();
    calcularSubtotalLinea();
  });

  $('#tipo_producto').on('change', function () {
    presetTipoIVA();
    presetPorcentaje();
    calcularSubtotalLinea();
  });

  $('#cliente_exento').on('change', function () {

    if ($('#cliente_exento:checked').length) {
      $('#tipo_iva').val('260');
      $('#tipo_iva').prop('readonly', true);
    } else {
      presetTipoIVA();
      presetPorcentaje();
      calcularSubtotalLinea();
      $('#tipo_iva').prop('readonly', false);
    }

    calcularSubtotalLinea();
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
    labelYearSelect: 'Elegir año'
  });

  $('.input-hora').pickatime({
    interval: 1,
    clear: 'Limpiar'
  });
});

/***/ })

/******/ });