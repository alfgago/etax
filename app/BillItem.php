<?php

namespace App;

use App\Bill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BillItem extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }  
    
    //Relacion con el cliente
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
    
    public function ivaType() {
      return $this->belongsTo(CodigoIvaSoportado::class, 'iva_type');
    }    
    
    public function productCategory() {
      return $this->belongsTo(ProductCategory::class, 'product_type');
    }
    
    public function getRealIVAPercentage(){
      if( $this->iva_amount ){
        return round($this->iva_amount/$this->subtotal * 100, 0);
      }else{
        return 0;
      }
    }
    
    public function fixIvaType() {
      try{
        if( $this->bill->document_type != '04' ){
          $initial = $this->iva_type[0];
          if( $initial != 'S' && $initial != 'B' &&  $initial != 'R' && 
              $this->iva_type != '098' && $this->iva_type != '099' ){
              $um = $this->measure_unit;
              if($um == 'Sp' || $um == 'Spe' || $um == 'St' || $um == 'Al' || $um == 'Alc' || $um == 'Cm' || $um == 'I' || $um == 'Os'){
                $this->iva_type = "S$this->iva_type";
              }else{
                $this->iva_type = "B$this->iva_type";
              }
          }
          
          if( preg_match('/\s/', $this->iva_type) ){
            $this->iva_type = trim($this->iva_type);
            $this->save();
          }
        }else{
          if( $this->iva_type != 'S097' || $this->iva_type != 'B097' ) {
            $um = $this->measure_unit;
            if($um == 'Sp' || $um == 'Spe' || $um == 'St' || $um == 'Al' || $um == 'Alc' || $um == 'Cm' || $um == 'I' || $um == 'Os'){
              $this->iva_type = 'S097';
            }else{
              $this->iva_type = 'B097';
            }
            $this->product_type = '57';
            $this->save();
          }
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
            return ProductCategory::whereNotNull('bill_iva_code')->get();
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
    
    function calcularAcreditablePorLinea(){
      try{
        $company = $this->company;
        $prorrataOperativa = $company->getProrrataOperativa( $this->year );
        $calc = new CalculatedTax();
        $calc->year = $this->year;
        $calc->month = $this->month;
        $calc->resetVars();
        $query = BillItem::with('bill')->where('id', $this->id);
        $calc->setDatosSoportados( $this->month, $this->year, $company->id, $query, true );
        $calc->setCalculosPorFactura( $prorrataOperativa, 0 );
  
        $this->iva_acreditable = round($calc->iva_deducible_operativo,2);
        $this->iva_gasto = round($calc->iva_no_deducible,2);
        $this->iva_devuelto = round($calc->iva_devuelto,2);
        
        $this->save();
      }catch(\Throwable $e){
        Log::error('No pudo calcular el IVA acreditable de la linea  ' . $e->getMessage());
      }
    }
 
}
