<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Log;
use App\Notification;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, LogsActivity;

	//$this->notificar(opcion, id, 'titulo', 'detalle', 'tipo', 'funcion','enlace');

 	public function notificar($option, $id, $title, $text = '', $type = 'info', $function = '', $link = '' ){ 
    	$notify = new Notification();
        Log::info( "prueba notificacion");
    	return $notify->enviar($option, $id, $title, $text, $type, $function, $link);
    }


}
