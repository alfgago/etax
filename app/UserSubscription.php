<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model {

    protected $table = 'user_subscriptions_history';
    protected $fillable = [
        'plan_id',
        'user_id',
        'start_date',
        'expiry_date',
        'status'
    ];

}
