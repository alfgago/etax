<?php

namespace App;

use App\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
  
    //Relacion con el cliente
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
