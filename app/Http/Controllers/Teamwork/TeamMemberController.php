<?php

namespace App\Http\Controllers\Teamwork;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Mpociot\Teamwork\Facades\Teamwork;
use Mpociot\Teamwork\TeamInvite;
use App\Mail\InviteMail;
use App\UserCompanyPermission;
use App\CompanyPermission;

class TeamMemberController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the members of the given team.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        abort(404); //Dont show this page for now

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        /* Only current team should be visible */
        if (!isExistInTeam($id, auth()->user()->id)) {
            abort(401);
        }

        return view('teamwork.members.list')->withTeam($team);
    }

    public function permissions($id) {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        if (empty($team)) {
            abort(404);
        }

        /* Only owner of company can manage permissions */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        $permissions = CompanyPermission::get();

        if (empty($permissions->toArray())) {
            abort(404);
        }

        return view('teamwork.members.permissions', compact('permissions'))->withTeam($team);
    }

    public function assignPermission(Request $request, $team_id) {

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);

        if (empty($team)) {
            abort(404);
        }

        /* Only owner of company can assign permissions */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        if (!empty($request->permissions)) {
            foreach ($request->permissions as $key => $value) {
                foreach ($value as $row) {
                    $insert_arr[] = [
                        'company_id' => $team['company_id'],
                        'user_id' => $key,
                        'permission_id' => $row
                    ];
                }
            }

            UserCompanyPermission::where(array('company_id' => $team['company_id']))->delete();
            UserCompanyPermission::insert($insert_arr);
        }

        return redirect()->back()->with('success', 'Permissions has been assigned to user successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $team_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($team_id, $user_id) {

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);

        /* Only owner of company can delete that company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        $userModel = config('teamwork.user_model');
        $user = $userModel::findOrFail($user_id);

        if ($user->getKey() === auth()->user()->getKey()) {
            abort(403);
        }

        $user->detachTeam($team);

        return redirect()->back()->with('success', 'User has been removed from company successfully.');
    }

    /**
     * @param Request $request
     * @param int $team_id
     * @return $this
     */
    public function invite(Request $request, $team_id) {

        if (empty($request->email)) {
            return redirect()->back()->withErrors(['email' => 'Please enter email address.']);
        }

        if (empty($request->role)) {
            return redirect()->back()->withErrors(['role' => 'Please select a type.']);
        }

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);

        /* Only owner of company can invite members to their company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        /* Check plan is active or not */
        $is_plan_active = \App\Company::isPlanActive();

        if (!$is_plan_active) {
            return redirect()->back()->withErrors(['limit' => 'Plan is no longer active.']);
        }

        /* Check plan limits */
        if (plan_invitations_exceeds($team, $request->role)) {
            return redirect()->back()->withErrors(['limit' => 'Plan limit exceeds to invite users.Please subscribe to another plan.']);
        }

        if (!Teamwork::hasPendingInvite($request->email, $team)) {
            $userRegistered = auth()->user()->getUserData($request->email);

            if ($userRegistered) {
                // already memeber of a team
                if (isExistInTeam($team->id, $userRegistered->getKey())) {
                    flash($request->email . ' Already member of the team.')->error()->important();
                    return redirect()->back()->withErrors(['email' => $request->email . ' Already member of the team.']);
                }
            }

            $is_user_exist = \App\User::where('email', $request->email)->first();
            $path = !empty($is_user_exist) ? 'teams.accept_invite' : 'invites.accept_invite';

            Teamwork::inviteToTeam($request->email, $team, function( $invite ) use ($request, $path) {

                Mail::to($request->email)->send(new InviteMail(['team' => $invite->team, 'invite' => $invite, 'path' => $path]));
                // Send email to user
                flash('User ' . $invite->email . ' has been invited.')->success()->important();

                \App\TeamInvitation::where('id', $invite->id)->update(['role' => $request->role]);
            });

        } else {
            flash($request->email . 'address is already invited to the team pending for acceptance.')->error()->important();
            return redirect()->back()->withErrors(['email' => $request->email . ' is already invited to the team and is pending for acceptance.']);
        }

        return redirect()->back()->with('success', 'Invitation mail has been sent to user successfully.');
    }

    /**
     * Resend an invitation mail.
     * 
     * @param $invite_id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function resendInvite($invite_id) {

        $invite = TeamInvite::findOrFail($invite_id);

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($invite->team_id);

        /* Only owner of company can re-invite members to their company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        $is_user_exist = \App\User::where('email', $invite->email)->first();
        $path = !empty($is_user_exist) ? 'teams.accept_invite' : 'invites.accept_invite';

        Mail::to($invite->email)->send(new InviteMail(['team' => $invite->team, 'invite' => $invite, 'path' => $path]));

        /* Old Code
          Mail::send('teamwork.emails.invite', ['team' => $invite->team, 'invite' => $invite], function ($m) use ($invite) {
          $m->to($invite->email)->subject('Invitation to join team ' . $invite->team->name);
          });

          return redirect(route('teams.members.show', $invite->team));
         */

        flash('User ' . $invite->email . ' has been re-invited.')->success()->important();
        return redirect()->back()->with('success', 'User ' . $invite->email . ' has been re-invited.');
    }

}
