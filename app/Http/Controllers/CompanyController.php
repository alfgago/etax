<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AtvCertificate;
use App\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUser;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller {
    
    use SoftDeletes;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        if( ! auth()->user()->isContador() ) {
            return redirect('/')->withMessage('Su cuenta actual no permite más empresas.');
        }
        
        $available_companies_count = User::checkCountAvailableCompanies();
        
        if ($available_companies_count == 0) {
            return redirect()->route('User.companies')->withError('Su cuenta actual no permite más empresas.');
        }
        
        return view('Company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'tipo_persona' => 'required',
            'id_number' => 'required|unique:companies',
            'name' => 'required',
            'email' => 'required',
            'country' => 'required'
        ]);

        $company = new Company();
        $id = auth()->user()->id;
        $company->user_id = $id;

        $company->type = $request->tipo_persona;
        $company->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
        $company->name = $request->name;
        $company->last_name = $request->last_name;
        $company->last_name2 = $request->last_name2;
        $company->country = $request->country;
        $company->state = ($request->state != '0') ? $request->state : NULL;
        $company->city = ($request->city != '0') ? $request->city : NULL;
        $company->district = ($request->district != '0') ? $request->district : NULL;
        $company->neighborhood = $request->neighborhood;
        $company->zip = $request->zip;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->invoice_email = $request->invoice_email;
        $company->email = $request->email;
        $company->default_currency = !empty($request->default_currency) ? $request->default_currency : 'CRC';

        /* Add company to a plan */
        $company->subscription_id = getCurrentUserSubscriptions()[0]->id;

        $company->save();

        /* Add Company to Team */
        $team = Team::firstOrCreate([
                    'name' => "(".$company->id.") " . $company->id_number,
                    'owner_id' => $id,
                    'company_id' => $company->id
                        ]
        );

        auth()->user()->attachTeam($team);

        return redirect()->route('User.companies')->withMessage('La compañía ha sido agregada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function show(Company $empresa) {
        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit() {
        $company = currentCompanyModel();
        
        if (!$company) {
            return redirect('/');
        }

        $users = User::with(['roles' => function($q) {
                        $q->where('name', 'admin');
                    }])->get();
        $team = Team::where('company_id', $company->id)->first();

        $certificate = AtvCertificate::where('company_id', $company->id)->first();
        return view('Company.edit', compact('company', 'users', 'certificate', 'team'))->withTeam($team);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function editConfiguracion() {

        $company = currentCompanyModel();

        if (!$company) {
            return redirect('/');
        }
        
        return view('Company.edit-advanced', compact('company'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function editCertificate() {

        $company = currentCompanyModel();

        if (!$company) {
            return redirect('/');
        }

        $certificate = AtvCertificate::where('company_id', $company->id)->first();
        
        return view('Company.edit-certificate', compact('company', 'certificate'));
    }
    
        /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function editTeam() {
 $company = currentCompanyModel();

        $company = currentCompanyModel();

        if (!$company) {
            return redirect('/');
        }

        $users = User::with(['roles' => function($q) {
                        $q->where('name', 'admin');
                    }])->get();
                    
        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company can edit that company */
        if ( !auth()->user()->isOwnerOfTeam($team) ) {
            abort(401);
        }
        
        return view('Company.edit-team', compact('company', 'users', 'team'))->withTeam($team);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $company = Company::find($id);
        $this->authorize('update', $company);

        if (!$company) {
            return redirect('/');
        }
        
        $request->validate([
            'id_number' => 'required',
            'tipo_persona' => 'required',
            'name' => 'required',
            'email' => 'required',
            'country' => 'required'
        ]);
        
        if( $company->id_number != $request->id_number ) {
            $request->validate([
                'id_number' => 'required|unique:companies',
            ]);
        }

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) && get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            return redirect()->back()->withError('Usted no está autorizado para actualizar esta información');
        }


        
        if ($request->file('input_logo')) {
            
            if ( isset( $company->logo_url ) ){
                if (Storage::exists( $company->logo_url )) {
                    Storage::delete( $company->logo_url );
                }
            }
            
            $pathLogo = Storage::putFileAs(
                "empresa-$request->id_number", $request->file('input_logo'),
                "logo.".$request->file('input_logo')->getClientOriginalExtension()
            );
            $company->logo_url = $pathLogo;
        }

        $company->type = $request->tipo_persona;
        $company->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
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
        $company->save();

        //Update Team name based on company
        /*$team->name = "(".$company->id.") " . $company->id_number;
        $team->save();*/

        return redirect()->route('Company.edit')->withMessage('La información de la empresa ha sido actualizada.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function updateConfig(Request $request, $id) {

        $company = Company::find($id);

        if (!$company) {
            return redirect()->back()->withError('No se ha encontrado una compañía a su nombre.');
        }

        $this->authorize('update', $company);

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) &&
                get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            return redirect()->back()->withError('Usted no está autorizado para actualizar esta información');
        }

        $company->default_currency = $request->default_currency;
        $company->default_invoice_notes = $request->default_invoice_notes;
        $company->default_vat_code = $request->default_vat_code;
        $company->last_document = $request->last_document;
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

        $company->save();
        
        clearLastTaxesCache( $company->id, 2018);

        return redirect()->route('Company.edit_config')->withMessage('La configuración de la empresa ha sido actualizada.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function updateCertificado(Request $request, $id) {

        $company = Company::find($id);
        $this->authorize('update', $company);

        if (!$company) {
            return redirect()->back()->withError('No se ha encontrado una compañía a su nombre.');
        }

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) && get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            return redirect()->back()->withError('Usted no está autorizado para actualizar esta información');
        }

        $id_number = $company->id_number;
        if (Storage::exists("empresa-$id_number/$id_number.p12")) {
            Storage::delete("empresa-$id_number/$id_number.p12");
        }

        $pathCert = Storage::putFileAs(
            "empresa-$id_number", $request->file('cert'), "$id_number.p12"
        );

        $cert = AtvCertificate::firstOrNew(
            [
                'company_id' => $id,
            ]
        );

        $cert->user = $request->user;
        $cert->password = $request->password;
        $cert->key_url = $pathCert;
        $cert->pin = $request->pin;
        
        $cert->save();
        
        return redirect()->route('Company.edit_cert')->with('success', 'El certificado ATV ha sido actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request ) {
        if( 'ELIMINAR' != $request->confirmacion ){
            return 404;
            return redirect('/')->withError('La compañía no pudo ser eliminada, verifique el texto de confirmación');
        }
        
        $user = auth()->user();
        $currentCompany = currentCompanyModel();
        
        if( $currentCompany->user_id != $user->id ) {
            Log::warning( 'El usuario '. $user->id .' intentó eliminar la empresa #' . $currentCompany->id . ' - ' . $currentCompany->id_number );
            return redirect('/')->withError('Usted no tiene permisos para eliminar la empresa');
        }
        
        Log::warning( 'El usuario '. $user->id .' eliminó la empresa #' . $currentCompany->id . ' - ' . $currentCompany->id_number );
        $currentCompany->delete();
        return redirect('/')->withSuccess('La empresa ha sido eliminada satisfactoriamente.');
    }

    public function changeCompany(Request $request) {

        if (!$request->ajax()) {
            return redirect()->back()->withError('Ha ocurrido un error, inténtelo de nuevo.');
        }

        try {
            $companyId = $request->companyId;
            $team = Team::where( 'company_id', $companyId )->first();
	        auth()->user()->switchTeam( $team );
        } catch( UserNotInTeamException $e )
        {
        	
        }
        
    }
    
    public function setProrrata2018PorFacturas() {

        $company = currentCompanyModel();
        $this->authorize('update', $company);

        clearLastTaxesCache( $company->id, 2018);
        $company->first_prorrata_type = 3;
        $company->save();

        return redirect('/facturas-emitidas')->with('success', 'Empiece calculando su prorrata 2018 ingresando todas sus facturas de dicho periodo.');
    }

    public function confirmCompanyDeactivation($token) {

        $company = Company::where('deactivation_token', $token)->first();

        if ($company) {

            /* Check Token validity for 24 Hours */
            if (!token_expired($token)) {
                if ($company->user_id == auth()->user()->id) {
                    if (empty($company->deleted_at)) {
                        $company->deactivation_token = null;
                        $company->save();
                        $company->delete();
                        Team::where('company_id', $company->id)->delete();
                        session(['current_company' => '']);
                        return redirect()->route('User.companies')->with('success', 'Company deactivated successfully.');
                    } else {
                        return redirect()->route('User.companies')->withErrors(['error' => 'Company already deactivated.']);
                    }
                } else {
                    return redirect()->route('User.companies')->withErrors(['error' => 'You are not authorize to deactivate this company.']);
                }
            } else {
                return redirect()->route('User.companies')->withErrors(['error' => 'Link has been Expired.']);
            }
        } else {
            return redirect()->route('User.companies')->withErrors(['error' => 'Invalid Link or Company already deactivated.']);
        }
    }
}
