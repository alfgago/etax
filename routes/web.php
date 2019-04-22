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

Route::get('login', function () {
    return view('login');
})->name('login');;

Auth::routes();

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
Route::post('/reportes/reporte-dashboard', 'ReportsController@reporteDashboard');
Route::post('/reportes/cuentas-contables', 'ReportsController@reporteCuentasContables');
Route::get('/reportes/resumen-ejecutivo', 'ReportsController@reporteEjecutivo');
Route::post('/reportes/detalle-debito', 'ReportsController@reporteDetalleDebitoFiscal');
Route::post('/reportes/detalle-credito', 'ReportsController@reporteDetalleCreditoFiscal');

//Cierres de mes
Route::get('/cierres', 'BookController@index');
Route::patch('/cierres/cerrar-mes/{id}', 'BookController@close');
Route::patch('/cierres/abrir-rectificacion/{id}', 'BookController@openForRectification');

// Rutas de autenticación
Route::get('/perfil', 'UserController@profile')->name('user-profile');
Route::put('update-profile/{user}', 'UserController@update_profile')->name('users.update_profile');

// Rutas de empresa
Route::get('/empresas/editar', 'CompanyController@edit')->name('Company.edit');
Route::get('/empresas/configuracion', 'CompanyController@editConfiguracion')->name('Company.edit_config');
Route::get('/empresas/certificado', 'CompanyController@editCertificate')->name('Company.edit_cert');
Route::get('/empresas/equipo', 'CompanyController@editTeam')->name('Company.team');
Route::patch('update/{id}', 'CompanyController@update')->name('Company.update');
Route::patch('update-configuracion/{id}', 'CompanyController@updateConfig')->name('Company.update_config');
Route::patch('update-certificado/{id}', 'CompanyController@updateCertificado')->name('Company.update_cert');

// Rutas autogeneradas de CRUD
Route::resource('clientes', 'ClientController');
Route::resource('proveedores', 'ProviderController');
Route::resource('productos', 'ProductController');
Route::resource('facturas-emitidas', 'InvoiceController');
Route::resource('facturas-recibidas', 'BillController');
Route::resource('plans', 'PlanController');

//Middlewares de autenticación
Route::group(['middleware' => ['auth']], function() {
    Route::resource('permissions', 'PermissionController');
    Route::resource('roles', 'RoleController');
    Route::resource('users', 'UserController');
    Route::resource('products', 'ProductController');
});

/**
 * Teamwork routes
 */
Route::group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function()
{
    Route::get('/', 'TeamController@index')->name('teams.index');
    //Route::get('create', 'TeamController@create')->name('teams.create');
    Route::post('teams', 'TeamController@store')->name('teams.store');
    Route::get('edit/{id}', 'TeamController@edit')->name('teams.edit');
    Route::put('edit/{id}', 'TeamController@update')->name('teams.update');
    Route::delete('destroy/{id}', 'TeamController@destroy')->name('teams.destroy');
    Route::get('switch/{id}', 'TeamController@switchTeam')->name('teams.switch');

    Route::get('members/{id}', 'TeamMemberController@show')->name('teams.members.show');
    Route::get('members/resend/{invite_id}', 'TeamMemberController@resendInvite')->name('teams.members.resend_invite');
    Route::post('members/{id}', 'TeamMemberController@invite')->name('teams.members.invite');
    Route::delete('members/{id}/{user_id}', 'TeamMemberController@destroy')->name('teams.members.destroy');

    Route::get('accept/{token}', 'AuthController@acceptInvite')->name('teams.accept_invite');
});

//OM Routing
Route::get('invite/register/{token}', 'InviteController@index')->name('invites.accept_invite');
Route::post('change-company', 'CompanyController@changeCompany');
