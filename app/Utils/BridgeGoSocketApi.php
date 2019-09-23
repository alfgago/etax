<?php
/**
 * Created by PhpStorm.
 * User: xavierp
 * Date: 2019-05-31
 * Time: 23:28
 */

namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Log;


class BridgeGoSocketApi
{
    public function login($token) {
        try {
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
            $user_gs = json_decode($APIStatus->getBody()->getContents(), true);

        } catch (ClientException $error) {
            Log::info('Error al iniciar session en API HACIENDA -->>'. $error->getMessage() );
            return false;
        }
    }
}
