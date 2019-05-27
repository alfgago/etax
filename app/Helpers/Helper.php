<?php

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
    
        $current_company = session('current_company');

        if (!$current_company) {
            if (auth()->user()->companies->first()) {
                $company_id = auth()->user()->companies->first()->id;
                session(['current_company' => $company_id]);
                $current_company = $company_id;
            }
        }

        return $current_company;
    }

}

if (!function_exists('currentCompanyModel')) {

    function currentCompanyModel() {

        $current_company = currentCompany();
        $company = App\Company::find($current_company);

        return ( $current_company ) ? $company : false;
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
if (!function_exists('getCurrentUserSubscriptions')) {

    function getCurrentUserSubscriptions() {

        $user_id = auth()->user()->id;

        $subscriptions = App\Subscription::where('user_id', $user_id)->where('status', '1')->get();
        
        return $subscriptions;
        /*
        if ( !empty($plans->toArray()) ) {
            $data = array();
            foreach ($suscriptions as $row) {

                $company_registered_on_plan = \App\Company::where(array('user_id' => $user_id, 'plan_no' => $row->unique_no))->count();

                if ($company_registered_on_plan > 0) {
                    if (($company_registered_on_plan < $row->no_of_companies) || empty($row->no_of_companies)) {//If no. of companies is unlimited
                        $data[] = $row->unique_no;
                    }
                } else {
                    $data[] = $row->unique_no;
                }
            }

            return $data;
        } else {
            return false;
        }*/
    }

}


/* Get current user active subscriptions */
if (!function_exists('getCurrentSubscription')) {

    function getCurrentSubscription() {

        $company = currentCompanyModel();
        $subscription = $company->subscription;
        
        if( ! $subscription ) {
            $user_id = auth()->user()->id;
            $subscription = App\Subscription::where('user_id', $user_id)->where('status', '1')->first();
        }
        
        return $subscription;
        
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
        
        dd($query);

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