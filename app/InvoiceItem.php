<?php

namespace App;

use App\Invoice;
use App\CodigoIvaRepercutido;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

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

      }catch(\Throwable $e){
        Log::error('No pudo asignar un codigo de producto a legacy bill. ' . $e->getMessage());
      }
    }
  
    public function fixCategoria() {
      try{
      
        $this->fixIvaType();

        $cat = $this->product_type;
        $alt = $this->product_type;
        $categorias = Cache::rememberForever ('cachekey-categorias-repercutidas', function () {
            return ProductCategory::whereNotNull('invoice_iva_code')->get();
        });

        $categoriaCorrecta = false;
        foreach( $categorias as $c ) {
          if (strpos($c->open_codes, $this->iva_type) !== false) {
            $alt = $c->id;
            if( $cat == $c->id){
              $categoriaCorrecta = true;
            }
          }
        }
        if( !$categoriaCorrecta ){
          $this->product_type = $alt;
          $this->save();
        }

      }catch(\Throwable $e){
        Log::error('No pudo asignar un codigo de producto a legacy bill. ' . $e->getMessage());
      }
    }
    
    
    public function getSubtotalParaCalculo() {
      if( $this->invoice->generation_method == 'XML' ) {
        if( $this->discount > 0){
          return $this->total - $this->iva_amount;
        }
      }
      return $this->subtotal;
    }
    
}
