<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvailableInvoices extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    //Relación con el plan
    public function company() {
        return $this->belongsTo(Company::class);
    }

}
