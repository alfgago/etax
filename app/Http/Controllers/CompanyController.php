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

        $company->save();

        /* Add Company to Team */
        $teamModel = config('teamwork.team_model');

        /*$team_id = \DB::table('teams')->insertGetId([
            'name' => $request->name,
            'owner_id' => $id,
            'company_id' => $company->id
        ]);
        $team = $teamModel::findOrFail($team_id);*/
        
        $team = Team::firstOrCreate(
            [
                'name' => $request->name,
                'owner_id' => $id,
                'company_id' => $company->id
            ]    
        );

        auth()->user()->attachTeam($team);

        return redirect()->route('teams.index')->with('success', 'Company added successfully');
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

        $company = currentCompanyModel();

        if (!$company) {
            abort(404);
        }
        
        return view('Company.edit', compact('company'));
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
        $company->state = $request->state;
        $company->city = $request->city;
        $company->district = $request->district;
        $company->neighborhood = $request->neighborhood;
        $company->zip = $request->zip;
        $company->address = $request->address;
        $company->phone = $request->phone;
        
        $company->save();

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
        $this->authorize('update', $company);
        
        if (!$company) {
            abort(404);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
   /* public function update_company(Request $request, $id) {

        $company = Company::find($id);

        if (!$company) {
            abort(404);
        }

        $input = $request->all();

        $cert_exist = AtvCertificate::where('company_id', $id)->first();

        if ($cert_exist) {
            $cert_exist->user = !empty($input['user_name']) ? $input['user_name'] : $cert_exist->user;
            $cert_exist->password = !empty($input['password']) ? $input['password'] : $cert_exist->password;
            $cert_exist->pin = !empty($input['pin']) ? $input['pin'] : $cert_exist->pin;
            $cert_exist->generated_date = !empty($input['generated_date']) ? $input['generated_date'] : $cert_exist->generated_date;
            $cert_exist->due_date = !empty($input['due_date']) ? $input['due_date'] : $cert_exist->due_date;

            if ($request->key_url != null) {
                $image = time() . '.' . $request->key_url->getClientOriginalExtension();
                $request->key_url->move(public_path('atv_certificates'), $image);

                $cert_exist->key_url = $image;
            }

            $cert_exist->save();
        } else {

            if (!empty($input['user_name']) && !empty($input['password']) && !empty($input['pin'])) {
                $cert_data['company_id'] = $id;
                $cert_data['user'] = !empty($input['user_name']) ? $input['user_name'] : null;
                $cert_data['password'] = !empty($input['password']) ? $input['password'] : null;
                $cert_data['pin'] = !empty($input['pin']) ? $input['pin'] : null;
                $cert_data['generated_date'] = !empty($input['generated_date']) ? $input['generated_date'] : null;
                $cert_data['due_date'] = !empty($input['due_date']) ? $input['due_date'] : null;

                if ($request->key_url != null) {
                    $image = time() . '.' . $request->key_url->getClientOriginalExtension();
                    $request->key_url->move(public_path('atv_certificates'), $image);

                    $cert_data['key_url'] = $image;
                }

                AtvCertificate::create($cert_data);
            }
        }

        unset($input['user_name'], $input['password'], $input['pin'], $input['key_url']);
        $company->update($input);

        //Update Team name based on company
        \DB::table('teams')->where(array('company_id' => $id))->update(array('name' => $input['name']));
        return redirect()->route('teams.index')->with('success', 'Company information updated successfully');
    }*/

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy() {
        $post = Company::where('id', $id)->first();

        if ($post != null) {
            $post->delete();
            return redirect()->route('empresas.index')->with('success', 'Company deleted successfully');
        }

        return redirect()->route('empresas.index')->with(['message' => 'Wrong ID!!']);
    }
    
    /** @author Akhil Bansal
     * This Method is used to switch companies workspaces
     * @param Request $request 
     * @return $url
    */
    public function changeCompany(Request $request) {

        if (!$request->ajax()) {
            abort(403);
        }

        $company_id_no = $request->id;
        $company = Company::where('id_number', trim($company_id_no))->first();

        $url = '/';

        if (!empty($company)) {
            session(['current_company' => $company->id]);

            $team = \DB::table('teams')->where('company_id', $company->id)->first();

            $url = ($request->is_edit == 1) ? '/empresas/' . $company->id . '/edit' : (($request->is_edit == 3) ? '/companies/permissions/' . $team->id : '/empresas/company-profile/' . $company->id);
        }

        return $url;
    }

}
