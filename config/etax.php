<?php
/**
 * Created by PhpStorm.
 * User: xavierp
 * Date: 2019-06-10
 * Time: 20:35
 */
return [
    'exchange_url' =>  env('EXCHANGE_URL', 'https://gee.bccr.fi.cr/Indicadores/Suscripciones/WS/wsindicadoreseconomicos.asmx/ObtenerIndicadoresEconomicos'),
    'namebccr' => env('NAMEBCCR', 'Alfredo'),
    'emailbccr' => env('EMAILBCCR', 'alfredo@5e.cr'),
    'tokenbccr' => env('TOKENBCCR', '1DPLA072ER')
];