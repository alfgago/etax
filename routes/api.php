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
    Route::post('login', 'AuthController@login');
    Route::post('refresh-token', 'AuthController@refreshToken');
	Route::post('notificaciones', 'NotificationController@create');
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('actividades-economicas', 'CompanyAPIController@getActivities')->name('CompanyAPIController.getActivities');
        Route::post('emitir-factura', 'InvoiceAPIController@emitir')->name('InvoiceAPIController.emitirFactura');
        //Facturas de Venta
        Route::prefix('facturas-venta')->group(function() {
            Route::post('emitir', 'InvoiceAPIController@emitir')->name('InvoiceAPIController.emitir'); // Recibe JSON con todos los datos de un XML de Hacienda para registro, envia a Hacienda
            Route::post('emitir/n-credito/{key}', 'InvoiceAPIController@anularInvoice')->name('FacturaVenta.notaCredito'); //Recibe la clave de factura y crea nota de credito
            Route::post('emitir/n-debito/{key}', 'InvoiceAPIController@sendNotaDebito')->name('FacturaVenta.notaDebito'); //Recibe la clave de factura y crea nota de credito
            Route::post('registrar', 'InvoiceAPIController@')->name('FacturaVenta.registrar'); // Recibe JSON con todos los datos de un XML de Hacienda para registro, no envia a Hacienda
            Route::post('validar', 'InvoiceAPIController@validateInvoice')->name('FacturaVenta.validar'); // Recibe JSON con clave, lineas, codigo eTAX y categoría de declaración
            Route::post('autorizar-correo', 'InvoiceAPIController@authorizeInvoice')->name('FacturaVenta.autorizarCorreo'); // Recibe clave y autoriza la enviada por correo
            Route::get('consultar', 'InvoiceAPIController@getInvoice')->name('FacturaVenta.consultar'); // Consulta existencia de una factura por clave
            //Route::get('consultar-hacienda{clave}', 'InvoiceController@')->name('FacturaVenta.consultarHacienda'); //Recibe clave de factura y retorna estado con hacienda.
            Route::get('descargar-xml/{clave}', 'InvoiceAPIController@')->name('FacturaVenta.descargarXml'); // Descarga XML firmado. Si no existe, eTax devuelve mensaje indicando que fue emitida por otro sistema. Devuele URL temporal de S3 si nosotros lo guardamos
            Route::get('descargar-xml-respuesta/{clave}', 'InvoiceAPIController@')->name('FacturaVenta.descargarXmlRespuesta'); // Descargar XML de respuesta. eTax lo consulta a Hacienda, si no existe indica el problema
            Route::post('lista', 'InvoiceAPIController@listInvoice')->name('FacturaVenta.lista'); // Retorna JSON paginado de 50 en 50. Con todas las facturas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-correo', 'InvoiceAPIController@')->name('FacturaVenta.listaCorreo'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no autorizadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-no-validadas', 'InvoiceAPIController@')->name('FacturaVenta.listaNoValidadas'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no validadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('consultar-hacienda', 'InvoiceAPIController@consultarHacienda')->name('FacturaVenta.consultarHacienda'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no validadas, sus lineas, pagina actual y cantidad total de paginas.

        });

        //Facturas de Compra
        Route::prefix('facturas-compra')->group(function() {
            Route::post('aceptar', 'BillAPIController@sendAcceptMessage')->name('FacturaCompra.aceptar'); //Recibe la clave de la factura y acepta la factura
            Route::post('registrar', 'BillAPIController@store')->name('FacturaCompra.registrar'); // Recibe JSON con todos los datos de un XML de Hacienda para registro, no envia a Hacienda
            Route::post('validar', 'BillAPIController@validateBill')->name('FacturaCompra.validar'); // Recibe JSON con clave, lineas, codigo eTAX y categoría de declaración
            Route::post('autorizar-correo', 'BillAPIController@authorizeBill')->name('FacturaCompra.autorizarCorreo'); // Recibe clave y autoriza la enviada por correo
            Route::get('consultar', 'BillAPIController@getBill')->name('FacturaCompra.consultar'); // Consulta existencia de una factura por clave
            Route::get('descargar-xml/{clave}', 'BillAPIController@')->name('FacturaCompra.descargarXml'); // Descarga XML firmado. Si no existe, eTax devuelve mensaje indicando que fue emitida por otro sistema. Devuele URL temporal de S3 si nosotros lo guardamos
            Route::get('descargar-xml-respuesta/{clave}', 'BillAPIController@')->name('FacturaCompra.descargarXmlRespuesta'); // Descargar XML de respuesta. eTax lo consulta a Hacienda, si no existe indica el problema
            Route::get('lista', 'BillAPIController@listBill')->name('FacturaCompra.lista'); // Retorna JSON paginado de 50 en 50. Con todas las facturas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-correo', 'BillAPIController@')->name('FacturaCompra.listaCorreo'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no autorizadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-no-validadas', 'BillAPIController@')->name('FacturaCompra.listaNoValidadas'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no validadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('lista-no-aceptadas', 'BillAPIController@')->name('FacturaCompra.listaNoAceptadas'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no aceptadas, sus lineas, pagina actual y cantidad total de paginas.
            Route::get('consultar-hacienda', 'BillAPIController@consultarHacienda')->name('FacturaCompra.consultarHacienda'); // Retorna JSON paginado de 50 en 50. Con todas las facturas no validadas, sus lineas, pagina actual y cantidad total de paginas.
        });
        //Cierres de mes
        Route::prefix('cierres')->group(function() {
            Route::get('lista-cierres/{empresa}', 'BookController@listBook')->name('Cierres.lista-cierres');//Retorna Json con todos los cierres de mes
            Route::post('cerrar-mes', 'BookController@Close')->name('Cierres.cerrarMes'); //Se recibe el id del mes y se cierra ese mes.
            Route::post('rectificacion', 'BookController@openForRectification'); //Se recibe el id del mes y se abre nuevamente ese mes
            Route::get('retenciones-tarjeta', 'BookController@Retenciones')->name('Libro.retencionesTarjeta'); //Se recibe id y se retorna Json con la informacion de las retenciones de tarjetas de ese id
            Route::post('actualizar-retencion-tarjeta', 'BookController@ActualizarRetencion')->name('Libro.actualizaRetencionTarjeta');//Se recibe Json con los datos de la retencion de la tarjeta y se actualiza la informacion
        });

        //Reportes
        Route::prefix('reportes')->group(function() {
            Route::get('cuentas-contables', 'ReportsController@reporteCuentasContables')->name('Reporte.reporteCuentasContables'); //Retorna Json con los datos de las cuentas contables.
            Route::get('resumen-ejecutivo', 'ReportsController@'); //Retorna Json con los datos del resumen ejecutivo
            Route::get('detalle-debito', 'ReportsController@reporteDetalleDebitoFiscal'); //Retorna Json con el los datos del detalle debito
            Route::get('detalle-credito', 'ReportsController@reporteDetalleCreditoFiscal'); //Retorna Json con el los datos de detalle credito
            Route::get('libro-ventas', 'ReportsController@reporteLibroVentas'); //Retorna Json con el los datos libro de ventas
            Route::get('libro-compras', 'ReportsController@reporteLibroCompras')->name('Reporte.reporteLibroCompras'); //Retorna Json con el los datos libro de compras
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
            Route::get('actividad-comercial/{empresa}', 'ActividadesController@getActivities')->name('ActividadesController.getActivities'); //Retorna las actividades comerciales
        });

    });
});

Route::post('email-facturas', 'EmailController@receiveEmailXML');

Route::post('corbana-envio', 'CorbanaController@sendInvoice');
Route::post('corbana-anular', 'CorbanaController@anularInvoice');
Route::post('corbana-aceptar-rechazar/{id}', 'CorbanaController@aceptarRechazar');
Route::get('corbana-query-invoice/{id}', 'CorbanaController@queryInvoice');
Route::get('corbana-query-bills/{pCia}/{pAct}', 'CorbanaController@queryBills');
Route::get('corbana-query-bill-files/{pId}', 'CorbanaController@queryBillFiles');
Route::get('corbana-query-invoice-files/{pId}', 'CorbanaController@queryInvoiceFiles');
Route::get('corbana-usd', 'CorbanaController@getUSDRate');
Route::get('prueba-zttp', 'CorbanaController@pruebaZttp');

Route::get('corbana-getbyid/{pCia}/{pAct}/{pId}', 'CorbanaController@queryBillById');
