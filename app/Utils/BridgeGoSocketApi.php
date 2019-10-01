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
use mysql_xdevapi\Exception;


class BridgeGoSocketApi
{
    public $link = "http://api.sandbox.gosocket.net/";

    public function getUser($token) {
        try {
            $applicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($applicationIdGS.":".$token);
            $GoSocket = new Client();
            $APIStatus = $GoSocket->request('GET', $this->link."api/Gadget/GetUser", [
                'headers' => [
                    'Content-Type' => "application/json",
                    'Accept' => "application/json",
                    'Authorization' => "Basic " . $base64
                ],
                'json' => [
                ],
                'verify' => false
            ]);
            return json_decode($APIStatus->getBody()->getContents(), true);
        } catch ( \Exception $e) {
            Log::info('Error al iniciar session con GoSocket -->>'. $e->getMessage());
            return false;
        }
    }

    public function getAccount($token, $user) {
        try {
            $applicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($applicationIdGS.":".$token);
            $GoSocket = new Client();
            $APIStatus = $GoSocket->request('GET', $this->link."api/Gadget/GetAccount?accountId=".$user, [
                'headers' => [
                    'Content-Type' => "application/json",
                    'Accept' => "application/json",
                    'Authorization' => "Basic " . $base64
                ],
                'json' => [
                ],
                'verify' => false,
            ]);
             return json_decode($APIStatus->getBody()->getContents(), true);

        } catch ( \Exception $e) {
            Log::info('Error al traer cuenta GoSocket -->>'. $e->getMessage());
            return false;
        }
    }
}
