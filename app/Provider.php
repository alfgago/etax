<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
  
  //Relacion con la empresa
  public function company()
  {
      return $this->belongsTo(Company::class);
  }
    
  public function getFullName() {
    return $this->first_name . " " . $this->last_name . " " . $this->last_name2;
  }

  public function getTipoPersona() {
    $tipoStr = 'Física';
    if( $this->tipo_persona == 1 ) {
      $tipoStr = 'Física';
    }else if( $this->tipo_persona == 2 ) {
      $tipoStr = 'Jurídica';
    }else if( $this->tipo_persona == 3 ) {
      $tipoStr = 'DIMEX';
    }else if( $this->tipo_persona == 4 ) {
      $tipoStr = 'Extranjero';
    }else if( $this->tipo_persona == 5 ) {
      $tipoStr = 'NITE';
    }else if( $this->tipo_persona == 6 ) {
      $tipoStr = 'Otro';
    }
  }
  
}
