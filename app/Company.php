<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Subscription;
use App\SubscriptionPlan;
use Carbon\Carbon;

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
    
    //Relación con el equipo
    public function team() {
        return $this->hasOne(Team::class);
    }

    //Relación con Usuario
    public function owner() {
        return $this->belongsTo( User::class );
    }
    
    //Relación con el plan
    public function subscription() {
        return $this->hasOne(Sales::class);
    }

    /* Changes the current selected company to chosen plan. As long as the plan has available company slots. */
    public function setPlan( $subscription_id ) {
        $plan = Sale::findOrFail($subscription_id);
        if ($plan) {
            
            if( $plan )
            
            $this->subscription_id = $subscription_id;
            $this->save();
            return true;
            
        } else {
            return false;
        }
    }

    /* Check if current company's plan has been paid and active */
    public function isPlanActive() {
        $subscription = $this->subscription;
        
        if (!$subscription) {
            return false;
        }
        
        if ( Carbon::now() > $subscription->next_payment_date ) {
            
            /*if( checkPayment() ) {
                return true;
            } else {
                if ( Carbon::now() > $subscription->next_payment_date->addDays(5) ) {
                    return true;
                }
            }*/
            
            return false;
            
        }
        
        return true;
    }

    /* Returns count of total available bills Current plan bills + bought add-on bills */
    public function getCountAvailableBills() {

        try{
            
            $count =  getCurrentSubscription()->product->plan->num_bills;
            
            if( !$count ) {
                return -1;
            }
            
            return $count;
            
        }catch( \Exception $ex ){
            return 5000;
        }
    }

    /* Returns count of total available invoices. Current plan invoices + bought add-on invoices */
    public function getAvailableInvoices( $year, $month ) {

        try{
            if( $month && $year ) {
                $today = Carbon::parse(now('America/Costa_Rica'));
                $month = $today->month;
                $year = $today->year;
            }
            
            $available_invoices = AvailableInvoices::where('company_id', $this->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->first();
                                
            // Si no encontró nada, tiene que crearla.
            if( ! $available_invoices ) {
                $subscriptionPlan = getCurrentSubscription()->product->plan;

                $available_invoices = AvailableInvoices::create(
                    [
                        'company_id' => $this->id,
                        'monthly_quota' => $subscriptionPlan->num_invoices,
                        'month' => $month,
                        'year' => $year,
                        'current_month_sent' => 0
                    ]
                );
            }
            
            return $available_invoices;
        
        }catch( \Exception $ex ){
            Log::error('Error en getAvailableInvoices: ' . $ex->getMessage() );
            return null;
        }
    }
    
    public function addSentInvoice($year, $month) {
        try {
            if($month && $year) {
                $today = Carbon::parse(now('America/Costa_Rica'));
                $month = $today->month;
                $year = $today->year;
            }
        
            $available_invoices = AvailableInvoices::where('company_id', $this->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->first();

            $available_invoices->current_month_sent = $available_invoices->current_month_sent + 1;
            $available_invoices->save();
            return $available_invoices;

        }catch (\Throwable $ex){
             Log::error('Error en addSentInvoice: ' . $ex->getMessage() );
        }
    }
    
    public function setFirstAvailableInvoices( $year, $month, $count ) {

        try{
            $available_invoices = AvailableInvoices::where('company_id', $this->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->first();
                                
            // Si no encontró nada, tiene que crearla.
            if( ! $available_invoices ) {
                $subscriptionPlan = getCurrentSubscription()->product->plan;
                $available_invoices = AvailableInvoices::create(
                    [
                        'company_id' => $this->id,
                        'monthly_quota' => $subscriptionPlan->num_invoices,
                        'month' => $month,
                        'year' => $year,
                        'current_month_sent' => $count
                    ]
                );
                return true;
            }
            
            return false;
        
        }catch( \Exception $ex ){
            Log::error('Error en setAvailableInvoices: ' . $ex->getMessage() );
            return false;
        }
    }

    //Deprecado. Ya no se debería estar usando.
    public function getCountPurchasedInvoices(){
        $company = currentCompanyModel();
        $purchased_invoices = $company->additional_invoices;
        
        if( !$purchased_invoices ) {
            return -1;
        }
        
        return $purchased_invoices;
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
