<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    public function subitems()
    {	
        return $this->hasMany(MenuItem::class,'padre');
    }
}
