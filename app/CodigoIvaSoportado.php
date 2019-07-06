<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoIvaSoportado extends Model
{
    
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    
}
