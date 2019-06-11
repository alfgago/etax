<?php

namespace App;

use \App\Variables;
use \App\UnidadMedicion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
  use SoftDeletes;

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
    try{
      return UnidadMedicion::where('code', $this->measure_unit)->first()->name;
    }catch( \Throwable $e ){
      return $this->measure_unit;
    }
  }
  
  
}
