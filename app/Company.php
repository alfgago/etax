<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\BridgeHaciendaApi;
use App\Subscription;
use App\SubscriptionPlan;
use App\CalculatedTax;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    //Relación con productos
    public function products() {
        return $this->hasMany(Product::class);
    }
    
    //Relación con Certificado
    public function atv() {
        return $this->hasOne(AtvCertificate::class);
    }

    //Relacion con Actividades comerciales
    public function actividades(){
        return $this->hasOne(Actividades::class);
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
    public function plan() {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_id');
    }
    
    //Relación con el plan
    public function subscription() {
        return $this->hasOne(Sales::class);
    }
    
    public function getActivities() {
        $arrayActividades = [];
        $companyActivities = explode( ',', $this->commercial_activities );
    	
    	foreach( $companyActivities as $a ){
    	    $act = Actividades::where('codigo', trim($a))->first();
    	    if( $act ) {
    	        array_push($arrayActividades, $act);
    	    }
    	}
    	
    	return $arrayActividades;
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
    
    public function getProrrataOperativa( $ano ){
      
      $anoAnterior = $ano > 2018 ? $ano-1 : 2018;
      
      if($anoAnterior == 2018) {
        if( $this->first_prorrata_type == 1 ){
          $prorrataOperativa = $this->first_prorrata ? $this->first_prorrata / 100 : 1;
        }else {
          $anterior = CalculatedTax::getProrrataPeriodoAnterior( $anoAnterior );
          $prorrataOperativa = $anterior->prorrata;
        }
      }else{
        $anterior = CalculatedTax::getProrrataPeriodoAnterior( $anoAnterior );
        $prorrataOperativa = $anterior->prorrata;
      }

      return $prorrataOperativa;
    }
    
    public function getLastBalance($month, $year) {
      
      if( $year != 2018 ) {
        
        if( $month == 1 ) {
          $month = 11;
          $year = $year - 1;
        } else {
          $month = $month - 1;
        }
        
        //Solicita a BD el saldo_favor del periodo anterior.
        $lastBalance = CalculatedTax::where('company_id', $this->id)
                              ->where('month', $month)
                              ->where('year', $year)
                              ->where('is_final', true)
                              ->where('is_closed', true)
                              ->value('saldo_favor');
        //Si el saldo es mayor que nulo, lo pone en 0.                     
        $lastBalance = $lastBalance ? $lastBalance : 0;
        
      }else{
        $lastBalance = 0;
      }
      
      return $lastBalance;
      
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
            
            $count =  getCurrentSubscription()->plan->num_bills;
            
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
            if( !$month || !$year ) {
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
                $subscriptionPlan = getCurrentSubscription()->plan;

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
             Log::warning('Error en addSentInvoice: ' . $ex->getMessage() );
        }
    }
    
    public function setFirstAvailableInvoices( $year, $month, $count ) {

        try{
            if( ( $month == 1 || $month == 2 || $month == 3 || $month == 4 || $month == 5 || $month == 6 ) && $year <= 2019) {
                $count = 0;
            }
            
            $available_invoices = AvailableInvoices::where('company_id', $this->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->first();
                                
            // Si no encontró nada, tiene que crearla.
            if( ! $available_invoices ) {
                $subscriptionPlan = getCurrentSubscription()->plan;
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
    
    /*
    *Checks various validations to make sure that company is able to emit new invoices.
    *
    *
    */
    public function validateEmit(){
        
        //Revisa límite de facturas emitidas en el mes actual
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $month = $start_date->month;
        $year = $start_date->year;
        
        $available_invoices = $this->getAvailableInvoices( $year, $month );
        
        $available_plan_invoices = $available_invoices->monthly_quota - $available_invoices->current_month_sent;
        if($available_plan_invoices < 1 && $this->additional_invoices < 1){
            return $errors = ['url' => '/', 'mensaje' => 'Usted ha sobrepasado el límite de facturas mensuales de su plan actual.'];
        }
        //Termina de revisar limite de facturas.

        if ($this->atv_validation == false) {
            $apiHacienda = new BridgeHaciendaApi();
            $token = $apiHacienda->login(false);
            $validateAtv = $apiHacienda->validateAtv($token, $this);
            
            if( $validateAtv ) {
                if ($validateAtv['status'] == 400) {
                    Log::info('Atv Not Validated Company: '. $this->id_number);
                    if (strpos($validateAtv['message'], 'ATV no son válidos') !== false) {
                        $validateAtv['message'] = "Los parámetros actuales de acceso a ATV no son válidos";
                    }
                    return $errors = ['url' => '/empresas/certificado', 'mensaje' => "Error al validar el certificado: " . $validateAtv['message']];
                    
                } else {
                    Log::info('Atv Validated Company: '. $this->id_number);
                    $this->atv_validation = true;
                    $this->save();
                    
                    $user = auth()->user();
                    Cache::forget("cache-currentcompany-$user->id");
                }
            }else {
                return $errors = ['url' => '/empresas/certificado', 'mensaje' => 'Hubo un error al validar su certificado digital. Verifique que lo haya ingresado correctamente. Si cree que está correcto. '];
            }
        }
               
        if($this->last_note_ref_number === null) {
            return $errors = ['url' => '/empresas/configuracion', 'mensaje' => 'No ha ingresado ultimo consecutivo de nota credito'];
        }
        if($this->last_ticket_ref_number === null) {
            return $errors = ['url' => '/empresas/configuracion', 'mensaje' => 'No ha ingresado ultimo consecutivo de tiquetes'];
        }
        
        return $errors = false;
        
    }

}
