<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperativePercentages extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
