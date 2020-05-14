<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Log;
use App\Notification;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, LogsActivity;

	//$this->notificar(opcion, id, 'titulo', 'detalle', 'tipo', 'funcion','enlace');

 	protected function notificar($option, $id, $company, $title, $text = '', $type = 'info', $function = '', $link = '' ){ 
    	$notify = new Notification();
        Log::info($text);
    	return $notify->enviar($option, $id, $company, $title, $text, $type, $function, $link);
    }

     protected function createResponse($response_code, $status, $message = 'Mensaje',  $data = [],  $model = []) {
        if(is_array($model)){
            $pagination = null;
        }else{
            $pagination = [
               'desde'                  => $model->firstItem() ?? '',
               'hasta'                    => $model->lastItem() ?? '',
               'total'                 => $model->total() ?? '',
               'por_pagina'              => $model->perPage() ?? '',
               'pagina_actual'          => $model->currentPage() ?? '',
               'ultima_pagina'             => $model->lastPage() ?? '',
               'enlace_primera_pagina'        => $model->url(1) ?? '',
               'enlace_ultima_pagina'         => $model->url($model->lastPage()) ?? '',
               'enlace_pagina_anterior'     => $model->previousPageUrl() ?? '',
               'enlace_proxima_pagina'         => $model->nextPageUrl() ?? ''
           ];
        }
    	$json = [
                "estado" => $status,
                "mensaje" => $message,
                "datos" => $data,
                "paginacion" => $pagination
            ];
        return response()->json($json, $response_code)->header('Content-Lenght', mb_strlen(json_encode($json), '8bit') );
   }

    protected function validatePersonType($tipo_persona){
        if( $tipo_persona == 1 || $tipo_persona == 'F' || $tipo_persona == '01') {
            $tipo_persona = '01';
        }else if( $tipo_persona == 2 || $tipo_persona == 'J' || $tipo_persona == '02' ) {
            $tipo_persona = '02';
        }else if( $tipo_persona == 3 || $tipo_persona == 'D' || $tipo_persona == '03' ) {
            $tipo_persona = '03';
        }else if( $tipo_persona == 4 || $tipo_persona == 'E' || $tipo_persona == '04' ) {
            $tipo_persona = '04';
        }else if( $tipo_persona == 5 || $tipo_persona == 'N' || $tipo_persona == '05' ) {
            $tipo_persona = '05';
        }else if( $tipo_persona == 6 || $tipo_persona == 'O' || $tipo_persona == '06') {
            $tipo_persona = '06';
        }else{
            $tipo_persona = false;
        }
        return $tipo_persona;
    }


}
