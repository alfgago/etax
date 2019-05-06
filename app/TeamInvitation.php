<?php

namespace App;

use Mpociot\Teamwork\TeamInvite;

class TeamInvitation extends TeamInvite {

    protected $guarded = [];
    
    public function team() {
        return $this->belongsTo('App\Team');
    }

}
