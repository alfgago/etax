<?php

namespace App\Http\Controllers\Teamwork;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
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
            return redirect()->back()->withError('Usted no está autorizado para acceder a esta información');
        }

        /* Only owner of company can manage permissions */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            return redirect()->back()->withError('Usted no está autorizado para acceder a esta información');
        }

        $permissions = CompanyPermission::get();

        if ( empty($permissions->toArray()) ) {
            return redirect()->back()->withError('Usted no está autorizado para acceder a esta información');
        }

        return view('teamwork.members.permissions', compact('permissions'))->withTeam($team);
    }


    public function assignPermission(Request $request, $team_id) {
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);
        //dd($team);

        if (empty($team)) {
            return redirect()->back()->withError('Usted no está autorizado para actualizar a esta información');
        }

        /* Only owner of company can assign permissions */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            return redirect()->back()->withError('Usted no está autorizado para actualizar a esta información');
        }
        
        $companyId = $team['company_id'];
        if (!empty($request->permissions)) {
            foreach ($request->permissions as $key => $value) {
                foreach ($value as $row) {
                    $insert_arr[] = [
                        'company_id' => $team['company_id'],
                        'user_id' => $key,
                        'permission_id' => $row
                    ];
                }
                
                clearPermissionsCache($companyId, $key);
            }

            UserCompanyPermission::where(array('company_id' => $team['company_id']))->delete();
            UserCompanyPermission::insert($insert_arr);
        }
        return redirect()->back()->withMessage('Se han actualizado los permisos exitosamente');
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
            return redirect()->back()->withError('Usted no está autorizado para actualizar a esta información');
        }

        $userModel = config('teamwork.user_model');
        $user = $userModel::findOrFail($user_id);

        if ($user->getKey() === auth()->user()->getKey()) {
            return redirect()->back()->withError('Usted no está autorizado para actualizar a esta información');
        }

        $user->detachTeam($team);

        return redirect()->back()->with('success', 'El usuario ha sido removido del equipo.');
    }

    /**
     * @param Request $request
     * @param int $team_id
     * @return $this
     */
    public function invite(Request $request, $team_id) {
        
        $request->validate([
          'email' => 'required|email'
        ]);
        
        if (empty($request->email)) {
            return redirect()->back()->withErrors(['email' => 'Ingrese un correo válido.']);
        }
        
        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);

        /* Only owner of company can invite members to their company */
        if (!auth()->user()->isOwnerOfTeam($team)) {
            return redirect()->back()->withError('Usted no está autorizado para actualizar a esta información');
        }

        /* Check plan is active or not */
        $is_plan_active = currentCompanyModel()->isPlanActive();

        if (!$is_plan_active) {
            //return redirect()->back()->withErrors(['limit' => 'Debe renovar el plan antes de continuar.']);
        }

        /* Check plan limits */
        //if (plan_invitations_exceeds($team, $request->role)) {
            //return redirect()->back()->withErrors(['limit' => 'Usted ha llegado al límite de invitaciones disponibles para su plan.']);
        //}

        if (!Teamwork::hasPendingInvite($request->email, $team)) {
            $userRegistered = auth()->user()->getUserData($request->email);

            if ($userRegistered) {
                // already memeber of a team
                if (isExistInTeam($team->id, $userRegistered->getKey())) {
                    return redirect()->back()->withErrors(['email' => $request->email . ' ya es miembro del equipo.']);
                }
            }

            $is_user_exist = \App\User::where('email', $request->email)->first();
            $path = !empty($is_user_exist) ? 'teams.accept_invite' : 'invites.accept_invite';
            
            Teamwork::inviteToTeam($request->email, $team, function( $invite ) use ($request, $path) {
                
                Mail::to($request->email)->send(new InviteMail(
                    ['team' => $invite->team, 
                    'invite' => $invite, 
                    'path' => $path,
                    'name' => currentCompanyModel()->name
                    ]));
                // Send email to user
                flash('User ' . $invite->email . ' ha sido invitado.')->success()->important();

                \App\TeamInvitation::where('id', $invite->id)->update(['role' => $request->role]);
            });

        } else {
            return redirect()->back()->withErrors(['email' => $request->email . ' ya tiene una invitación pendiente.']);
        }

        return redirect()->back()->withMessage('La invitación se ha enviado satisfactoriamente.');
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
            return redirect()->back()->withError('Usted no está autorizado para actualizar a esta información');
        }

        $is_user_exist = \App\User::where('email', $invite->email)->first();
        $path = !empty($is_user_exist) ? 'teams.accept_invite' : 'invites.accept_invite';

        Mail::to($invite->email)->send(new InviteMail([
            'team' => $invite->team, 
            'invite' => $invite, 
            'path' => $path,
            'name' => currentCompanyModel()->name
        ]));

        /* Old Code
          Mail::send('teamwork.emails.invite', ['team' => $invite->team, 'invite' => $invite], function ($m) use ($invite) {
          $m->to($invite->email)->subject('Invitation to join team ' . $invite->team->name);
          });

          return redirect(route('teams.members.show', $invite->team));
         */

        return redirect()->back()->withMessage('Se ha re-enviado invitación a ' . $invite->email . ' satisfactoriamente.');
    }


}
