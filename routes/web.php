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

// Rutas de empresa
Route::get('/empresas/editar', 'CompanyController@edit')->name('Company.edit');
Route::get('/empresas/configuracion', 'CompanyController@editConfiguracion')->name('Company.edit_config');
Route::get('/empresas/certificado', 'CompanyController@editCertificate')->name('Company.edit_cert');
Route::get('/empresas/equipo', 'CompanyController@editTeam')->name('Company.team');
Route::patch('update/{id}', 'CompanyController@update')->name('Company.update');
Route::patch('update-configuracion/{id}', 'CompanyController@updateConfig')->name('Company.update_config');
Route::patch('update-certificado/{id}', 'CompanyController@updateCertificado')->name('Company.update_cert');
Route::get('/empresas/company-profile/{id}', 'CompanyController@company_profile')->name('Company.company_profile');

// Rutas de facturación
Route::get('/facturas-emitidas/emitir-factura', 'InvoiceController@emitFactura')->name('Invoice.emit_01');
Route::get('/facturas-emitidas/emitir-tiquete', 'InvoiceController@emitTiquete')->name('Invoice.emit_04');
Route::post('/facturas-emitidas/enviar-hacienda', 'InvoiceController@sendHacienda')->name('Invoice.send');

// Rutas de Wizard
Route::get('wizard', 'WizardController@index')->name('Wizard.index');
Route::get('editar-totales-2018', 'WizardController@setTotales2018')->name('Wizard.edit_2018');
Route::post('update-totales-2018', 'WizardController@storeTotales2018')->name('Wizard.update_2018');

// Rutas de usuario
Route::get('/usuario/overview', 'UserController@overview')->name('User.overview');
Route::get('/usuario/general', 'UserController@editInformation')->name('User.edit_information');
Route::get('/usuario/seguridad', 'UserController@editPassword')->name('User.edit_password');
Route::patch('update-infomation/{id}', 'UserController@updateInformation')->name('User.update_information');
Route::patch('update-password/{id}', 'UserController@updatePassword')->name('User.update_password');
Route::get('/usuario/planes', 'UserController@plans')->name('User.plans');
Route::get('/usuario/empresas', 'UserController@companies')->name('User.companies');
Route::get('/usuario/usuarios-invitados', 'UserController@invitedUsersList')->name('User.invited-users-list');

// Rutas autogeneradas de CRUD
Route::resource('clientes', 'ClientController');
Route::resource('proveedores', 'ProviderController');
Route::resource('productos', 'ProductController');
Route::resource('facturas-emitidas', 'InvoiceController');
Route::resource('facturas-recibidas', 'BillController');
Route::resource('plans', 'PlanController');
Route::resource('empresas', 'CompanyController');

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
Route::group(['prefix' => 'companies', 'namespace' => 'Teamwork', 'middleware' => ['auth']], function() {
    Route::get('/', 'TeamController@index')->name('teams.index');
    Route::get('create', 'TeamController@create')->name('teams.create');
    Route::post('teams12', 'TeamController@store')->name('teams.store');
    Route::get('edit/{id}', 'TeamController@edit')->name('teams.edit');
    Route::put('edit/{id}', 'TeamController@update')->name('teams.update');
    Route::delete('destroy/{id}', 'TeamController@destroy')->name('teams.destroy');
    Route::get('switch/{id}', 'TeamController@switchTeam')->name('teams.switch');

    Route::get('members/{id}', 'TeamMemberController@show')->name('teams.members.show');
    Route::get('members/resend/{invite_id}', 'TeamMemberController@resendInvite')->name('teams.members.resend_invite');
    Route::post('members/{id}', 'TeamMemberController@invite')->name('teams.members.invite');
    Route::delete('members/{id}/{user_id}', 'TeamMemberController@destroy')->name('teams.members.destroy');

    Route::get('accept/{token}', 'AuthController@acceptInvite')->name('teams.accept_invite');
    Route::get('permissions/{id}', 'TeamMemberController@permissions')->name('teams.members.permissions');
    Route::post('permissions/{id}', 'TeamMemberController@assignPermission')->name('teams.members.assign_permissions');
});

//OM Routing
Route::get('invite/register/{token}', 'InviteController@index')->name('invites.accept_invite');
Route::post('change-company', 'CompanyController@changeCompany');
Route::get('company-deactivate/{token}', 'CompanyController@confirmCompanyDeactivation')->name('company-deactivate');
Route::patch('/plans/cancel-plan/{planNo}', 'PlanController@cancelPlan')->name('Plan.cancel_plan');
Route::get('/plans/confirm-cancel-plan/{token}', 'PlanController@confirmCancelPlan')->name('Plan.confirm-cancel-plan');

//Temp Routing
Route::get('show-plans', 'PlanController@show_plans')->name('plans.show-data');
Route::post('purchase', 'PlanController@purchase')->name('plans.purchase');
Route::get('plans/switch-plan/{plan}/{newPlan}', 'PlanController@switchPlan')->name('plans.switch-plan');
