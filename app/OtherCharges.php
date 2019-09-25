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
    
}
