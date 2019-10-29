<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class XlsInvoice extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'id_number'
    ];
}
