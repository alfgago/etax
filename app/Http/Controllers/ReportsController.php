<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\User;
use App\CalculatedTax;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function dashboard() {
      
      $anterior = CalculatedTax::calcularFacturacionPorMesAno( -1, 2018, 0, 0 );
      $anterior->prorrata = 0.7837;
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, 2019, 0, $anterior->prorrata );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, 2019, 0, $anterior->prorrata );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, 2019, 0, $anterior->prorrata );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, 2019, 0, $anterior->prorrata );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, 2019, 0, $anterior->prorrata );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, 2019, 0, $anterior->prorrata );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, 2019, 0, $anterior->prorrata );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, 2019, 0, $anterior->prorrata );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, 2019, 0, $anterior->prorrata );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, 2019, 0, $anterior->prorrata );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, 2019, 0, $anterior->prorrata );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, 2019, 0, $anterior->prorrata );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, 2019, 0, $anterior->prorrata );
      
      
      return view('/dashboard', compact( 'anterior', 'acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd'));

    }
  
}
