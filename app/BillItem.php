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
    }
 
}
