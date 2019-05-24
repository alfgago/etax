<?php

namespace App;

use Mpociot\Teamwork\TeamworkTeam;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends TeamworkTeam {

    use SoftDeletes;
    
    protected $fillable = [
        'owner_id',
        'name',
        'company_id'
    ];
    
    //Relacion con la empresa
      public function company()
      {
          return $this->belongsTo(Company::class);
      }

}
