<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class RegisterController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Register Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users as well as their
      | validation and creation. By default this controller uses a trait to
      | provide this functionality without requiring any additional code.
      |
     */

use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
                    'first_name' => ['required', 'string', 'max:255'],
                    'last_name' => ['required', 'string', 'max:255'],
                    'last_name2' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
        
        $user = User::create([
                    'user_name' => $data['email'],
                    'email' => $data['email'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'last_name2' => $data['last_name2'],
                    'password' => Hash::make($data['password'])
        ]);

        $user->assignRole(array('Subscriber'));
        
        $user->addCompany();

        $phone =$user->phone;
        if(!isset($phone)){
            $phone = '22802130';
        }
        $client = new Client();
        $result = $client->request('POST', "http://www.fttserver.com:4217/api/createUser", [
            'headers' => [
                'Content-Type'  => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX',
                'userName' => $data['email'],
                'userFirstName' => $data['first_name'],
                'userLastName' => $data['last_name'],
                'userPassword' => Hash::make($data['password']),
                'userEmail' => $data['email'],
                'userCallerId' => $phone
            ],
            'verify' => false,
        ]);
        $output = json_decode($result->getBody()->getContents(), true);

        /* Old Code
         * If user is registering from invitation,it is added as normal user else as admin user
          if (!empty(session('invite_token'))) {
          $user->assignRole(array('Normal User'));
          } else {
          //$user->addCompany();
          $user->assignRole(array('Admin'));
          }
         */
        return $user;
    }

}
