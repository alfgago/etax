<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCompanyPermission extends Model {

    public $timestamps = false;
    protected $fillable = [
        'company_id',
        'user_id',
        'permission_id'
    ];

}
