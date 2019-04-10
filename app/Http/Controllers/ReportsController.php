<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\User;
use App\CalculatedTax;
use App\Variables;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
  
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    public function dashboard() {
      
      return view('/Dashboard/index');

    }
    
    public function reports() {
      
      return view('/Reports/index');

    }
    
    public function reporteDashboard( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, 2019, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, 2019, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, 2019, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, 2019, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, 2019, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, 2019, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, 2019, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, 2019, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, 2019, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, 2019, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, 2019, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, 2019, 0, $prorrataOperativa );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
      $nombreMes = Variables::getMonthName($mes);
      $dataMes = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
      
      return view('/Dashboard/reporte-dashboard', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes'));

    }
    
    public function reporteCuentasContables( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 0;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
      $data = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
      $nombreMes = Variables::getMonthName($mes);
      
      return view('/Reports/reporte-cuentas', compact('data', 'ano', 'nombreMes') );

    }
    
    public function reporteEjecutivo( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, 2019, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, 2019, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, 2019, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, 2019, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, 2019, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, 2019, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, 2019, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, 2019, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, 2019, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, 2019, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, 2019, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, 2019, 0, $prorrataOperativa );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
      $nombreMes = Variables::getMonthName($mes);
      $dataMes = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
      
      return view('/Reports/reporte-resumen-ejecutivo', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes'));

    }
    
    public function reporteDetalleDebitoFiscal( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, 2019, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, 2019, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, 2019, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, 2019, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, 2019, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, 2019, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, 2019, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, 2019, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, 2019, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, 2019, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, 2019, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, 2019, 0, $prorrataOperativa );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
      
      return view('/Reports/reporte-detalle-debito-fiscal', compact( 'acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'ano' ));

    }
    
    public function reporteDetalleCreditoFiscal( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, 2019, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, 2019, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, 2019, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, 2019, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, 2019, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, 2019, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, 2019, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, 2019, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, 2019, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, 2019, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, 2019, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, 2019, 0, $prorrataOperativa );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
      
      return view('/Reports/reporte-detalle-credito-fiscal', compact( 'acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'ano' ));

    }
    
    public function getProrrataOperativa($ano){
      $anoAnterior = $ano > 2018 ? $ano-1 : 2018;
      $anterior = CalculatedTax::calcularFacturacionPorMesAno( -1, $ano-1, 0, 0 );
      $prorrataOperativa = 0.88;
      
      return $prorrataOperativa;
    }
  
}
