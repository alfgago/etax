<?php

namespace App\Http\Controllers;

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
            return redirect()->route('User.companies')->withErrors(['email' => 'Invitation has already been accepted.']);
        }

        session(['invite_token' => $token]);
        return redirect()->route('register');
    }

}
