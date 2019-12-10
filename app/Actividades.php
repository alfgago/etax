<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actividades extends Model
{
    protected $guarded = [];

    use SoftDeletes;

	public function getAllActivities(){
    	$activities = Actividades::select('codigo','actividad','descripcion')->get();
    	return $activities;
    }

    public function getActivities($activities_company){
    	$activities_company = explode(", ",$activities_company);
    	$activities = Actividades::select('codigo','actividad','descripcion')
    		->whereIn("codigo",$activities_company)->get();
    	return $activities;
    }

}
