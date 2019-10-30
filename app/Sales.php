<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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

        if($this->is_subscription) {
            return $this->belongsTo(SubscriptionPlan::class, 'etax_product_id');
        } else {
            return $this->belongsTo(EtaxProducts::class, 'etax_product_id');
        }
    }
    
    public function saleDescription()
    {
        if($this->is_subscription){
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
        $user = auth()->user();
        
        $startDate = Carbon::parse( now('America/Costa_Rica') );
        $nextPaymentDate = Carbon::parse( now('America/Costa_Rica') )->addMonths(1);
        
        $sale = Sales::updateOrCreate (
            [ 
                'user_id' => $user->id ,
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
        
        return $sale;
    }
    
    public static function startTrial ( $productId, $recurrency ) {
        $trialEndDate = Carbon::parse( now('America/Costa_Rica') )->addDays(2);
        
        $sale = Sales::createUpdateSubscriptionSale ( $productId, $recurrency );
        $sale->status = 4;
        $sale->trial_end_date = $trialEndDate;
        $sale->save();
        
        return $sale;
    }
    
}
