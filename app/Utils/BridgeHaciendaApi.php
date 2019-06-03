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


class BridgeHaciendaApi
{
    public function login() {
        try {
            $client = new Client();
            $result = $client->request('POST', env('API_HACIENDA_URL') . '/index.php/auth/login', [
                'headers' => [
                    'Auth-Key'  => 'simplerestapi',
                    'Client-Service' => 'frontend-client',
                    'Connection' => 'Close'
                ],
                'json' => ["username" =>env('API_HACIENDA_USERNAME'),
                    "password" => env('API_HACIENDA_PASSWORD')], 'verify' => false,
            ]);

            return json_decode($result->getBody()->getContents(), true);
        } catch (ClientException $error) {
            return false;
        }
    }

}