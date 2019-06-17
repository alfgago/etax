<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadMedicion extends Model
{
    public static function getUnidadMedicionName($value){
        $lista = UnidadMedicion::all()->toArray();

        $value = $value != '1' ? $value : 'Unid';
        $value = $value != '2' ? $value : 'Sp';
        
        if($value == 'Otros'){
          return $value;
        }

        foreach ($lista as $tipo) {
            if( $value == $tipo['code'] ){
                return $tipo['name'];
            }
        }
        return "Otros";
    }
}
