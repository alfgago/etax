<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    //Relacion con la empresa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relacion con la empresa
    public function product()
    {
        return $this->belongsTo(EtaxProducts::class, 'etax_product_id');
    }

    public function subscription_plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'id');
    }
}
