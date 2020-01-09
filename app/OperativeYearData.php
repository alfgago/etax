<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\CalculatedTax;
use App\Book;
use App\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OperativeYearData extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function getDataPeriodo() {
      
      $currentCompany = $this->company;
      $currentCompanyId = $currentCompany->id;
      $cacheKey = "cache-lasttaxes-$currentCompanyId-0-" . $this->year;
      
      if ( !Cache::has($cacheKey) ) {
      
        $data = CalculatedTax::firstOrNew(
            [
                'company_id' => $currentCompanyId,
                'month' => 0,
                'year' => $this->year,
                'is_final' => true,
            ]
        );
        
        if( !$data->is_closed ) {
            if($this->year == 2018 && $this->method == 2 ){
              $data->resetVars();
              $data->calcularFacturacion( 0, $this->year, 0, 1 );
              
              if( $data->count_invoices || $data->count_bills || $data->id ) {
                $data->save();
                $book = Book::calcularAsientos( $data );
                $book->save();
                $data->book = $book;
              }
            }else {
              $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $this->year, 0 );
              $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $this->year, 0 );
              $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $this->year, 0 );
              $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $this->year, 0 );
              $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $this->year, 0 );
              $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $this->year, 0 );
              $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $this->year, 0 );
              $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $this->year, 0 );
              $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $this->year, 0 );
              $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $this->year, 0 );
              $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $this->year, 0 );
              $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $this->year, 0 );
              $data->resetVars();
              $data->calcularFacturacionAcumulado( $this->year, 1 );
              if( $data->count_invoices || $data->count_bills ) {
                $data->save();
                $book = Book::calcularAsientos( $data );
                $book->save();
                $data->book = $book;
              }
            }
        }
        $data->prorrata = ($data->prorrata == 1) ? 0.9999 : $data->prorrata;

        Cache::put($cacheKey, $data, now()->addDays(120));

        if ( !$data->count_invoices ) {
            $this->prorrata_operativa = 0.9999;
            $this->operative_ratio1 = 0;
            $this->operative_ratio2 = 0;
            $this->operative_ratio3 = 1;
            $this->operative_ratio4 = 0;
            $this->operative_ratio8 = 0;
            $this->method = 1;
            $this->save();
            return true;
        }
        
        $this->prorrata_operativa = number_format( $data->prorrata, 4);
        $this->operative_ratio1 = number_format( $data->ratio1, 4);
        $this->operative_ratio2 = number_format( $data->ratio2, 4);
        $this->operative_ratio3 = number_format( $data->ratio3, 4);
        $this->operative_ratio4 = number_format( $data->ratio4, 4);
        
        $this->save();
        
      }
      
    }
    
}
