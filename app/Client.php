<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Client extends Model
{
  use Sortable, SoftDeletes;
  
  protected $guarded = [];
  public $sortable = ['code', 'first_name', 'email', 'id_number'];
  
  //Relacion con la empresa
  public function company()
  {
      return $this->belongsTo(Company::class);
  }
    
  public function getFullName() {
    return $this->first_name . " " . $this->last_name . " " . $this->last_name2;
  }
  
  public function toString() {
    return $this->id_number . " - " . $this->getFullName();
  }
  

  public function canInvoice( $tipoDoc = '01' ) {
    $allow = true;
    if(empty($this->email)) {
      $allow = false;
    }
    
    if( $tipoDoc == '04' ){
      if( empty($this->district) && $this->country == 'CR' ) {
        $allow = false;
      }
      if( empty($this->zip) && $this->country == 'CR' ) {
        $allow = false;
      }
    }elseif( $tipoDoc != '09' ){
        if( empty($this->district) ) {
          $allow = false;
        }
        if( empty($this->zip) ) {
          $allow = false;
        }
    }else {
      if( $this->country == 'CR' ) {
        $allow = false;
      }
    }

    return $allow;
  }

  public function getTipoPersona() {
    $tipoStr = 'Física';
    if( $this->tipo_persona == 1 || $this->tipo_persona == 'F' ) {
      $tipoStr = 'Física';
    }else if( $this->tipo_persona == 2 || $this->tipo_persona == 'J' ) {
      $tipoStr = 'Jurídica';
    }else if( $this->tipo_persona == 3 || $this->tipo_persona == 'D' ) {
      $tipoStr = 'DIMEX';
    }else if( $this->tipo_persona == 4 || $this->tipo_persona == 'E' ) {
      $tipoStr = 'Extranjero';
    }else if( $this->tipo_persona == 5 || $this->tipo_persona == 'N' ) {
      $tipoStr = 'NITE';
    }else if( $this->tipo_persona == 6 || $this->tipo_persona == 'O') {
      $tipoStr = 'Otro';
    }
    return $tipoStr;
  }
  
}
