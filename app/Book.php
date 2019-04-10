<?php

namespace App;

use App\Company;
use App\CalculatedTax;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];
    
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function calculos()
    {
        return $this->belongsTo(CalculatedTax::class);
    }
    
}
