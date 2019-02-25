<?php

namespace App;

use App\FacturaEmitida;
use Illuminate\Database\Eloquent\Model;

class LineaFacturaEmitida extends Model
{
    
    protected $guarded = [];
  
    //Relacion con el cliente
    public function factura()
    {
        return $this->belongsTo(FacturaEmitida::class, 'factura_emitida_id');
    }
}
