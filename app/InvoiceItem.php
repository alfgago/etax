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
