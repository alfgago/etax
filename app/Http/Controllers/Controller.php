<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Notification;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, LogsActivity;

//$this->notify(2, 3, 'prueba', 'hola prueba','', 'danger', 'prueba');
 	public function notify($option, $id, $title, $text = '', $type = 'info', $function = '', $link = '' ){
    	$notify = new Notification();
    	return $notify->enviar($option, $id, $title, $text, $type, $function, $link);
    }
}
