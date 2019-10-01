<?php

namespace App\Http\Controllers;

use App\Utils\BridgeGoSocketApi;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\IntegracionEmpresa;
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
                        $this->getInvoices($user);
                        $this->getBills($user);
                        
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
     
    
    public function getInvoices($user) {
        $token = $user->session_token;
        $apiGoSocket = new BridgeGoSocketApi();
	    $facturas = $apiGoSocket->getSentDocuments($token, $user->compose_token);

        foreach ($facturas as $factura) {
            $APIStatus = $apiGoSocket->getXML($token, $factura['DocumentId']);
            $company = currentCompanyModel();
            $xml  = base64_decode($APIStatus);
            $xml = simplexml_load_string( $xml);
            $json = json_encode( $xml );
            $arr = json_decode( $json, TRUE );
            try { 
                $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0 ;
            } catch(\Exception $e) {
                $identificacionReceptor = 0;
            };
                        
            $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
            $consecutivoComprobante = $arr['NumeroConsecutivo'];
                    
            //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
            if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionEmisor ) ) {
                //Registra el XML. Si todo sale bien, lo guarda en S3.
                 Invoice::saveInvoiceXML( $arr, 'GS' );
            }
            $company->save();
        }  
    }


    public function getBills($user) {
        $token = $user->session_token;
        $apiGoSocket = new BridgeGoSocketApi();
        $facturas = $apiGoSocket->getReceivedDocuments($token, $user->compose_token);

        foreach ($facturas as $factura) {
            $APIStatus = $apiGoSocket->getXML($token, $factura['DocumentId']);
            $company = currentCompanyModel();
            $xml  = base64_decode($APIStatus);
            $xml = simplexml_load_string( $xml);
            $json = json_encode( $xml );
            $arr = json_decode( $json, TRUE );
            $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0;
            $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
            $consecutivoComprobante = $arr['NumeroConsecutivo'];
            $clave = $arr['Clave'];
            //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
            if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionReceptor ) ) {
                //Registra el XML. Si todo sale bien, lo guarda en S3
                Bill::saveBillXML( $arr, 'XML' );
            }
            $company->save();
        }  
    }

}
    