<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use Illuminate\Http\Request;
use App\Company;
use App\Invoice;
use App\User;
use App\AtvCertificate;
use App\Team;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
    
    
      /**
     * Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateWizard(Request $request)
    {
        
        $company = currentCompanyModel();
        $invoice = Invoice::firstOrNew(
          [
            'company_id' => $company->id,
            'is_totales' => true,
            'year' => 2018
          ]
        );

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) && get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            abort(403);
        }

        $company->type = $request->tipo_persona;
        $company->id_number = $request->id_number;
        $company->business_name = $request->business_name;
        $company->activities = $request->activities;
        $company->name = $request->name;
        $company->last_name = $request->last_name;
        $company->last_name2 = $request->last_name2;
        $company->email = $request->email;
        $company->invoice_email = $request->invoice_email;
        $company->country = $request->country;
        $company->state = ($request->state != '0') ? $request->state : NULL;
        $company->city = ($request->city != '0') ? $request->city : NULL;
        $company->district = ($request->district != '0') ? $request->district : NULL;
        $company->neighborhood = $request->neighborhood;
        $company->zip = $request->zip;
        $company->address = $request->address;
        $company->phone = $request->phone;
        
        $company->default_currency = $request->default_currency;
        $company->default_invoice_notes = $request->default_invoice_notes;
        $company->default_vat_code = $request->default_vat_code;
        $company->last_document = $request->last_document;
        $company->first_prorrata = $request->first_prorrata;
        $company->first_prorrata_type = $request->first_prorrata_type;
        $company->use_invoicing = $request->use_invoicing;
        
        $company->wizard_finished = true;
        
        $company->save();
        //Update Team name based on company
        $team->name = $request->name;
        $team->save();
        
        clearLastTaxesCache( $company->id, 2018);
        
        try {
          if( $company->use_invoicing ) {
            if (Storage::exists("empresa-$id/cert.p12")) {
                Storage::delete("empresa-$id/cert.p12");
            }
            
            $path = \Storage::putFileAs(
                "empresa-$id", $request->file('cert'), "cert.p12"
            );
            
            $cert = AtvCertificate::firstOrNew(
                [
                    'company_id' => $id,
                ]
            );
    
            $cert->user = $request->user;
            $cert->password = $request->password;
            $cert->key_url = $path;
            $cert->pin = $request->pin;
            
            $cert->save();
          }
        }catch( \Exception $ex ){ }
        
        if( $company->first_prorrata_type == 1 ) {
          return redirect( '/' )->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');
        }
        
        if( $company->first_prorrata_type == 2 ) {
          return redirect( '/editar-totales-2018' )->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');
        }
      
        return redirect('/')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, solamente debe agregar sus facturas del periodo hasta el momento.');
    }
  
}
