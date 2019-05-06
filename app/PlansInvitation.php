<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlansInvitation extends Model {

    protected $fillable = [
        'plan_no',
        'company_id',
        'user_id',
        'is_admin',
        'is_read_only'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function company() {
        return $this->belongsTo('App\Company');
    }

}
