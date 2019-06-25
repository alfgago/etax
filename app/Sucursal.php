<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    //Relacion con el metodo de pago
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
