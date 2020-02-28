<?php

namespace App;

use App\Bill;
use App\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HaciendaResponse extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    //Relacion con la empresa
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }  
    
    //Relacion con el cliente
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
}
