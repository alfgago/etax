<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class GoSocketController extends Controller
{
     
    public function gosocketvalidate(){
    	$token = $_GET['token'];
    	$ApplicationIdGS = config('etax.applicationidgs');
		$base64 = base64_encode($ApplicationIdGS.":".$token);
    	$GoSocket = new Client();
	    $APIStatus = $GoSocket->request('GET', "http://api.sandbox.gosocket.net/api/Gadget/GetUser", [
	        'headers' => [
	            'Content-Type' => "application/json",
	            'Accept' => "application/json", 
	            'Authorization' => "Basic " . $base64
	        ],
	        'json' => [
	        ],
	        'verify' => false,
	    ]);
	    $APIStatus = json_decode($APIStatus->getBody()->getContents(), true);
	    dd($APIStatus['UserId']);
	    
    }
     
    
    public function getInvoices(){
    	$token = $_GET['token'];
    	$ApplicationIdGS = config('etax.applicationidgs');
		$base64 = base64_encode($ApplicationIdGS.":".$token);
    	$GoSocket = new Client();
	    $APIStatus = $GoSocket->request('GET', "http://api.sandbox.gosocket.net/api/Gadget/GetUser", [
	        'headers' => [
	            'Content-Type' => "application/json",
	            'Accept' => "application/json", 
	            'Authorization' => "Basic " . $base64
	        ],
	        'json' => [
	        ],
	        'verify' => false,
	    ]);
	    $APIStatus = json_decode($APIStatus->getBody()->getContents(), true);
	    dd($APIStatus);
	    
    }



}
