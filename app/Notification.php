<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Notification;
use App\NotificationUser;
use App\User;

class Notification extends Model
{
   public function enviar($option, $id, $company, $title, $text, $type, $function, $link){
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

   		try{
	   		$today = Carbon::parse(now('America/Costa_Rica'));
	   		
	   		if( isset($title) ){
		    	$this->type = $type;
		    	$this->title = $title;
		    	$this->text = $text;
		    	$this->link = $link;
		    	$this->function = $function;
	   		}
	    	
	    	$this->date = $today;  
	    	$this->save();
	    	$users = [];
	   		/************ solo un usuario *************************/
	   		if($option == 1){
	   			array_push($users, $id);
	   		}
	   		/************ envio a todos los de la compañia *************************/
	   		if($option == 2){
	   			$teams = Team::where('company_id' ,$id)->get();
		   		foreach ($teams as $team) {
		   			$teamusers = TeamUser::where('team_id', $team->id)->get();
			   		foreach ($teamusers as $teamuser) {
	   					array_push($users, $teamuser->user_id);
			   		}
		   		}
	   		}
	   		/************ dueño de la compañia *************************/
	   		if($option == 3){
	   			$teams = Team::where('company_id' ,$id)->get();
		   		foreach ($teams as $team) {
	   				array_push($users, $team->owner_id);
		   		}
	   		}
	   		/************ todos los usuarios *************************/
	   		if($option == 4){
	   			$usuarios = User::get();
		   		foreach ($usuarios as $usuario) {
	   				array_push($users, $usuario->id);
		   		}
	   		}
	   		
	   		foreach ($users as $user) {
	   			$notificationUser  = NotificationUser::updateOrCreate(
	                [
	                    'user_id' => $user,
	                    'company_id' => $company,
	                    'notification_id' => $this->id,
	                ],
	                [
	                	'status' => 1
	                ]
	            );
	            $this->reiniciarNotificaciones($user,$company);
	            //dd($notificationUser);
	   		}
	   		return true;
	   	}catch( \Exception $ex ) {
            Log::error("Error en guardar notificacion ".$ex);
            return false;
        }catch( \Throwable $ex ) {
            Log::error("Error en guardar notificacion ".$ex);
            return false;
        }
   }

   public function notificaciones(){
   		try{
			$user_id = auth()->user()->id;
			$company = currentCompany();
            $llave = "notificaciones-".$user_id."-".$company;
			if (!Cache::has($llave)) {
				$items = NotificationUser::with('notification')->where('user_id',$user_id)->whereIn('company_id',array($company,0))->where('status',1)->orderby('created_at','desc')->get();
                Cache::put($llave, $items, now()->addDays(10));
			}else{
				$items  = Cache::get($llave);
			}
			return $items;
		}catch( \Throwable $e) { 
            Log::error('Error leer notificacion $llave'. $e);
			return []; 
		}
   }

   public function cantidad(){
   		try{
   			$items = $this->notificaciones();
   			return $items->count();
		}catch( \Throwable $e) { 
            Log::error('Error cantidad notificacion $llave'. $e);
			return 0; 
		}
   }

   public function reiniciarNotificaciones($user_id,$company_id){
   		try{
   			$user = User::findOrFail($user_id);
   			if($company_id != 0){
	            $llave = "notificaciones-".$user_id."-".$company_id;
				$items = NotificationUser::with('notification')->where('user_id',$user_id)->whereIn('company_id',array($company_id,0))->where('status',1)->orderby('created_at','desc')->get();
	            Cache::put($llave, $items, now()->addDays(10));
   			}else{
   				foreach ($user->teams as $team) {
   					$llave = "notificaciones-".$user_id."-".$team->company_id;
					$items = NotificationUser::with('notification')->where('user_id',$user_id)->whereIn('company_id',array($team->company_id,0))->where('status',1)->orderby('created_at','desc')->get();
		            Cache::put($llave, $items, now()->addDays(10));
   				}
   			}
			return true;
		}catch( \Throwable $e) { 
            Log::error('Error agregar notificacion $llave'. $e);
			return []; 
		}
   }

   public function icon(){
   		$icon = '<span class="icono-notificaciones notificacion-info"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
   		if($this->type == 'info'){
   			$icon = '<span class="icono-notificaciones notificacion-info"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
   		}
   		if($this->type == 'error'){
   			$icon = '<span class="icono-notificaciones notificacion-error"><i class="fa fa-times-circle" aria-hidden="true"></i></span>';
   		}
   		if($this->type == 'success'){
   			$icon = '<span class="icono-notificaciones notificacion-success"><i class="fa fa-check-circle" aria-hidden="true"></i></span>';
   		}
   		if($this->type == 'warning'){
   			$icon = '<span class="icono-notificaciones notificacion-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>';
   		}
   		return $icon;
   }
}
