<?php
/**
 * Created by PhpStorm.
 * User: xavierp
 * Date: 2019-06-10
 * Time: 20:35
 */
return [
    //Setting Rates Banco Central de Costa Rica
    'exchange_url' =>  env('EXCHANGE_URL', 'https://gee.bccr.fi.cr/Indicadores/Suscripciones/WS/wsindicadoreseconomicos.asmx/ObtenerIndicadoresEconomicos'),
    'namebccr' => env('NAMEBCCR', 'Alfredo'),
    'emailbccr' => env('EMAILBCCR', 'alfredo@5e.cr'),
    'tokenbccr' => env('TOKENBCCR', '1DPLA072ER'),
    //Setting API Hacienda
    'api_hacienda_url' => env('API_HACIENDA_URL', 'http://etaxhaciendaelb-28215484.us-east-1.elb.amazonaws.com'),
    'api_hacienda_client' => env('API_HACIENDA_CLIENT', 'frontend-client'),
    'api_hacienda_key' => env('API_HACIENDA_KEY', 'simplerestapi'),
    'api_hacienda_user_id' => env('API_HACIENDA_USER_ID', '1'),
    'api_hacienda_username' => env('API_HACIENDA_USERNAME', 'admin'),
    'api_hacienda_password' => env('API_HACIENDA_PASSWORD', 'Cc1ksNQyl<KOrT'),
    'hacienda_ambiente' => env('HACIENDA_AMBIENTE', '01'),
    //Ambiente Pagos en línea
    'klap_app_name' => env('KLAP_APP_NAME', 'ETAX TEST'),
    'klap_app_password' => env('KLAP_APP_PASSWORD', 'ETFTTJUN0619%'),
    'applicationidgs' => env('APPLICATIONIDGS', '39d67874-d18d-4a38-a5b6-c3e6c2bbd1fa'),
    'merchant_id' => env('MERCHANT_ID', 'tc_cr_011007172'),
    'cybersource_transaction_key' => env('CYBERSOURCE_TRANSACTION_KEY'),
    'cybersource_url_wsdl' => env('CYBERSOURCE_URL_WSDL'),
    'cybersource_url_nvp_wsdl' => env('CYBERSOURCE_URL_NVP_WSDL'),
    'gosocket_url' => env('GOSOCKETURL', 'http://api.sandbox.gosocket.net/'),
    'queue_connections' => env('QUEUE_CONNECTION', 'redis')
];
