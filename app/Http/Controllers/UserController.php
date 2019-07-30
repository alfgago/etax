<?php

namespace App\Http\Controllers;

use App\Actividades;
use App\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Sales;
use App\EtaxProducts;
use App\PaymentMethod;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
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
            $user = auth()->user();
            if( ! $user->isContador() ) {
                return redirect('/');
            }
        
            $teams = $user->teams;
    
            /* Show registered companies list on specific plan */
            if ( isset($_GET['plan']) ) {
                $company_ids = \App\Company::where('subscription_id', decrypt($_GET['plan']))->get(['id'])->toArray();
                $teams = \Mpociot\Teamwork\TeamworkTeam::whereIn('company_id', $company_ids)->get();
            } else {
                //$teams = \Mpociot\Teamwork\TeamworkTeam::get();
            }
            $actividades = Actividades::all()->toArray();
            $availableCompanies = User::checkCountAvailableCompanies();
            
            return view('users.companies', compact('data', 'teams', 'availableCompanies'));

        }catch( \Throwable $ex ){
            return view('users.companies');
        }
        
    }

    public function plans() {

        $user_id = auth()->user()->id;
        $plans = Sales::where('user_id', $user_id)->get()->with('plan');

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
    public function destroy( $string ) {
        if( 'ELIMINAR' != $string ){
            return 404;
        }
        
        $user = auth()->user();
        
        Company::where('user_id', $user->id)->delete();
        $user->delete;
        
        Auth::logout();
        
        return redirect('/')->with('success', 'Su cuenta ha sido eliminada. Tiene 15 días para solicitar una restauración antes de que sus datos sean eliminados permanentemente.');
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

    public function payments(){
        $payments = auth()->user()->payments;
        return view('users.payment-history', compact('data'))->with('payments', $payments);
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
    
    public function impersonate( $id ) {
        
        if( Auth::user()->canImpersonate() ) {
            $user = User::findOrFail($id);
            if( !$user ){
                $user = User::where('email', $id)->first();
            }
            Auth::user()->impersonate($user);
        }
        return redirect( '/' );
        
    }
    
    public function leaveImpersonation( ) {
        
        Auth::user()->leaveImpersonation();
        return redirect( '/' );
        
    }

    public function updateUserTutorial(Request $request){
        if(request()->ajax()){
            $user = auth()->user();
            if($request->tutorialInicial==0){
                $user->hide_tutorial = 1;
                $user->save();
            }else if($request->tutorialInicial==1){
                $user->hide_tutorial = 2;
                $user->save();
            }else if($request->tutorialInicial==2){
                $user->hide_tutorial = 0;
                $user->save();
            }
        }
    }

    public function cancelar(){
        return view('users.cancelar');
    }
    public function updatecancelar(Request $request){
        $company = currentCompanyModel();
        $user_id = auth()->user()->companies->first()->user_id;
       
        Sales::where('user_id', $user_id)
               ->where('company_id', $company->id)
               ->update(['status' => 4, 'cancellation_reason' => $request->motivo]);

        Mail::to($company->email)->send(new \App\Mail\NotifyCancellation(auth()->user()->companies->first()));
        Auth::logout();
        return redirect("login")->withError('Su subscripción se ha cancelado');
    }

    public function CompraContabilidades(){
        $company = currentCompanyModel();
        $date_now = Carbon::parse( now('America/Costa_Rica') )->format('Y-m-d') ;
        $sale = Sales::join('subscription_plans','subscription_plans.id','sales.etax_product_id')->where('company_id', $company->id)
                                                        ->where('is_subscription', 1)->first();
        if( !$sale ){
            return back()->withError( 'Solamente el administrador de la empresa puede comprar facturas.' );
        }
        $paymentMethods = PaymentMethod::where('user_id', auth()->user()->id)->get();
        $fechavencimiento = date('Y-m-d', strtotime($sale->next_payment_date));
        $fechavencimiento = Carbon::parse($fechavencimiento);
        $diff = $fechavencimiento->diffInDays($date_now);
        //dd($diff);
        $fechavencimiento = date('d/m/Y', strtotime($sale->next_payment_date));
        return view('users.compra_contabilidades')->with('company', $company)
                                    ->with('sale', $sale)
                                    ->with('fechavencimiento', $fechavencimiento)
                                    ->with('diff', $diff)
                                    ->with('paymentMethods', $paymentMethods);

    }


}
