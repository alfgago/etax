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
/******/ 	return __webpack_require__(__webpack_require__.s = 43);
/******/ })
/************************************************************************/
/******/ ({

/***/ 43:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(44);


/***/ }),

/***/ 44:
/***/ (function(module, exports) {

function calcularSubtotalLinea() {

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
}

function presetPorcentaje() {
  var tipo = $('#tipo_iva').val();
  var porcentaje = $('#tipo_iva :selected').attr('porcentaje');

  if ($('#cliente_exento:checked').length) {
    porcentaje = '0';
  }

  $('#porc_iva').val(porcentaje);
}

function presetTipoIVA() {
  if (!$('#cliente_exento:checked').length) {
    var tipoIVA = $('#tipo_producto :selected').attr('codigo');
    $('#tipo_iva').val(tipoIVA);
  } else {
    $('#tipo_iva').val('260');
  }
}

function agregarLinea() {

  var numero = $('.linea-tabla').length + 1;
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

  if (subtotal && codigo && nombre) {
    var html = "<tr class='linea-tabla' id='linea-tabla-" + numero + "-" + codigo + "' >";
    html += "<td>" + numero + " <input type='hidden' name='lineas[" + numero + "][numero]' value='" + numero + "'> <input type='hidden' name='lineas[" + numero + "][id]'> </td>";
    html += "<td>" + codigo + " <input type='hidden' name='lineas[" + numero + "][codigo]' value='" + codigo + "'></td>";
    html += "<td>" + nombre + " <input type='hidden' name='lineas[" + numero + "][nombre]' value='" + nombre + "'></td>";
    html += "<td>" + tipo_producto + " <input type='hidden' name='lineas[" + numero + "][tipo_producto]' value='" + tipo_producto + "'></td>";
    html += "<td>" + cantidad + " <input type='hidden' name='lineas[" + numero + "][cantidad]' value='" + cantidad + "'></td>";
    html += "<td>" + unidad_medicion + " <input type='hidden' name='lineas[" + numero + "][unidad_medicion]' value='" + unidad_medicion + "'></td>";
    html += "<td>" + precio_unitario + " <input type='hidden' name='lineas[" + numero + "][precio_unitario]' value='" + precio_unitario + "'></td>";
    html += "<td>" + tipo_iva + " <input type='hidden' name='lineas[" + numero + "][tipo_iva]' value='" + tipo_iva + "'></td>";
    html += "<td>" + subtotal + " <input class='subtotal' type='hidden' name='lineas[" + numero + "][subtotal]' value='" + subtotal + "'></td>";
    html += "<td>" + porc_iva + " <input class='porc_iva' type='hidden' name='lineas[" + numero + "][porc_iva]' value='" + porc_iva + "'></td>";
    html += "<td>" + total + " <input class='total' type='hidden' name='lineas[" + numero + "][total]' value='" + total + "'></td>";
    html += "</tr>";

    $('#tabla-lineas-factura tbody').append(html);
    $('#tabla-lineas-factura').show();

    $('.linea-factura input, .linea-factura select').val('');
    $('#tipo_producto').val('Bienes generales').change();
    $('#unidad_medicion').val(1);
    $('#cantidad').val(1);

    calcularTotalFactura();
  } else {
    alert('Debe completar los datos de la linea antes de continuar');
  }
}

function calcularTotalFactura() {
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
}

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

  $('#btn-agregar-linea').on('click', function () {
    agregarLinea();
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

    presetIVA();
    calcularSubtotalLinea();
  });
});

/***/ })

/******/ });