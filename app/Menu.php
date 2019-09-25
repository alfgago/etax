<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\MenuItem;

class Menu extends Model
{
	public function menu($menu){ 
		//try{
			$user_id = auth()->user()->id;
            $current_company = currentCompany();
            $llave = "menu-".$user_id."-".$current_company."-".$menu;
			//if (!Cache::has($llave)) {
			if(true){
				$menu = Menu::where('slug',$menu)->first();
				$permiso = auth()->user()->permisos();
				$items = [];
				$datos = MenuItemsPermiso::select('menu_items.id', 'menu_items.name', 'menu_items.link', 'menu_items.icon', 'menu_items.type','menu_items.parent', 'menu_items.order')
							->join('menu_items','menu_items_permisos.menu_item_id','=','menu_items.id')
							->whereIn('menu_items_permisos.permission_id',$permiso)->where('menu_items.status', 1)
							->where('menu_items.menu_id', $menu->id)->distinct()->orderBy('menu_items.order', 'asc')->get();
				$datos_subitem = $datos;
				foreach ($datos as $dato) {
					if($dato->parent == 0){
						$subitems = [];
						foreach ($datos_subitem as $subitem) {
							if($subitem->parent == $dato->id){
								array_push($subitems, $subitem);
							}
						}
						$dato->subitems = $subitems;
						array_push($items, $dato);
					}
				}
				Cache::put($llave, $items, 3600);
			}else{
				$items  = Cache::get($llave);
			}
			return $items;
		/*}catch( \Throwable $e) { 
              \Illuminate\Support\Facades\Log::error('Error items menu'. $e);
			return []; 
		}*/
	}

	
    
}
