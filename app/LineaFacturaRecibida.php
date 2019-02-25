<?php

namespace App;

use App\FacturaRecibida;
use Illuminate\Database\Eloquent\Model;

class LineaFacturaRecibida extends Model
{
    
    protected $guarded = [];
    
    //Relacion con el cliente
    public function factura()
    {
        return $this->belongsTo(FacturaRecibida::class, 'factura_recibida_id');
    }
 
}
