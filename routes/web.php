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

Route::get('/', 'ReportsController@dashboard');
Route::resource('clientes', 'ClienteController');
Route::resource('productos', 'ProductoController');
Route::resource('empresas', 'EmpresaController');
Route::resource('facturas-emitidas', 'FacturaEmitidaController');
Route::resource('facturas-recibidas', 'FacturaRecibidaController');

Route::get('login', function () {
    return view('login');
})->name('login');;
