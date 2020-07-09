<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoSocketData extends Model
{

    use SoftDeletes;
    protected $table = 'go_socket_data';
    protected $guarded = [];
    
}
