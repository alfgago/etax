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
        
        $start_date = Carbon::parse( now('America/Costa_Rica') );
        $next_payment_date = Carbon::parse( now('America/Costa_Rica') )->addMonths(1);
        
        $sale = Sales::updateOrCreate (
            [ 
                'user_id' => $user->id ,
                'is_subscription' => true,
            ],
            [ 
                'company_id' => $company->id,
                'status'  => 3,
                'recurrency' => $recurrency,
                'start_date' => $start_date, 
                'next_payment_date' => $next_payment_date, 
                'etax_product_id' => $productId
            ]
        );
        
        return $sale;
    }
    
    public static function startTrial ( $productId, $recurrency ) {
        $trial_end_date = Carbon::parse( now('America/Costa_Rica') )->addDays(2);
        
        $sale = Sales::createUpdateSubscriptionSale ( $productId, $recurrency );
        $sale->status = 4;
        $sale->trial_end_date = $trial_end_date;
        $sale->save();
        
        return $sale;
    }
    
}
