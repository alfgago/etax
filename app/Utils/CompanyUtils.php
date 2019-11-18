<?php

namespace App\Utils;


use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class CompanyUtils
{
	//devuelve los datos principales de la cedula que se esta procesando
	public function datosCedula($cedula){
		try{
			$client = new Client();
	        $response = $client->request('GET', 'https://apis.gometa.org/cedulas/'.$cedula.'&key=1AJTv1VNqFtSMpc');
	        $statusCode = $response->getStatusCode();
	        $body = $response->getBody();
	        $data = json_decode($body,true);
	        return $data['results'][0];
    	} catch ( \Exception $e) {
            Log::info('Error en consultar cedula en gometa: '. $e->getMessage());
            return null;
        }
	}

	public function datosHacienda($cedula){
		$client = new Client();
        $response = $client->request('GET', 'api.hacienda.go.cr/fe/ae?identificacion=186201160217');
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        //$data = json_decode($body,true);
        return $body;
	}

}