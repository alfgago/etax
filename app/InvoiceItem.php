<?php

namespace App;

use App\Invoice;
use App\CodigoIvaRepercutido;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
  
    //Relacion con el cliente
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    
    public function ivaType() {
      return $this->belongsTo(CodigoIvaRepercutido::class, 'iva_type');
    }
    
    public function productCategory() {
      return $this->belongsTo(ProductCategory::class, 'product_type');
    }

    public function fixIvaType() {
      $initial = $this->iva_type[0];
      if( $initial != 'S' && $initial != 'B' && 
          $this->iva_type != '098' && $this->iva_type != '099' ){
          $um = $this->measure_unit;
          if($um == 'Sp' || $um == 'Spe' || $um == 'St' || $um == 'Al' || $um == 'Alc' || $um == 'Cm' || $um == 'I' || $um == 'Os'){
            $this->iva_type = "S$this->iva_type";
          }else{
            $this->iva_type = "B$this->iva_type";
          }
          $this->save();
      }
      //Asigna Prod Type;
      $cat = $this->product_type;
      if( !$cat || $cat > 49 || ( $cat < 6 && $this->iva_percentage != 1 )
          || ( $cat > 6 && $cat <= 10 && $this->iva_percentage != 2 )
          || ( $cat > 10 && $cat <= 14 && $this->iva_percentage != 4 ) 
          || ( $cat > 14 && $cat <= 19  && $this->iva_percentage != 13 ) ){
        $cat = ProductCategory::where('invoice_iva_code', $this->iva_type)->first();
        if( $cat ){
          $this->product_type = $cat->id;
        }else{
          foreach( ProductCategory::get() as $c ) {
            if (strpos($c->open_codes, $this->iva_type) !== false) {
              $this->product_type = $c->id;
            }
          }
        }
        $this->save();
      }
      
      if($cat == 'Plan') {
        $this->product_type = 17;
        $this->save();
      }
    
    }
    
}
