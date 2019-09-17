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
	          		['nombre'=>'Escritorio','url'=>'/','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'assets/images/iconos/dashb.png'],
	          		['nombre'=>'Ventas','url'=>'/facturas-emitidas','padre'=>'0','tipo'=>'href', 'icono'=>'assets/images/iconos/ventas.png', 'subitems'=>[
	          				['nombre'=>'Ver todas','url'=>'/facturas-emitidas','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Emitir factura electrónica','url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Registrar factura existente','url'=>'/facturas-emitidas/create','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Importar facturas','url'=>"abrirPopup('importar-emitidas-popup');",'tipo'=>'onclick','icono'=>''],
	          				['nombre'=>'Validar facturas','url'=>'/facturas-emitidas/validaciones','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Autorizar facturas por email','url'=>'/facturas-emitidas/autorizaciones','tipo'=>'href','icono'=>'']
	          			]
	          		],
	          		['nombre'=>'Compras','url'=>'/facturas-recibidas','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/compras.png', 'subitems'=>[
	          				['nombre'=>'Ver todas','url'=>'/facturas-recibidas','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Registrar factura existente','url'=>'/facturas-recibidas/create','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Importar facturas','url'=>'abrirPopup("importar-recibidas-popup");','tipo'=>'onclick','icono'=>''],
	          				['nombre'=>'Validar facturas','url'=>"/facturas-recibidas/validaciones",'tipo'=>'href','icono'=>''],
	          				['nombre'=>'Aceptación de facturas recibidas','url'=>'/facturas-recibidas/aceptaciones','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Autorizar facturas por email','url'=>'/facturas-recibidas/autorizaciones','tipo'=>'href','icono'=>'']
	          			]
	          		],
	          		['nombre'=>'Facturación','url'=>'/facturas-emitidas','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/facturacion.png', 'subitems'=>[
	          				['nombre'=>'Ver documentos emitidos','url'=>'/facturas-emitidas','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Emitir factura electrónica','url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Emitir tiquete electrónico','url'=>'/facturas-emitidas/emitir-factura/04','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Emitir factura electrónica de exportación','url'=>'/facturas-emitidas/emitir-factura/09','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Emitir factura electrónica de compra','url'=>'/facturas-emitidas/emitir-factura/08','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Aceptación de facturas recibidas','url'=>'/facturas-emitidas/aceptaciones','tipo'=>'href','icono'=>'']
	          			]
	          		],
	          		['nombre'=>'Cierres de mes','url'=>'/cierres','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'assets/images/iconos/report.png'],
	          		['nombre'=>'Reportes','url'=>'/reportes','padre'=>'0', 'subitems'=>[], 'icono'=>'assets/images/iconos/report.png','tipo'=>'href'], 
	          		['nombre'=>'Clientes','url'=>'/clientes','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/cliente.png', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/clientes','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Crear cliente','url'=>'/clientes/create','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Importar clientes','url'=>'abrirPopup("importar-clientes-popup");','tipo'=>'onclick','icono'=>'']
	          			]
	          		],
	          		['nombre'=>'Proveedores','url'=>'/proveedores','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/prove.png', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/proveedores','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Crear cliente','url'=>'/proveedores/create','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Importar proveedores','url'=>'abrirPopup("importar-proveedores-popup");','tipo'=>'onclick','icono'=>'']
	          			]
	          		],
	          		['nombre'=>'Productos','url'=>'/productos','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/produ.png', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/productos','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Crear cliente','url'=>'/productos/create','tipo'=>'href','icono'=>''],
	          				['nombre'=>'Importar productos','url'=>'abrirPopup("importar-productos-popup");','tipo'=>'onclick','icono'=>'']
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
		              'icono' =>$item_menu['icono'],
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
		              		'icono' =>$subitem_menu['icono'],
			              'estado' => 1
			            ]);
	            	}
            	}
          }catch(\Throwable $e){}
        }

    }
}
