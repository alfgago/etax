<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Terminal extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    //Relacion con el metodo de pago
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
