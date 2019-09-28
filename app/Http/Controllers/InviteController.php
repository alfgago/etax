<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use Mpociot\Teamwork\Facades\Teamwork;

class InviteController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($token) {

        $invite = Teamwork::getInviteFromAcceptToken($token);

        // check inviation is on pending state or not
        if (!$invite) {
            return redirect()->route('User.companies')->withErrors(['email' => 'La invitación ha sido aceptada.']);
        }

        session(['invite_token' => $token]);
        return redirect()->route('register');
    }
    
        
    public function removeInvitation($id) {
        $invite = \App\TeamInvitation::findOrFail($id);
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($invite->team_id);

        /* Only owner of company can re-invite members to their company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            return redirect()->back()->withError('Usted no está autorizado para eliminar invitaciones');
        }
        
            $user = auth()->user();
              Activity::dispatch(
                  $user,
                  $invite,
                  [
                      'company_id' => $invite->company_id
                  ],
                  "La invitación ha sido eliminada satisfactoriamente."
              )->onConnection(config('etax.queue_connections'))
              ->onQueue('log_queue');
        $invite->delete();
        
        return redirect('/empresas/equipo')->withMessage('La invitación ha sido eliminada satisfactoriamente.');
    }

}
