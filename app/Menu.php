<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MenuItem;

class Menu extends Model
{
	public function menu($menu){ 
		try{
			$permiso = auth()->user()->permisos();
			$items = [];
			$datos = MenuItemsPermiso::select('menu_items.id', 'menu_items.name', 'menu_items.link', 'menu_items.icon', 'menu_items.type','menu_items.parent')
						->join('menu_items','menu_items_permisos.menu_item_id','=','menu_items.id')
						->whereIn('menu_items_permisos.permission_id',$permiso)->where('menu_items.status', 1)
						->where('menu_items.menu_id', $menu)->distinct()->get();
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
			return $items;
		}catch( \Throwable $e) { 
              \Illuminate\Support\Facades\Log::error('Error items menu'. $e);
			return []; 
		}
	}

	/*public function menu($menu){ //TODO BUSCAR SIMPLICAFICARLO
		try{
			$permiso = auth()->user()->permisos();
			$items = MenuItemsPermiso::select('menu_items.id', 'menu_items.name', 'menu_items.link', 'menu_items.icon', 'menu_items.type')
						->join('menu_items','menu_items_permisos.menu_item_id','=','menu_items.id')
						->whereIn('menu_items_permisos.permission_id',$permiso)->where('menu_items.status', 1)
						->where('menu_items.menu_id', $menu)->where('menu_items.parent', 0)->distinct()->get();
			foreach ($items as $item) {
				$item->subitems = MenuItemsPermiso::select('menu_items.id', 'menu_items.name', 'menu_items.link', 'menu_items.icon', 'menu_items.type')
						->join('menu_items','menu_items_permisos.menu_item_id','=','menu_items.id')
						->whereIn('menu_items_permisos.permission_id',$permiso)->where('menu_items.status', 1)
						->where('menu_items.menu_id', $menu)->where('menu_items.parent', $item->id)->distinct()->get();
			}
			return $items;
		}catch( \Throwable $e) { 
			return []; 
		}
	}*/
    
}
