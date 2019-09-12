<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntegracionEmpresa extends Model
{
     protected $fillable = [
        'user_token',
        "company_token",
        "integration_id",
        "user_id",
        "company_id",
        "status"
    ];
}
