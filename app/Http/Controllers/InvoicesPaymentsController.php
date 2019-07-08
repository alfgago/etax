<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InvoicesPayments;
use App\BankAccount;

class InvoicesPaymentsController extends Controller
{
    
    public function lista(){

        $company = auth()->user()->companies->first()->id;
    	$invoices = InvoicesPayments::join('invoices','invoices_payments.invoice_id', '=','invoices.id')
    		->where('invoices.company_id',$company)->get();
    	$cuentas = BankAccount::where('company_id',$company)->get();
    	//dd($invoices);
    	return view('invoices_payments.index')->with('invoices',$invoices)->with('cuentas',$cuentas);
    }



}
