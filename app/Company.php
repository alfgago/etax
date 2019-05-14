<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model {

    use SoftDeletes;

    protected $table = 'companies';
    protected $guarded = [];

    //Relación con facturas emitidas
    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    //Relación con facturas recibidas
    public function bills() {
        return $this->hasMany(Bill::class);
    }

    //Relación con clientes
    public function clients() {
        return $this->hasMany(Client::class);
    }

    //Relación con proveedores
    public function providers() {
        return $this->hasMany(Provider::class);
    }
    
    //Relación con Certificado
    public function atv() {
        return $this->hasOne(AtvCertificate::class);
    }
    
    //Revisa si el certificado existe
    public function certificateExists() {
        if( $this->atv ){
            return $this->atv->certificateExists();
        }else {
            return false;
        }
    }

    //Relación con Usuario
    public function owner() {
        return $this->belongsTo('App\User');
    }

    /* Changes the current selected company to chosen plan. As long as the plan has available company slots. */

    public static function setPlan($plan_no) {
        $query = \App\UserSubscription::query();

        $query->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id');
        $query->where(array('user_subscriptions_history.status' => '1', 'unique_no' => $plan_no));
        $query->whereRaw('DATE(start_date) <="' . date('Y-m-d') . '"')->whereRaw('DATE(expiry_date) >="' . date('Y-m-d') . '"');

        $plan = $query->select('user_subscriptions_history.*', 'subscription_plans.plan_type', 'subscription_plans.plan_name', 'subscription_plans.no_of_companies', 'subscription_plans.no_of_invited_user', 'user_subscriptions_history.user_id')->first();

        if ($plan) {

            $company_registered_on_plan = Company::where('plan_no', $plan_no)->count();

            if (($company_registered_on_plan < $plan->no_of_companies) || empty($plan->no_of_companies)) {//If no. of companies is unlimited
                Company::where('id', currentCompany())->update(array('plan_no' => $plan_no));
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /* Check if current company's plan has been paid and active */

    public static function isPlanActive() {
        $current_company_plan = Company::find(currentCompany())->plan_no;

        $user_subscription = \App\UserSubscription::where(array('unique_no' => $current_company_plan))->first();

        if (strtotime(date('Y-m-d')) > strtotime($user_subscription->expiry_date)) {
            return false;
        } else {
            if ($user_subscription->status == '1') {
                return true;
            } else {
                return false;
            }
        }
    }

    /* Returns count of total available bills Current plan bills + bought add-on bills */

    public function checkCountAvailableBills() {

        try{
            
            $query = Company::query();
    
            $query->leftJoin('user_subscriptions_history', 'user_subscriptions_history.unique_no', '=', 'companies.plan_no');
            $query->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id');
            $query->where('companies.id', $this->id);
    
            $plan_details = $query->select('subscription_plans.no_of_bills')->first();
    
            $bill_count = \App\Bill::where(array('company_id' => $this->id, 'month' => date('m'), 'year' => date('Y')))->count();
    
            if ($plan_details) {
    
                if ( is_null($plan_details->no_of_bills) || $plan_details->no_of_bill == -1 ) { //If bills are unlimited
                    return 5000;
                } else {
                    $available_bills = $plan_details->no_of_bills - $bill_count;
                    return ($available_bills > 0) ? $available_bills : 0;
                }
            } else {
                return 0;
            }
        }catch( \Exception $ex ){
            return 5000;
        }
    }

    /* Returns count of total available invoices. Current plan invoices + bought add-on invoices */

    public function checkCountAvailableInvoices() {
        
        try{
            
            $query = Company::query();
    
            $query->leftJoin('user_subscriptions_history', 'user_subscriptions_history.unique_no', '=', 'companies.plan_no');
            $query->leftJoin('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions_history.plan_id');
            $query->where('companies.id', $this->id);
    
            $plan_details = $query->select('subscription_plans.no_of_invoices')->first();
    
            $invoice_count = \App\Invoice::where(array('company_id' => $this->id, 'month' => date('m'), 'year' => date('Y')))->count();
    
            if ($plan_details) {
    
                if (is_null($plan_details->no_of_invoices)) {//If invoices are unlimited
                    return 5000;
                } else {
                    $available_invoices = $plan_details->no_of_invoices - $invoice_count;
                    return ($available_invoices > 0) ? $available_invoices : 0;
                }
            } else {
                return 0;
            }
        
        }catch( \Exception $ex ){
            return 5000;
        }
    }

    /* Email to deactivate the current company so user can add another one on same plan without deleting the data. */

    public static function deactivateCompany() {

        $company_id = currentCompany();
        $company = Company::find($company_id);

        if ($company) {
            $user = User::where('id', $company->user_id)->first();

            if ($user) {
                $company->deactivation_token = base64_encode(date('Y-m-d H:i') . '|' . $company_id);
                $company->save();
                \Mail::to($user->email)->send(new Mail\CompanyDeactivationMail(['team' => $company->name, 'token' => $company->deactivation_token]));
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
