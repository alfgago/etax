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
});

Route::post('email-facturas', 'EmailController@receiveEmailXML');

Route::post('corbana-envio', 'CorbanaController@sendInvoice');
Route::post('corbana-anular', 'CorbanaController@anularInvoice');
Route::get('corbana-aceptar-rechazar/{id}', 'CorbanaController@aceptarRechazar');
Route::get('corbana-query-invoice/{id}', 'CorbanaController@queryInvoice');
Route::get('corbana-query-bills/{pCia}/{pAct}', 'CorbanaController@queryBills');
Route::get('corbana-query-bill-files/{pId}', 'CorbanaController@queryBillFiles');