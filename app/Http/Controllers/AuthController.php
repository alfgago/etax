<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function loginSyncGoSocket() {

        //TODO: Esto es referencia para el login, se cambiar el query del usuario enviando el user_name y la compañia.
        $user = User::where('user_name', '=', 'xavierperna@gmail.com.ve')->first();
        if ($user !== null && Auth::loginUsingId($user->id)) {
            return redirect('/');
        } else {
            return redirect('/login');
       }
    }


}
