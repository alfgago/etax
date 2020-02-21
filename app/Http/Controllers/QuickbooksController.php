<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use \Carbon\Carbon;
use App\Company;
use App\Provider;
use App\Client;
use App\Product;
use App\Invoice;
use App\Bill;
use Illuminate\Http\Request;

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
        return view('Quickbooks/config');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function variableMapIndex()
    {
        return view('Quickbooks/variable-map');
    }
    
    /**
     * Config screen for quickbooks
     *
     * @return \Illuminate\Http\Response
     */
    public function invoiceSyncIndex()
    {
        return view('Quickbooks/invoice-sync');
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
