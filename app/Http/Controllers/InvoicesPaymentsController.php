<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InvoicesPayments;
use App\BankAccount;
use App\Invoice;

class InvoicesPaymentsController extends Controller
{
    
    public function lista(){
		$company = currentCompanyModel();
    	$invoices = InvoicesPayments::join('invoices','invoices_payments.invoice_id', '=','invoices.id')
    		->leftjoin('bank_accounts','invoices_payments.bank_account_id', '=','bank_accounts.id')
    		->where('invoices.company_id',$company->id)->get();
    	$cuentas = BankAccount::where('company_id',$company->id)->get();
    	//dd($invoices);
    	return view('invoices_payments.index')->with('invoices',$invoices)->with('cuentas',$cuentas);
    }

    public function consolidar($id){
    	$company = currentCompanyModel();
    	$invoice = Invoice::where('id',$id)->first();
    	$payments = InvoicesPayments::where('invoice_id',$id)->get();
    	$cuentas = BankAccount::where('company_id',$company->id)->get();
    	//dd($cuentas);
    	return view('invoices_payments.formulario_consolidar')->with('payments',$payments)->with('invoice',$invoice)->with('cuentas',$cuentas)->with('i',0);
    }

    public function new_payment_form($i){
    	$company = currentCompanyModel();
    	$cuentas = BankAccount::where('company_id',$company->id)->get();
    	//dd($i);
    	return view('invoices_payments.new_payment_form')->with('cuentas',$cuentas)->with('i',$i);
    }

    public function actualizar_consolidacion(Request $request){
    	InvoicesPayments::where('invoice_id', '=', $request->invocie_id)->delete();
    	for($i = 0; $i < count($request->payment_type); $i++) 
		{ 
    	//dd($request->proof[$i]);
			InvoicesPayments::insert(
			    ['invoice_id' => $request->invocie_id, 'amount' => $request->amount[$i], 'payment_type' => $request->payment_type[$i],
			     'bank_account_id' => $request->bank_account_id[$i], 'proof' => $request->proof[$i], 'payment_date' => $request->payment_date[$i],
			      'status' => $request->status[$i] ]
			);
		} 
    }
}
