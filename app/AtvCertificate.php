<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AtvCertificate extends Model
{
	
    protected $fillable = [
        'company_id',
        'user',
        'password',
        'key_url',
        'pin',
        'generated_date',
        'due_date'
    ];
    
}
