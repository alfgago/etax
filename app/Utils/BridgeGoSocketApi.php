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
use \Carbon\Carbon;

class BridgeGoSocketApi
{
    protected $link = 'http://api.gosocket.net/';

    public function getUser($token) {
        try {
            $applicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($applicationIdGS.":".$token);
            $goSocket = new Client();
            $APIStatus = $goSocket->request('GET', $this->link."api/Gadget/GetUser", [
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
            $goSocket = new Client();
            $APIStatus = $goSocket->request('GET', $this->link."api/Gadget/GetAccount?accountId=".$user, [
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

    public function getSentDocuments($token, $companyToken, $tipoFactura, $queryDates) {
        try {
            $ApplicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($ApplicationIdGS . ":" . $token);
            $goSocket = new Client();
            $APIStatus = $goSocket->request('GET', $this->link . "api/Gadget/GetSentDocuments?MyAccountId=" . $companyToken . "&".$queryDates."&DocumentTypeId=".$tipoFactura['DocumentTypeId']."&ReceiverCode=-1&Number=-1&Page=1&ReadMode=json", [
                'headers' => [
                    'Content-Type' => "application/json",
                    'Accept' => "application/json",
                    'Authorization' => "Basic " . $base64
                ],
                'json' => [],
                'verify' => false,
            ]);
            return json_decode($APIStatus->getBody()->getContents(), true);

        } catch (\Exception $e) {
            Log::info('Error al traer invoices GoSocket -->>'. $e->getMessage());
            return [];
        }
    }

    public function getReceivedDocuments($token, $companyToken, $tipoFactura, $queryDates) {
        try {
            $ApplicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($ApplicationIdGS.":".$token);
            $goSocket = new Client();
            $APIStatus = $goSocket->request('GET', $this->link."api/Gadget/GetReceivedDocuments?MyAccountId=".$companyToken."&".$queryDates."&DocumentTypeId=".$tipoFactura['DocumentTypeId']."&ReceiverCode=-1&Number=-1&Page=1&ReadMode=json ", [
                'headers' => [
                    'Content-Type' => "application/json",
                    'Accept' => "application/json",
                    'Authorization' => "Basic " . $base64
                ],
                'json' => [],
                'verify' => false,
            ]);
            return json_decode($APIStatus->getBody()->getContents(), true);

        } catch (\Exception $e) {
            Log::info('Error al traer invoices GoSocket -->>'. $e->getMessage());
            return [];
        }
    }
    
    public function getQueryDates($dataIntegracion){
        $queryDates = [];
        $today = Carbon::parse(now('America/Costa_Rica'));
        if(isset($dataIntegracion->first_sync_gs) && $dataIntegracion->first_sync_gs == false) {
            Log::info("Ya tiene primer sync de recibidos con gosocket, generando mes anterior ");
            $first_date = Carbon::createFromFormat('Y-m-d H:i:s',
                $dataIntegracion->updated_at,
                'America/Costa_Rica'
            )->subDays(31)->toDateString();
            $second_date = Carbon::createFromFormat('Y-m-d H:i:s',
                $dataIntegracion->updated_at,
                'America/Costa_Rica'
            )->toDateString();
            $queryDates[] = "fromDate=$first_date&toDate=$second_date";
        } else {
            Log::info("Es el primer sync");
            $years = [2019,2020];
            $months = [1,2,3,4,5,6,7,8,9,10,11,12];
            foreach($years as $y){
                foreach($months as $m){
                    $dt = Carbon::create($y, $m, 1, 12, 0, 0);
                    $first_date = $dt->startOfMonth()->toDateString();
                    $second_date = $dt->endOfMonth()->toDateString();
                    $queryDates[] = "fromDate=$first_date&toDate=$second_date";
                }
            }
        }
        Log::debug( "Gosocket Dates: ". json_encode($queryDates) );
        return $queryDates;
    }

    public function getXML($token, $factura) {
        try {
            $ApplicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($ApplicationIdGS . ":" . $token);
            $goSocket = new Client();
            $APIStatus = $goSocket->request('GET', $this->link."api/Gadget/GetXml?DocumentId=".$factura."", [
                'headers' => [
                    'Content-Type' => "application/json",
                    'Accept' => "application/json",
                    'Authorization' => "Basic " . $base64
                ],
                'json' => [],
                'verify' => false,
            ]);

            return json_decode($APIStatus->getBody()->getContents(), true);

        } catch (\Exception $e) {
            Log::info('Error al traer invoices GoSocket -->>'. $e->getMessage());
            return false;
        }
    }

    public function getDocumentTypes($token) {
        try {
            $ApplicationIdGS = config('etax.applicationidgs');
            $base64 = base64_encode($ApplicationIdGS . ":" . $token);
            $goSocket = new Client();
            $APIStatus = $goSocket->request('GET', $this->link."api/Gadget/GetDocumentTypes", [
                'headers' => [
                    'Content-Type' => "application/json",
                    'Accept' => "application/json",
                    'Authorization' => "Basic " . $base64
                ],
                'json' => [],
                'verify' => false,
            ]);

            return json_decode($APIStatus->getBody()->getContents(), true);

        } catch (\Exception $e) {
            Log::info('Error al traer invoices GoSocket -->>'. $e->getMessage());
            return [];
        }
    }
}
