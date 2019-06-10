<?php

namespace App;

use App\Bill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillItem extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    //Relacion con el cliente
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
    
    
 
}
