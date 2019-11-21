<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Notification;
use App\NotificationUser;

class Notification extends Model
{
   public function enviar($option, $id, $title, $text, $type, $function, $link){
   		try{
	   		$today = Carbon::parse(now('America/Costa_Rica'));
	    	$this->type = $type;
	    	$this->title = $title;
	    	$this->text = $text;
	    	$this->link = $link;
	    	$this->function = $function;  
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
	   		
	   		foreach ($users as $user) {
	   			$NotificationUser  = NotificationUser::updateOrCreate(
	                [
	                    'user_id' => $user,
	                    'notification_id' => $this->id,
	                ],
	                [
	                	'status' => 1
	                ]
	            );
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
}
