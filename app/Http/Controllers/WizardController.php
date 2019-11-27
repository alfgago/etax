<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use App\Actividades;
use App\SubscriptionPlan;
use Illuminate\Http\Request;
use App\Company;
use App\Invoice;
use App\AtvCertificate;
use App\Team;
use App\CalculatedTax;
use App\UserCompanyPermission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

/**
 * @group Controller - Wizard
 *
 * Funciones de WizardController.
 */
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
      
      $user = auth()->user();
      if( !$user->has_klap_user ) {
          $user->createKlapUser();
      }
      
      $subscription = getCurrentSubscription();
      if( ! isset( $subscription ) ) {
          return redirect('/elegir-plan');
      }
      
      $company = currentCompanyModel();
      $actividades = Actividades::all();
      return view('/wizard/index', compact( 'subscription', 'company', 'actividades') );

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
        clearLastTaxesCache($company_id, 2018);
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
        $invoice->reference_number = 0;
        
        $invoice->setInvoiceData($request);
        
        $invoice->month = 0;
        
        foreach($invoice->items as $item) {
            $item->month = 0;
            $item->save();
        }
        
        $invoice->save();
        
        $company->first_prorrata_type = 2;
        
        clearLastTaxesCache($company->id, 2018);
        clearLastTaxesCache($company->id, 2019);
        
        $calc = CalculatedTax::getProrrataPeriodoAnterior(2018);

        $company->operative_prorrata = number_format( $calc->prorrata*100, 2);
        $company->first_prorrata   = number_format( $calc->prorrata*100, 2);
        $company->operative_ratio1 = number_format( $calc->ratio1*100, 2);
        $company->operative_ratio2 = number_format( $calc->ratio2*100, 2);
        $company->operative_ratio3 = number_format( $calc->ratio3*100, 2);
        $company->operative_ratio4 = number_format( $calc->ratio4*100, 2);
        $company->save();
        $user = auth()->user();
	    Cache::forget("cache-currentcompany-$user->id");
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $company,
            [
                'company_id' => $company->id
            ],
            "Su prorrata operativa 2018 es de: ". number_format( $calc->prorrata*100, 2) ."%"
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/empresas/configuracion')->withMessage( 'Su prorrata operativa 2018 es de: '. number_format( $calc->prorrata*100, 2) . '%' );
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
            if( $company->id_number != $request->id_number ) {
                $request->validate([
                    'id_number' => 'required|unique:companies',
                ]);
            }

            $invoice = Invoice::firstOrNew(
                [
                    'company_id' => $company->id,
                    'is_totales' => true,
                    'year' => 2018
                ]
            );

            $team = Team::where('company_id', $company->id)->first();
            $user_id = auth()->user()->id;
            $current_company = currentCompany();
            $permisos = UserCompanyPermission::where('company_id',$current_company)
                        ->where('user_id',$user_id)->where('permission_id',8)->get();
            /* Only owner of company or user invited as admin for that company can edit company details */
            if ( !auth()->user()->isOwnerOfTeam($team) && !$permisos) 
            {
                abort(403);
            }

            $company->type = $request->tipo_persona;
            $company->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
            $company->business_name = $request->business_name;
            $company->commercial_activities = $request->commercial_activities;
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
            $company->last_document = $request->last_document ? $request->last_document : 0;
            $company->last_invoice_ref_number = $request->last_document ? getInvoiceReference($request->last_document) : 0;
            $company->first_prorrata = $request->first_prorrata;
            $company->first_prorrata_type = $request->first_prorrata_type;
            $company->use_invoicing = $request->use_invoicing;
            $company->card_retention = $request->card_retention;
            $company->default_product_category = $request->default_category_producto_code;
            
            if( $company->first_prorrata_type == 1 ) {
                $company->operative_prorrata = $request->first_prorrata;
                $company->operative_ratio1 = $request->operative_ratio1;
                $company->operative_ratio2 = $request->operative_ratio2;
                $company->operative_ratio3 = $request->operative_ratio3;
                $company->operative_ratio4 = $request->operative_ratio4;
            }
            
            $id_number = $company->id_number;
            $id_company = $company->id;
            if ( $request->file('input_logo') ) {

                $pathLogo = Storage::putFileAs(
                    "empresa-$id_number", $request->file('input_logo'),
                    "logo.".$request->file('input_logo')->getClientOriginalExtension()
                );
                $company->logo_url = $pathLogo;
                
            }
                
            $company->wizard_finished = true;
            $company->save();
            
            //Update Team name based on company
            $team->name = $company->id . ": " . $request->id_number . " - " . $request->name;
            $team->save();

            clearLastTaxesCache($company->id, 2018);
            
            if ($company->use_invoicing && $request->file('cert')) {
                if (Storage::exists("empresa-$id_number/$id_number.p12")) {
                    Storage::delete("empresa-$id_number/$id_number.p12");
                }

                $pathCert = Storage::putFileAs(
                    "empresa-$id_number", $request->file('cert'), "$id_number.p12"
                );

                $cert = AtvCertificate::firstOrNew(
                    [
                        'company_id' => $id_company,
                    ]
                );

                $cert->user = $request->user;
                $cert->password = $request->password;
                $cert->key_url = $pathCert;
                $cert->pin = $request->pin;
                $cert->save();
            }

            if ($company->first_prorrata_type == 2) {
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
                return redirect('/editar-totales-2018')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');

            }

            if ($company->first_prorrata_type == 3) {
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
                return redirect('/')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');
            }
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, solamente debe agregar sus facturas del periodo hasta el momento."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');

            return redirect('/')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, solamente debe agregar sus facturas del periodo hasta el momento.');
        
    }
    
    /**
     * Guarda los totales por código para el 2018. Se us apara calcular la prorrata operativa inicial si desea usar este metodo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createWizard(Request $request)
    {
        
        $request->validate([
            'id_number' => 'required|unique:companies',
        ]);
        
          
        try {  
            $user = auth()->user();
            $company = new Company();
            
            $company->subscription_id = getCurrentSubscription()->id;
            $company->user_id = $user->id;
            $company->type = $request->tipo_persona;
            $company->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
            $company->business_name = $request->business_name;
            $company->commercial_activities = $request->commercial_activities;
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
            $company->last_document = $request->last_document ? $request->last_document : 0;
            $company->last_invoice_ref_number = $request->last_document ? getInvoiceReference($request->last_document) : 0;
            $company->first_prorrata = $request->first_prorrata;
            $company->first_prorrata_type = $request->first_prorrata_type;
            $company->use_invoicing = $request->use_invoicing;
            
            if( $company->first_prorrata_type == 1 ) {
                $company->operative_prorrata = $request->first_prorrata;
                $company->operative_ratio1 = $request->operative_ratio1;
                $company->operative_ratio2 = $request->operative_ratio2;
                $company->operative_ratio3 = $request->operative_ratio3;
                $company->operative_ratio4 = $request->operative_ratio4;
            }

            $id_number = $company->id_number;
            $id_company = $company->id;
            if ( $request->file('input_logo') ) {

                $pathLogo = Storage::putFileAs(
                    "empresa-$id_number", $request->file('input_logo'),
                    "logo.".$request->file('input_logo')->getClientOriginalExtension()
                );
                $company->logo_url = $pathLogo;
                
            }
            
            $company->wizard_finished = true;
            $company->save();
            
            $invoice = Invoice::firstOrNew(
                [
                    'company_id' => $company->id,
                    'is_totales' => true,
                    'year' => 2018
                ]
            );
            /*$subscription_plan = SubscriptionPlan::create([
                'num_companies'=> $request->num_companies
            ]);*/

            $team = new Team();
    		$team->name = "(".$company->id.") " . $company->id_number;
    		$team->owner_id = $user->id;;
    		$team->company_id = $company->id;;
            $team->save();
            $user->attachTeam($team);

            clearLastTaxesCache($company->id, 2018);

            if ($company->use_invoicing && $request->file('cert')) {
                if (Storage::exists("empresa-$id_number/$id_number.p12")) {
                    Storage::delete("empresa-$id_number/$id_number.p12");
                }

                $pathCert = Storage::putFileAs(
                    "empresa-$id_number", $request->file('cert'), "$id_number.p12"
                );

                $cert = AtvCertificate::firstOrNew(
                    [
                        'company_id' => $id_company,
                    ]
                );

                $cert->user = $request->user;
                $cert->password = $request->password;
                $cert->key_url = $pathCert;
                $cert->pin = $request->pin;
                $cert->save();
            }

            if ($company->first_prorrata_type == 2) {
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
                return redirect('/editar-totales-2018')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');
            }

            if ($company->first_prorrata_type == 3) {
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
                return redirect('/')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');
            }
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, solamente debe agregar sus facturas del periodo hasta el momento."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
            return redirect('/')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, solamente debe agregar sus facturas del periodo hasta el momento.');
        } catch( \Exception $ex ) {
            Log::error('Error al crear compania: '.$ex->getMessage());
            return back()->withError('Ha ocurrido un error al registrar la compañía' . $ex->getMessage());
        }
    }
    
    
}
