<?php

namespace App\Http\Controllers\Teamwork;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Company;
use App\AtvCertificate;
use App\UserCompanyPermission;
use Mpociot\Teamwork\Exceptions\UserNotInTeamException;
use Illuminate\Support\Facades\Cache;

class TeamController extends Controller {

    public function __construct() {
        $this->middleware('permission:team-list');
        $this->middleware('permission:team-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:team-edit', ['only' => ['edit', 'update', 'switchTeam']]);
        $this->middleware('permission:team-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        abort(404); //This method is not used and functionality is shifted in UserController

        $teams = auth()->user()->teams;

        /* Show all teams if current user is super admin */
        if (auth()->user()->roles[0]->name == 'Super Admin') {
            $teams = \Mpociot\Teamwork\TeamworkTeam::get();
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

        return view('teamwork.index', compact('data'))->with('teams', $teams);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return view('Company.create');

        /*
          if (auth()->user()->roles[0]->name != 'Super Admin') {
          if (auth()->user()->current_team_id) {
          abort('401');
          }
          }

          return view('teamwork.create');
         */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        request()->validate([
            'name' => 'required',
        ]);

        $teamModel = config('teamwork.team_model');

        $team = $teamModel::create([
                    'name' => $request->name,
                    'owner_id' => $request->user()->getKey()
        ]);
        $request->user()->attachTeam($team);

        return redirect()->route('User.companies')->with('success', 'Team created successfully');
    }

    /**
     * Switch to the given team.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function switchTeam($id) {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);
        try {
            auth()->user()->switchTeam($team);
        } catch (UserNotInTeamException $e) {
            abort(403);
        }

        return redirect(route('User.companies'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        /* Only owner of company can edit their company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        return view('teamwork.edit')->withTeam($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $teamModel = config('teamwork.team_model');

        $team = $teamModel::findOrFail($id);
        $team->name = $request->name;
        $team->save();

        return redirect()->route('User.companies')->with('success', 'Team updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $teamModel = config('teamwork.team_model');

        $team = $teamModel::findOrFail($id);

        /* Only owner of company can delete their company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            return redirect()->back()->withError('Solamente el usuario creador de la empresa puede eliminarla.');
        }

        /* Delete company,certificate and permissions related to that company */
        $company = Company::findOrFail($team->company_id);
        $certificate = AtvCertificate::where('company_id', $team->company_id)->first();

        UserCompanyPermission::where('company_id', $team->company_id)->delete();

        $team->delete();
        $company->delete();

        if ($certificate) {
            $certificate->delete();
        }

        $userModel = config('teamwork.user_model');
        $userModel::where('current_team_id', $id)
                ->update(['current_team_id' => null]);

        return redirect(route('User.companies'));
    }

}
