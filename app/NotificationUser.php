<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notification;

class NotificationUser extends Model
{
    protected $fillable = [
        'user_id',
        'notification_id',
        'status'
    ];
    
    public function notification()
    {
        return $this->belongsTo(Notification::class,'notification_id');
    }

    public function vista(){
   		try{
            $this->status = 0;
            $this->save();
            $llave = "notificaciones-".$this->user_id;
			$items = NotificationUser::with('notification')->where('user_id',$this->user_id)->where('status',1)->get();
            Cache::put($llave, $items, now()->addDays(10));
			return true;
		}catch( \Throwable $e) { 
            Log::error('Error en poner en vista'. $e);
			return false; 
		}
   }
}
