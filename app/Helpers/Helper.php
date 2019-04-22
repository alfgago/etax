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
        $arr = array('' => 'Select', 'personal' => 'Personal', 'business' => 'Business');
        return $arr;
    }

}

if (!function_exists('default_dropdown_values')) {

    function default_dropdown_values() {
        $arr = array('' => 'Select', 'yes' => 'Yes', 'no' => 'No');
        return $arr;
    }

}

if (!function_exists('initial_setup')) {

    function initial_setup() {
        $arr = array('' => 'Select', 'yes' => 'Yes', 'no' => 'No', 'access-to-video' => 'Access to Introductory Video');
        return $arr;
    }

}

if (!function_exists('get_company_details')) {

    function get_company_details($id) {

        $companies = App\Company::find($id);
        return $companies;
    }

}

if (!function_exists('isExistInTeam')) {

    function isExistInTeam($team_id, $user_id) {

        $result = App\TeamUser::where(array('user_id' => $user_id, 'team_id' => $team_id))->first();

        return ($result) ? TRUE : FALSE;
    }

}

if (!function_exists('isExistInCompanyTeam')) {

    function isExistInCompanyTeam($company_id, $user_id) {
        
        $team_id = App\Team::where([
                    'owner_id' => $user_id, 
                    'company_id' => $company_id
                ])->first()->id;
        
        $result = App\TeamUser::where(array('user_id' => $user_id, 'team_id' => $team_id))->first();

        return ($result) ? TRUE : FALSE;
    }

}

if (!function_exists('userCompanies')) {

    function userCompanies() {

        $current_user = auth()->user()->id;
        $query = App\TeamUser::query()->where(array('team_user.user_id' => $current_user));

        /*if (auth()->user()->roles[0]->name != 'Super Admin') {
            
            $query->where(array('team_user.user_id' => $current_user));
        }*/

        $result = $query->leftJoin('teams', 'teams.id', '=', 'team_user.team_id')
                ->leftJoin('companies', 'companies.id', '=', 'teams.company_id')
                ->orderBy('teams.name')->groupBy('team_user.team_id')
                ->get(['company_id', 'companies.name', 'companies.id_number', 'companies.last_name', 'companies.last_name2']);

        return ($result) ? $result : FALSE;
    }

}

if (!function_exists('currentCompany')) {

    function currentCompany() {
        
        $current_company = session('current_company');
        
        if( !$current_company ){
            $company_id = auth()->user()->companies->first()->id;
            session(['current_company' => $company_id]);
            $current_company = $company_id;
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


if (!function_exists('get_user_company_permissions')) {

    function get_user_company_permissions($company_id, $user_id) {

        $permissions = App\UserCompanyPermission::where(array('company_id' => $company_id, 'user_id' => $user_id))->get();
        return !empty($permissions->toArray()) ? array_column($permissions->toArray(), 'permission_id') : array();
    }

}

if (!function_exists('auth_has_permission')) {

    function auth_has_permission($permission) {

        $user_id = auth()->user()->id;
        $company_id = session('current_company');

        $company = \DB::table('teams')
                    ->where(array(
                        'owner_id' => $user_id, 
                        'company_id' => $company_id)
                    )->first();

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

if (!function_exists('get_current_user_permissions')) {

    function get_current_user_permissions() {

        $user_id = auth()->user()->id;
        $company_id = session('current_company');

        $company = \DB::table('teams')
                ->where([
                    'owner_id' => $user_id, 
                    'company_id' => $company_id
                ])->first();

        /* If user is owner of company,then all permissions are granted */
        if ($company) {
            $permissions = App\CompanyPermission::get();
        } else {
            $permissions = App\UserCompanyPermission::select('user_company_permissions.*', 'company_permissions.permission')
                        ->leftJoin('company_permissions', 'company_permissions.id', '=', 'user_company_permissions.permission_id')
                        ->where(array('company_id' => $company_id, 'user_id' => $user_id))
                        ->get();
        }

        if (!empty($permissions->toArray())) {
            return array_column($permissions->toArray(), 'permission');
        } else {
            return array();
        }
    }

}