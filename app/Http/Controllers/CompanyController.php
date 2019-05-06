<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AtvCertificate;
use App\Team;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        if (auth()->user()->roles[0]->name != 'Super Admin') {
            /* Not able to create company if dont have any active plan */
            $available_companies_count = User::checkCountAvailableCompanies();

            if ($available_companies_count == 0) {
                abort(403);
            }
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
            'id_number' => 'required',
            'name' => 'required',
            'email' => 'required',
            'country' => 'required'
        ]);

        $company = new Company();
        $id = auth()->user()->id;
        $company->user_id = $id;

        $company->type = $request->tipo_persona;
        $company->id_number = $request->id_number;
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
        $company->default_currency = !empty($request->default_currency) ? $request->default_currency : 'crc';

        /* Add company to a plan */
        $company->plan_no = (auth()->user()->roles[0]->name != 'Super Admin') ? get_current_user_subscriptions()[0] : '0';

        $company->save();

        /* Add Company to Team */
        $team = Team::firstOrCreate([
                    'name' => $request->name,
                    'owner_id' => $id,
                    'company_id' => $company->id
                        ]
        );

        auth()->user()->attachTeam($team);

        return redirect()->route('User.companies')->with('success', 'Company added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function show(Company $empresa) {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit() {

        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $company = Company::find($_GET['id']);

            if ($company) {
                session(['current_company' => $company->id]);
            }
        }

        $company = currentCompanyModel();

        if (!$company) {
            abort(404);
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
            abort(404);
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
            abort(404);
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

        if (!$company) {
            abort(404);
        }

        $users = User::with(['roles' => function($q) {
                        $q->where('name', 'admin');
                    }])->get();
                    
        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company can edit that company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
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
            abort(404);
        }

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) && get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            abort(403);
        }

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) && get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            abort(403);
        }

        $company->type = $request->type;
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
        $company->save();

        //Update Team name based on company
        $team->name = $request->name;
        $team->save();

        return redirect()->route('Company.edit')->with('success', 'La información de la empresa ha sido actualizada.');
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
            abort(404);
        }

        $this->authorize('update', $company);

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) && get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            abort(403);
        }

        $company->default_currency = $request->default_currency;
        $company->default_invoice_notes = $request->default_invoice_notes;
        $company->default_vat_code = $request->default_vat_code;
        $company->last_document = $request->last_document;
        $company->first_prorrata = $request->first_prorrata;
        $company->first_prorrata_type = $request->first_prorrata_type;
        $company->use_invoicing = $request->use_invoicing;

        $company->save();

        return redirect()->route('Company.edit_config')->with('success', 'La configuración de la empresa ha sido actualizada.');
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
            abort(404);
        }

        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company or user invited as admin for that company can edit company details */
        if (!auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($company->id, auth()->user()->id) && get_plan_invitation($company->id, auth()->user()->id)->is_admin != '1')) {
            abort(403);
        }

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
        
        return redirect()->route('Company.edit_cert')->with('success', 'El certificado ATV ha sido actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $post = Company::where('id', $id)->first();

        if ($post != null) {
            $post->delete();
            return redirect()->route('empresas.index')->with('success', 'Company deleted successfully');
        }

        return redirect()->route('empresas.index')->with(['message' => 'Wrong ID!!']);
    }

    public function changeCompany(Request $request) {

        if (!$request->ajax()) {
            abort(403);
        }

        $company_id_no = $request->id;
        $company = Company::where('id_number', trim($company_id_no))->first();

        $url = '/';

        if (!empty($company)) {
            session(['current_company' => $company->id]);

            $team = Team::where('company_id', $company->id)->first();

            $url = ($request->is_edit == 1) ? '/empresas/' . $company->id . '/edit' : (($request->is_edit == 3) ? '/companies/permissions/' . $team->id : '/empresas/company-profile/' . $company->id);
        }

        return $url;
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
