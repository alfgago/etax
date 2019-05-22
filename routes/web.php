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
Route::post('facturas-emitidas/importarExcel', 'InvoiceController@importExcel');
Route::post('facturas-emitidas/importarXML', 'InvoiceController@importXML');
Route::post('facturas-recibidas/importarExcel', 'BillController@importExcel');
Route::post('facturas-recibidas/importarXML', 'BillController@importXML');

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
Route::prefix('cierres')->group(function() {
    Route::get('/', 'BookController@index');
    Route::patch('cerrar-mes/{id}', 'BookController@close');
    Route::patch('abrir-rectificacion/{id}', 'BookController@openForRectification');
});

// Rutas de empresa
Route::prefix('empresas')->group(function() {
    Route::get('editar', 'CompanyController@edit')->name('Company.edit');
    Route::get('configuracion', 'CompanyController@editConfiguracion')->name('Company.edit_config');
    Route::get('certificado', 'CompanyController@editCertificate')->name('Company.edit_cert');
    Route::get('equipo', 'CompanyController@editTeam')->name('Company.team');
    Route::patch('update/{id}', 'CompanyController@update')->name('Company.update');
    Route::patch('update-configuracion/{id}', 'CompanyController@updateConfig')->name('Company.update_config');
    Route::patch('update-certificado/{id}', 'CompanyController@updateCertificado')->name('Company.update_cert');
    Route::get('company-profile/{id}', 'CompanyController@company_profile')->name('Company.company_profile');
    Route::get('set-prorrata-2018-facturas', 'CompanyController@setProrrata2018PorFacturas')->name('Company.set_prorrata_2018_facturas');
});

// Rutas de facturación
Route::prefix('facturas-emitidas')->group(function() {
    Route::get('emitir-factura', 'InvoiceController@emitFactura')->name('Invoice.emit_01');
    Route::get('emitir-tiquete', 'InvoiceController@emitTiquete')->name('Invoice.emit_04');
    Route::post('enviar-hacienda', 'InvoiceController@sendHacienda')->name('Invoice.send');
});

// Rutas de aceptación de XML
Route::get('/facturas-recibidas/aceptaciones', 'BillController@indexAccepts')->name('Bill.accepts');
Route::post('/facturas-recibidas/respondStatus', 'BillController@respondStatus')->name('Bill.respond');

// Rutas de Wizard
Route::get('/wizard', 'WizardController@index')->name('Wizard.index');
Route::get('/editar-totales-2018', 'WizardController@setTotales2018')->name('Wizard.edit_2018');
Route::post('/update-totales-2018', 'WizardController@storeTotales2018')->name('Wizard.update_2018');
Route::post('/update-wizard', 'WizardController@updateWizard')->name('Wizard.update_wizard');

// Rutas de usuario
Route::prefix('usuario')->group(function() {
    Route::get('overview', 'UserController@overview')->name('User.overview');
    Route::get('general', 'UserController@editInformation')->name('User.edit_information');
    Route::get('seguridad', 'UserController@editPassword')->name('User.edit_password');
    Route::get('planes', 'UserController@plans')->name('User.plans');
    Route::get('zendesk', 'UserController@zendesk')->name('User.zendesk');
    Route::get('crear_ticket', 'UserController@crear_ticket')->name('User.crear_ticket');
    Route::get('ver_consultas', 'UserController@ver_consultas')->name('User.ver_consultas');
    Route::get('empresas', 'UserController@companies')->name('User.companies');
    Route::get('usuarios-invitados', 'UserController@invitedUsersList')->name('User.invited-users-list');

});

Route::patch('update-infomation/{id}', 'UserController@updateInformation')->name('User.update_information');
Route::patch('update-password/{id}', 'UserController@updatePassword')->name('User.update_password');

// Rutas de API data para ajax
Route::get('/api/invoices', 'InvoiceController@indexData')->name('Invoice.data');
Route::get('/api/bills', 'BillController@indexData')->name('Bill.data');
Route::get('/api/billsAccepts', 'BillController@indexDataAccepts')->name('Bill.data_accepts');
Route::get('/api/clients', 'ClientController@indexData')->name('Client.data');
Route::get('/api/providers', 'ProviderController@indexData')->name('Provider.data');
Route::get('/api/products', 'ProductController@indexData')->name('Product.data');
Route::get('/api/books', 'BookController@indexData')->name('Book.data');

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
