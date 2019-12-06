<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function() {
	Route::post('notificaciones', 'NotificationController@create');
	Route::post('login', 'AuthController@login');
    Route::post('refresh-token', 'AuthController@refreshToken');
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('test', 'AuthController@test');
        //Facturas de Venta
        Route::prefix('facturas-venta')->group(function() {
            Route::post('emitir', 'InvoiceController@sendHaciendaApi')->name('InvoiceController.emitir'); // Recibe JSON con todos los datos de un XML de Hacienda para registro, envia a Hacienda
            Route::post('anular/{clave}', 'InvoiceController@')->name('FacturaVenta.anular'); //Recibe la clave de factura y crea nota de credito
            Route::post('registrar', 'InvoiceController@')->name('FacturaVenta.registrar'); // Recibe JSON con todos los datos de un XML de Hacienda para registro, no envia a Hacienda
            Route::post('validar', 'InvoiceController@')->name('FacturaVenta.validar'); // Recibe JSON con clave, lineas, codigo eTAX y categoría de declaración
            Route::post('autorizar-correo/{clave}', 'InvoiceController@')->name('FacturaVenta.autorizarCorreo'); // Recibe clave y autoriza la enviada por correo
            Route::post('consulta', 'InvoiceController@getInvoice')->name('FacturaVenta.consultar'); // Consulta existencia de una factura por clave
            Route::get('consultar-hacienda{clave}', 'InvoiceController@')->name('FacturaVenta.consultarHacienda'); //Recibe clave de factura y retorna estado con hacienda.
            Route::get('descargar-xml/{clave}', 'InvoiceController@')->name('FacturaVenta.descargarXml'); // Descarga XML firmado. Si no existe, eTax devuelve mensaje indicando que fue emitida por otro sistema. Devuele URL temporal de S3 si nosotros lo guardamos
            Route::get('descargar-xml-respuesta/{clave}', 'InvoiceController@')->name('FacturaVenta.descargarXmlRespuesta'); // Descargar XML de respuesta. eTax lo consulta a Hacienda, si no existe indica el problema
            Route::post('lista', 'InvoiceController@listInvoice')->name('FacturaVenta.lista'); // Retorna JSON paginado de 50 en 50. Con todas las facturas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-correo', 'InvoiceController@')->name('FacturaVenta.listaCorreo'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no autorizadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-no-validadas', 'InvoiceController@')->name('FacturaVenta.listaNoValidadas'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no validadas, sus lineas, pagina actual y cantidad total de paginas.
        });

        //Facturas de Compra
        Route::prefix('facturas-compra')->group(function() {
            Route::post('aceptar', 'BillController@sendAcceptMessage')->name('FacturaCompra.aceptar'); //Recibe la clave de la factura y acepta la factura
            Route::post('registrar', 'BillController@store')->name('FacturaCompra.registrar'); // Recibe JSON con todos los datos de un XML de Hacienda para registro, no envia a Hacienda
            Route::post('validar', 'BillController@validateBill')->name('FacturaCompra.validar'); // Recibe JSON con clave, lineas, codigo eTAX y categoría de declaración
            Route::post('autorizar-correo/{clave}', 'BillController@')->name('FacturaCompra.autorizarCorreo'); // Recibe clave y autoriza la enviada por correo
            Route::post('consultar', 'BillController@getBill')->name('FacturaCompra.consultar'); // Consulta existencia de una factura por clave
            Route::get('consultar-hacienda{clave}', 'FacturaCompraController@')->name('FacturaCompra.consultarHacienda'); //Recibe clave de factura y retorna estado con hacienda.
            Route::get('descargar-xml/{clave}', 'BillController@')->name('FacturaCompra.descargarXml'); // Descarga XML firmado. Si no existe, eTax devuelve mensaje indicando que fue emitida por otro sistema. Devuele URL temporal de S3 si nosotros lo guardamos
            Route::get('descargar-xml-respuesta/{clave}', 'BillController@')->name('FacturaCompra.descargarXmlRespuesta'); // Descargar XML de respuesta. eTax lo consulta a Hacienda, si no existe indica el problema
            Route::post('lista', 'BillController@listBill')->name('FacturaCompra.lista'); // Retorna JSON paginado de 50 en 50. Con todas las facturas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-correo', 'BillController@')->name('FacturaCompra.listaCorreo'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no autorizadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-no-validadas', 'BillController@')->name('FacturaCompra.listaNoValidadas'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no validadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-no-aceptadas', 'BillController@')->name('FacturaCompra.listaNoAceptadas'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no aceptadas, sus lineas, pagina actual y cantidad total de paginas.
        });



        //Cierres de mes
        Route::prefix('cierres')->group(function() {
            Route::post('lista-cierres', 'BookController@listBook')->name('Cierres.lista-cierres');//Retorna Json con todos los cierres de mes
            Route::post('cerrar-mes', 'BookController@Close')->name('Cierres.cerrarMes'); //Se recibe el id del mes y se cierra ese mes.
            Route::post('rectificacion', 'BookController@openForRectification'); //Se recibe el id del mes y se abre nuevamente ese mes
            Route::post('retenciones-tarjeta', 'BookController@Retenciones')->name('Libro.retencionesTarjeta'); //Se recibe id y se retorna Json con la informacion de las retenciones de tarjetas de ese id
            Route::post('actualizar-retencion-tarjeta', 'BookController@ActualizarRetencion')->name('Libro.actualizaRetencionTarjeta');//Se recibe Json con los datos de la retencion de la tarjeta y se actualiza la informacion
        });

        //Reportes
        Route::prefix('reportes')->group(function() {
            Route::post('cuentas-contables', 'ReportsController@reporteCuentasContables')->name('Reporte.reporteCuentasContables'); //Retorna Json con los datos de las cuentas contables.
            Route::get('resumen-ejecutivo', 'ReportsController@'); //Retorna Json con los datos del resumen ejecutivo
            Route::post('detalle-debito', 'ReportsController@reporteDetalleDebitoFiscal'); //Retorna Json con el los datos del detalle debito
            Route::post('detalle-credito', 'ReportsController@reporteDetalleCreditoFiscal'); //Retorna Json con el los datos de detalle credito
            Route::get('libro-ventas', 'ReportsController@'); //Retorna Json con el los datos libro de ventas
            Route::post('libro-compras', 'ReportsController@reporteLibroCompras')->name('Reporte.reporteLibroCompras'); //Retorna Json con el los datos libro de compras
            Route::get('borrador-iva', 'ReportsController@'); //Retorna Json con el los datos de la declaracion IVA
        });

        Route::prefix('utilidades')->group(function(){
            Route::get('codigos-venta', 'CodigoIvaRepercutidoController@getCode')->name('CodigoIvaRepercutidoController.getAllCode'); //Retorna Json con los codigos eTax
            Route::get('codigos-compra', 'CodigoIvaSoportadoController@getCode')->name('CodigoIvaSoportadoController.getAllCode'); //Retorna Json con los codigos eTax
            Route::get('categorias-hacienda', 'ProductCategoryController@getAllCategories' )->name('ProductCategoryController.getAllCategories');//Retorna Json con los codigos eTax
            Route::get('categorias-hacienda/{code}', 'ProductCategoryController@getCategories' )->name('ProductCategoryController.getCategories');//Retorna Json con los codigos eTax
            Route::get('unidades-medicion', 'UnidadMedicionController@getUnidades')->name('UnidadesMedicion.lista');//Retorna Json con todos las unidades de medicion
            Route::get('pais', 'CodigosPaisesController@getPaises')->name('CodigoPais.lista');//Retorna Json con todos las unidades de medicion
            Route::get('canton', '@lista')->name('Canton.lista');//Retorna Json con todos las unidades de medicion
            Route::get('distrito', '@lista')->name('Distrito.lista');//Retorna Json con todos las unidades de medicion
            Route::get('provincias', 'CodigosPaisesController@getProvincias')->name('Provincia.lista');//Retorna Json con las provincias
            Route::post('crear-compania', 'CompaniaController@')->name('Compania.crear'); //Crear una compania con los datos enviados por el cliente.
            Route::post('agregar-atv', 'CompaniaController@')->name('Compania.atv'); //Agrega el ATV a una empresas creada
            Route::post('crear-usuario', 'UsuarioController@')->name('Usuario.crear'); //Crear un usuario con los datos enviados por el cliente.
            Route::post('desactivar-compania', 'CompaniaController@')->name('Compania.borrar'); //desactivar una compania con los datos enviados por el cliente.
            Route::post('desactivar-usuario', 'UsuarioController@')->name('Usuario.borrar'); //desactivar un usuario con los datos enviados por el cliente.
            Route::post('login', '@login')->name('Login'); //login into the API
            Route::get('actividad-comerciales', 'ActividadesController@getAllActivities')->name('ActividadesController.getAllActivities'); //Retorna las actividades comerciales
            Route::post('actividad-comercial', 'ActividadesController@getActivities')->name('ActividadesController.getActivities'); //Retorna las actividades comerciales
        });
	});
});
Route::post('email-facturas', 'EmailController@receiveEmailXML');