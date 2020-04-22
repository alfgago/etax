<?php

use Carbon\Carbon;
use App\Notification;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

if (!function_exists('prd')) {

    function prd($arr = 'No Data') {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        die;
    }

}

if (!function_exists('pr')) {

    function pr($arr = 'No Data') {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }

}

if (!function_exists('db_date_time')) {

    function db_date_time($date_time = false) {
        return $date_time ? date('Y-m-d H:i:s', strtotime($date_time)) : date('Y-m-d H:i:s');
    }

}

if (!function_exists('calculateAge')) {

    function calculateAge($dob) {

        $dob = date('Y-m-d', strtotime($dob));

        $from = new DateTime($dob);
        $to = new DateTime('today');
        return $from->diff($to)->y;
    }

}

if (!function_exists('get_plan_type')) {

    function get_plan_type() {
        $arr = array('' => 'Select', 'personal' => 'Personal', 'business' => 'Business', 'accountants' => 'Accountants');
        return $arr;
    }

}

if (!function_exists('default_dropdown_values')) {

    function default_dropdown_values() {
        $arr = array('' => 'Select', 'yes' => 'Yes', 'no' => 'No');
        return $arr;
    }

}

/* Get Details of single company */
if (!function_exists('get_company_details')) {

    function get_company_details($id) {

        $companies = App\Company::find($id);
        return $companies;
    }

}

/* Get Details of single user */
if (!function_exists('isEmailRegistered')) {

    function isEmailRegistered($email) {

        $result = App\User::where('email', $email)->first();
        return $result;
    }

}

/* Check if user exist in company team or not */
if (!function_exists('isExistInTeam')) {

    function isExistInTeam($team, $user) {

        $result = App\TeamUser::where(array('user_id' => $user, 'team_id' => $team))->first();

        return ($result) ? TRUE : FALSE;
    }

}

if (!function_exists('isExistInCompanyTeam')) {

    function isExistInCompanyTeam($company_id, $user_id) {

        $team = App\Team::where([
                    'company_id' => $company_id
                ])->first();

        if ($team) {
            $team_id = $team->id;
            $result = App\TeamUser::where(array('user_id' => $user_id, 'team_id' => $team_id))->first();
            return ($result) ? TRUE : FALSE;
        } else {
            return FALSE;
        }
    }

}

/* Get all companies of users */
if (!function_exists('userCompanies')) {

    function userCompanies() {

        $current_user = auth()->user()->id;
        $query = App\TeamUser::query()->where(array('team_user.user_id' => $current_user));

        /* if (auth()->user()->roles[0]->name != 'Super Admin') {

          $query->where(array('team_user.user_id' => $current_user));
          } */

        $result = $query->leftJoin('teams', 'teams.id', '=', 'team_user.team_id')
                ->leftJoin('companies', 'companies.id', '=', 'teams.company_id')
                ->whereRaw('companies.deleted_at IS NULL')
                ->orderBy('teams.name')->groupBy('team_user.team_id')
                ->get(['company_id', 'companies.name', 'companies.id_number', 'companies.last_name', 'companies.last_name2', 'companies.deleted_at']);

        return ($result) ? $result : FALSE;
    }

}

/* Get currently selected company */
if (!function_exists('currentCompany')) {

    function currentCompany() {
        try {
        $user = auth()->user();
        if ( !$user->companies->count() ) {
            return auth()->user()->addCompany();
        }
        
        try{ 
            return $user->currentTeam->company->id;
        }catch( \Throwable $e ){
            return $user->teams[0]->company->id;
        }
        }catch(\Throwable $e){}
    }

}

if (!function_exists('currentCompanyModel')) {

    function currentCompanyModel($cache = true) {
        $user = auth()->user();

        if ($cache == false) {
            if (!$user->teams->count()) {
                auth()->user()->addCompany();
            }

            $company = $user->currentTeam->company;
            if (!$company) {
                $company = auth()->user()->teams->first()->company;
            }
            return $company;
        }

        $cacheKey = "cache-currentcompany-$user->id";
        if ( !Illuminate\Support\Facades\Cache::has($cacheKey) ) {
            if ( !$user->teams->count() ) {
                auth()->user()->addCompany();
            }
            
            $company = $user->currentTeam->company;
            if ( !$company ) {
                $company = auth()->user()->teams->first()->company;
            }
            Illuminate\Support\Facades\Cache::put($cacheKey, $company, now()->addMinutes(15));
        }
        return Illuminate\Support\Facades\Cache::get($cacheKey);
    }

}


/* Get current user permission company wise */
if (!function_exists('allowTo')) {

    function allowTo( $permiso ) {
        
        $companyId = currentCompany();
        $user = auth()->user();
        
        $cacheKey = "cache-allow-$companyId-$user->id-$permiso";
        if ( !Illuminate\Support\Facades\Cache::has($cacheKey) ) {
            
            $userId = $user->id;
        
            $team = App\Team::where('company_id', $companyId)->first();
            $hasPermisoAdmin = App\UserCompanyPermission::where(  [    
                'company_id' => $companyId,
                'user_id' => $userId,
                'permission_id' => 1 
            ])->count();
            if( $hasPermisoAdmin || $user->isOwnerOfTeam($team) ) {
                Illuminate\Support\Facades\Cache::put($cacheKey, 1, now()->addDays(120));
                return 1;
            }
            
            $permisoId = 1;
            if( $permiso == 'admin') {
                $permisoId = 1;
            }else if( $permiso == 'invoicing') {
                $permisoId = 2;
            }else if( $permiso == 'billing') {
                $permisoId = 3;
            }else if( $permiso == 'validation') {
                $permisoId = 4;
            }else if( $permiso == 'books') {
                $permisoId = 5;
            }else if( $permiso == 'reports') {
                $permisoId = 6;
            }else if( $permiso == 'catalogue') {
                $permisoId = 7;
            }
            
            $hasPermiso = App\UserCompanyPermission::where(  [    
                'company_id' => $companyId,
                'user_id' => $userId,
                'permission_id' => $permisoId 
            ])->count();
            
            $allowed = $hasPermiso ;
            
            Illuminate\Support\Facades\Cache::put($cacheKey, $allowed, now()->addDays(120));
        }
        
        return Illuminate\Support\Facades\Cache::get($cacheKey);;
    }

}

/* Get company wise permissions of user */
if (!function_exists('get_user_company_permissions')) {

    function get_user_company_permissions($company_id, $user_id) {

        $permissions = App\UserCompanyPermission::where(array('company_id' => $company_id, 'user_id' => $user_id))->get();
        return !empty($permissions->toArray()) ? array_column($permissions->toArray(), 'permission_id') : array();
    }

}

/* Check if logged-in user has given permission or not */
if (!function_exists('auth_has_permission')) {

    function auth_has_permission($permission) {

        $user_id = auth()->user()->id;
        $company_id = session('current_company');

        $company = App\Team::where(array('owner_id' => $user_id, 'company_id' => $company_id))->first();

        /* If user is owner of company,then all permissions are granted */
        if ($company) {
            return true;
        } else {
            $permissions = App\UserCompanyPermission::select('user_company_permissions.*', 'company_permissions.permission')->leftJoin('company_permissions', 'company_permissions.id', '=', 'user_company_permissions.permission_id')->where(array('company_id' => $company_id, 'user_id' => $user_id))->get();

            if (!empty($permissions->toArray())) {

                if (in_array($permission, array_column($permissions->toArray(), 'permission'))) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

}

/* Get current user permission company wise */
if (!function_exists('get_current_user_permissions')) {

    function get_current_user_permissions() {

        $user_id = auth()->user()->id;
        $company_id = session('current_company');

        $company = App\Team::where(array('owner_id' => $user_id, 'company_id' => $company_id))->first();

        /* If user is owner of company,then all permissions are granted */
        if ($company) {
            $permissions = App\CompanyPermission::get();
        } else {
            $permissions = App\UserCompanyPermission::select('user_company_permissions.*', 'company_permissions.permission')->leftJoin('company_permissions', 'company_permissions.id', '=', 'user_company_permissions.permission_id')->where(array('company_id' => $company_id, 'user_id' => $user_id))->get();
        }

        if (!empty($permissions->toArray())) {
            return array_column($permissions->toArray(), 'permission');
        } else {
            return array();
        }
    }

}

/* Get current user active subscriptions */
/*if (!function_exists('getCurrentUserSubscriptions')) {

    function getCurrentUserSubscriptions() {

        $user_id = auth()->user()->id;

        $subscriptions = App\Sales::where('user_id', $user_id)->where('status', '1')->get();
        
        return $subscriptions;
        
    }

}*/

/* Get current user active subscriptions */
if (!function_exists('getCurrentSubscription')) {

    function getCurrentSubscription($companyId = false) {


        $company = $companyId != false ? \App\Company::find($companyId) : currentCompanyModel();

        $sale = $company->subscription;
        
        if( ! isset($sale) ) {
            $sale = \App\Sales::with('plan')->where('company_id', $company->id)
                ->where('is_subscription', true)
                ->first();
        }
        
        if( ! isset($sale) ) {
            $owner_id = $company->user_id;
            $plan_tier = "Pro ($owner_id)";
            $subscriptionPlan = \App\SubscriptionPlan::where('plan_tier',$plan_tier)->first();
            if($subscriptionPlan){
                $sale = \App\Sales::where('user_id', $owner_id)
                ->where('is_subscription', true)
                ->first();

            }
        }
        

        if( ! isset($sale) ) {
            $sale = \App\Sales::with('plan')->where('id', $company->subscription_id)
                ->where('is_subscription', true)
                ->first();
        }
        
        if( ! isset($sale) ) {
            $user_id = auth()->user()->id;
            $sale = \App\Sales::where('user_id', $user_id)
                ->where('company_id', $company->id)
                ->where('is_subscription', true)
                 ->first();
        }
        return $sale;
        
    }

}

/* Check id plan limit exceeds or not */
if (!function_exists('is_plan_limit_exceed')) {

    function is_plan_limit_exceed( $plan_id, $user_id) {

        $user_subscription = \App\Subscription::where(array('user_id' => $user_id, 'plan_id' => $plan_id))->first();
        $company_registered_on_plan = \App\Company::where(array( 'user_id' => $user_id, 'subscription_id' => $user_subscription->id ))->count();
        
        $plan = $user_subscription->plan;

        $company_limit_allowed = $plan->num_companies;

        if (empty($company_limit_allowed)) {
            return false;
        } else {
            if ($company_registered_on_plan >= $company_limit_allowed) {
                return true;
            } else {
                return false;
            }
        }
    }

}

/* Get all subscribed plan of a user */
if (!function_exists('user_subscribed_plans')) {

    function user_subscribed_plans($user_id) {

        $query = \App\UserSubscription::query();
        
        $query->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id');
        $query->where(array('user_id' => $user_id));

        $user_subscriptions = $query->select('user_subscriptions_history.*', 'subscription_plans.plan_type', 'subscription_plans.plan_name', 'subscription_plans.no_of_companies', 'subscription_plans.no_of_invited_user', 'user_subscriptions_history.user_id')->orderBy('user_subscriptions_history.created_at', 'DESC')->get();

        if (!empty($user_subscriptions->toArray())) {
            return $user_subscriptions->toArray();
        } else {
            return array();
        }
        
    }

}

/* Get all companies registered to plan */
if (!function_exists('plan_registered_companies')) {

    function plan_registered_companies($plan_no) {

        $company_registered_on_plan = \App\Company::where(array('plan_no' => $plan_no))->count();

        return $company_registered_on_plan;
    }

}

/* Get subscription plan status */
if (!function_exists('get_plan_status')) {

    function get_plan_status($plan_no) {

        $user_subscription = \App\UserSubscription::where(array('unique_no' => $plan_no))->first();

        if (strtotime(date('Y-m-d')) > strtotime($user_subscription->expiry_date)) {
            return '<label class="badge badge-danger">Expired</label>';
        } else {
            if ($user_subscription->status == '1') {
                return '<label class="badge badge-success">Active</label>';
            } else {
                return '<label class="badge badge-danger">Cancelled</label>';
            }
        }

        /*
          $company_registered_on_plan = \App\Company::where(array('plan_no' => $plan_no))->count();
          $plan = \App\Plan::find($user_subscription->plan_id);

          $company_limit_allowed = $plan->no_of_companies;

          if ($company_registered_on_plan >= $company_limit_allowed) {
          return '<label class="badge badge-warning">Limit Reached</label>';
          } else {
          if (strtotime(date('Y-m-d')) > strtotime($user_subscription->expiry_date)) {
          return '<label class="badge badge-danger">Expired</label>';
          } else {
          if ($user_subscription->status == '1') {
          return '<label class="badge badge-success">Active</label>';
          } else {
          return '<label class="badge badge-danger">Cancelled</label>';
          }
          }
          }
         */
    }

}

/* Check Plan Invitations */
if (!function_exists('plan_invitations_exceeds')) {

    function plan_invitations_exceeds($team, $type) {

        $company = $team->company->id;

        $query = \App\Subscription::query();

        $query->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id');
        $query->where('unique_no', $company->plan_no);

        $plan_details = $query->select('subscription_plans.*')->first();

        if ($type == 'admin') {

            $admin_count = App\TeamInvitation::where(array('user_id' => auth()->user()->id, 'team_id' => $team->id, 'role' => 'admin'))->count();
            $admin_count += \App\PlansInvitation::where(array('company_id' => $company->id, 'is_admin' => '1'))->count();

            if ($plan_details) {

                if (is_null($plan_details->no_of_admin_user)) {//If case of unlimited value
                    return false;
                } else {
                    if ($admin_count >= $plan_details->no_of_admin_user) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return true;
            }
        } else {
            $readonly_count = App\TeamInvitation::where(array('user_id' => auth()->user()->id, 'team_id' => $team->id, 'role' => 'readonly'))->count();
            $readonly_count += \App\PlansInvitation::where(array('company_id' => $company->id, 'is_read_only' => '1'))->count();

            if ($plan_details) {

                if (is_null($plan_details->no_of_invited_user)) {//If case of unlimited value
                    return false;
                } else {
                    if ($readonly_count >= $plan_details->no_of_invited_user) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return true;
            }
        }
    }

}

/* Get no. of invited users */
if (!function_exists('get_invited_users')) {

    function get_invited_users($plan_no) {

        $companies = \App\Company::where('plan_no', $plan_no)->get(['id']);

        $teams = App\Team::whereIn('company_id', array_column($companies->toArray(), 'id'))->get(['id']);

        $team_ids = !empty($teams->toArray()) ? array_column($teams->toArray(), 'id') : array();

        $admin_count = App\TeamInvitation::whereIn('team_id', $team_ids)->where(array('user_id' => auth()->user()->id, 'role' => 'admin'))->count();
        $admin_count += \App\PlansInvitation::where(array('plan_no' => $plan_no, 'is_admin' => '1'))->count();

        $readonly_count = App\TeamInvitation::whereIn('team_id', $team_ids)->where(array('user_id' => auth()->user()->id, 'role' => 'readonly'))->count();
        $readonly_count += \App\PlansInvitation::where(array('plan_no' => $plan_no, 'is_read_only' => '1'))->count();

        $data['admin_count'] = $admin_count;
        $data['readonly_count'] = $readonly_count;

        return $data;
    }

}

/* Get plan invitation */
if (!function_exists('get_plan_invitation')) {

    function get_plan_invitation($company_id, $user_id) {

        $invitation = \App\PlansInvitation::where(array('company_id' => $company_id, 'user_id' => $user_id))->first();
        return $invitation ? $invitation : false;
    }

}

/* Check token expiration */
if (!function_exists('token_expired')) {

    function token_expired($token) {

        if (!empty($token)) {

            $token_generated_date = explode('|', base64_decode($token))[0];
            $currenttime = strtotime(date('Y-m-d H:i'));
            $difference = abs($currenttime - strtotime($token_generated_date)) / 3600;

            if ($difference > 24) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

/* Check token expiration */
if (!function_exists('get_microtime')) {
    function getMicrotime(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }
}

/* Get Document Key */
if (!function_exists('getNextRef')) {
    function getNextRef($docType, $company = false) {

        if(!$company) {
            $company = currentCompanyModel(false);
        }
        if ($docType == '01') {
            $ref = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $ref = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $ref = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '03') {
            $ref = $company->last_note_ref_number + 1;
        }
        if ($docType == '04') {
            $ref = $company->last_ticket_ref_number + 1;
        }
        return $ref;
    }
}

/* Get Document Key */
if (!function_exists('getDocumentKey')) {
    function getDocumentKey($docType, $company = false, $ref = false) {

        if(!$company) {
            $company = currentCompanyModel(false);
        }
        if ($ref) {
            return $key = '506'.shortDate().getIdFormat($company->id_number).getDocReference($docType, $company, $ref). '1'.getHashFromRef($ref);
        }
        if ($docType == '01') {
            $ref = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $ref = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $ref = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '03') {
            $ref = $company->last_note_ref_number + 1;
        }
        if ($docType == '04') {
            $ref = $company->last_ticket_ref_number + 1;
        }
        $key = '506'.shortDate().getIdFormat($company->id_number).getDocReference($docType, $company).'1'.getHashFromRef($ref);

        return $key;
    }
}

/* Get Document Reference */
if (!function_exists('getDocReference')) {
    function getDocReference($docType, $company = false, $ref = false) {

        if($ref) {
            return $consecutive = "001"."00001".$docType.substr("0000000000".$ref, -10);
        }

        if(!$company){
            $company = currentCompanyModel(false);
        }
        if ($docType == '01') {
            $lastSale = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $lastSale = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $lastSale = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '03') {
            $lastSale = $company->last_note_ref_number + 1;
        }
        if ($docType == '04') {
            $lastSale = $company->last_ticket_ref_number + 1;
        }
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }
}


if (!function_exists('shortDate')) {
    function shortDate() {
        date_default_timezone_set("America/Costa_Rica");
        $date = date_create();
        return date_format($date,'dmy');
    }
}

if (!function_exists('getIdFormat')) {
    function getIdFormat($id){
        $clean="000000".trim(str_replace("-","",$id));
        return substr($clean, -12);
    }
}

if (!function_exists('getHashFromRef')) {
    function getHashFromRef($ref) {
        $salesId = $ref;
        if ($salesId === null) {
            return '';
        }
        return substr('0000'.hexdec(substr(sha1($ref.'Factel'.$salesId.'Facthor'), 0, 15)) % 999999999, -8);
    }
}    

/* Get Invoice Reference */
if (!function_exists('getInvoiceReference')) {
    function getInvoiceReference($ref)
    {
        $lastSale = substr($ref, -10);
        $lastSale = (int)$lastSale;
        return $lastSale;
    }
}

/* Get get_rates */
if (!function_exists('get_rates')) {
    function get_rates(){
        
        $cacheKey = "usd_rate";
        $lastRateKey = "last_usd_rate";
        try {
            if ( !Cache::has($cacheKey) ) {

                $today = new Carbon();
                $client = new \GuzzleHttp\Client();
                $response = $client->get( config('etax.exchange_url'),
                    [
                        'query' => [
                            'Indicador' => '318',
                            'FechaInicio' => $today::now()->format('d/m/Y'),
                            'FechaFinal' => $today::now()->format('d/m/Y'),
                            'Nombre' => config('etax.namebccr'),
                            'SubNiveles' => 'N',
                            'CorreoElectronico' => config('etax.emailbccr'),
                            'Token' => config('etax.tokenbccr'),
                        ],
                        'timeout' => 15,
                        'connect_timeout' => 15,
                        'read_timeout' => 15,
                    ]
                );
                
                $body = $response->getBody()->getContents();
                $xml = new \SimpleXMLElement($body);
                $xml->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                $tables = $xml->xpath('//INGC011_CAT_INDICADORECONOMIC[@d:id="INGC011_CAT_INDICADORECONOMIC1"]');
                $valor =  json_decode($tables[0]->NUM_VALOR);

                Cache::put($cacheKey, $valor, now()->addHours(2));
                Cache::put($lastRateKey, $valor, now()->addDays(3));
            }

            $value = Cache::get($cacheKey);
            return $value;

        } catch( \Exception $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: ');
            $value = Cache::get($lastRateKey);
            return $value;
        } catch (RequestException $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: '.
                $e->getResponse()->getReasonPhrase());
            $value = Cache::get($lastRateKey);
            return $value;
        }
        
        /*try {
            $value = Cache::remember('usd_rate', '60000', function () {
                $today = new Carbon();
                $client = new \GuzzleHttp\Client();
                $response = $client->get(config('etax.exchange_url'),
                    ['query' => [
                        'Indicador' => '318',
                        'FechaInicio' => $today::now()->format('d/m/Y'),
                        'FechaFinal' => $today::now()->format('d/m/Y'),
                        'Nombre' => config('etax.namebccr'),
                        'SubNiveles' => 'N',
                        'CorreoElectronico' => config('etax.emailbccr'),
                        'Token' => config('etax.tokenbccr')
                    ]
                    ]
                );
                $body = $response->getBody()->getContents();
                $xml = new \SimpleXMLElement($body);
                $xml->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                $tables = $xml->xpath('//INGC011_CAT_INDICADORECONOMIC[@d:id="INGC011_CAT_INDICADORECONOMIC1"]');
                return json_decode($tables[0]->NUM_VALOR);
            });

            return $value;

        } catch( \Exception $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: ');
        } catch (RequestException $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: '.
                $e->getResponse()->getReasonPhrase());
            return null;
        }*/

    }
}

/* Get notificatios of user */
if (!function_exists('hasAvailableInvoices')) {

    function hasAvailableInvoices($year, $month, $amountToCheck = 1, $company = null) {
        if( !isset($company) ){
            $company = currentCompanyModel();
        }
        $availableInvoices = $company->getAvailableInvoices( $year, $month , $company);
        Log::info("Facturas disponibles". $availableInvoices);
        $facturasDisponibles = $availableInvoices->monthly_quota + $company->additional_invoices - $availableInvoices->current_month_sent;
        if( $facturasDisponibles < $amountToCheck ){
            return false;
        }
        return true;
    }

}


/* Get notificatios of user */
if (!function_exists('notifications')) {

    function notifications() {
        $notify = new Notification();
        return $notify->notificaciones();
    }

}


/* Get count notifications of user */
if (!function_exists('notification_count')) {

     function notification_count(){ 
        $notify = new Notification();
        return $notify->cantidad();
    }

}

/* Get count notifications of user */
if (!function_exists('replaceAccents')) {

     function replaceAccents($string){ 
        $string = preg_replace('/\s+/', '', $string);
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                                    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                                    'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                                    'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                                    'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        return strtr( $string, $unwanted_array );
    }

}

if (!function_exists('userHasCompany')) {
    function userHasCompany($companyId) {
        $user = auth()->user();
        $cacheKey = "cache-has-company-$companyId-$user->id";
        if (!Illuminate\Support\Facades\Cache::has($cacheKey)) {
            try{
                $company = App\Company::where('id_number', $companyId)->with('team')->first();
                $companyKey = "cache-api-company-$companyId-$user->id";
                $hasPermisoAdmin = App\UserCompanyPermission::where(  [
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'permission_id' => 1
                ])->count();
                $hasPermission = false;
                if( $hasPermisoAdmin || $user->isOwnerOfTeam($company->team) ) {
                    $hasPermission = true;
                }
            }catch(Exception $e){
                $hasPermission = false;
            }

            Illuminate\Support\Facades\Cache::put($companyKey, $company, now()->addMinutes(15));
            Illuminate\Support\Facades\Cache::put($cacheKey, $hasPermission, now()->addMinutes(15));
        }

        return Illuminate\Support\Facades\Cache::get($cacheKey);
    }
}
