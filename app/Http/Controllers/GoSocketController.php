<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use App\Jobs\GoSocketInvoicesSync;
use App\Utils\BridgeGoSocketApi;
use App\Utils\CompanyUtils;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\IntegracionEmpresa;
use App\Integracion;
use App\User;
use App\Company;
use App\Team;
use App\Invoice;
use App\InvoiceItem;
use App\RecurringInvoice;
use App\Bill;
use \Carbon\Carbon;
use App\UserCompanyPermission;
use App\Actividades;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class GoSocketController extends Controller
{
    

    public function index(Request $request){
        $token = $request->token;
        if (!empty($token)) {
            $apiGoSocket = new BridgeGoSocketApi();
            $user_gs = $apiGoSocket->getUser($token);
            $user = IntegracionEmpresa::where("user_token",$user_gs['UserId'])->where("company_token",$user_gs['CurrentAccountId'])->first();
            if (is_null($user)) {
                return view('gosocket.index')->with('token',$token);
            }else{
                return redirect('gosocket/ingresar?token='.$token);
            }
        }
        return view('gosocket.index')->with('token',$token);
    }

    public function configuracion(){
        $actividades = Actividades::all();
        return view('gosocket.configuracion')->with('actividades',$actividades);
    }

    public function updateWizard(Request $request){
        try{
           $company = currentCompanyModel();
            $invoice = Invoice::firstOrNew(
                [
                    'company_id' => $company->id,
                    'is_totales' => true,
                    'year' => 2018
                ]
            );

            $team = Team::where('company_id', $company->id)->first();
            /* Only owner of company or user invited as admin for that company can edit company details */
            if ( !auth()->user()->isOwnerOfTeam($team) && !in_array(8, auth()->user()->permisos())) 
            {
                abort(403);
            }

            $company->commercial_activities = $request->commercial_activities;
            $company->default_currency = 'CRC';
            $company->first_prorrata = $request->first_prorrata;
            $company->first_prorrata_type = $request->first_prorrata_type;
            $company->use_invoicing = false;
            
            if( $company->first_prorrata_type == 1 ) {
                $company->operative_prorrata = $request->first_prorrata;
                $company->operative_ratio1 = $request->operative_ratio1;
                $company->operative_ratio2 = $request->operative_ratio2;
                $company->operative_ratio3 = $request->operative_ratio3;
                $company->operative_ratio4 = $request->operative_ratio4;
            }
            
                
            $company->wizard_finished = true;
            $company->save();


            clearLastTaxesCache($company->id, 2018);
            
            if ($company->first_prorrata_type == 2) {
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
                return redirect('/editar-totales-2018')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');

            }

            if ($company->first_prorrata_type == 3) {
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
                return redirect('/')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, debe empezar ingresando sus facturas del periodo anterior.');
            }
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $company,
                    [
                        'company_id' => $company->id
                    ],
                    "La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, solamente debe agregar sus facturas del periodo hasta el momento."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');

            return redirect('/')->withMessage('La configuración inicial ha sido realizada con éxito! Para empezar a calcular su IVA, solamente debe agregar sus facturas del periodo hasta el momento.');

        }catch( \Exception $ex ) {
            Log::error("Error en wizard gosocket ".$ex);
            return redirect()->back()->withError('Error en guardar configuracion');
        }catch( \Throwable $ex ) {
            Log::error("Error en login gosocket ".$ex);
            return redirect()->back()->withError('Error en guardar configuracion');
        }
    }

    public function gosocketValidate(Request $request) {
        $company = currentCompany();
        try{

        	$token = $request->token;
        	Log::info("Iniciando validacion de token gosocket: " . $token);
        	if (!empty($token)) {
                $apiGoSocket = new BridgeGoSocketApi();
                $user_gs = $apiGoSocket->getUser($token);
                $user = IntegracionEmpresa::where("user_token",$user_gs['UserId'])->where("company_token",$user_gs['CurrentAccountId'])->first();

                if (is_null($user)) {
                    Log::info("Creando usuario");
                    $company_gs = $apiGoSocket->getAccount($token, $user_gs['CurrentAccountId']);
                    $companyUtils = new CompanyUtils();
                    $datosCedula = $companyUtils->datosCedula($company_gs['Code']);
                    $company_etax = Company::where('id_number',$company_gs['Code'])->first();
                    if($company_etax){
                        return redirect('gosocket/login?token='.$token);
                    }
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
                        'type' => $datosCedula['type'],
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
                        'email' => $user_etax['email']
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
                    Log::info("El usuario existe y se inicio sesion enviando job del sync gosocket");
                    GoSocketInvoicesSync::dispatch($user, $companyId)->onConnection(config('etax.queue_connections'))->onQueue('gosocket');

                    return redirect('/');
                } else {
                    Log::info("El usuario Gosocket no se puedo loguear");
                    return redirect('/login');
                }
            } else {
                Log::info("El usuario Gosocket no se puedo loguear no tiene token");
                return redirect('/login');
            }
        }catch( \Exception $ex ) {
            Log::error("Error en login gosocket ".$ex);
            return redirect('/login');
        }catch( \Throwable $ex ) {
            Log::error("Error en login gosocket ".$ex);
            return redirect('/login');
        }
	    
    }

    public function login(Request $request){
        $token = $request->token;
        if (!empty($token)) {
            return view('gosocket.login')->with('token',$token);
        }
        return view('gosocket.index')->with('token',$token);
    }

    public function validarCuenta(Request $request){
        try{
            if( Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                $user = User::where('email',$request->email)->first();
                $token = $request->token;
                $apiGoSocket = new BridgeGoSocketApi();
                $user_gs = $apiGoSocket->getUser($token);
                $company_gs = $apiGoSocket->getAccount($token, $user_gs['CurrentAccountId']);
                $permiso = false;
                foreach($user->companies as $company){
                    if($company->id_number ==  $company_gs['Code']){
                        $permiso = true;
                    }
                }
                if($permiso){
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
                    if ($user !== null && Auth::loginUsingId($user->user_id)) {

                        $user->session_token = $token;
                        $user->save();
                        $companyId = $user->company_id;
                        $user_login = auth()->user();
                        $team = Team::where( 'company_id', $companyId )->first();
                        $user_login->switchTeam( $team );

                        Cache::forget("cache-currentcompany-$user_login->id");
                        Log::info("El usuario existe y se inicio sesion enviando job del sync gosocket");
                        GoSocketInvoicesSync::dispatch($user, $companyId)->onConnection(config('etax.queue_connections'))->onQueue('gosocket');

                        return redirect('/');
                    } else {
                        Log::info("El usuario Gosocket no se puedo loguear");
                        return redirect('/login');
                    }
                } else {
                    return redirect()->back()->withError('Este usuario no tiene permiso para esta empresa');
                }
            } else {
                return redirect()->back()->withError('Usuario y contraseña invalido');
            }
        }catch( \Exception $ex ) {
            Log::error("Error en login gosocket ".$ex);
            return redirect('/login');
        }catch( \Throwable $ex ) {
            Log::error("Error en login gosocket ".$ex);
            return redirect('/login');
        }
    }


}
    