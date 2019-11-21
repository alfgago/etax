<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    protected $fillable = [
        'user_id',
        'notification_id',
        'status'
    ];
}
