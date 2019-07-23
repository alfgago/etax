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
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    //Relación con la venta
    public function sale() {
        return $this->belongsTo(Sale::class);
    }
    
    //Relación con las empresas
    public function companies() {
        return $this->hasMany(Company::class);
    }

}
