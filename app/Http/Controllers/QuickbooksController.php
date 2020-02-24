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
        $dataService = $qb->getAuthenticatedDS();
        
        $taxRates = $dataService->Query("SELECT * FROM TaxRate");
        $paymentMethods = $dataService->Query("SELECT * FROM PaymentMethod");
        $terms = $dataService->Query("SELECT * FROM Term");
        
        //dd($terms, $taxRates, $paymentMethods);
        
        
        return view('Quickbooks/variable-map', compact(['qb', 'taxRates', 'paymentMethods', 'terms']));
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function saveVariables($request)
    {
        $company = currentCompanyModel();
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        $dataService = $qb->getAuthenticatedDS();
        
        $taxRates = $dataService->Query("SELECT * FROM TaxRate");
        $paymentMethods = $dataService->Query("SELECT * FROM PaymentMethod");
        $terms = $dataService->Query("SELECT * FROM Term");
        
        //dd($terms, $taxRates, $paymentMethods);
        
        
        return view('Quickbooks/variable-map', compact(['qb', 'taxRates', 'paymentMethods', 'terms']));
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function invoiceSyncIndex()
    {
        $company = currentCompanyModel();
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        $dataService = $qb->getAuthenticatedDS();
        
        $qbInvoices   = $dataService->Query("SELECT * FROM Invoice MAXRESULTS 7");
        $etaxInvoices = Invoice::where("company_id", $company->id)->with('client')->limit(15)->get();
        $facturas = new \stdClass();
        
        $cachekey = "qb-invoicelist-$company->id_number"; 
        if ( !Cache::has($cachekey) ) {
            foreach($qbInvoices as $invoice){
                $docNumber = $invoice->DocNumber;
                $fac = new \stdClass();
                $fac->numero_qb = $docNumber;
                $fac->fecha_qb = $invoice->TxnDate;
                $fac->total_qb = $invoice->TotalAmt;
                $fac->cliente_qb = $dataService->Query("SELECT FullyQualifiedName FROM Customer WHERE Id='".$invoice->CustomerRef."'")[0]->FullyQualifiedName;
                $facturas->$docNumber = $fac;
            }
            Cache::put($cachekey, $facturas, 120);
        }else{
            $facturas = Cache::get($cachekey);
        }
        
        foreach($etaxInvoices as $invoice){
            $docNumber = $invoice->reference_number;
            if( isset($facturas->$docNumber) ){
                $fac = $facturas->$docNumber;
            }else{
                $fac = new \stdClass();
            }
            $fac->numero_etax = $docNumber;
            $fac->fecha_etax = $invoice->generatedDate()->format('d/m/Y');
            $fac->total_etax = $invoice->total;
            $fac->cliente_etax = "$invoice->client_first_name $invoice->client_last_name $invoice->client_last_name2";
            $facturas->$docNumber = $fac;
        }
        
        return view('Quickbooks/invoice-sync', compact(['facturas']));
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
