<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Rutas de exportación
Route::get('clientes/exportar', 'ClientController@export');
Route::get('proveedores/exportar', 'ProviderController@export');
Route::get('facturas-emitidas/exportar', 'InvoiceController@export');
Route::get('facturas-recibidas/exportar', 'BillController@export');

// Rutas de importación
Route::post('clientes/importar', 'ClientController@import');
Route::post('proveedores/importar', 'ProviderController@import');
Route::post('facturas-emitidas/importar', 'InvoiceController@import');
Route::post('facturas-recibidas/importar', 'BillController@import');

// Rutas de reportes
Route::get('/', 'ReportsController@dashboard');
Route::get('/dashboard', 'ReportsController@dashboard');
Route::get('/reportes', 'ReportsController@reports');
Route::get('/reportes/cuentas-contables', 'ReportsController@ccReport');
Route::get('/reportes/reporte-ejecutivo', 'ReportsController@reporteEjecutivo');

// Rutas autogeneradas de CRUD
Route::resource('clientes', 'ClientController');
Route::resource('proveedores', 'ProviderController');
Route::resource('productos', 'ProductController');
Route::resource('empresas', 'CompanyController');
Route::resource('facturas-emitidas', 'InvoiceController');
Route::resource('facturas-recibidas', 'BillController');


Route::get('login', function () {
    return view('login');
})->name('login');;

Auth::routes();

