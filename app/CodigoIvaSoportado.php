<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoIvaSoportado extends Model
{
    
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    
 	public function getCode(){
        $codes = CodigoIvaSoportado::select("id", "name as nombre")->get();
        return $codes;
    }
}
