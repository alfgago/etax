<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\BridgeHaciendaApi;
use App\Subscription;
use App\SubscriptionPlan;
use App\CalculatedTax;
use App\OperativeYearData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
    
    //Relación con facturas recibidas
    public function operativeYearData() {
        return $this->hasMany(OperativeYearData::class);
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

    //Relacion con Codigos Repercutidos
    public function repercutidos()
    {   
        return $this->belongsToMany('App\CodigoIvaRepercutido');
    }

    //Relacion con Codigos Repercutidos
    public function soportados()
    {   
        return $this->belongsToMany('App\CodigoIvaSoportado');
    }

    public function repercutidosRelation()
    {   
        return $this->hasMany(CodigoIvaRepercutidoCompany::class);
    }

    public function soportadosRelation()
    {   
        return $this->hasMany(CodigoIvaSoportadoCompany::class);
    }

    //retorna los codigosRepercutidos Preselectos
    public function codigosRepercutidos(){
        $repercutidos = $this->repercutidos;
        if(count($repercutidos) < 1){
            return CodigoIvaRepercutido::all();
        }else{
            return $repercutidos;
        }
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
    
    public function clientsForSelect2( $limit = 25000 ){
        $clients = DB::table('clients')->select(
          array(
            'id',
            DB::raw('CONCAT(id_number, " - ", IFNULL(first_name, ""), " ", IFNULL(last_name, ""), " ", IFNULL(last_name2, "")) AS text')
          )
        )->where('company_id', $this->id)->take($limit)->get();
        return $clients;
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
    
    public function getOperativeData( $ano ){
        $anoAnterior = $ano-1;
        if($anoAnterior < 2018){
            //Si es menor a 2018, devuelve uno falso. Esto evita que se quede en un ciclo, porque para calcular el operativo necesita llamar al anterior.
            $fake = new OperativeYearData();
            return $fake; 
        }
        $operativeData = OperativeYearData::firstOrCreate(
            [
                'company_id' => $this->id,
                'year' => $anoAnterior
            ]
        );
        if( $operativeData->method > 1 ){
            $operativeData->getDataPeriodo();
        }
        return $operativeData;
    }
    
    
    public function getProrrataOperativa( $ano ){
        $anoAnterior = $ano-1;
        if($anoAnterior < 2018){
            return 1;
        }
        
        $cacheKey = "prorrata-$this->id-$ano"; 
        if ( !Cache::has($cacheKey) ) {
            $prorrataOperativa = $this->getOperativeData( $ano )->prorrata_operativa;
            Cache::put($cacheKey, $prorrataOperativa, 30);
        }
        return (double)Cache::get($cacheKey);
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
      
        if( $month == 6 ) {
            return $this->saldo_favor_2018;
        }
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
            $available_invoices = $this->getAvailableInvoices($year , $month);

            $available_invoices->current_month_sent = $available_invoices->current_month_sent + 1;
            $available_invoices->save();
            return $available_invoices;

        } catch (\Throwable $ex) {
             Log::error('Error en addSentInvoice: ' . $ex->getMessage() );
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

    public function preselectVatCodes($preselected_vat_codes){

        $repercutidos = $this->repercutidosRelation;
        if($preselected_vat_codes[0] == 1){
            foreach($repercutidos as $repercutido){
                $repercutido->delete();    
            }
            return true;
        }else{
            foreach($repercutidos as $repercutido){
                $repercutido->erase = true;
                foreach($preselected_vat_codes as $key => $preselected){
                    if($repercutido->codigo_iva_repercutido_id === $preselected){
                        $repercutido->erase = false;
                        unset($preselected_vat_codes[$key]);
                    }
                }
                if($repercutido->erase){
                    $repercutido->delete();
                }
            }
            foreach($preselected_vat_codes as $preselected){
                $repercutidosCompany = new CodigoIvaRepercutidoCompany();
                $repercutidosCompany->codigo_iva_repercutido_id = $preselected;
                $repercutidosCompany->company_id = $this->id;
                $repercutidosCompany->save();      
            }
        return true;
        }  
        return false;
    }

    public function preselectSoportados($preselected_sop_codes){

        $soportados = $this->soportadosRelation;
        if($preselected_sop_codes[0] == 1){
            foreach($soportados as $soportado){
                $soportado->delete();    
            }
            return true;
        }else{
            foreach($soportados as $soportado){
                $soportado->erase = true;
                foreach($preselected_sop_codes as $key => $preselected){
                    if($soportado->codigo_iva_soportado_id === $preselected){
                        $soportado->erase = false;
                        unset($preselected_sop_codes[$key]);
                    }
                }
                if($soportado->erase){
                    $soportado->delete();
                }
            }
            foreach($preselected_sop_codes as $preselected){
                $soportadoCompany = new CodigoIvaSoportadoCompany();
                $soportadoCompany->codigo_iva_soportado_id = $preselected;
                $soportadoCompany->company_id = $this->id;
                $soportadoCompany->save();      
            }
        return true;
        }  
        return false;
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
