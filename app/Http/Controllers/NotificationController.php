<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    

    public function create(Request $request){
    	try{
	    	if($request->clave == "AXYYS-8S2SS-8RY7V4-6F55S"){
	    		$this->notificar($request->opcion, $request->id, $request->empresa, $request->titulo, $request->texto, $request->tipo,   $request->funcion, $request->enlace); 
	            return $this->createResponse('200', 'OK' , 'Notificación creada exitosamente.');
	    	}else{
	            return $this->createResponse('400', 'ERROR' , 'Error en crear notificación, la clave no coincide.');
	    	}
    	}catch ( Exception $e) {
            Log::error ("Error al crear aceptacion de factura ".$e);
              return $this->createResponse('400', 'ERROR' ,  'Error en crear notificación. Por favor contáctenos.');
        }
    }
}
