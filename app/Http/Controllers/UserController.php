<?php

namespace App\Http\Controllers;
//header('Access-Control-Allow-Origin: *');
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Hash;
use Auth;
require __DIR__.'/../../../vendor/autoload.php';
use Zendesk\API\HttpClient as ZendeskAPI;
use \Firebase\JWT\JWT;
use Carbon\Carbon;

class UserController extends Controller {

    function __construct() {
        $this->middleware('permission:user-list');
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $data = User::orderBy('id', 'DESC')->paginate(5);
        return view('users.index', compact('data'))
                        ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //prd($request->all());
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user) {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'same:confirm_password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }

        $user->update($input);
        $user->syncRoles($request->roles);
        return redirect()->route('users.index')->withSuccess('User Updated Successfully!');
    }

    public function editInformation() {
        return view('users.edit-information');
    }

    public function overview() {
        return view('users.overview');
    }

    public function editPassword() {
        return view('users.edit-password');
    }

    public function companies() {

        $teams = auth()->user()->teams;

        /* Show registered companies list on specific plan */
        if (isset($_GET['plan'])) {
            $company_ids = \App\Company::where('plan_no', decrypt($_GET['plan']))->get(['id'])->toArray();
            $teams = \Mpociot\Teamwork\TeamworkTeam::whereIn('company_id', $company_ids)->get();
        } else {
            /* Show all teams if current user is super admin */
            if (auth()->user()->roles[0]->name == 'Super Admin') {
                $teams = \Mpociot\Teamwork\TeamworkTeam::get();
            }
        }

        $data['class'] = '';
        $data['url'] = '/empresas/create';

        /* Check subscription plan for users */
        if (auth()->user()->roles[0]->name != 'Super Admin') {

            $available_companies_count = User::checkCountAvailableCompanies();

            if ($available_companies_count == 0) {
                $data['url'] = 'javascript:void(0)';
                $data['class'] = 'no_active_plan_popup';
            }
        }

        return view('users.companies', compact('data'))->with('teams', $teams);
    }

    public function plans() {

        $user_id = auth()->user()->id;
        $plans = user_subscribed_plans($user_id);

        return view('users.subscribed-plans', compact('plans'));
    }

    public function invitedUsersList() {

        if (!isset($_GET['plan']) || !isset($_GET['type'])) {
            abort(404);
        }

        if (($_GET['type'] != 'admin') && ($_GET['type'] != 'readonly')) {
            abort(404);
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

    public function updateInformation(Request $request, $id) {

        $this->validate($request, [
            'first_name' => 'required',
            'phone' => 'digits:10|unique:users,phone,' . $id,
            'address' => 'required',
            'profile_pic' => 'nullable|image'
        ]);

        $user = User::find($id);
        $input = $request->all();

        if ($request->profile_pic != null) {
            $input['image'] = time() . '.' . $request->profile_pic->getClientOriginalExtension();
            $request->profile_pic->move(public_path('profile_pics'), $input['image']);
        }

        $user->update($input);
        return redirect()->back()->with('success', 'Profile information updated successfully');
    }

    public function updatePassword(Request $request, $id) {

        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|same:confirm_password'
        ]);

        $user = User::find($id);
        $input = $request->all();

        if (!empty($input['password'])) {
            $current_password = $user->password;
            if (!Hash::check($request->old_password, $current_password)) {
                return redirect()->back()->with('error', 'Invalid Old Password');
            } elseif (Hash::check($request->password, $current_password)) {
                return redirect()->back()->with('error', 'New Password should not be same as old password');
            }
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }

        $user->update($input);
        return redirect()->back()->with('success', 'Profile password updated successfully');
    }
    /*
    *Retorna las consultas de zendesk del usuario
    *
    */
    public function consulta_zendesk(){
        $subdomain = "etax";
        $username  = 'agago@etaxcr.com'; // replace this with your registered email
        $token     = "v1rk6PURKashoSQ0sK6gVXcd7LEIYDp6hEjWti8b"; // replace this with your token
        $client = new ZendeskAPI($subdomain);
        $client->setAuth('basic', ['username' => $username, 'token' => $token]);
        // $tickets = $client->tickets()->findAll(['per_page' => 25, 'page' => 1]);
        // $all = json_decode(json_encode($tickets), true);
        $zendesk_id = auth()->user()->zendesk_id;
        $tickets = $client->users($zendesk_id)->requests()->findAll();
        return $tickets;
    }
    /*
    *
    *
    */
    public function zendesk(){ 
        $all = $this->consulta_zendesk();
        $total = count($all['tickets']); 
        $submitter_id = $all['tickets'][0]['submitter_id'];
        $organization_id = $all['tickets'][0]['organization_id'];
        $group_id = $all['tickets'][0]['group_id'];
        return view('users.zendesk')->with('submitter_id', $submitter_id)
                                    ->with('total', $total)
                                    ->with('group_id', $group_id)
                                    ->with('organization_id', $organization_id);
    }
    /*
    *
    *
    */
    public function zendeskdetalle($id){ 
        $tickets = (array) $this->consulta_zendesk();
        $array = array();
        for ($i=0; $i < count($tickets['requests']); $i++) { 
            if($tickets['requests'][$i]->id = $id){
                array_push($array, $tickets['requests'][$i]);
                $id = $tickets['requests'][$i]->id;
            }
        }

        return view('users.zendeskdetalle')->with('tickets', $array)
                                           ->with('id', $id);
    }
    /*
    *
    *Crear tickets en zendesk
    * 
    */
    public function crearTicket(){
        $user_id = auth()->user()->id;
        return view('users.zendesk_add')->with('user_id', $user_id);
    }
    /*
    *
    *Consulta de tickets
    *
    */
    public function ver_consultas(){
        $tickets = $this->consulta_zendesk();
        return view('users.zendesk_all')->with('tickets', $tickets);
    }
    /*
    *
    *
    */
    public function crearRequest(Request $request){
        $type        = ($request->type) ? $request->type : 'incident';
        $subject     = ($request->subject) ? $request->subject : 'No definido por el usuario';
        $priority    = ($request->priority) ? $request->priority : 'low';
        $description = ($request->description) ? $request->description : 'No definido por el usuario';
        $name = auth()->user()->last_name;
        $email = auth()->user()->email;
        $subdomain = "etax";
        $username = "agago@etaxcr.com";
        $token = "v1rk6PURKashoSQ0sK6gVXcd7LEIYDp6hEjWti8b";
        $client = new ZendeskAPI($subdomain);
        $client->setAuth('basic', ['username' => $username, 'token' => $token]);
        $newTicket = $client->tickets()->create(array('type' => $type, 'tags' => array('demo', 'testing', 'api', 'zendesk'), 'subject' => $subject, 'comment' => array('body' => $description), 'requester' => array('locale_id' => '1', 'name' => $name, 'email' => $email), 'priority' => $priority));
        $user_id = auth()->user()->zendesk_id;
        return view('users.zendesk_add')->with('user_id', $user_id);
    }
    /*
    */
    public function zendeskJwt(){
        $user = auth()->user();
        $iat = Carbon::now()->timestamp;
        $externalId = $user->id;
        $payload2 = array(
            'name' => $user->first_name,
            'email' => $user->email,
            'iat' => $iat,
            'external_id' => $externalId
        );
        $payload = json_encode($payload2, true);
        $secret = json_encode('8707438E70BF8AAE575B4A20455DB6B0AEBDCD2781B02C36492518B005B116F4', true);
        $token = JWT::encode($payload, $secret);
        return response()->json(['token' => $token]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully');
    }

}
