<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoIvaRepercutido extends Model
{
    
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';

     //Relacion con Codigos Repercutidos
    public function company()
    {
        return $this->belongsToMany('App\Company');
    }
    
     public function getCode(){
        $codes = CodigoIvaRepercutido::select("id", "name as nombre")->get();
        return $codes;
    }
}
