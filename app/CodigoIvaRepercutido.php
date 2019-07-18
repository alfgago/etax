<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoIvaRepercutido extends Model
{
    
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    
}
