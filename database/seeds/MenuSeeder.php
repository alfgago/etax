<?php

use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lista = [
        	[
	          	'nombre'=>'Menú principal',
	          	'items'=>[
	          		['nombre'=>'Escritorio','url'=>'/','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'assets/images/iconos/dashb.png', 'permisos'=>[1,2,3,4,5,6,7,8]],
	          		['nombre'=>'Ventas','url'=>'/facturas-emitidas','padre'=>'0','tipo'=>'href', 'icono'=>'assets/images/iconos/ventas.png', 'permisos'=>[1,2,4,6,8], 'subitems'=>[
	          				['nombre'=>'Ver todas','url'=>'/facturas-emitidas','tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,4,6,8]],
	          				['nombre'=>'Emitir factura electrónica','url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Registrar factura existente','url'=>'/facturas-emitidas/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Importar facturas','url'=>"abrirPopup('importar-emitidas-popup');",'tipo'=>'onclick','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Validar facturas','url'=>'/facturas-emitidas/validaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,4,8]],
	          				['nombre'=>'Autorizar facturas por email','url'=>'/facturas-emitidas/autorizaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,4,8]]
	          			]
	          		],
	          		['nombre'=>'Compras','url'=>'/facturas-recibidas','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/compras.png', 'permisos'=>[1,3,4,6,8], 'subitems'=>[
	          				['nombre'=>'Ver todas','url'=>'/facturas-recibidas','tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,6,8]],
	          				['nombre'=>'Registrar factura existente','url'=>'/facturas-recibidas/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,3]],
	          				['nombre'=>'Importar facturas','url'=>'abrirPopup("importar-recibidas-popup");','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,3]],
	          				['nombre'=>'Validar facturas','url'=>"/facturas-recibidas/validaciones",'tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,8]],
	          				['nombre'=>'Aceptación de facturas recibidas','url'=>'/facturas-recibidas/aceptaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,8]],
	          				['nombre'=>'Autorizar facturas por email','url'=>'/facturas-recibidas/autorizaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,8]]
	          			]
	          		],
	          		['nombre'=>'Facturación','url'=>'/facturas-emitidas','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/facturacion.png', 'permisos'=>[1,2,3], 'subitems'=>[
	          				['nombre'=>'Ver documentos emitidos','url'=>'/facturas-emitidas','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Emitir factura electrónica','url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Emitir tiquete electrónico','url'=>'/facturas-emitidas/emitir-factura/04','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Emitir factura electrónica de exportación','url'=>'/facturas-emitidas/emitir-factura/09','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Emitir factura electrónica de compra','url'=>'/facturas-emitidas/emitir-factura/08','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Aceptación de facturas recibidas','url'=>'/facturas-emitidas/aceptaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,3]]
	          			]
	          		],
	          		['nombre'=>'Cierres de mes','url'=>'/cierres','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'assets/images/iconos/report.png', 'permisos'=>[1,5,8]],
	          		['nombre'=>'Reportes','url'=>'/reportes','padre'=>'0', 'subitems'=>[], 'icono'=>'assets/images/iconos/report.png','tipo'=>'href', 'permisos'=>[1,6,8]], 
	          		['nombre'=>'Clientes','url'=>'/clientes','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/cliente.png', 'permisos'=>[1,2,7], 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/clientes','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,7]],
	          				['nombre'=>'Crear cliente','url'=>'/clientes/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,7]],
	          				['nombre'=>'Importar clientes','url'=>'abrirPopup("importar-clientes-popup");','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,2,7]]
	          			]
	          		],
	          		['nombre'=>'Proveedores','url'=>'/proveedores','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/prove.png', 'permisos'=>[1,2,3,7], 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/proveedores','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,3,7]],
	          				['nombre'=>'Crear proveedor','url'=>'/proveedores/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,3,7]],
	          				['nombre'=>'Importar proveedores','url'=>'abrirPopup("importar-proveedores-popup");','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,2,3,7]]
	          			]
	          		],
	          		['nombre'=>'Productos','url'=>'/productos','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/produ.png' , 'permisos'=>[1,7], 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/productos','tipo'=>'href','icono'=>'', 'permisos'=>[1,7]],
	          				['nombre'=>'Crear producto','url'=>'/productos/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,7]],
	          				['nombre'=>'Importar productos','url'=>'abrirPopup("importar-productos-popup");','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,7]]
	          			]
	          		]
	          	]
	        ]
      	];

      	foreach( $lista as $item ) {
          try{
            	$menu = App\Menu::updateOrCreate(
		            [ 
		              'name' => $item['nombre']
		          	],
		            [
		              'status' => 1
		            ]);	

            	foreach($item['items'] as $item_menu){
            		$menu_item = App\MenuItem::updateOrCreate(
		            [ 
		              'name' => $item_menu['nombre'],
		              'link' => $item_menu['url'],
		              'parent' => $item_menu['padre'],
		              'menu_id' => $menu->id
		          	],
		            [
		              'type' => $item_menu['tipo'],
		              'icon' =>$item_menu['icono'],
		              'status' => 1
		            ]);
		            foreach($item_menu['permisos'] as $permiso){

		            	$permiso_item  = App\MenuItemsPermiso::updateOrCreate(
		            	 	[
		            	 		'menu_item_id'=>$menu_item->id,
		            	 		'permission_id'=>$permiso
		            	 	],
		            	 	[]
		            	 );
		            }
		            foreach($item_menu['subitems'] as $subitem_menu){
	            		$menu_subitem = App\MenuItem::updateOrCreate(
			            [ 
			              'name' => $subitem_menu['nombre'],
			              'link' => $subitem_menu['url'],
			              'parent' => $menu_item->id,
			              'menu_id' => $menu->id,
			          	],
			            [
			              'type' => $subitem_menu['tipo'],
		              		'icon' =>$subitem_menu['icono'],
			              'status' => 1
			            ]);
			            foreach($subitem_menu['permisos'] as $permiso){
			            	$permiso_item = App\MenuItemsPermiso::updateOrCreate(
		            	 	[
		            	 		'menu_item_id'=>$menu_subitem->id,
		            	 		'permission_id'=>$permiso
		            	 	],
		            	 	[]
		            	 );
			            }
	            	}
            	}
          }catch(\Throwable $e){}
        }

    }
}
