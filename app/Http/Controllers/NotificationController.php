<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Notification;
use App\NotificationUser;
use App\User;
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
    

    public function enviarNotificacionesBD($id){
    	$notificacion = Notification::findOrFail($id);
    	$notificacion->enviar(4, null, 0, null, null, null, null, null);
    	return "Enviadas";
    	//enviar($option, $id, $company , $title, $text, $type, $function, $link){
   		//$opcion: los tipos de notificaciones que hay de momento.
   			//1: Solo un usuario
   			//2: Todos los usuarios de una compañia
   			//3: Dueño de la compañia
   			//4: Todos los usuarios
   		//$id: Se pasa el Id de la persona o compañia a la que se quiere notificar.
   		//$company: La compañia involucrada en la notificacion
   		//$title: El titulo de la notificacion
   		//$text: El texto de la notificacion.
   		//$tpye: El tipo de notificacion que se esta usando.
   			//info
   			//error
   			//success
   			//warning
   		//$function: en cual funcion/metodo es que paso el problema
   		//$link: algun link al que quiera enviar su notificacion.
    }
}
