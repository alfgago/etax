<?php

namespace App\Http\Controllers\Teamwork;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Mpociot\Teamwork\Facades\Teamwork;
use Mpociot\Teamwork\TeamInvite;
use App\PlansInvitation;
use App\Company;

class AuthController extends Controller {

    /**
     * Accept the given invite
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvite($token) {

        $invite = Teamwork::getInviteFromAcceptToken($token);

        // check invitation is on pending state or not
        if (!$invite) {
            flash('Invitation has already been accepted.')->error()->important();
            return redirect()->route('User.companies')->withErrors(['email' => 'Invitation has already been accepted.']);
        }

        // check valid user acceptance or not
        if ($invite->email != auth()->user()->email) {
            flash('You are not an authorized user to accept the request please login/signup with valid credentials.')->error()->important();
            return redirect()->route('User.companies')->withErrors(['email' => 'You are not an authorized user to accept the request please login/signup with valid credentials.']);
        }

        // already memeber of a team
        $userRegistered = auth()->user()->getUserData($invite->email);
        if ($userRegistered) {
            if (isExistInTeam($invite->team_id, $userRegistered->getKey())) {
                flash('User already member of team.')->warning()->important();
                return redirect()->route('User.companies')->withErrors(['email' => 'User already member of team.']);
            }
        }

        // checked user is logged in or not if not then redirected to login first
        if (auth()->check()) {
            Teamwork::acceptInvite($invite);
            flash('I have accepted.')->success()->important();

            /* Add entry in plan invitations table */           
            $team = \App\Team::findOrFail($invite->team_id);
            $company = Company::find($team->company_id);

            $is_admin = ($invite->role == 'admin') ? '1' : '0';
            $is_readonly = ($invite->role == 'readonly') ? '1' : '0';
            PlansInvitation::create(['plan_no' => $company->plan_no, 'company_id' => $company->id, 'user_id' => auth()->user()->id, 'is_admin' => $is_admin, 'is_read_only' => $is_readonly]);
            /* Ends here */

            return redirect()->route('User.companies')->with('success', 'Invitation has been accepted');
        } else {
            session(['invite_token' => $token]);
            return redirect()->to('login');
        }
    }

}
