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
            flash('La invitación ya ha sido aceptada.')->error()->important();
            return redirect()->route('User.companies')->withErrors(['email' => 'La invitación ya ha sido aceptada.']);
        }

        // check valid user acceptance or not
        if ($invite->email != auth()->user()->email) {
            flash('Este usuario no está autorizado para aceptar la invitación.')->error()->important();
            return redirect()->route('User.companies')->withErrors(['email' => 'Este usuario no está autorizado para aceptar la invitación.']);
        }

        // already memeber of a team
        $userRegistered = auth()->user()->getUserData($invite->email);
        if ($userRegistered) {
            if (isExistInTeam($invite->team_id, $userRegistered->getKey())) {
                flash('Usuario ya es miembro de la organización.')->warning()->important();
                return redirect()->route('User.companies')->withErrors(['email' => 'Usuario ya es miembro de la organización.']);
            }
        }

        // checked user is logged in or not if not then redirected to login first
        if (auth()->check()) {
            Teamwork::acceptInvite($invite);
            flash('Se ha aceptado la invitación.')->success()->important();

            /* Add entry in plan invitations table */           
            $team = \App\Team::findOrFail($invite->team_id);
            $company = Company::find($team->company_id);

            $is_admin = ($invite->role == 'admin') ? '1' : '0';
            $is_readonly = ($invite->role == 'readonly') ? '1' : '0';
            PlansInvitation::create( [
                'subscription_id' => $company->subscription_id, 
                'company_id' => $company->id, 
                'user_id' => auth()->user()->id
            ] );
            /* Ends here */

            return redirect()->route('User.companies')->withMessage('La invitación ha sido aceptada.');
        } else {
            session(['invite_token' => $token]);
            return redirect()->to('login');
        }
    }

}
