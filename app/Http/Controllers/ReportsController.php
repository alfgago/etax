<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\LineaFacturaEmitida;
use App\LineaFacturaRecibida;
use App\FacturaEmitida;
use App\FacturaRecibida;
use App\Empresa;
use App\CalculosFacturacion;
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
      
      $calculosAcumulados = CalculosFacturacion::calcularFacturacion( $fromAcumulado, $toAcumulado, 0, 0 );
      /*$calculosAnterior = CalculosFacturacion::calcularFacturacion( $fromAnterior, $toAnterior, -$calculosAcumulados->liquidacion, $calculosAcumulados->prorrata );
      $calculos = CalculosFacturacion::calcularFacturacion( $from, $to, -$calculosAnterior->liquidacion, $calculosAcumulados->prorrata );*/
      
      $calculosAnterior = CalculosFacturacion::calcularFacturacion( $fromAnterior, $toAnterior, 0, $calculosAcumulados->prorrata );
      $calculos = CalculosFacturacion::calcularFacturacion( $from, $to, 0, $calculosAcumulados->prorrata );
      
      return view('/dashboard', compact('calculos', 'calculosAnterior', 'calculosAcumulados'));
      
    }
  
}
