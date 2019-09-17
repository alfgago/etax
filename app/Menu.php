<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MenuItem;

class Menu extends Model
{
    public function items()
    {	
        return $this->hasMany(MenuItem::class)->where('menu_items.padre', '=', 0);
    }
    
}
