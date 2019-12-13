<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtaxProducts extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    //Relación con Usuario

    //Relación con el plan
    public function plan() {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
    
    public function getName(){
        return $this->plan->getName();
    }

}
