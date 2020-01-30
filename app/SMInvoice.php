<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;
use App\Client;
use App\Invoice;
use App\InvoiceItem;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SMInvoice extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    //Relacion con la factura
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
