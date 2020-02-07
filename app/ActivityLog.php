<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    protected $guarded = [];
    protected $connection= 'log_db';
    protected $table = 'activity_log';

    use SoftDeletes;
}
