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
        'company_id',
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
            $company = currentCompany();
            $llave = "notificaciones-".$this->user_id."-".$company;
            $items = NotificationUser::with('notification')->where('user_id',$this->user_id)->whereIn('company_id',array($company,0))->where('status',1)->orderby('created_at','desc')->get();
            Cache::put($llave, $items, now()->addDays(10));
			return true;
		}catch( \Throwable $e) { 
            Log::error('Error en poner en vista'. $e);
			return false; 
		}
   }
}
