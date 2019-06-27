<?php

namespace App;

use \App\Variables;
use \App\UnidadMedicion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use SoftDeletes, Sortable;

    protected $guarded = [];
    public $sortable = ['code', 'name', 'measure_unit', 'unit_price'];

    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getName() {
        return $this->name . " - " . $this->measure_unit . " - " . $this->unit_price;
    }

    public function toString() {
        return $this->code . " - " . $this->getName();
    }

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
