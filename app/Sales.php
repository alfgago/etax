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
        return $this->belongsTo(SubscriptionPlan::class, 'id');
    }
    
    public static function createUpdateSubscriptionSale ( $procuctId, $recurrency ) {
        $company = currentCompanyModel();
        $user = auth()->user();
        
        $start_date = Carbon::parse( now('America/Costa_Rica') );
        $trial_end_date = $start_date->addDays(1);
        $next_payment_date = $start_date->addMonths(1);
        
        $sale = Sales::updateOrCreate (
            [ 
                'user_id' => $user->id 
            ],
            [ 
                'company_id' => $company->id,
                'status'  => 1,
                'recurrency' => $recurrency,
                'trial_end_date' => $trial_end_date,
                'start_date' => $start_date, 
                'next_payment_date' => $next_payment_date, 
                'etax_product_id' => $procuctId
            ]
        );
        
        return $sale;
    }
    
}
