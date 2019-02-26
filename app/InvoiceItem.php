<?php

namespace App;

use App\Invoice;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    
    protected $guarded = [];
  
    //Relacion con el cliente
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
