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
	          	'slug' => 'menu_sidebar',
	          	'items'=>[
	          		['nombre'=>'Escritorio','url'=>'/','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'assets/images/iconos/dashb.png', 'permisos'=>[1,2,3,4,5,6,7,8], 'orden' => '1'],
	          		['nombre'=>'Ventas','url'=>'/facturas-emitidas','padre'=>'0','tipo'=>'href', 'icono'=>'assets/images/iconos/ventas.png', 'permisos'=>[1,2,4,6,8], 'orden' => '2', 'subitems'=>[	
	          				['nombre'=>'Ver todas','url'=>'/facturas-emitidas', 'orden' => '0','tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,4,6,8]],
	          				['nombre'=>'Emitir factura electrónica', 'orden' => '1', 'url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href','icono'=>'2', 'permisos'=>[1,2]],
	          				['nombre'=>'Registrar factura existente', 'orden' => '2','url'=>'/facturas-emitidas/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Importar facturas', 'orden' => '3','url'=>"abrirPopup('importar-emitidas-popup');",'tipo'=>'onclick','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Envio masivo de Facturas','url'=>'abrirPopup("enviar-emitidas-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,2], 'orden' => '4'],
	          				['nombre'=>'Validar facturas', 'orden' => '5','url'=>'/facturas-emitidas/validaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,4,8]],
	          				['nombre'=>'Autorizar facturas por email', 'orden' => '6','url'=>'/facturas-emitidas/autorizaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,4,8]]
	          			]
	          		],
	          		['nombre'=>'Compras','url'=>'/facturas-recibidas','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/compras.png', 'permisos'=>[1,3,4,6,8], 'orden' => '3', 'subitems'=>[
	          				['nombre'=>'Ver todas', 'orden' => '0','url'=>'/facturas-recibidas','tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,6,8]],
	          				['nombre'=>'Registrar factura existente', 'orden' => '1','url'=>'/facturas-recibidas/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,3]],
	          				['nombre'=>'Importar facturas', 'orden' => '2','url'=>'abrirPopup("importar-recibidas-popup");','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,3]],
	          				['nombre'=>'Validar facturas', 'orden' => '3','url'=>"/facturas-recibidas/validaciones",'tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,8]],
	          				['nombre'=>'Aceptación de facturas recibidas', 'orden' => '4','url'=>'/facturas-recibidas/aceptaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,8]],
	          				['nombre'=>'Autorizar facturas por email', 'orden' => '5','url'=>'/facturas-recibidas/autorizaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,3,4,8]]
	          			]
	          		],
	          		['nombre'=>'Facturación','url'=>'/facturas-emitidas','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/facturacion.png', 'permisos'=>[1,2,3], 'orden' => '4',  'subitems'=>[
	          				['nombre'=>'Ver documentos emitidos', 'orden' => '0','url'=>'/facturas-emitidas','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Emitir factura electrónica', 'orden' => '1','url'=>'/facturas-emitidas/emitir-factura/01','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Emitir tiquete electrónico', 'orden' => '2','url'=>'/facturas-emitidas/emitir-factura/04','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Envio masivo de Facturas','url'=>'abrirPopup("enviar-emitidas-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,2], 'orden' => '3'],
	          				['nombre'=>'Emitir factura electrónica de exportación', 'orden' => '4','url'=>'/facturas-emitidas/emitir-factura/09','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Emitir factura electrónica de compra', 'orden' => '5','url'=>'/facturas-emitidas/emitir-factura/08','tipo'=>'href','icono'=>'', 'permisos'=>[1,2]],
	          				['nombre'=>'Aceptación de facturas recibidas', 'orden' => '6','url'=>'/facturas-recibidas/aceptaciones','tipo'=>'href','icono'=>'', 'permisos'=>[1,3]]
	          			]
	          		],
	          		['nombre'=>'Cierres de mes','url'=>'/cierres','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'assets/images/iconos/report.png', 'permisos'=>[1,5,8], 'orden' => '5'],
	          		['nombre'=>'Reportes','url'=>'/reportes','padre'=>'0', 'subitems'=>[], 'icono'=>'assets/images/iconos/report.png','tipo'=>'href', 'permisos'=>[1,6,8], 'orden' => '6'], 
	          		['nombre'=>'Clientes','url'=>'/clientes','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/cliente.png', 'permisos'=>[1,2,7], 'orden' => '7', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/clientes', 'orden' => '0','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,7]],
	          				['nombre'=>'Crear cliente', 'orden' => '1','url'=>'/clientes/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,7]],
	          				['nombre'=>'Importar clientes', 'orden' => '2','url'=>'abrirPopup("importar-clientes-popup");','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,2,7]]
	          			]
	          		],
	          		['nombre'=>'Proveedores','url'=>'/proveedores','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/prove.png', 'permisos'=>[1,2,3,7], 'orden' => '8', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/proveedores', 'orden' => '0','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,3,7]],
	          				['nombre'=>'Crear proveedor', 'orden' => '1','url'=>'/proveedores/create','tipo'=>'href','icono'=>'', 'permisos'=>[1,2,3,7]],
	          				['nombre'=>'Importar proveedores', 'orden' => '2','url'=>'abrirPopup("importar-proveedores-popup");','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,2,3,7]]
	          			]
	          		],
	          		['nombre'=>'Productos','url'=>'/productos','padre'=>'0', 'tipo'=>'href', 'icono'=>'assets/images/iconos/produ.png' , 'permisos'=>[1,7], 'orden' => '9', 
	          			'subitems'=>[
	          				['nombre'=>'Ver todos','url'=>'/productos','tipo'=>'href','icono'=>'', 'orden' => '0', 'permisos'=>[1,7]],
	          				['nombre'=>'Crear producto','url'=>'/productos/create','tipo'=>'href', 'orden' => '1','icono'=>'', 'permisos'=>[1,7]],
	          				['nombre'=>'Importar productos','url'=>'abrirPopup("importar-productos-popup");', 'orden' => '2','tipo'=>'onclick','icono'=>'', 'permisos'=>[1,7]]
	          			]
	          		]
	          	]
	        ],
	        [
	        	'nombre'=>'Menú dashboard',
	          	'slug' => 'menu_dashboard',
	          	'items'=>[
	          		['nombre'=>'Emitir facturas','url'=>'/facturas-emitidas/emitir-factura/01','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2], 'orden' => '0'],
	          		['nombre'=>'Importar facturas de venta','url'=>'abrirPopup("importar-emitidas-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,2], 'orden' => '1'],
	          		['nombre'=>'Importar facturas de compra','url'=>'abrirPopup("importar-recibidas-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,3], 'orden' => '2'],
	          		['nombre'=>'Cierres de mes','url'=>'/cierres','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'','orden' => '3', 'permisos'=>[1,5]], 
	          		['nombre'=>'Generar presentación de IVA','url'=>'/reportes','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,6], 'orden' => '4']
	          	]
	        ],
	        [
	        	'nombre'=>'Menú ventas',
	          	'slug' => 'menu_ventas',
	          	'items'=>[
	          		['nombre'=>'Emitir factura nueva','url'=>'/facturas-emitidas/emitir-factura/01','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2], 'orden' => '0'],
	          		['nombre'=>'Ingresar factura existente','url'=>'/facturas-emitidas/create','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'','orden' => '1', 'permisos'=>[1,2]],
	          		['nombre'=>'Importar facturas emitidas','url'=>'abrirPopup("importar-emitidas-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,2], 'orden' => '2'],
	          		['nombre'=>'Envio masivo de Facturas','url'=>'abrirPopup("enviar-emitidas-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,2], 'orden' => '3']
	          	
	          	]
	        ],
	        [
	        	'nombre'=>'Menú compras',
	          	'slug' => 'menu_compras',
	          	'items'=>[
	          		['nombre'=>'Ingresar factura existente','url'=>'/facturas-recibidas/create','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,3], 'orden' => '0'],
	          		['nombre'=>'Importar facturas recibidas','url'=>'abrirPopup("importar-recibidas-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,3], 'orden' => '1'],
	          		['nombre'=>'Aceptación de facturas','url'=>'/facturas-recibidas/aceptaciones','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'','orden' => '2', 'permisos'=>[1,3]],
	          		['nombre'=>'Autorizar facturas por email','url'=>'/facturas-recibidas/autorizaciones','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'','orden' => '3', 'permisos'=>[1,3]]
	          	]
	        ],
	        [
	        	'nombre'=>'Menú clientes',
	          	'slug' => 'menu_clientes',
	          	'items'=>[
	          		['nombre'=>'Crear cliente','url'=>'/clientes/create','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,7], 'orden' => '0'],
	          		['nombre'=>'Importar clientes','url'=>'abrirPopup("importar-clientes-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,2,7], 'orden' => '1']
	          	]
	        ],
	        [
	        	'nombre'=>'Menú proveedores',
	          	'slug' => 'menu_proveedores',
	          	'items'=>[
	          		['nombre'=>'Crear proveedor','url'=>'/proveedores/create','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,3,7], 'orden' => '0'],
	          		['nombre'=>'Importar proveedores','url'=>'abrirPopup("importar-proveedores-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,2,3,7], 'orden' => '1']
	          	]
	        ],
	        [
	        	'nombre'=>'Menú productos',
	          	'slug' => 'menu_productos',
	          	'items'=>[
	          		['nombre'=>'Crear producto','url'=>'/productos/create','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,7], 'orden' => '0'],
	          		['nombre'=>'Importar productos','url'=>'abrirPopup("importar-productos-popup");','padre'=>'0', 'subitems'=>[],'tipo'=>'onclick', 'icono'=>'', 'permisos'=>[1,7], 'orden' => '1']
	          	]
	        ],
	        [
	        	'nombre'=>'Menú dropdown header',
	          	'slug' => 'menu_dropdown_header',
	          	'items'=>[
	          		['nombre'=>'Perfil','url'=>'/usuario/perfil','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,3,4,5,6,7,8], 'orden' => '0'],
	          		['nombre'=>'Configuración de empresa','url'=>'/empresas/editar','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '1'],
	          		['nombre'=>'Gestión de pagos','url'=>'/payments-methods','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '2']
	          	]
	        ],
	        [
	        	'nombre'=>'Menú perfil',
	          	'slug' => 'menu_perfil',
	          	'items'=>[
	          		['nombre'=>'Editar información personal','url'=>'/usuario/perfil','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,3,4,5,6,7,8], 'orden' => '0'],
	          		['nombre'=>'Seguridad','url'=>'/usuario/seguridad','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,3,4,5,6,7,8], 'orden' => '1'],
	          		['nombre'=>'Cambiar plan','url'=>'/cambiar-plan','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,2,3,4,5,6,7,8], 'orden' => '2']
	          	]
	        ],
	        [
	        	'nombre'=>'Menú empresas',
	          	'slug' => 'menu_empresas',
	          	'items'=>[
	          		['nombre'=>'Editar perfil de empresa','url'=>'/empresas/editar','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '0'],
	          		['nombre'=>'Configuración avanzada','url'=>'/empresas/configuracion','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '1'],
	          		['nombre'=>'Certificado digital','url'=>'/empresas/certificado','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '2'],
	          		['nombre'=>'Equipo de trabajo','url'=>'/empresas/equipo','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '3'],
	          		['nombre'=>'Comprar facturas','url'=>'/empresas/comprar-facturas-vista','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '4']
	          	]
	        ],
	        [
	        	'nombre'=>'Menú gestion pagos',
	          	'slug' => 'menu_gestion_pagos',
	          	'items'=>[
	          		['nombre'=>'Métodos de pagos','url'=>'/payments-methods','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '0'],
	          		['nombre'=>'Historial de pagos','url'=>'/payments','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '1'],
	          		['nombre'=>'Cargos Pendientes','url'=>'/payment/pending-charges','padre'=>'0', 'subitems'=>[],'tipo'=>'href', 'icono'=>'', 'permisos'=>[1,8], 'orden' => '2']
	          	]
	        ]
      	];

      	foreach( $lista as $item ) {
          try{
            	$menu = App\Menu::updateOrCreate(
		            [ 
		              'slug' => $item['slug']
		          	],
		            [
		              'name' => $item['nombre'],
		              'status' => 1
		            ]);	

            	foreach($item['items'] as $item_menu){
            		$menu_item = App\MenuItem::updateOrCreate(
		            [ 
		              'name' => $item_menu['nombre'],
		              'parent' => $item_menu['padre'],
		              'menu_id' => $menu->id
		          	],
		            [
		              'link' => $item_menu['url'],
		              'type' => $item_menu['tipo'],
		              'icon' =>$item_menu['icono'],
		              'order' =>$item_menu['orden'],
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
			              'parent' => $menu_item->id,
			              'menu_id' => $menu->id,
			          	],
			            [
			              'link' => $subitem_menu['url'],
			              'type' => $subitem_menu['tipo'],
			              'icon' =>$subitem_menu['icono'],
			              'order' =>$subitem_menu['orden'],
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
