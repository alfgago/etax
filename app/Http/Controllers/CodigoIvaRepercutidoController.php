<?php

namespace App\Http\Controllers;

use App\CodigoIvaRepercutido;
use Illuminate\Http\Request;

class CodigoIvaRepercutidoController extends Controller
{
    public function getCode(){
        $codes = new CodigoIvaRepercutido();
        return $this->createResponse('200', 'OK' , 'Códigos para venta.', $codes->getCode());        
    }
}
