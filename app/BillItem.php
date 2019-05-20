<?php

namespace App;

use App\Bill;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    
    protected $guarded = [];
    
    //Relacion con el cliente
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
    
    
 
}
