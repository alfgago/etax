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
    public function notify($option, $id, $title, $text = '', $link = '' , $type = 'info', $function = ''){
    	$notify = new Notification();
    	$notify->type = $type;
    	$notify->title = $title;
    	$notify->text = $text;
    	$notify->link = $link;
    	$notify->function = $function; 
    }
}
