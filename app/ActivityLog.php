<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends \Spatie\Activitylog\Models\Activity
{
    protected $guarded = [];
    protected $connection= 'log_db';
    protected $table = 'log_actividad';

    use SoftDeletes;
}
