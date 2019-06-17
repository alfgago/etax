<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $guarded = [];

    public function getName(){
        return $this->plan_type . " - " . $this->plan_tier;
    }
}
