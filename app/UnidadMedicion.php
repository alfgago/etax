<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadMedicion extends Model
{
    public static function getUnidadMedicionName($value){
        $lista = UnidadMedicion::all()->toArray();

        foreach ($lista as $tipo) {
            if( $value == $tipo['code'] ){
                return $tipo['name'];
            }
        }
        return "Otros";
    }
}
