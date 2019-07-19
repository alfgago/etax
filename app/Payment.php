<?php

namespace App;

use App\Http\Controllers\PaymentMethodController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    //Relacion con el metodo de pago
    public function sale(){
        return $this->belongsTo(Sales::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupons::class);
    }
    //
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function nextPaymentDate(){
        return $this->belongsTo(PaymentMethod::class);
    }
    
    public function getStatusString() {
        //1: Pendiente, 2: Procesado, 0: Cancelado
        $estado = "Pendiente";
        if( $this->payment_status == 2 ){
            $estado = "Procesado";
        }else if( $this->payment_status == 0 ){
            $estado = "Cancelado";
        }
        
        return $estado;
    }
}
