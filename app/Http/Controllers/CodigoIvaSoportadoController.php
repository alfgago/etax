<?php

namespace App\Http\Controllers;

use App\CodigoIvaSoportado;
use Illuminate\Http\Request;

class CodigoIvaSoportadoController extends Controller
{
    public function getCode(){
        $codes = new CodigoIvaSoportado();
        return $this->createResponse('200', 'OK' , 'Códigos para compra.', $codes->getCode());        
    }
}
