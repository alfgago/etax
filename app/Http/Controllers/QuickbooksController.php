<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use \Carbon\Carbon;
use App\Quickbooks;
use App\Company;
use App\Provider;
use App\Client;
use App\Product;
use App\Invoice;
use App\Bill;
use App\QuickbooksInvoice;
use App\QuickbooksBill;
use App\QuickbooksCustomer;
use App\QuickbooksProvider;
use App\QuickbooksProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QuickbooksController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('CheckSubscription');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function config()
    {
        $company = currentCompanyModel();
        $qb = Quickbooks::firstOrNew(
            [
                'company_id' => $company->id
            ]    
        );
        
        $authUrl = $qb->getAuthenticationUrl();
        return view('Quickbooks/config', compact(['qb', 'authUrl']));
    }
    
    public function auth()
    {
        $request = request();
        $request->validate([
            'code' => 'required',
            'realmId' => 'required'
        ]);
        
        $authorizationCode= $request->code;
        $RealmID= $request->realmId;
        $company = currentCompanyModel();
        $qb = new Quickbooks();
        $qb->registerQuickbooks( $company, $authorizationCode, $RealmID );
        return redirect('quickbooks/configuracion');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function saveConfig($request)
    {
        return view('Quickbooks/config');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function variableMapIndex()
    {
        $company = currentCompanyModel();
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        
        $taxRates = $dataService->Query("SELECT * FROM TaxCode");
        $paymentMethods = $dataService->Query("SELECT * FROM PaymentMethod");
        $terms = $dataService->Query("SELECT * FROM Term");

        return view('Quickbooks/variable-map', compact(['qb', 'taxRates', 'paymentMethods', 'terms']));
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function saveVariables(Request $request)
    {
        $company = currentCompanyModel();
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $qb->conditions_json = $request->sale_condition;
        $qb->payment_methods_json = $request->payment_type;
        $taxesJson = [
            'tipo_iva' => $request->tipo_iva,
            'tipo_producto' => $request->tipo_producto
        ];
        $qb->taxes_json = $taxesJson;
        $qb->save();
        return back()->withMessage( 'Se guardaron las variables de Quickbooks correctamente.' );
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function invoiceSyncIndex()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        return view('Quickbooks/Invoices/sync', compact(['year', 'month']));
    }
    
    public function invoiceSyncIndexEtaxaqb()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        
        $query = Invoice::where('company_id', $company->id)
                ->where('year', $year)
                ->where('month', $month)
                ->with('client')
                ->with('quickbooksInvoice');

        $facturasQb = QuickbooksInvoice::where('company_id', $company->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->get();

        return view('Quickbooks/Invoices/sync-qb', compact(['year', 'month']));
    }
    
        /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataInvoicesQbaetax( Request $request ) {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $query = QuickbooksInvoice::select(['id','qb_id','invoice_id','qb_date','qb_doc_number','qb_client','qb_total'])
                        ->where('company_id', $company->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->with('invoice');

        $facturasEtax = Invoice::select(['id','document_number','client_first_name','client_last_name','client_last_name2'])
                        ->where('company_id', $company->id)
                        ->get();

        return datatables()->eloquent( $query )
            ->addColumn('link', function($invoice) use($facturasEtax){
                if( $invoice->invoice_id ){
                    return "<a href='/facturas-emitidas/$invoice->invoice_id'>".$invoice->invoice->document_number."</a>";
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('actions', function($invoice) use($facturasEtax){
                return view('Quickbooks.Invoices.select', [
                    'facturasEtax' => $facturasEtax,
                    'facturaQb' => $invoice
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    public function indexDataInvoicesEtaxaqb( Request $request ) {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $query = Invoice::where('company_id', $company->id)
                ->where('year', $year)
                ->where('month', $month)
                ->with('client')
                ->with('quickbooksInvoice');

        $facturasQb = QuickbooksInvoice::where('company_id', $company->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->get();

        return datatables()->eloquent( $query )
            ->editColumn('client', function($invoice) {
                return !empty($invoice->client_first_name) ? $invoice->client_first_name.' '.$invoice->client_last_name : $invoice->clientName();
            })
            ->editColumn('generated_date', function($invoice) {
                return $invoice->generatedDate()->format('d/m/Y');
            })
            ->addColumn('total_real', function($invoice) {
                $total = number_format($invoice->total ,2);
                if($invoice->total_exonerados){
                    return "$total <br><small style='font-size:.8em !important;'>($invoice->total_exonerados exonerados)</small>";
                }
                return $total;
            })
            ->addColumn('link', function($invoice) {
                if( isset($invoice->quickbooksInvoice) ){
                    return "QB-".$invoice->quickbooksInvoice->qb_id;
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('actions', function($invoice) use($facturasQb){
                if( isset($invoice->quickbooksInvoice) ){
                    $invoice->qbid = $invoice->quickbooksInvoice->qb_id;
                }else{
                    $invoice->qbid = 0;
                }
                return view('Quickbooks.Invoices.select-qb', [
                    'facturasQb' => $facturasQb,
                    'facturaEtax' => $invoice
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    
    
    public function loadMonthlyInvoices()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $cachekey = "qb-resync-invoices-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
            if( !isset($qb) ){
                return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
            }
            $dataService = $qb->getAuthenticatedDS();
            QuickbooksInvoice::syncMonthlyInvoices($dataService, $year, $month, $company);
        
            Cache::put($cachekey, true, 30); //Se usa cache para evitar que el usuario haga muchos requests al API de QB innecesariamente
        }
        
        return redirect("/quickbooks/emitidas/comparativo/$year/$month")->withMessage('Se re-sincronizaron los facturas del mes correctamente');
    }
    
    public function syncInvoicesQBaetax(){
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        $invoices = request()->invoices;
        foreach($invoices as $key => $value){
            $qbInvoice = QuickbooksInvoice::where('company_id', $company->id)->where('qb_id', $key)->first();
            if($value == 'N'){
                $qbInvoice->saveQbaetax();
            }else{
                $qbInvoice->invoice_id = $value;
                $qbInvoice->save();
            }
        }
        
        return redirect("/quickbooks/emitidas/comparativo/$year/$month")->withMessage('La sincronización de facturas se ha guardado correctamente');
    }
    
    public function syncInvoicesEtaxaqb(){
        $company = currentCompanyModel();
        $invoices = request()->invoices;
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        foreach($invoices as $key => $value){
            if($value == 'N'){
                $etaxInvoice = Invoice::find($key);
                if( isset($etaxInvoice) ){
                    QuickbooksInvoice::saveEtaxaqb($dataService, $etaxInvoice);
                }
            }else{
                $qbInvoice = QuickbooksInvoice::where('company_id', $company->id)->where('qb_id', $value)->first();
                if( isset($qbInvoice) ){
                    $qbInvoice->invoice_id = $key;
                    $qbInvoice->save();
                }
            }
        }
        
        return redirect("/quickbooks/emitidas/comparativo-etaxaqb/")->withMessage('La sincronización de facturas se ha guardado correctamente');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    /*public function invoiceSyncIndex()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        $facturas = QuickBooksInvoice::syncMonthlyInvoices($dataService, $year, $month, $company);
        
        return view('Quickbooks/Invoices/sync', compact(['facturas', 'year', 'month']));
    }
    
    public function loadMonthlyInvoices()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $cachekey = "qb-resync-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
            if( !isset($qb) ){
                return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
            }
            $dataService = $qb->getAuthenticatedDS();
            QuickbooksInvoice::loadQuickbooksInvoices($dataService, $year, $month, $company);
        
            Cache::put($cachekey, true, 15); //Se usa cache para evitar que el usuario haga muchos requests al API de QB innecesariamente
        }
        
        return redirect("/emitidas//comparativo/$year/$month")->withMessage('Se re-sincronizaron las facturas correctamente');
    }*/
    
    public function saveInvoiceFromQuickbooks(){
        $company = currentCompanyModel();
        $qbInvoice = QuickbooksInvoice::where( 'qb_id', request()->invoiceId )
                    ->where('company_id', $company->id)
                    ->first();
        if( !isset($qbInvoice) ){
            return redirect("/quickbooks/comparativo-emitidas")->withError('Está intentando guardar una factura inexistente o que no le pertenece a su empresa.');
        }
        
        $invoice = $qbInvoice->saveInvoiceFromQuickbooks();
        
        return redirect("/emitidas//comparativo/$invoice->year/$invoice->month")->withMessage('La factura ha sido guardada correctamente en eTax');
    }

/************************************************  END INVOICES  */

    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function billSyncIndex()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        return view('Quickbooks/Bills/sync', compact(['year', 'month']));
    }
    
    public function billSyncIndexEtaxaqb()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        
        $query = Bill::where('company_id', $company->id)
                ->where('year', $year)
                ->where('month', $month)
                ->with('provider')
                ->with('quickbooksBill');

        $facturasQb = QuickbooksBill::where('company_id', $company->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->get();

        return view('Quickbooks/Bills/sync-qb', compact(['year', 'month']));
    }
    
        /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataBillsQbaetax( Request $request ) {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $query = QuickbooksBill::select(['id','qb_id','bill_id','qb_date','qb_doc_number','qb_provider','qb_total'])
                        ->where('company_id', $company->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->with('bill');

        $facturasEtax = Bill::select(['id','document_number','provider_first_name','provider_last_name','provider_last_name2'])
                        ->where('company_id', $company->id)
                        ->get();

        return datatables()->eloquent( $query )
            ->addColumn('link', function($bill) use($facturasEtax){
                if( $bill->bill_id ){
                    return "<a href='/facturas-recibidas/$bill->bill_id'>".$bill->bill->document_number."</a>";
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('actions', function($bill) use($facturasEtax){
                return view('Quickbooks.Bills.select', [
                    'facturasEtax' => $facturasEtax,
                    'facturaQb' => $bill
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    public function indexDataBillsEtaxaqb( Request $request ) {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $query = Bill::where('company_id', $company->id)
                ->where('year', $year)
                ->where('month', $month)
                ->with('provider')
                ->with('quickbooksBill');
        $facturasQb = QuickbooksBill::where('company_id', $company->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->get();
                        
                        
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $cuentasQb = $qb->getAccounts($company);

        return datatables()->eloquent( $query )
            ->editColumn('provider', function($bill) {
                return !empty($bill->provider_first_name) ? $bill->provider_first_name.' '.$bill->provider_last_name : $bill->providerName();
            })
            ->editColumn('generated_date', function($bill) {
                return $bill->generatedDate()->format('d/m/Y');
            })
            ->addColumn('total_real', function($bill) {
                $total = number_format($bill->total ,2);
                if($bill->total_exonerados){
                    return "$total <br><small style='font-size:.8em !important;'>($bill->total_exonerados exonerados)</small>";
                }
                return $total;
            })
            ->addColumn('link', function($bill) {
                if( isset($bill->quickbooksBill) ){
                    return "QB-".$bill->quickbooksBill->qb_id;
                }else{
                    return "No asociado";
                }
            })->addColumn('accounts', function($bill) use($cuentasQb){
                if( isset($bill->quickbooksBill) ){
                    $bill->accId = $bill->quickbooksBill->qb_account;
                }else{
                    $bill->accId = 0;
                }
                return view('Quickbooks.Bills.cuentas-contables', [
                    'cuentasQb' => $cuentasQb,
                    'facturaEtax' => $bill
                ])->render();
            })
            ->addColumn('actions', function($bill) use($facturasQb){
                if( isset($bill->quickbooksBill) ){
                    $bill->qbid = $bill->quickbooksBill->qb_id;
                }else{
                    $bill->qbid = 0;
                }
                return view('Quickbooks.Bills.select-qb', [
                    'facturasQb' => $facturasQb,
                    'facturaEtax' => $bill
                ])->render();
            })
            ->rawColumns(['actions', 'link', 'accounts'])
            ->toJson();
    }
    
    
    
    public function loadMonthlyBills()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $cachekey = "qb-resync-bills-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
            if( !isset($qb) ){
                return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
            }
            $dataService = $qb->getAuthenticatedDS();
            QuickbooksBill::syncMonthlyBills($dataService, $year, $month, $company);
        
            Cache::put($cachekey, true, 30); //Se usa cache para evitar que el usuario haga muchos requests al API de QB innecesariamente
        }
        
        return redirect("/quickbooks/recibidas/comparativo/$year/$month")->withMessage('Se re-sincronizaron los facturas del mes correctamente');
    }
    
    public function syncBillsQBaetax(){
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        $bills = request()->bills;
        foreach($bills as $key => $value){
            $qbBill = QuickbooksBill::where('company_id', $company->id)->where('qb_id', $key)->first();
            if($value == 'N'){
                $qbBill->saveQbaetax();
            }else{
                $qbBill->bill_id = $value;
                $qbBill->save();
            }
        }
        
        return redirect("/quickbooks/recibidas/comparativo/$year/$month")->withMessage('La sincronización de facturas se ha guardado correctamente');
    }
    
    public function syncBillsEtaxaqb(){
        $company = currentCompanyModel();
        $bills = request()->bills;
        $billAccounts = request()->bills_accounts;
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        foreach($bills as $key => $value){
            if($value == 'N'){
                $etaxBill = Bill::find($key);
                $accountRef = $billAccounts[$key];
                
                if( isset($etaxBill) ){
                    QuickbooksBill::saveEtaxaqb($dataService, $etaxBill, $accountRef);
                }
            }else{
                $qbBill = QuickbooksBill::where('company_id', $company->id)->where('qb_id', $value)->first();
                if( isset($qbBill) ){
                    $qbBill->bill_id = $key;
                    $qbBill->save();
                }
            }
        }
        
        return redirect("/quickbooks/recibidas/comparativo-etaxaqb/")->withMessage('La sincronización de facturas se ha guardado correctamente');
    }

/************************************************  END BILLS     */
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function clientSyncIndex()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        return view('Quickbooks/Clients/sync', compact(['year', 'month']));
    }
    
    public function clientSyncIndexEtaxaqb()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        return view('Quickbooks/Clients/sync-qb', compact(['year', 'month']));
    }
    
        /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataClientsQbaetax( Request $request ) {
        $company = currentCompanyModel();
        $query = QuickbooksCustomer::where('company_id', $company->id);

        $clientesEtax = Client::where('company_id', $company->id)->get();

        return datatables()->eloquent( $query )
            ->addColumn('link', function($client) use($clientesEtax){
                if( $client->client_id ){
                    if( empty($client->client->id_number) ){
                        return "Datos faltantes - <a href='/clients/clients-update-view/$client->client_id'>Editar</a>";
                    }
                    return "<a href='/clients/clients-update-view/$client->client_id'>".$client->client->id_number."</a>";
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('actions', function($client) use($clientesEtax){
                return view('Quickbooks.Clients.select', [
                    'clientesEtax' => $clientesEtax,
                    'clienteQb' => $client
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    public function indexDataClientsEtaxaqb( Request $request ) {
        $company = currentCompanyModel();
        $query = Client::where('company_id', $company->id)
                ->with('quickbooksCustomer');

        $clientesQb = QuickbooksCustomer::where('company_id', $company->id)->get();

        return datatables()->eloquent( $query )
            ->addColumn('id_number2', function($client) {
                if( !empty($client->id_number) ){
                    return $client->id_number;
                }else{
                    return "No indica, debe corregir";
                }
            })
            ->addColumn('fullname2', function($client) {
                return "$client->first_name $client->last_name $client->last_name2";
            })
            ->addColumn('link', function($client) {
                if( isset($client->quickbooksCustomer) ){
                    return "QB-".$client->quickbooksCustomer->qb_id;
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('actions', function($client) use($clientesQb){
                if( isset($client->quickbooksCustomer) ){
                    $client->qbid = $client->quickbooksCustomer->qb_id;
                }else{
                    $client->qbid = 0;
                }
                return view('Quickbooks.Clients.select-qb', [
                    'clientesQb' => $clientesQb,
                    'clienteEtax' => $client
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    public function loadMonthlyClients()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $cachekey = "qb-resync-clients-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
            if( !isset($qb) ){
                return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
            }
            $dataService = $qb->getAuthenticatedDS();
            QuickbooksCustomer::syncMonthlyClients($dataService, $year, $month, $company);
        
            Cache::put($cachekey, true, 30); //Se usa cache para evitar que el usuario haga muchos requests al API de QB innecesariamente
        }
        
        return redirect("/quickbooks/clientes/comparativo/")->withMessage('Se re-sincronizaron los clientes correctamente');
    }
    
    public function syncClientsQBaetax(){
        $company = currentCompanyModel();
        $clients = request()->clients;
        foreach($clients as $key => $value){
            $qbCustomer = QuickbooksCustomer::where('company_id', $company->id)->where('qb_id', $key)->first();
            if($value == 'N'){
                $qbCustomer->saveQbaetax();
            }else{
                $qbCustomer->client_id = $value;
                $qbCustomer->save();
            }
        }
        
        return redirect("/quickbooks/clientes/comparativo/")->withMessage('La sincronización de clientes se ha guardado correctamente');
    }
    
    public function syncClientsEtaxaqb(){
        $company = currentCompanyModel();
        $clients = request()->clients;
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        foreach($clients as $key => $value){
            if($value == 'N'){
                $etaxClient = Client::find($key);
                if( isset($etaxClient) ){
                    QuickbooksCustomer::saveEtaxaqb($dataService, $etaxClient);
                }
            }else{
                $qbCustomer = QuickbooksCustomer::where('company_id', $company->id)->where('qb_id', $value)->first();
                if( isset($qbCustomer) ){
                    $qbCustomer->client_id = $key;
                    $qbCustomer->save();
                }
            }
        }
        
        return redirect("/quickbooks/clientes/comparativo-etaxaqb/")->withMessage('La sincronización de clientes se ha guardado correctamente');
    }
    
/*
**********************************************END CLIENT
*/
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function providerSyncIndex()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        return view('Quickbooks/Providers/sync', compact(['year', 'month']));
    }
    
    public function providerSyncIndexEtaxaqb()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        return view('Quickbooks/Providers/sync-qb', compact(['year', 'month']));
    }
    
        /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataProvidersQbaetax( Request $request ) {
        $company = currentCompanyModel();
        $query = QuickbooksProvider::where('company_id', $company->id);

        $providersEtax = Provider::where('company_id', $company->id)->get();

        return datatables()->eloquent( $query )
            ->addColumn('link', function($provider) use($providersEtax){
                if( $provider->provider_id ){
                    if( empty($provider->provider->id_number) ){
                        return "Datos faltantes - <a href='/providers/providers-update-view/$provider->provider_id'>Editar</a>";
                    }
                    return "<a href='/providers/providers-update-view/$provider->provider_id'>".$provider->provider->id_number."</a>";
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('actions', function($provider) use($providersEtax){
                return view('Quickbooks.Providers.select', [
                    'proveedoresEtax' => $providersEtax,
                    'proveedorQb' => $provider
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    public function indexDataProvidersEtaxaqb( Request $request ) {
        $company = currentCompanyModel();
        $query = Provider::where('company_id', $company->id)
                ->with('quickbooksProvider');

        $providersQb = QuickbooksProvider::where('company_id', $company->id)->get();

        return datatables()->eloquent( $query )
            ->addColumn('id_number2', function($provider) {
                if( !empty($provider->id_number) ){
                    return $provider->id_number;
                }else{
                    return "No indica, debe corregir";
                }
            })
            ->addColumn('fullname2', function($provider) {
                return "$provider->first_name $provider->last_name $provider->last_name2";
            })
            ->addColumn('link', function($provider) {
                if( isset($provider->quickbooksProvider) ){
                    return "QB-".$provider->quickbooksProvider->qb_id;
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('actions', function($provider) use($providersQb){
                if( isset($provider->quickbooksProvider) ){
                    $provider->qbid = $provider->quickbooksProvider->qb_id;
                }else{
                    $provider->qbid = 0;
                }
                return view('Quickbooks.Providers.select-qb', [
                    'proveedoresQb' => $providersQb,
                    'proveedorEtax' => $provider
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    public function loadMonthlyProviders()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $cachekey = "qb-resync-providers-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
            if( !isset($qb) ){
                return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
            }
            $dataService = $qb->getAuthenticatedDS();
            QuickbooksProvider::syncMonthlyProviders($dataService, $year, $month, $company);
        
            Cache::put($cachekey, true, 30); //Se usa cache para evitar que el usuario haga muchos requests al API de QB innecesariamente
        }
        
        return redirect("/quickbooks/proveedores/comparativo/")->withMessage('Se re-sincronizaron los proveedores correctamente');
    }
    
    public function syncProvidersQBaetax(){
        $company = currentCompanyModel();
        $providers = request()->providers;
        foreach($providers as $key => $value){
            $qbProvider = QuickbooksProvider::where('company_id', $company->id)->where('qb_id', $key)->first();
            //dd($qbProvider);
            if($value == 'N'){
                $qbProvider->saveQbaetax();
            }else{
                $qbProvider->provider_id = $value;
                $qbProvider->save();
            }
        }
        
        return redirect("/quickbooks/proveedores/comparativo/")->withMessage('La sincronización de proveedores se ha guardado correctamente');
    }
    
    public function syncProvidersEtaxaqb(){
        $company = currentCompanyModel();
        $providers = request()->providers;
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        foreach($providers as $key => $value){
            if($value == 'N'){
                $etaxProvider = Provider::find($key);
                if( isset($etaxProvider) ){
                    QuickbooksProvider::saveEtaxaqb($dataService, $etaxProvider);
                }
            }else{
                $qbProvider = QuickbooksProvider::where('company_id', $company->id)->where('qb_id', $value)->first();
                if( isset($qbProvider) ){
                    $qbProvider->provider_id = $key;
                    $qbProvider->save();
                }
            }
        }
        
        return redirect("/quickbooks/proveedores/comparativo-etaxaqb/")->withMessage('La sincronización de proveedores se ha guardado correctamente');
    }
    
/*
**********************************************END CLIENT
*/
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function productSyncIndex()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        return view('Quickbooks/Products/sync', compact(['year', 'month']));
    }
    
    public function productSyncIndexEtaxaqb()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        if( !$year || !$month ){
            $year = \Carbon\Carbon::now()->year;
            $month = \Carbon\Carbon::now()->month;
        }
        
        return view('Quickbooks/Products/sync-qb', compact(['year', 'month']));
    }
    
        /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataProductsQbaetax( Request $request ) {
        $company = currentCompanyModel();
        $query = QuickbooksProduct::where('company_id', $company->id);
        $productsEtax = Product::where('company_id', $company->id)->get();
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        
        return datatables()->eloquent( $query )
            ->addColumn('link', function($product) {
                if( $product->product_id ){
                        return "<a href='/productos/$product->product_id/edit/'>$product->product_id</a>";
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('nombre', function($product) {
                return $product->name ?? 'No indica';
            })
            ->addColumn('account', function($product) use($qb) {
                if( isset($product->account_ref) ){
                    $accountName = $qb->getAccountByRef($product->account_ref);
                }else{
                    return $product->account_ref;
                }
                return $accountName;
            })
            ->addColumn('actions', function($product) use($productsEtax){
                return view('Quickbooks.Products.select', [
                    'productosEtax' => $productsEtax,
                    'productoQb' => $product
                ])->render();
            })
            ->rawColumns(['actions', 'link'])
            ->toJson();
    }
    
    public function indexDataProductsEtaxaqb( Request $request ) {
        $company = currentCompanyModel();
        $query = Product::where('company_id', $company->id)
                ->with('quickbooksProduct');

        $productsQb = QuickbooksProduct::where('company_id', $company->id)->get();
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        $cuentasQb = $qb->getAccounts($company, 'Income');

        return datatables()->eloquent( $query )
            ->addColumn('link', function($product) {
                if( isset($product->quickbooksProduct) ){
                    return "QB-".$product->quickbooksProduct->qb_id;
                }else{
                    return "No asociado";
                }
            })
            ->addColumn('accounts', function($product) use($cuentasQb){
                if( isset($product->quickbooksProduct) ){
                    $product->accId = $product->quickbooksProduct->account_ref;
                }else{
                    $product->accId = 0;
                }
                return view('Quickbooks.Products.cuentas-contables', [
                    'cuentasQb' => $cuentasQb,
                    'productoEtax' => $product
                ])->render();
            })
            ->addColumn('actions', function($product) use($productsQb){
                if( isset($product->quickbooksProduct) ){
                    $product->qbid = $product->quickbooksProduct->qb_id;
                }else{
                    $product->qbid = 0;
                }
                return view('Quickbooks.Products.select-qb', [
                    'productosQb' => $productsQb,
                    'productoEtax' => $product
                ])->render();
            })
            ->rawColumns(['actions', 'link', 'accounts'])
            ->toJson();
    }
    
    public function loadMonthlyProducts()
    {
        $company = currentCompanyModel();
        $year = request()->year;
        $month = request()->month;
        
        $cachekey = "qb-resync-products-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
            if( !isset($qb) ){
                return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
            }
            $dataService = $qb->getAuthenticatedDS();
            QuickbooksProduct::syncMonthlyProducts($dataService, $year, $month, $company);
        
            Cache::put($cachekey, true, 30); //Se usa cache para evitar que el usuario haga muchos requests al API de QB innecesariamente
        }
        
        return redirect("/quickbooks/productos/comparativo/")->withMessage('Se re-sincronizaron los productos correctamente');
    }
    
    public function syncProductsQBaetax(){
        $company = currentCompanyModel();
        $products = request()->products;
        foreach($products as $key => $value){
            $qbProduct = QuickbooksProduct::where('company_id', $company->id)->where('qb_id', $key)->first();
            if($value == 'N'){
                $qbProduct->saveQbaetax();
            }else{
                $qbProduct->product_id = $value;
                $qbProduct->save();
            }
        }
        
        return redirect("/quickbooks/productos/comparativo/")->withMessage('La sincronización de productos se ha guardado correctamente');
    }
    
    public function syncProductsEtaxaqb(){
        $company = currentCompanyModel();
        $products = request()->products;
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        foreach($products as $key => $arrayValue){
            $value = $arrayValue['qbid'];
            $accountRef = $arrayValue['account'];

            if($value == 'N'){
                $etaxProduct = Product::find($key);
                if( isset($etaxProduct) ){
                    QuickbooksProduct::saveEtaxaqb($dataService, $etaxProduct, $accountRef);
                }
            }else{
                $qbProduct = QuickbooksProduct::where('company_id', $company->id)->where('qb_id', $value)->first();
                if( isset($qbProduct) ){
                    $qbProduct->product_id = $key;
                    $qbProduct->account_ref = $accountRef;
                    $qbProduct->save();
                }
            }
        }
        
        return redirect("/quickbooks/productos/comparativo-etaxaqb/")->withMessage('La sincronización de productos se ha guardado correctamente');
    }
    
    
}
