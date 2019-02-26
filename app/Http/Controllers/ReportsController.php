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
      
      $fromAcumulado = new Carbon('first day of June 2018');
      $toAcumulado = new Carbon('last day of December 2018');
      
      $fromAnterior = Carbon::now()->subMonth()->startOfMonth();
      $toAnterior = Carbon::now()->subMonth()->endOfMonth();
      
      $from = Carbon::now()->startOfMonth();
      $to = Carbon::now()->endOfMonth();
      
      $calculosAcumulados = CalculatedTax::calcularFacturacion( $fromAcumulado, $toAcumulado, 0, 0 );
      $calculosAnterior = CalculatedTax::calcularFacturacion( $fromAnterior, $toAnterior, 0, $calculosAcumulados->prorrata );
      $calculos = CalculatedTax::calcularFacturacion( $from, $to, 0, $calculosAcumulados->prorrata );
      
      return view('/dashboard', compact('calculos', 'calculosAnterior', 'calculosAcumulados'));

    }
  
}
