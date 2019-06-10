<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtaxProducts extends Model
{
    use SoftDeletes;
    protected $guarded = [];
}
