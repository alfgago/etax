<?php

namespace App;

use \App\Variables;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  
  public function productCategory() {
    return $this->belongsTo(Product::class, 'product_category_id');
  }
  
  public function getTipoIVAName(){
    return Variables::getTipoRepercutidoIVAName( $this->tipo_iva_defecto );
  }
  
  public function getTipoIVAPorc(){
    return Variables::getTipoRepercutidoIVAPorc( $this->tipo_iva_defecto );
  }
  
  public function getUnidadMedicionName(){
    return Variables::getUnidadMedicionName( $this->unidad_medicion );
  }
  
  
}
