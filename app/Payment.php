<?php

namespace App;

use App\Http\Controllers\PaymentMethodController;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    //Relacion con el metodo de pago
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function nextPaymentDate(){
        return $this->belongsTo(PaymentMethod::class);
    }

}
