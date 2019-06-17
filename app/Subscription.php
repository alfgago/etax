<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {

    protected $table = 'subscriptions';
    
    protected $guarded = [];
    
    //Relación con el plan
    public function plan() {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    //Relación con Usuario
    public function owner() {
        return $this->belongsTo(User::class);
    }

}
