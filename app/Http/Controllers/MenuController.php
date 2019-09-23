<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;

class MenuController extends Controller
{

	public function menu($nombre){
		$menu = Menu::where('nombre',$nombre)->first();
		$items = $menu->items;
		dd($items)
	}
    
}
