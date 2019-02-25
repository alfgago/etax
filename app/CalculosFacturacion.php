<?php

namespace App;

use \Carbon\Carbon;
use App\LineaFacturaEmitida;
use App\LineaFacturaRecibida;
use App\FacturaEmitida;
use App\FacturaRecibida;
use App\Empresa;
use Illuminate\Database\Eloquent\Model;

class CalculosFacturacion extends Model
{
    
    protected $guarded = [];
  
    //Recibe fecha de inicio y fecha de fin en base a las cuales se desea calcular la prorrata.
    public static function calcularFacturacion( $from, $to, $saldoAnterior, $prorrata_anterior ) {
      
      $calculos = new CalculosFacturacion();
      
      $facturasEmitidas = LineaFacturaEmitida::whereHas('factura', function ($query) use ($from, $to){
        $query->whereBetween('fecha_generada', [$from, $to]);
      })->get();
      
      $facturasRecibidas = LineaFacturaRecibida::whereHas('factura', function ($query) use ($from, $to){
        $query->whereBetween('fecha_recibida', [$from, $to]);
      })->get();
      
      $calculos->count_emitidas = count( $facturasEmitidas );
      $calculos->count_recibidas = count( $facturasRecibidas );
      
      $calculos->prorrata = 0;
      $importeIVA = 0;
      $calculos->iva_no_deducible = 0;
      $calculos->iva_deducible = 0;
      $calculos->subtotal_recibido = 0;
      $calculos->subtotal_emitido = 0;
      $calculos->total_emitido_sin_exencion = 0;
      $calculos->total_iva_repercutido = 0;
      $calculos->total_iva_soportado = 0;
      $iva100deducible = 0;
      
      $sumaRepercutido1 = 0;
      $sumaRepercutido2 = 0;
      $sumaRepercutido3 = 0;
      $sumaRepercutido4 = 0;
      $sumaRepercutidoEx = 0;
      
      $calculos->ratio1 = 0;
      $calculos->ratio2 = 0;
      $calculos->ratio3 = 0;
      $calculos->ratio4 = 0;
      
      $calculos->liquidacion = 0;
      
      //Recorre todas las facturas emitidas y aumenta los montos corresponsientes.
      for ($i = 0; $i < $calculos->count_emitidas; $i++) {
        $subtotal = $facturasEmitidas[$i]->subtotal;
        
        if( $facturasEmitidas[$i]->tipo_iva > 199 ){
          $calculos->total_emitido_sin_exencion += $subtotal;
        }
        $calculos->subtotal_emitido += $subtotal;
        $calculos->total_iva_repercutido += $subtotal * $facturasEmitidas[$i]->porc_iva / 100;
        
        //Suma los del 1%
        if( $facturasEmitidas[$i]->tipo_iva == 101 || $facturasEmitidas[$i]->tipo_iva == 121 || $facturasEmitidas[$i]->tipo_iva == 141 ){
          $sumaRepercutido1 += $subtotal;
        }
        
        //Suma los del 2%
        if( $facturasEmitidas[$i]->tipo_iva == 102 || $facturasEmitidas[$i]->tipo_iva == 122 || $facturasEmitidas[$i]->tipo_iva == 142 ){
          $sumaRepercutido2 += $subtotal;
        }
        
        //Suma los del 13%
        if( $facturasEmitidas[$i]->tipo_iva == 103 || $facturasEmitidas[$i]->tipo_iva == 123 || $facturasEmitidas[$i]->tipo_iva == 143 || $facturasEmitidas[$i]->tipo_iva == 130 ){
          $sumaRepercutido3 += $subtotal;
        }
        
        //Suma los del 4%
        if( $facturasEmitidas[$i]->tipo_iva == 104 || $facturasEmitidas[$i]->tipo_iva == 124 || $facturasEmitidas[$i]->tipo_iva == 144 ){
          $sumaRepercutido4 += $subtotal;
        }
        
        //Suma los del exentos. Estos se suman como si fueran 13 para efectos del cálculo.
        if( $facturasEmitidas[$i]->tipo_iva == 150 || $facturasEmitidas[$i]->tipo_iva == 160 || $facturasEmitidas[$i]->tipo_iva == 199 ){
          $sumaRepercutido3 += $subtotal;
        }
        
      }
      
      //Recorre todas las facturas recibidas y aumenta los montos corresponsientes.
      for ($i = 0; $i < $calculos->count_recibidas; $i++) {
        $calculos->subtotal_recibido += $facturasRecibidas[$i]->subtotal;
        $calculos->total_iva_soportado += $facturasRecibidas[$i]->subtotal * $facturasRecibidas[$i]->porc_iva / 100;
        
        if( $facturasRecibidas[$i]->tipo_iva == '61' || $facturasRecibidas[$i]->tipo_iva == '62' || $facturasRecibidas[$i]->tipo_iva == '63' || $facturasRecibidas[$i]->tipo_iva == '64' )
        {
          $iva100deducible += $facturasRecibidas[$i]->subtotal;
        }
      }
      //Resta el 100% deducible al subtotal, para que no sea usado en prorrata.
      $calculos->subtotal_recibido = $calculos->subtotal_recibido - $iva100deducible;
      
      //Determina numerador y denominador.
      $numeradorProrrata = $calculos->subtotal_emitido - $calculos->total_emitido_sin_exencion;
      $denumeradorProrrata = $calculos->subtotal_emitido;
      
      
      if( $calculos->subtotal_emitido > 0 ){
        $calculos->prorrata = $numeradorProrrata / $denumeradorProrrata;
        //$importeIVA = $calculos->total_iva_repercutido - ( $calculos->total_iva_soportado * $calculos->prorrata );
        
        $calculos->ratio1 = $sumaRepercutido1 / $numeradorProrrata;
        $calculos->ratio2 = $sumaRepercutido2 / $numeradorProrrata;
        $calculos->ratio3 = $sumaRepercutido3 / $numeradorProrrata;
        $calculos->ratio4 = $sumaRepercutido4 / $numeradorProrrata;
        
        $calculos->iva_deducible = ($calculos->subtotal_recibido*$calculos->ratio1*0.01 + $calculos->subtotal_recibido*$calculos->ratio2*0.02 + $calculos->subtotal_recibido*$calculos->ratio3*0.13 + $calculos->subtotal_recibido*$calculos->ratio4*0.04) * $calculos->prorrata ;
        $calculos->iva_no_deducible = $calculos->total_iva_soportado - $calculos->iva_deducible;
        $calculos->liquidacion = -$saldoAnterior + $calculos->total_iva_repercutido - $calculos->iva_deducible;

        $calculos->iva_deducible_anterior = ($calculos->subtotal_recibido*$calculos->ratio1*0.01 + $calculos->subtotal_recibido*$calculos->ratio2*0.02 + $calculos->subtotal_recibido*$calculos->ratio3*0.13 + $calculos->subtotal_recibido*$calculos->ratio4*0.04) * $prorrata_anterior ;
        $calculos->liquidacion_anterior = -$saldoAnterior + $calculos->total_iva_repercutido - $calculos->iva_deducible_anterior;
        
      }
      
      return $calculos;
    }
  
}
