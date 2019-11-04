<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use App\Jobs\GoSocketInvoicesSync;
use App\Utils\BridgeGoSocketApi;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\IntegracionEmpresa;
use App\Integracion;
use App\User;
use App\Company;
use App\Team;
use App\Invoice;
use App\Bill;
use App\UserCompanyPermission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoSocketController extends Controller
{

    public function index(Request $request){
        $token = $request->token;
        return view('gosocket.index')->with('token',$token);
    }

    public function gosocketValidate(Request $request) {
        try{
        	$token = $request->token;
        	if (!empty($token)) {
                $apiGoSocket = new BridgeGoSocketApi();
                $user_gs = $apiGoSocket->getUser($token);
                $user = IntegracionEmpresa::where("user_token",$user_gs['UserId'])->where("company_token",$user_gs['CurrentAccountId'])->first();

                if (is_null($user)) {
                    $company_gs = $apiGoSocket->getAccount($token, $user_gs['CurrentAccountId']);
                    $user_etax = User::firstOrCreate(
                        ['email' => $user_gs['Email']],
                        ['user_name' => $user_gs['Email'],
                        'first_name' => $user_gs['Name'],
                        'password' => 'asd889as56ad9as66asd6as9']
                    );

                    $new_user_gs = User::firstOrCreate(
                        ['user_name' => "GS_".$user_gs['UserId']],
                        ['email' => $user_etax['email'].".gs",
                        'first_name' => $user_etax['first_name'],
                        'last_name' => $user_etax['last_name'],
                        'last_name2' => $user_etax['last_name2'],
                        'phone' => $user_etax['phone'],
                        'password' => 'password']
                    );


                    $company_etax = Company::firstOrCreate(
                        ['id_number' => $company_gs['Code']],
                        ['user_id' => $user_etax['id'],
                        'business_name' => $company_gs['Name'],
                        'id_number' => $company_gs['Code'],
                        'district' => $company_gs['Address'],
                        'city' => $company_gs['City'],
                        'state' => $company_gs['Province'],
                        'email' => $company_gs['ContactEmail']
                        ]
                    );
                    $team = Team::firstOrCreate(
                        [
                            'owner_id' => $user_etax->id,
                            'company_id' => $company_etax->id
                        ],
                        [    'name' => "slug_" . $company_etax->id . "_" . $user_etax->id,
                            'slug' => "(" . $company_etax->id . ") -" .  $user_etax->id
                        ]
                    );
                    

                    $team->save();
                    $user_etax->attachTeam($team);
                    $new_user_gs->attachTeam($team);
                    $CompanyPermission = UserCompanyPermission::firstOrCreate(
                        [
                            'user_id' => $new_user_gs->id,
                            'company_id' => $company_etax->id
                        ],
                        [    'permission_id' => 8
                        ]
                    );
                    $user = IntegracionEmpresa::Create(
                        [  "user_token"=> $user_gs['UserId'],
                            "company_token"=> $user_gs['CurrentAccountId'],
                            "integration_id"=> 1,
                           "user_id"=> $new_user_gs->id,
                            "company_id"=> $company_etax->id,
                            "status"=> 1
                        ]
                    );
                }
                if ($user !== null && Auth::loginUsingId($user->user_id)) {

                        $user->session_token = $token;
                        $user->save();
                        $companyId = $user->company_id;
                    	$user_login = auth()->user();
                        $team = Team::where( 'company_id', $companyId )->first();
                        $user_login->switchTeam( $team );
                        
                        Cache::forget("cache-currentcompany-$user_login->id");
                        GoSocketInvoicesSync::dispatch($user, $companyId)->onConnection(config('etax.queue_connections'))->onQueue('gosocket');

                    return redirect('/');
                } else {
                    return redirect('/login');
                }
            } else {
                return redirect('/login');
            }
        }catch( \Exception $ex ) {
            Log::error("Error en login gosocket $ex");
            return redirect('/login');
        }catch( \Throwable $ex ) {
            Log::error("Error en login gosocket $ex");
            return redirect('/login');
        }
	    
    }


}
    