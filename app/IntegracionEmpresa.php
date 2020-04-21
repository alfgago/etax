<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Company;
use App\User;

class IntegracionEmpresa extends Model
{
     protected $fillable = [
         "user_token",
         "company_token",
         "integration_id",
         "user_id",
         "company_id",
         "status",
         "first_sync_gs"
    ];

    //Relación con Company
    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    //Relación con User
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
