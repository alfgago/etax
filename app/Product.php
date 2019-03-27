<?php

namespace App;

use \App\Variables;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  
  protected $guarded = [];
  
  public function productCategory() {
    return $this->belongsTo(ProductCategory::class, 'product_category_id');
  }
  
  public function getTipoIVAName(){
    return Variables::getTipoRepercutidoIVAName( $this->default_iva_type );
  }
  
  public function getTipoIVAPorc(){
    return Variables::getTipoRepercutidoIVAPorc( $this->default_iva_type );
  }
  
  public function getUnidadMedicionName(){
    return Variables::getUnidadMedicionName( $this->measure_unit );
  }
  
  
}
