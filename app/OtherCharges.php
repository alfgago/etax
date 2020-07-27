<?php

namespace App;

use App\Invoice;
use App\Bill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class OtherCharges extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
  
    //Relacion con la factura
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
  
    //Relacion con la factura
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
    
    public function getTypeString()
    {
        $type = $this->document_type;
        if($type == "01"){
            return "Contribución parafiscal";
        }else if($type == "02"){
            return "Timbre de la Cruz Roja";
        }else if($type == "03"){
            return "Timbre de Benemérito Cuerpo de Bomberos de Costa Rica";
        }else if($type == "04"){
            return "Cobro de un tercero";
        }else if($type == "05"){
            return "Costos de Exportación";
        }else if($type == "06"){
            return "Impuesto de Servicio 10%";
        }else if($type == "07"){
            return "Timbre de Colegios Profesionales";
        }else{
            return "Otros Cargos";
        }
        
    }
    
}
