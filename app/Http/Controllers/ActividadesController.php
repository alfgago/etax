<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Actividades;

class ActividadesController extends Controller
{
    public function getAllActivities(){
        $activities = new Actividades();
        return $this->createResponse('200', 'OK' , 'Actividades comerciales.', $activities->getAllActivities());  
    }

    public function getActivities(Request $request){
    	if(!userHasCompany($request->empresa)){
        	return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.', []);
        }
        $company = Cache::get("cache-api-company-".$request->empresa);
        $activities = new Actividades();
        return $this->createResponse('200', 'OK' , 'Actividades comerciales.', $activities->getActivities($company->commercial_activities));  
    }

}





