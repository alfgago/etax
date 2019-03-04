<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $guarded = [];
  
    //Relación con facturas emitidas
    public function facturasEmitidas()
    {
        return $this->hasMany('App\FacturaEmitida');
    }
  
    //Relación con facturas emitidas
    public function facturasRecibidas()
    {
        return $this->hasMany('App\FacturaRecibida');
    }
  
    //Relación con Usuario
    public function owner()
    {
        return $this->belongsTo('App\User');
    }
  
}
