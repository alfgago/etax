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
Route::get('facturas-emitidas/exportar/{year}/{month}', 'InvoiceController@export');
Route::get('facturas-recibidas/exportar/{year}/{month}', 'BillController@export');
Route::get('exportar-libro-compras/{year}/{month}', 'BillController@exportLibroCompras');
Route::get('exportar-libro-ventas/{year}/{month}', 'InvoiceController@exportLibroVentas');

// Rutas de importación
Route::post('clientes/importar', 'ClientController@import');
Route::post('proveedores/importar', 'ProviderController@import');
Route::post('productos/importar', 'ProductController@import');
Route::post('facturas-emitidas/importarExcel', 'InvoiceController@importExcel');
Route::post('facturas-emitidas/importarExcelSM', 'InvoiceController@importExcelSM');
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
Route::post('/reportes/libro-ventas', 'ReportsController@reporteLibroVentas');
Route::post('/reportes/libro-compras', 'ReportsController@reporteLibroCompras');
Route::get('/reportes/borrador-iva', 'ReportsController@reporteBorradorIVA');
/*Exportar XML DEPRECADOS*/
Route::post('/reportes/export-cuentas-contables', 'ReportsController@exportCuentasContables');
Route::post('/reportes/export-detalle-debito-fiscal', 'ReportsController@exportDetalleDebitoFiscal');
Route::post('/reportes/export-detalle-credito-fiscal', 'ReportsController@exportDetalleCreditoFiscal');
Route::post('/reportes/export-libro-compras', 'ReportsController@exportLibroCompras');
Route::post('/reportes/export-libro-ventas', 'ReportsController@exportLibroVentas');
Route::post('/reportes/export-resumen-ejecutivo', 'ReportsController@exportResumenEjecutivo');
Route::post('/reportes/export-reporte-proveedores', 'ReportsController@exportReporteProveedores');
Route::post('/reportes/export-reporte-clientes', 'ReportsController@exportReporteClientes');
Route::post('/reportes/export-declaracion-iva', 'ReportsController@exportDeclaracionIVA');
/**/

//Cierres de mes
Route::prefix('cierres')->group(function() {
    Route::get('/', 'BookController@index');
    Route::patch('cerrar-mes/{id}', 'BookController@close');
    Route::get('validar-cierre/{id}', 'BookController@validar');
    Route::patch('abrir-rectificacion/{id}', 'BookController@openForRectification');
    Route::get('/retenciones-tarjeta/{id}', 'BookController@retenciones_tarjeta')->name('Book.retenciones_tarjeta');
    Route::post('/actualizar-retencion-tarjeta', 'BookController@actualizar_retencion_tarjeta')->name('Book.actualizar_retencion_tarjeta');
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
    Route::get('comprar-facturas-vista', 'CompanyController@comprarFacturasVista')->name('Company.comprar_facturas_vista');
    Route::patch('seleccionar-cliente', 'CompanyController@seleccionarCliente')->name('Company.seleccionar_cliente');
});

// Rutas de facturación
Route::prefix('facturas-emitidas')->group(function() {
    Route::get('emitir-factura/{tipoDocumento}', 'InvoiceController@emitFactura')->name('Invoice.emit_01');
    Route::get('emitir-sujeto-pasivo', 'InvoiceController@emitSujetoPasivo')->name('Invoice.emitSujetoPasivo');
    Route::get('emitir-tiquete', 'InvoiceController@emitTiquete')->name('Invoice.emit_04');
    Route::get('emitir-tiquete', 'InvoiceController@emitTiquete')->name('Invoice.emit_04');
    Route::get('nota-debito/{id}', 'InvoiceController@notaDebito')->name('Invoice.notadebito');
    Route::get('validaciones', 'InvoiceController@indexValidaciones')->name('Invoice.validaciones');
    Route::patch('confirmar-validacion/{id}', 'InvoiceController@confirmarValidacion')->name('Invoice.confirmar_validacion');
    Route::get('autorizaciones', 'InvoiceController@indexAuthorize')->name('Invoice.validaciones');
    Route::patch('confirmar-autorizacion/{id}', 'InvoiceController@authorizeInvoice')->name('Invoice.confirmar_validacion');
    Route::post('send', 'InvoiceController@sendHacienda')->name('Invoice.sendHacienda');
    Route::patch('/anular/{id}', 'InvoiceController@anularInvoice')->name('Invoice.anular');
    Route::post('/nota-debito/send/{id}', 'InvoiceController@sendNotaDebito')->name('Invoice.sendNotaDebit');
    Route::get('download-pdf/{id}', 'InvoiceController@downloadPdf')->name('Invoice.downloadPdf');
    Route::get('stream-pdf/{id}', 'InvoiceController@streamPdf')->name('Invoice.downloadPdf');
    Route::get('download-xml/{id}', 'InvoiceController@downloadXml')->name('Invoice.downloadXml');
    Route::get('reenviar-email/{id}', 'InvoiceController@resendInvoiceEmail')->name('Invoice.resendInvoiceEmail');
    Route::get('consult/{id}', 'InvoiceController@consultInvoice')->name('Invoice.consultInvoice');
    Route::get('query-invoice/{id}', 'InvoiceController@queryInvoice')->name('Invoice.queryInvoice');
    Route::post('actualizar-categorias', 'InvoiceController@actualizar_categorias')->name('Invoice.actualizar_categorias');
    Route::patch('switch-ocultar/{id}', 'InvoiceController@hideInvoice')->name('Invoice.hideInvoice');
    Route::get('validar/{id}', 'InvoiceController@validar')->name('Invoice.validar');
    Route::post('guardar-validar', 'InvoiceController@guardarValidar')->name('Invoice.GuardarValidar');
});

// Rutas de facturacion recibida
Route::prefix('facturas-recibidas')->group(function() {
    Route::get('aceptaciones', 'BillController@indexAccepts')->name('Bill.accepts');
    Route::post('respondStatus', 'BillController@respondStatus')->name('Bill.respond');
    Route::patch('respuesta-aceptacion/{id}', 'BillController@sendAcceptMessage')->name('Bill.sendAcceptMessage');
    Route::get('validaciones', 'BillController@indexValidaciones')->name('Bill.validaciones');
    Route::patch('confirmar-validacion/{id}', 'BillController@confirmarValidacion')->name('Bill.confirmar_validacion');
    Route::get('autorizaciones', 'BillController@indexAuthorize')->name('Bill.validaciones');
    Route::patch('confirmar-autorizacion/{id}', 'BillController@authorizeBill')->name('Bill.authorizeBill');
    Route::get('aceptaciones-otros', 'BillController@indexAcceptsOther')->name('Bill.acceptOthers');
    Route::patch('confirmar-aceptacion-otros/{id}', 'BillController@correctAccepted')->name('Bill.correctAccepted');
    Route::patch('marcar-para-aceptacion/{id}', 'BillController@markAsNotAccepted')->name('Bill.markAsNotAccepted');
    Route::get('validar/{id}', 'BillController@validar')->name('Bill.validar');
    Route::post('guardar-validar', 'BillController@guardarValidar')->name('Bill.GuardarValidar');
    Route::get('edit-aceptacion', 'BillController@editAccept')->name('Bill.editAccept');
    Route::get('update-aceptacion', 'BillController@updateAccept')->name('Bill.updateAccept');
    Route::patch('switch-ocultar/{id}', 'BillController@hideBill')->name('Bill.hideBill');
});

// Rutas de Wizard
Route::get('/wizard', 'WizardController@index')->name('Wizard.index');
Route::get('/editar-totales-2018', 'WizardController@setTotales2018')->name('Wizard.edit_2018');
Route::post('/update-totales-2018', 'WizardController@storeTotales2018')->name('Wizard.update_2018');
Route::post('/update-wizard', 'WizardController@updateWizard')->name('Wizard.update_wizard');
Route::post('/store-wizard', 'WizardController@createWizard')->name('Wizard.store_wizard');

//Rutas para suscripciones
Route::get('/cambiar-plan', 'SubscriptionPlanController@changePlan')->name('Subscription.cambiar_plan');
Route::get('/elegir-plan', 'SubscriptionPlanController@selectPlan')->name('Subscription.select_plan');
Route::get('/periodo-pruebas', 'SubscriptionPlanController@startTrial')->name('Subscription.startTrial');
Route::post('/confirmar-plan', 'SubscriptionPlanController@confirmPlanChange')->name('Subscription.confirmar_plan');
Route::get('/confirmar-codigo/{codigo}/{precio}/{banco}/{plan}/{companies}', 'SubscriptionPlanController@confirmCode')->name('Subscription.confirmar_code');
Route::get('/codigo-contador/{codigo}', 'SubscriptionPlanController@confirmCodeAccount')->name('Subscription.confirmCodeAccount');
Route::post('/suscripciones/confirmar-pruebas', 'SubscriptionPlanController@confirmStartTrial')->name('Subscription.confirmStartTrial');
Route::get('/confirmar-codigo/{codigo}/{precio}/{banco}', 'SubscriptionPlanController@confirmCode')->name('Subscription.confirmar_code');


// Rutas de usuario
Route::prefix('usuario')->group(function() {
    Route::get('perfil', 'UserController@edit')->name('User.edit');
    Route::patch('update-perfil', 'UserController@update')->name('User.update');
    Route::get('admin-edit/{email}', 'UserController@adminEdit')->name('User.admin_edit');
    Route::patch('update-admin/{id}', 'UserController@updateAdmin')->name('User.update_admin');
    Route::get('seguridad', 'UserController@editPassword')->name('User.edit_password');
    Route::get('planes', 'UserController@plans')->name('User.plans');
    Route::get('empresas', 'UserController@companies')->name('User.companies');
    Route::get('usuarios-invitados', 'UserController@invitedUsersList')->name('User.invited-users-list');
    Route::get('zendesk-jwt', 'UserController@zendeskJwt')->name('User.zendesk_jwt');
    Route::patch('update-password/{id}', 'UserController@updatePassword')->name('User.update_password');
    Route::post('update-user-tutorial', 'UserController@updateUserTutorial')->name('User.update_user_tutorial');
    Route::get('wallet', 'InfluencersController@wallet')->name('Influencers.wallet');
    Route::post('add-retiro', 'InfluencersController@retiro')->name('Influencers.retiro');
    Route::get('cancelar', 'UserController@cancelar')->name('User.cancelar');
    Route::patch('update-cancelar', 'UserController@updatecancelar')->name('User.updatecancelar');
    Route::get('compra-contabilidades', 'UserController@CompraContabilidades')->name('Payment.CompraContabilidades');

});

//Rutas de Pagos de la aplicacion
Route::prefix('payment')->group(function(){
    Route::get('payment-crear', 'PaymentController@paymentCrear')->name('Payment.payment_crear');
    Route::post('confirm-payment', 'PaymentController@confirmPayment')->name('Payment.payment_card');
    Route::post('payment-token-transaction', 'PaymentController@paymentTokenTransaction')->name('Payment.payment_token_transaction');
    Route::post('payment-charge', 'PaymentController@paymentCharge')->name('Payment.payment_charge');
    Route::get('pending-charges', 'PaymentController@pendingCharges')->name('Payment.pending_charges');
    Route::post('comprar-facturas', 'PaymentController@comprarFacturas')->name('Payment.comprar_facturas');
    Route::post('comprar-contabilidades', 'PaymentController@comprarContabilidades')->name('Payment.comprarContabilidades');
    Route::post('seleccion-empresas', 'PaymentController@seleccionEmpresas')->name('Payment.seleccionEmpresas');
    Route::patch('pagar-cargo/{id}', 'PaymentController@pagarCargo')->name('Payment.pagar-cargo');
});


Route::prefix('clients')->group(function(){
    Route::get('clients-update-view/{id}', 'ClientController@edit')->name('clients_update_view');
    Route::delete('clients-delete/{id}', 'ClientController@destroy')->name('clients_delete');
});
Route::prefix('payment-methods')->group(function(){
    Route::get('payment-method-create-view', 'PaymentMethodController@createView')->name('PaymentMethod.payment_method_create_view');
    Route::post('payment-method-create', 'PaymentMethodController@create')->name('PaymentMethod.payment_create');
    Route::get('payment-method-token-update-view/{id}', 'PaymentMethodController@paymentMethodTokenUpdateView')->name('PaymentMethod.payment_method_token_update_view');
    Route::patch('payment-method-token-update', 'PaymentMethodController@tokenUpdate')->name('Payment.payment_token_update');
    Route::delete('payment-method-token-delete/{id}', 'PaymentMethodController@tokenDelete')->name('Payment.payment_token_delete');
    Route::patch('payment-method-default-card-change/{id}', 'PaymentMethodController@updateDefault')->name('Payment.payment_method_default_card_change');
});

// Rutas de API data para ajax
Route::get('/api/invoices', 'InvoiceController@indexData')->name('Invoice.data');
Route::get('/api/invoicesAuthorize', 'InvoiceController@indexDataAuthorize')->name('Invoice.data_authorizes');
Route::get('/api/bills', 'BillController@indexData')->name('Bill.data');
Route::get('/api/billsAccepts', 'BillController@indexDataAccepts')->name('Bill.data_accepts');
Route::get('/api/billsAuthorize', 'BillController@indexDataAuthorize')->name('Bill.data_authorizes');
Route::get('/api/clients', 'ClientController@indexData')->name('Client.data');
Route::get('/api/providers', 'ProviderController@indexData')->name('Provider.data');
Route::get('/api/products', 'ProductController@indexData')->name('Product.data');
Route::get('/api/books', 'BookController@indexData')->name('Book.data');
Route::get('/api/payments', 'PaymentController@indexData')->name('Payment.data');
Route::get('/api/paymentsMethods', 'PaymentMethodController@indexData')->name('PaymentMethod.data');


//Rutas de recover
Route::patch('/facturas-recibidas/{id}/restore', 'BillController@restore')->name('Bill.restore');
Route::patch('/facturas-emitidas/{id}/restore', 'InvoiceController@restore')->name('Invoice.restore');
Route::patch('/proveedores/{id}/restore', 'ProviderController@restore')->name('Provider.restore');
Route::patch('/clientes/{id}/restore', 'ClientController@restore')->name('Client.restore');
Route::patch('/productos/{id}/restore', 'ProductController@restore')->name('Product.restore');

// Rutas autogeneradas de CRUD
Route::resource('clientes', 'ClientController');
Route::resource('proveedores', 'ProviderController');
Route::resource('productos', 'ProductController');
Route::resource('facturas-emitidas', 'InvoiceController');
Route::resource('facturas-recibidas', 'BillController');
Route::resource('plans', 'PlanController');
Route::resource('empresas', 'CompanyController');
Route::resource('payments', 'PaymentController');
Route::resource('payments-methods', 'PaymentMethodController');

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
Route::delete('invite/delete/{id}', 'InviteController@removeInvitation')->name('teams.members.removeInvitation');
Route::post('change-company', 'CompanyController@changeCompany');
Route::get('company-deactivate/{token}', 'CompanyController@confirmCompanyDeactivation')->name('company-deactivate');
Route::patch('/plans/cancel-plan/{planNo}', 'PlanController@cancelPlan')->name('Plan.cancel_plan');
Route::get('/plans/confirm-cancel-plan/{token}', 'PlanController@confirmCancelPlan')->name('Plan.confirm-cancel-plan');
Route::get('getproduct', 'ProductController@consultarProductos');

//Temp Routing
Route::get('show-plans', 'PlanController@show_plans')->name('plans.show-data');
Route::post('purchase', 'PlanController@purchase')->name('plans.purchase');
Route::get('plans/switch-plan/{plan}/{newPlan}', 'PlanController@switchPlan')->name('plans.switch-plan');

Route::post('payment-test', 'PaymentController@checkout')->name('payment.test');

Route::get('/private/all', 'SubscriptionPlanController@all')->name('subscriptions.all');
Route::get('/private/exportar', 'SubscriptionPlanController@exportar')->name('subscriptions.exportar');

Route::get('/admin/impersonate/{id}', 'UserController@impersonate');
Route::get('/admin/leave', 'UserController@leaveImpersonation');

