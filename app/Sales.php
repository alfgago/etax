<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\SubscriptionPlan;

class Sales extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    //Relacion con la empresa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relacion con la empresa
    public function product()
    {
        return $this->belongsTo(EtaxProducts::class, 'etax_product_id');
    }
    
    public function plan()
    {
        //dd($this);
        //if($this->is_subscription) {
            return $this->belongsTo(SubscriptionPlan::class, 'etax_product_id');
        
    }
    
    public function saleDescription()
    {
        if( isset($this->is_subscription) ){
            return $this->plan->plan_type . " " . $this->plan->plan_tier;
        }else{
            return $this->product->name; 
        }
    }

    public function subscription_plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    public static function createUpdateSubscriptionSale ( $productId, $recurrency ) {
        $company = currentCompanyModel();
        $user = $company->user_id;
        
        $startDate = Carbon::parse( now('America/Costa_Rica') );
        $nextPaymentDate = Carbon::parse( now('America/Costa_Rica') )->addMonths(1);
        $plan = SubscriptionPlan::find($productId);
        $plan_tier = "Pro ($user)";
        if($plan_tier == $plan->plan_tier){
            $sale = Sales::updateOrCreate (
                [ 
                    'user_id' => $user ,
                    'is_subscription' => true,
                ],
                [ 
                    'company_id' => $company->id,
                    'status'  => 3,
                    'recurrency' => $recurrency,
                    'start_date' => $startDate, 
                    'next_payment_date' => $nextPaymentDate, 
                    'etax_product_id' => $productId,
                    'subscription_plan_id' => $productId
                ]
            );
        }else{
            $sale = Sales::updateOrCreate (
                [ 
                    'user_id' => $user ,
                    'company_id' => $company->id,
                    'is_subscription' => true,
                ],
                [ 
                    'status'  => 3,
                    'recurrency' => $recurrency,
                    'start_date' => $startDate, 
                    'next_payment_date' => $nextPaymentDate, 
                    'etax_product_id' => $productId,
                    'subscription_plan_id' => $productId
                ]
            );
        }
        
        return $sale;
    }
    
    public static function startTrial ( $productId, $recurrency ) {
        $trialEndDate = Carbon::parse( now('America/Costa_Rica') )->addDays(15);
        
        $sale = Sales::createUpdateSubscriptionSale ( $productId, $recurrency );
        $sale->status = 4;
        $sale->trial_end_date = $trialEndDate;
        $sale->save();
        
        return $sale;
    }
    
}
