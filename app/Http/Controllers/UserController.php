<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Subscription;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Auth;
use \Firebase\JWT\JWT;
use Carbon\Carbon;

class UserController extends Controller {

    function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit() {
        $user = auth()->user();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
        //Edita al usuario logueado.
        $user = auth()->user();
        
        $this->validate($request, [
            'first_name' => 'required',
        ]);
        
        if( $user->id_number != $request->id_number ) {
            $request->validate([
                'id_number' => 'required|unique:users',
            ]);
        }
        
        $user->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->last_name2 = $request->last_name2;
        $user->email = $request->email;
        $user->country = $request->country;
        $user->state = ($request->state != '0') ? $request->state : NULL;
        $user->city = ($request->city != '0') ? $request->city : NULL;
        $user->district = ($request->district != '0') ? $request->district : NULL;
        $user->neighborhood = $request->neighborhood;
        $user->zip = $request->zip;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->save();
        
        return redirect()->back()->withMessage('La información de su perfil ha sido actualizada');
        
    }

    /**
     * Lleva a formulario para cambiar password
     *
     * @return \Illuminate\Http\Response
     */
    public function editPassword() {
        return view('users.edit-password');
    }
    
    
    /**
     * Actualiza contraseña de usuario logueado
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $id) {

        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|same:confirm_password'
        ]);

        $user = auth()->user();
        $input = $request->all();

        if (!empty($input['password'])) {
            $current_password = $user->password;
            if (!Hash::check($request->old_password, $current_password)) {
                return redirect()->back()->withError('Invalid Old Password');
            } elseif (Hash::check($request->password, $current_password)) {
                return redirect()->back()->withError('New Password should not be same as old password');
            }
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }

        $user->update($input);
        return redirect()->back()->withMessage('Profile password updated successfully');
    }

    public function companies() {
        
        $data['class'] = '';
        $data['url'] = '/empresas/create';
        
        try {
        
            $teams = auth()->user()->teams;
    
            /* Show registered companies list on specific plan */
            if ( isset($_GET['plan']) ) {
                $company_ids = \App\Company::where('subscription_id', decrypt($_GET['plan']))->get(['id'])->toArray();
                $teams = \Mpociot\Teamwork\TeamworkTeam::whereIn('company_id', $company_ids)->get();
            } else {
                //$teams = \Mpociot\Teamwork\TeamworkTeam::get();
            }
    
            $available_companies_count = User::checkCountAvailableCompanies();
            
            return view('users.companies', compact('data'))->with('teams', $teams);
        
        }catch( \Throwable $ex ){
            return view('users.companies');
        }
        
    }

    public function plans() {

        $user_id = auth()->user()->id;
        $plans = Subscription::where('user_id', $user_id)->get()->with('plan');

        return view('users.subscribed-plans', compact('plans'));
    }

    public function invitedUsersList() {

        if (!isset($_GET['plan']) || !isset($_GET['type'])) {
            return redirect()->back()->withError('Su usuario no tiene permisos para invitar miembros al equipo');
        }

        if (($_GET['type'] != 'admin') && ($_GET['type'] != 'readonly')) {
            return redirect()->back()->withError('Su usuario no tiene permisos para invitar miembros al equipo');
        }

        $companies = \App\Company::where('plan_no', decrypt($_GET['plan']))->get(['id']);

        $teams = \App\Team::whereIn('company_id', array_column($companies->toArray(), 'id'))->get(['id']);

        $team_ids = !empty($teams->toArray()) ? array_column($teams->toArray(), 'id') : array();

        $pending_invites_users = \App\TeamInvitation::whereIn('team_id', $team_ids)->where(array('user_id' => auth()->user()->id, 'role' => $_GET['type']))->with('team')->get()->toArray();

        if ($_GET['type'] == 'admin') {
            $accepted_invites_users = \App\PlansInvitation::where(array('plan_no' => decrypt($_GET['plan']), 'is_admin' => '1'))->with(array('user', 'company'))->get()->toArray();
        } else {
            $accepted_invites_users = \App\PlansInvitation::where(array('plan_no' => decrypt($_GET['plan']), 'is_read_only' => '1'))->with(array('user', 'company'))->get()->toArray();
        }

        $users_details = array_merge($pending_invites_users, $accepted_invites_users);

        return view('users.invited-users-list', compact('users_details'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        /*User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully');*/
    }
    
    public function changePlan() {
        
        return view('wizard.change-plan');
        
    }
    
    public function confirmPlanChange(Request $request) {
        
        $company = currentCompanyModel();
        $user = auth()->user();
        
        $start_date = Carbon::parse( now('America/Costa_Rica') );
        $trial_end_date = $start_date->addMonths(1);
        $next_payment_date = $start_date->addMonths(1);
        
        $sub = Subscription::updateOrCreate (
            
            [ 
                'user_id' => $user->id 
            ],
            [ 
                'plan_id' => $request->plan_id, 
                'status'  => 1, 
                'trial_end_date' => $trial_end_date, 
                'start_date' => $start_date, 
                'next_payment_date' => $next_payment_date, 
            ]
                
        );
        
        $company->subscription_id = $sub->id;
        $company->save();
        
        $nombre = $sub->plan->getName();
        
        return redirect('/')->withMessage("Su cuenta ha sido creada con el plan $nombre");
    }
    
    
    /**
     * Devuelve una llave JWT para ser usada por Zendesk y así validar al usuario. 
     **/
    public function zendeskJwt(){
       $user = auth()->user();
       $iat = Carbon::parse( now('America/Costa_Rica') )->timestamp ;
       $exp = Carbon::parse( now('America/Costa_Rica') )->addMinutes(5)->timestamp ;
       
       $payload = array(
           'name' => $user->first_name . ' ' . $user->last_name . ' ' . $user->last_name2,
           'email' => $user->email,
           'iat' => $iat,
           'exp' => $exp,
           'jti' => $user->id,
           'external_id' => $user->id
       );
       
       $key = 'A128DF3DC9D9DB0718AD9E31D76463A5B34928F3E4FC689137D80C128AEA3D8F';
       $jwt = JWT::encode($payload, $key);
       
       return $jwt;
    }
    
    
    public function adminEdit( $email ) {
        
        if( auth()->user()->user_name != "alfgago" ) {
            return redirect(404);
        }
        
        $user = User::where('user_name', $email)->first();
        
        return view('users.edit-admin', compact('user'));
        
        
    }

    public function updateAdmin(Request $request, $id) {
        //Edita al usuario logueado.
        if( auth()->user()->user_name != "alfgago" ) {
            return redirect(404);
        }
        
        $user = User::findOrFail($id);
        
        $this->validate($request, [
            'first_name' => 'required',
        ]);
        
        if( $user->id_number != $request->id_number ) {
            $request->validate([
                'id_number' => 'required|unique:users',
            ]);
        }
        
        if( $user->user_name != $request->user_name ) {
            $request->validate([
                'user_name' => 'required|unique:users',
            ]);
        }
        
        $user->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
        $user->user_name = $request->user_name;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->last_name2 = $request->last_name2;
        $user->email = $request->email;
        $user->country = $request->country;
        $user->state = ($request->state != '0') ? $request->state : NULL;
        $user->city = ($request->city != '0') ? $request->city : NULL;
        $user->district = ($request->district != '0') ? $request->district : NULL;
        $user->neighborhood = $request->neighborhood;
        $user->zip = $request->zip;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->save();
        
        return redirect()->back()->withMessage('La información del usuario $user->email ha sido actualizada');
        
    }

}
