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
        
        $taxRates = $dataService->Query("SELECT * FROM TaxRate");
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
        
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        $facturas = QuickBooksInvoice::syncMonthlyInvoices($dataService, $year, $month, $company);
        
        return view('Quickbooks/invoice-sync', compact(['facturas', 'year', 'month']));
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
        
        return redirect("/quickbooks/comparativo-emitidas/$year/$month")->withMessage('Se re-sincronizaron las facturas correctamente');
    }
    
    public function saveInvoiceFromQuickbooks(){
        $company = currentCompanyModel();
        $qbInvoice = QuickbooksInvoice::where( 'qb_id', request()->invoiceId )
                    ->where('company_id', $company->id)
                    ->first();
        if( !isset($qbInvoice) ){
            return redirect("/quickbooks/comparativo-emitidas")->withError('Está intentando guardar una factura inexistente o que no le pertenece a su empresa.');
        }
        
        $invoice = $qbInvoice->saveInvoiceFromQuickbooks();
        dd($invoice);
        return redirect("/quickbooks/comparativo-emitidas/$invoice->year/$invoice->month")->withMessage('La factura ha sido guardada correctamente en eTax');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function billSyncIndex()
    {
        return view('Quickbooks/bill-sync');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function clientSyncIndex()
    {
        return view('Quickbooks/client-sync');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function providerSyncIndex()
    {
        return view('Quickbooks/provider-sync');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function productSyncIndex()
    {
        return view('Quickbooks/product-sync');
    }
    
    
}
