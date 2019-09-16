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
	          		['nombre'=>'Escritorio','url'=>'/','padre'=>'0', 'subitems'=>[],'tipo'=>'href'],
	          		['nombre'=>'Ventas','url'=>'/facturas-emitidas','padre'=>'0','tipo'=>'href', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todas','url'=>'/facturas-emitidas','tipo'=>'href'],
	          				['nombre'=>'Emitir factura electrónica','url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href'],
	          				['nombre'=>'Registrar factura existente','url'=>'/facturas-emitidas/create','tipo'=>'href'],
	          				['nombre'=>'Importar facturas','url'=>"abrirPopup('importar-emitidas-popup');",'tipo'=>'onclick'],
	          				['nombre'=>'Validar facturas','url'=>'/facturas-emitidas/validaciones','tipo'=>'href'],
	          				['nombre'=>'Autorizar facturas por email','url'=>'/facturas-emitidas/autorizaciones','tipo'=>'href']
	          			]
	          		],
	          		['nombre'=>'Compras','url'=>'/facturas-recibidas','padre'=>'0', 'tipo'=>'href', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todas','url'=>'/facturas-recibidas','tipo'=>'href'],
	          				['nombre'=>'Registrar factura existente','url'=>'/facturas-recibidas/create','tipo'=>'href'],
	          				['nombre'=>'Importar facturas','url'=>'abrirPopup("importar-recibidas-popup");','tipo'=>'onclick'],
	          				['nombre'=>'Validar facturas','url'=>"/facturas-recibidas/validaciones",'tipo'=>'href'],
	          				['nombre'=>'Aceptación de facturas recibidas','url'=>'/facturas-recibidas/aceptaciones','tipo'=>'href'],
	          				['nombre'=>'Autorizar facturas por email','url'=>'/facturas-recibidas/autorizaciones','tipo'=>'href']
	          			]
	          		],
	          		['nombre'=>'Facturación','url'=>'/facturas-emitidas','padre'=>'0', 'tipo'=>'href', 
	          			'subitems'=>[
	          				['nombre'=>'Ver documentos emitidos','url'=>'/facturas-emitidas','tipo'=>'href'],
	          				['nombre'=>'Emitir factura electrónica','url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href'],
	          				['nombre'=>'Emitir tiquete electrónico','url'=>'/facturas-emitidas/emitir-factura/04','tipo'=>'href'],
	          				['nombre'=>'Emitir factura electrónica de exportación','url'=>'/facturas-emitidas/emitir-factura/09','tipo'=>'href'],
	          				['nombre'=>'Emitir factura electrónica de compra','url'=>'/facturas-emitidas/emitir-factura/08','tipo'=>'href'],
	          				['nombre'=>'Aceptación de facturas recibidas','url'=>'/facturas-emitidas/aceptaciones','tipo'=>'href']
	          			]
	          		],
	          		['nombre'=>'Cierres de mes','url'=>'/cierres','padre'=>'0', 'subitems'=>[],'tipo'=>'href'],
	          		['nombre'=>'Reportes','url'=>'/reportes','padre'=>'0', 'subitems'=>[],'tipo'=>'href'],
	          		['nombre'=>'Clientes','url'=>'/clientes','padre'=>'0', 'tipo'=>'href', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/clientes','tipo'=>'href'],
	          				['nombre'=>'Crear cliente','url'=>'/clientes/create','tipo'=>'href'],
	          				['nombre'=>'Importar clientes','url'=>'abrirPopup("importar-clientes-popup");','tipo'=>'onclick']
	          			]
	          		],
	          		['nombre'=>'Proveedores','url'=>'/proveedores','padre'=>'0', 'tipo'=>'href', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/proveedores','tipo'=>'href'],
	          				['nombre'=>'Crear cliente','url'=>'/proveedores/create','tipo'=>'href'],
	          				['nombre'=>'Importar proveedores','url'=>'abrirPopup("importar-proveedores-popup");','tipo'=>'onclick']
	          			]
	          		],
	          		['nombre'=>'Productos','url'=>'/productos','padre'=>'0', 'tipo'=>'href', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/productos','tipo'=>'href'],
	          				['nombre'=>'Crear cliente','url'=>'/productos/create','tipo'=>'href'],
	          				['nombre'=>'Importar productos','url'=>'abrirPopup("importar-productos-popup");','tipo'=>'onclick']
	          			]
	          		]
	          	]
	        ]
      	];

      	foreach( $lista as $item ) {
          try{
            	$menu = App\Menu::updateOrCreate(
		            [ 
		              'nombre' => $item['nombre']
		          	],
		            [
		              'estado' => 1
		            ]);

            	foreach($item['items'] as $item_menu){
            		$menu_item = App\MenuItem::updateOrCreate(
		            [ 
		              'nombre' => $item_menu['nombre'],
		              'url' => $item_menu['url'],
		              'padre' => $item_menu['padre'],
		              'menu_id' => $menu->id
		          	],
		            [
		              'tipo' => $item_menu['tipo'],
		              'estado' => 1
		            ]);
		            foreach($item_menu['subitems'] as $subitem_menu){
	            		$menu_subitem = App\MenuItem::updateOrCreate(
			            [ 
			              'nombre' => $subitem_menu['nombre'],
			              'url' => $subitem_menu['url'],
			              'padre' => $menu_item->id,
			              'menu_id' => $menu->id,
			          	],
			            [
			              'tipo' => $subitem_menu['tipo'],
			              'estado' => 1
			            ]);
	            	}
            	}
          }catch(\Throwable $e){}
        }

    }
}
