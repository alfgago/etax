<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\IntegracionEmpresa;
use App\User;
use App\Company;
use Illuminate\Support\Facades\Auth;

class GoSocketController extends Controller
{
     
    public function gosocketvalidate(Request $request) {
    	$token = $request->token;
    	if (!empty($token)) {
            $ApplicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($ApplicationIdGS.":".$token);
            $GoSocket = new Client();
            $APIStatus = $GoSocket->request('GET', "http://api.sandbox.gosocket.net/api/Gadget/GetUser", [
                'headers' => [
                    'Content-Type' => "application/json",
                    'Accept' => "application/json",
                    'Authorization' => "Basic " . $base64
                ],
                'json' => [
                ],
                'verify' => false,
            ]);
            $user_gs = json_decode($APIStatus->getBody()->getContents(), true);
            dd($user_gs);
            $user = IntegracionEmpresa::where("access_token",$user_gs['UserId'])->where("company_token",$user_gs['CurrentAccountId'])->first();
            if(is_null($user)){
                $GoSocket = new Client();
                $APIStatus = $GoSocket->request('GET', "http://api.sandbox.gosocket.net/api/Gadget/GetAccount?accountId=".$user_gs['CurrentAccountId'], [
                    'headers' => [
                        'Content-Type' => "application/json",
                        'Accept' => "application/json",
                        'Authorization' => "Basic " . $base64
                    ],
                    'json' => [
                    ],
                    'verify' => false,
                ]);
                $company_gs = json_decode($APIStatus->getBody()->getContents(), true);
                $user_etax = User::where('email',$user_gs['Email'])->first();
                $company_etax = Company::where('id_number',$company_gs['Code'])->first();

                //dd($company_etax->team->id);
                $new_user_gs = User::firstOrCreate(
                    ['user_name' => "GS_".$user_gs['UserId']],
                    ['email' => $user_etax['email'],
                    'first_name' => $user_etax['first_name'],
                    'last_name' => $user_etax['last_name'],
                    'last_name2' => $user_etax['last_name2'],
                    'phone' => $user_etax['phone'],
                    'password' => 'password']
                );
                $E = "slug_" . $company_etax->id . "_" . $new_user_gs->id;


            }
            // dd("asdas");
            if ($user !== null && Auth::loginUsingId($user->user_id)) {
                /*	$user = auth()->user();
                    $companyId = $request->companyId;
                    $team = Team::where( 'company_id', $companyId )->first();
                    $user->switchTeam( $team );
                    Cache::forget("cache-currentcompany-$user->id");*/
                return redirect('/');
            } else {
                return redirect('/login');
            }
        } else {
            return redirect('/login');
        }
	    
    }
     
    
    public function getInvoices(){
    	$token = $_GET['token'];
    	$ApplicationIdGS = config('etax.applicationidgs');
		$base64 = base64_encode($ApplicationIdGS.":".$token);
    	$GoSocket = new Client();
	    $APIStatus = $GoSocket->request('GET', "http://api.sandbox.gosocket.net/api/Gadget/GetUser", [
	        'headers' => [
	            'Content-Type' => "application/json",
	            'Accept' => "application/json", 
	            'Authorization' => "Basic " . $base64
	        ],
	        'json' => [
	        ],
	        'verify' => false,
	    ]);
	    $APIStatus = json_decode($APIStatus->getBody()->getContents(), true);
	    dd($APIStatus);
	    
    }



}
