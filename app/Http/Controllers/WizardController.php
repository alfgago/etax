<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\Invoice;
use Illuminate\Http\Request;

class WizardController extends Controller
{
  
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index() {
      
      return view('/wizard/index');

    }
    
     /**
     * Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setTotales2018()
    {
        $company_id = currentCompany();
        $totales = Invoice::firstOrNew(
          [
            'company_id' => $company_id,
            'is_totales' => true,
            'year' => 2018
          ]
        );
        return view("wizard/set-totales-2018", compact( 'totales' ) );
    }
    
     /**
     * Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTotales2018(Request $request)
    {
        
        $company = currentCompanyModel();
        $invoice = Invoice::firstOrNew(
          [
            'company_id' => $company->id,
            'is_totales' => true,
            'year' => 2018
          ]
        );
        
        if( !$request->subtotal ) {
          return redirect()->back()->with('error','Debe ingresar al menos una linea.');
        }

        //Datos generales y para Hacienda
        $invoice->document_type = "01";
        $invoice->hacienda_status = "01";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "";
        $invoice->is_totales = true;
        $invoice->generation_method = "TOTALES";
        $this->reference_number = 0;
        
        $invoice->setInvoiceData($request);
        
        $company->first_prorrata_type = 2;
        $company->save();
        
        clearInvoiceCache($invoice);
      
        return redirect('/empresas/configuracion');
    }
    
  
}
