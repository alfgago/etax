<?php

namespace App;

use App\Bill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillItem extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    //Relacion con el cliente
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
    
    public function ivaType() {
      return $this->belongsTo(CodigoIvaSoportado::class, 'iva_type');
    }    
    
    public function fixIvaType() {
      try{
        
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
        if( !$cat || $cat < 49 ){
          $cat = ProductCategory::where('bill_iva_code', $this->iva_type)->first();
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
          dd($this->id);
        }
        
      }catch(\Throwable $e){
        Log::warning('No pudo asignar un codigo de producto a legacy bill. ' . $e->getMessage());
      }
    }
 
}
