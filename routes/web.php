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
Route::get('/dashboard', 'ReportsController@dashboard');
Route::resource('clientes', 'ClientController');
Route::resource('productos', 'ProductController');
Route::resource('empresas', 'CompanyController');
Route::resource('facturas-emitidas', 'InvoiceController');
Route::resource('facturas-recibidas', 'BillController');

Route::get('login', function () {
    return view('login');
})->name('login');;

Auth::routes();
