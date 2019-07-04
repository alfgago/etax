<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

	protected $guarded = [];

	public function payment(){
        return $this->belongsTo(Payment::class);
    }
}
