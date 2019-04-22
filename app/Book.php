<?php

namespace App;

use App\Company;
use App\CalculatedTax;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];
    
    //Relacion con la empresa
    public function company()
    {
        return $calculos->belongsTo(Company::class);
    }
    
    public function calculos()
    {
        return $calculos->belongsTo(CalculatedTax::class, 'calculated_tax_id');
    }
    
    public static function calcularAsientos( $calculos ){
        
        $book = Book::firstOrNew(
              [
                  'calculated_tax_id' => $calculos->id
              ]
        );
        
        $book->company_id = $calculos->company_id;
        $book->setCuentaContableCompras( $calculos );
        $book->setCuentaContableVentas( $calculos );
        $book->setCuentaContableAjustes( $calculos );
        
        return $book;
        
    }
    
    public function setCuentaContableCompras( $calculos ){
      //Debe 1
      $this->cc_compras = $calculos->b001 + $calculos->b002 + $calculos->b003 + $calculos->b004 + 
                              $calculos->b061 + $calculos->b062 + $calculos->b063 + $calculos->b064 + $calculos->b060; 
                              
      $this->cc_importaciones = $calculos->b021 + $calculos->b022 + $calculos->b023 + $calculos->b024 +
                                    $calculos->b041 + $calculos->b042 + $calculos->b043 + $calculos->b044 + $calculos->b040;
      
      $this->cc_propiedades = $calculos->b011 + $calculos->b012 + $calculos->b013 + $calculos->b014 + 
                              $calculos->b031 + $calculos->b032 + $calculos->b033 + $calculos->b034 +
                              $calculos->b051 + $calculos->b052 + $calculos->b053 + $calculos->b054 + $calculos->b050 +
                              $calculos->b071 + $calculos->b072 + $calculos->b073 + $calculos->b074 + $calculos->b070;
      
      $this->cc_iva_compras = $calculos->i001 + $calculos->i002 + $calculos->i003 + $calculos->i004 + 
                              $calculos->i061 + $calculos->i062 + $calculos->i063 + $calculos->i064;
                                  
                                  
      $this->cc_iva_importaciones = $calculos->i021 + $calculos->i022 + $calculos->i023 + $calculos->i024 +
                                    $calculos->i041 + $calculos->i042 + $calculos->i043 + $calculos->i044;
                                        
      $this->cc_iva_propiedades = $calculos->i011 + $calculos->i012 + $calculos->i013 + $calculos->i014 + 
                                  $calculos->i031 + $calculos->i032 + $calculos->i033 + $calculos->i034 +
                                  $calculos->i051 + $calculos->i052 + $calculos->i053 + $calculos->i054 +
                                  $calculos->i071 + $calculos->i072 + $calculos->i073 + $calculos->i074;  
                                    
      $this->cc_compras_sin_derecho = $calculos->b080 + $calculos->b090 + $calculos->b097 + 
                              $calculos->i080 + $calculos->i090 + $calculos->i097;                                
      
    //Haber 1
      $this->cc_proveedores_credito = $calculos->total_proveedores_credito;
      $this->cc_proveedores_contado = $calculos->total_proveedores_contado;
    }
    
    public function setCuentaContableVentas( $calculos ){
      //Haber 2 
      $this->cc_ventas_1 = $calculos->b101 + $calculos->b121;
      $this->cc_ventas_2 = $calculos->b102 + $calculos->b122;
      $this->cc_ventas_13 = $calculos->b103 + $calculos->b123;
      $this->cc_ventas_4 = $calculos->b104 + $calculos->b124;
      $this->cc_ventas_exp = $calculos->b150;
      $this->cc_ventas_estado = $calculos->b160;
      $this->cc_ventas_1_iva = $calculos->i101 + $calculos->i121;
      $this->cc_ventas_2_iva = $calculos->i102 + $calculos->i122;
      $this->cc_ventas_13_iva = $calculos->i103 + $calculos->i123 + $calculos->i130;
      $this->cc_ventas_4_iva = $calculos->i104 + $calculos->i124;
      $this->cc_ventas_sin_derecho = $calculos->b200 + $calculos->b201 + $calculos->b240 + $calculos->b250 + $calculos->b260;
      $this->cc_ventas_sum = $this->cc_ventas_1 + $this->cc_ventas_2 + $this->cc_ventas_13 + $this->cc_ventas_4 + 
                                 $this->cc_ventas_1_iva + $this->cc_ventas_2_iva + $this->cc_ventas_13_iva + $this->cc_ventas_4_iva + 
                                 $this->cc_ventas_exp + $this->cc_ventas_estado + $this->cc_ventas_sin_derecho;
      
    //Debe 2
      $this->cc_clientes_credito = $calculos->total_clientes_credito;
      $this->cc_clientes_contado = $calculos->total_clientes_contado;  
      $this->cc_clientes_credito_exp = $calculos->total_clientes_credito_exp;
      $this->cc_clientes_contado_exp = $calculos->total_clientes_contado_exp;  
      $this->cc_clientes_sum = $this->cc_clientes_credito + $this->cc_clientes_contado + $this->cc_clientes_credito_exp + $this->cc_clientes_contado_exp;
        
    }
    
    public function setCuentaContableAjustes( $calculos ){
      //Haber 3
        $this->cc_ppp_1 = $calculos->i011 + $calculos->i031 + $calculos->i051 + $calculos->i071;
        $this->cc_ppp_2 = $calculos->i012 + $calculos->i032 + $calculos->i052 + $calculos->i072;
        $this->cc_ppp_3 = $calculos->i013 + $calculos->i033 + $calculos->i053 + $calculos->i073;
        $this->cc_ppp_4 = $calculos->i014 + $calculos->i034 + $calculos->i054 + $calculos->i074;
        
        $this->cc_bs_1 = $calculos->i001 + $calculos->i021 + $calculos->i041 + $calculos->i061;
        $this->cc_bs_2 = $calculos->i002 + $calculos->i022 + $calculos->i042 + $calculos->i062;
        $this->cc_bs_3 = $calculos->i003 + $calculos->i023 + $calculos->i043 + $calculos->i063;
        $this->cc_bs_4 = $calculos->i004 + $calculos->i024 + $calculos->i044 + $calculos->i064;
        
        $this->cc_por_pagar = $calculos->balance_operativo;
      
      //Debe 3 
        $this->cc_iva_emitido_1 = $calculos->i101 + $calculos->i121;
        $this->cc_iva_emitido_2 = $calculos->i102 + $calculos->i122;
        $this->cc_iva_emitido_3 = $calculos->i103 + $calculos->i123;
        $this->cc_iva_emitido_4 = $calculos->i104 + $calculos->i124;  
        
        $bases_ppp = $calculos->b011 + $calculos->b031
        + $calculos->b012 + $calculos->b032 + 0 
        + $calculos->b013 + $calculos->b033 +
        + $calculos->b014 + $calculos->b034;
        
        $bases_bs = $calculos->b001 + $calculos->b021
        + $calculos->b002 + $calculos->b022
        + $calculos->b003 + $calculos->b023
        + $calculos->b004 + $calculos->b024;
        
        $this->cc_aj_ppp_1 = $calculos->i011 + $calculos->i031;
        $this->cc_aj_ppp_2 = $calculos->i012 + $calculos->i032;
        $this->cc_aj_ppp_3 = $calculos->i013 + $calculos->i033;
        $this->cc_aj_ppp_4 = $calculos->i014 + $calculos->i034;
        
        $this->cc_aj_bs_1 = $calculos->i001 + $calculos->i021 ;
        $this->cc_aj_bs_2 = $calculos->i002 + $calculos->i022 ;
        $this->cc_aj_bs_3 = $calculos->i003 + $calculos->i023 ;
        $this->cc_aj_bs_4 = $calculos->i004 + $calculos->i024 ;
        
        $acreditable_bs = ( ($bases_bs * $calculos->ratio1 * 0.01) + ($bases_bs * $calculos->ratio2 * 0.02) + ($bases_bs * $calculos->ratio3 * 0.13) + ($bases_bs * $calculos->ratio4 * 0.04) ) * $calculos->prorrata_operativa;
        $acreditable_ppp = ( ($bases_ppp * $calculos->ratio1 * 0.01) + ($bases_ppp * $calculos->ratio2 * 0.02) + ($bases_ppp * $calculos->ratio3 * 0.13) + ($bases_ppp * $calculos->ratio4 * 0.04) ) * $calculos->prorrata_operativa;

        $this->cc_ajuste_ppp =  - $acreditable_ppp + $this->cc_aj_ppp_1 + $this->cc_aj_ppp_2 + + $this->cc_aj_ppp_3 + + $this->cc_aj_ppp_4; 
        $this->cc_ajuste_bs =  - $acreditable_bs + $this->cc_aj_bs_1 + $this->cc_aj_bs_2 + $this->cc_aj_bs_3 + $this->cc_aj_bs_4;
        
        $this->cc_gasto_no_acreditable = $calculos->iva_no_acreditable_identificacion_plena;
        
        if( $this->cc_por_pagar > 0 ) {
            $this->cc_sum2 = $this->cc_ppp_1 + $this->cc_ppp_2 + $this->cc_ppp_3 + $this->cc_ppp_4 + $this->cc_por_pagar
                             + $this->cc_bs_1 + $this->cc_bs_2 + $this->cc_bs_3 + $this->cc_bs_4;
            $this->cc_sum1 = $this->cc_iva_emitido_1 + $this->cc_iva_emitido_2 + $this->cc_iva_emitido_3 + 
                                  $this->cc_iva_emitido_4 + $this->cc_ajuste_ppp + $this->cc_ajuste_bs + $this->cc_gasto_no_acreditable;
        }else {
            $this->cc_sum2 = $this->cc_ppp_1 + $this->cc_ppp_2 + $this->cc_ppp_3 + $this->cc_ppp_4 
                             + $this->cc_bs_1 + $this->cc_bs_2 + $this->cc_bs_3 + $this->cc_bs_4;
            $this->cc_sum1 = $this->cc_iva_emitido_1 + $this->cc_iva_emitido_2 + $this->cc_iva_emitido_3 + 
            $this->cc_iva_emitido_4 + $this->cc_ajuste_ppp + $this->cc_ajuste_bs + abs($this->cc_por_pagar) + $this->cc_gasto_no_acreditable;
        }
    }
    
}
