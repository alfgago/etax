<?php
/**
 * Created by PhpStorm.
 * User: xavierpernalete
 * Date: 4/1/20
 * Time: 3:11 PM
 */

namespace App\Http\Controllers\API;


use App\Actividades;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyAPIController extends Controller
{

    public function getActivities(Request $request)
    {

        $user = auth()->user();
        if (!userHasCompany($request->cedula)) {
            return $this->createResponse(403, 'ERROR', "Empresa no autorizada", []);
        }

        $company = Company::where('id_number', $request->cedula)->first();

        $actividades['actividades_empresa'] = $company->commercial_activities;
        $actividades['actividades'] = Actividades::all()->toArray();

        if (!empty($actividades)) {
            return $this->createResponse(201, 'SUCCESS',
                'Actividades', $actividades);
        } else {
            return $this->createResponse(400, 'ERROR', "No hay actividades", []);
        }
    }
}
