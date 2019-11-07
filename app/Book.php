<?php

namespace App;

use App\Company;
use App\CalculatedTax;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    
    //Relacion con la empresa
    public function company()
    {
        return $ivaData->belongsTo(Company::class);
    }

    
    public function calculos()
    {
        return $ivaData->belongsTo(CalculatedTax::class, 'calculated_tax_id');
    }
    public function calculo_tax()
    {
        return $this->belongsTo(CalculatedTax::class, 'calculated_tax_id');
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
      $ivaData = json_decode( $calculos->iva_data ) ?? new \stdClass();
      
      //Debe BASES
      $this->cc_compras1 = $ivaData->bB001 + $ivaData->bB061 +
                           $ivaData->bS001 + $ivaData->bS061; 
      $this->cc_compras2 = $ivaData->bB002 + $ivaData->bB062 +
                           $ivaData->bS002 + $ivaData->bS062;
      $this->cc_compras3 = $ivaData->bB003 + $ivaData->bB063 +
                           $ivaData->bS003 + $ivaData->bS063;
      $this->cc_compras4 = $ivaData->bB004 + $ivaData->bB064 +
                           $ivaData->bS004 + $ivaData->bS064;
                              
      $this->cc_importaciones1 = $ivaData->bB021 + $ivaData->bB041 +
                                 $ivaData->bS021 + $ivaData->bS041;
      $this->cc_importaciones2 = $ivaData->bB022 + $ivaData->bB042 +
                                 $ivaData->bS022 + $ivaData->bS042;
      $this->cc_importaciones3 = $ivaData->bB023 + $ivaData->bB043 +
                                 $ivaData->bS023 + $ivaData->bS043;
      $this->cc_importaciones4 = $ivaData->bB024 + $ivaData->bB044 +
                                 $ivaData->bS024 + $ivaData->bS044;
      
      $this->cc_propiedades1 = $ivaData->bB011 + $ivaData->bB031 + $ivaData->bB051 + $ivaData->bB071 + $ivaData->bB015  + $ivaData->bB035;
      $this->cc_propiedades2 = $ivaData->bB012 + $ivaData->bB032 + $ivaData->bB052 + $ivaData->bB072;
      $this->cc_propiedades3 = $ivaData->bB013 + $ivaData->bB033 + $ivaData->bB053 + $ivaData->bB073 + $ivaData->bB016  + $ivaData->bB036;
      $this->cc_propiedades4 = $ivaData->bB014 + $ivaData->bB034 + $ivaData->bB054 + $ivaData->bB074;
      
      //Debe IVAS
      $this->cc_iva_compras1 = $ivaData->iB001 + $ivaData->iB061 + 
                               $ivaData->iS001 + $ivaData->iS061; 
      $this->cc_iva_compras2 = $ivaData->iB002 + $ivaData->iB062 +
                               $ivaData->iS002 + $ivaData->iS062;
      $this->cc_iva_compras3 = $ivaData->iB003 + $ivaData->iB063 +
                               $ivaData->iS003 + $ivaData->iS063;
      $this->cc_iva_compras4 = $ivaData->iB004 + $ivaData->iB064 +
                               $ivaData->iS004 + $ivaData->iS064;
                              
      $this->cc_iva_importaciones1 = $ivaData->iB021 + $ivaData->iB041 + $ivaData->iB015 + $ivaData->iB035 +
                                     $ivaData->iS021 + $ivaData->iS041;
      $this->cc_iva_importaciones2 = $ivaData->iB022 + $ivaData->iB042 +
                                     $ivaData->iS022 + $ivaData->iS042;
      $this->cc_iva_importaciones3 = $ivaData->iB023 + $ivaData->iB043 + $ivaData->iB016 + $ivaData->iB036 +
                                     $ivaData->iS023 + $ivaData->iS043;
      $this->cc_iva_importaciones4 = $ivaData->iB024 + $ivaData->iB044 +
                                     $ivaData->iS024 + $ivaData->iS044;
      
      $this->cc_iva_propiedades1 = $ivaData->iB011 + $ivaData->iB031 + $ivaData->iB051 + $ivaData->iB071;
      $this->cc_iva_propiedades2 = $ivaData->iB012 + $ivaData->iB032 + $ivaData->iB052 + $ivaData->iB072;
      $this->cc_iva_propiedades3 = $ivaData->iB013 + $ivaData->iB033 + $ivaData->iB053 + $ivaData->iB073;
      $this->cc_iva_propiedades4 = $ivaData->iB014 + $ivaData->iB034 + $ivaData->iB054 + $ivaData->iB074;
      
      $this->cc_compras_exentas = $ivaData->iB040 + $ivaData->iB050 + $ivaData->iB060 + $ivaData->iB070 + $ivaData->bB040 + $ivaData->bB050 + $ivaData->bB060 + $ivaData->bB070 +
                                  $ivaData->iS040 + $ivaData->iS060 + $ivaData->bS040 + $ivaData->bS060;
      
      $this->cc_compras_sin_derecho = $ivaData->bB080 + $ivaData->bB090 + $ivaData->bB097 + $ivaData->b098 + $ivaData->b099 + 
                                      $ivaData->iB080 + $ivaData->iB090 + $ivaData->iB097 + $ivaData->i098 + $ivaData->i099 +
                                      $ivaData->bS080 + $ivaData->bS090 + $ivaData->bS097 +
                                      $ivaData->iS080 + $ivaData->iS090 + $ivaData->iS097 +
                                      $ivaData->bB091 + $ivaData->bB092 + $ivaData->bB093 + $ivaData->bB094 + $ivaData->iB091 + $ivaData->iB092 + $ivaData->iB093 + $ivaData->iB094;    
                                      
      $this->cc_restaurantes = $ivaData->bR001 + $ivaData->bR002 + $ivaData->bR003 + $ivaData->bR004 + $ivaData->bR005 + $ivaData->bR006;
      $this->cc_iva_restaurantes = $ivaData->iR001 + $ivaData->iR002 + $ivaData->iR003 + $ivaData->iR004 + $ivaData->iR005 + $ivaData->iR006;
      
      $this->cc_compras_sum = $this->cc_compras1 + $this->cc_compras2 + $this->cc_compras3 + $this->cc_compras4 + 
                              $this->cc_importaciones1 + $this->cc_importaciones2 + $this->cc_importaciones3 + $this->cc_importaciones4 +
                              $this->cc_propiedades1 + $this->cc_propiedades2 + $this->cc_propiedades3 + $this->cc_propiedades4 + 
                              $this->cc_iva_compras1 + $this->cc_iva_compras2 + $this->cc_iva_compras3 + $this->cc_iva_compras4 + 
                              $this->cc_iva_importaciones1 + $this->cc_iva_importaciones2 + $this->cc_iva_importaciones3 + $this->cc_iva_importaciones4 + 
                              $this->cc_iva_propiedades1 + $this->cc_iva_propiedades2 + $this->cc_iva_propiedades3 + $this->cc_iva_propiedades4 + 
                              $this->cc_compras_sin_derecho + $this->cc_compras_exentas + $this->cc_restaurantes + $this->cc_iva_restaurantes;
      
     //Haber 1
      $this->cc_proveedores_credito = $calculos->total_proveedores_credito;
      $this->cc_proveedores_contado = $calculos->total_proveedores_contado;
    }
    
    public function setCuentaContableVentas( $calculos ){
      $ivaData = json_decode( $calculos->iva_data ) ?? new \stdClass();
      
      //Haber 2 
      $this->cc_ventas_1 = $ivaData->bB101 + $ivaData->bB121 +
                           $ivaData->bS101 + $ivaData->bS121 + 
                           $ivaData->bB171;
      $this->cc_ventas_2 = $ivaData->bB102 + $ivaData->bB122 +
                           $ivaData->bS102 + $ivaData->bS122 + 
                           $ivaData->bB172;
      $this->cc_ventas_13 = $ivaData->bB103 + $ivaData->bB123 + $ivaData->bB130 + $ivaData->bS140 +
                            $ivaData->bS103 + $ivaData->bS123 + $ivaData->bS130 + 
                           $ivaData->bB173;
      $this->cc_ventas_4 = $ivaData->bB104 + $ivaData->bB124 + + $ivaData->bB114 +
                           $ivaData->bS104 + $ivaData->bS124 + + $ivaData->bS114 + 
                           $ivaData->bB174;
      $this->cc_ventas_exp = $ivaData->bB150 + $ivaData->bS150;
      $this->cc_ventas_estado = $ivaData->bB160 + $ivaData->bS160;
      try{ //Hace un tryCatch porque hay cierres donde aun no existian los codigos 180s;
      $this->cc_ventas_exentas = $ivaData->bB170 + $ivaData->bS170 + 
                                 $ivaData->bB181 + $ivaData->bS181 +
                                 $ivaData->bB182 + $ivaData->bS182 +
                                 $ivaData->bB183 + $ivaData->bS183 +
                                 $ivaData->bB184 + $ivaData->bS184;
      }catch(\Exception $e){ $this->cc_ventas_exentas = $ivaData->bB170 + $ivaData->bS170; }
                        
      $this->cc_ventas_canasta = $ivaData->bB165 + $ivaData->bS165;
      $this->cc_ventas_aduana = $ivaData->bB155 + $ivaData->iB155 +
                                $ivaData->bS155 + $ivaData->iS155;
      $this->cc_ventas_1_iva = $ivaData->iB101 + $ivaData->iB121 +
                               $ivaData->iS101 + $ivaData->iS121 + 
                               $ivaData->iB171;
      $this->cc_ventas_2_iva = $ivaData->iB102 + $ivaData->iB122 +
                               $ivaData->iS102 + $ivaData->iS122 + 
                               $ivaData->iB172;
      $this->cc_ventas_13_iva = $ivaData->iB103 + $ivaData->iB123 + $ivaData->iB130 + $ivaData->iS140 +
                                $ivaData->iS103 + $ivaData->iS123 + $ivaData->iS130 + 
                                $ivaData->iB173;
      $this->cc_ventas_4_iva = $ivaData->iB104 + $ivaData->iB124 + + $ivaData->iB114 +
                               $ivaData->iS104 + $ivaData->iS124 + + $ivaData->iS114 + 
                                $ivaData->iB174;
      try{ //Hace un tryCatch porque hay cierres donde aun no existia el codigo 300
      $this->cc_ventas_sin_derecho = $ivaData->bB200 + $ivaData->bB201 + $ivaData->bB240 + $ivaData->bB245 + $ivaData->bB250 + $ivaData->bB260 + $ivaData->iB200 + $ivaData->iB201 + $ivaData->iB240 + $ivaData->iB245 + $ivaData->iB250 + $ivaData->iB260 +
                                     $ivaData->bS200 + $ivaData->bS201 + $ivaData->bS240 + $ivaData->bS245 + $ivaData->bS250 + $ivaData->bS260 + $ivaData->iS200 + $ivaData->iS201 + $ivaData->iS240 + $ivaData->iS245 + $ivaData->iS250 + $ivaData->iS260 +
                                     $ivaData->bS300 + $ivaData->iS300;
      }catch(\Exception $e){ $this->cc_ventas_sin_derecho = $ivaData->bB200 + $ivaData->bB201 + $ivaData->bB240 + $ivaData->bB245 + $ivaData->bB250 + $ivaData->bB260 + $ivaData->iB200 + $ivaData->iB201 + $ivaData->iB240 + $ivaData->iB245 + $ivaData->iB250 + $ivaData->iB260 +
        $ivaData->bS200 + $ivaData->bS201 + $ivaData->bS240 + $ivaData->bS245 + $ivaData->bS250 + $ivaData->bS260 + $ivaData->iS200 + $ivaData->iS201 + $ivaData->iS240 + $ivaData->iS245 + $ivaData->iS250 + $ivaData->iS260; 
      }
      
      $this->cc_ventas_sum = $this->cc_ventas_1 + $this->cc_ventas_2 + $this->cc_ventas_13 + $this->cc_ventas_4 + 
                                 $this->cc_ventas_1_iva + $this->cc_ventas_2_iva + $this->cc_ventas_13_iva + $this->cc_ventas_4_iva + 
                                 $this->cc_ventas_exp + $this->cc_ventas_estado + $this->cc_ventas_sin_derecho + $this->cc_ventas_exentas +
                                 $this->cc_ventas_canasta + $this->cc_ventas_aduana;
      
      //Debe 2
      $this->cc_clientes_credito = $calculos->total_clientes_credito;
      $this->cc_clientes_contado = $calculos->total_clientes_contado;  
      $this->cc_clientes_credito_exp = $calculos->total_clientes_credito_exp;
      $this->cc_clientes_contado_exp = $calculos->total_clientes_contado_exp;  
      $this->cc_retenido = $calculos->iva_retenido;  
      
      $this->cc_clientes_sum = $this->cc_clientes_credito + $this->cc_clientes_contado + $this->cc_clientes_credito_exp + $this->cc_clientes_contado_exp + $this->cc_retenido;
    }
    
    public function setCuentaContableAjustes( $calculos ){
      $ivaData = json_decode( $calculos->iva_data ) ?? new \stdClass();
      
      $company = currentCompanyModel();
      
      $prorrataOperativa = $company->operative_prorrata / 100;
      $ratio1_operativo = $company->operative_ratio1 / 100;
      $ratio2_operativo = $company->operative_ratio2 / 100;
      $ratio3_operativo = $company->operative_ratio3 / 100;
      $ratio4_operativo = $company->operative_ratio4 / 100;
      
      //Haber 3
        $iva_no_acreditables = $ivaData->iB080 + $ivaData->iB090 + $ivaData->iB097 + $ivaData->i098 + $ivaData->i099 +
                               $ivaData->iS080 + $ivaData->iS090 + $ivaData->iS097 +
                               
                               $ivaData->iB091 + $ivaData->iB092 + $ivaData->iB093 + $ivaData->iB094;
      
        $this->cc_ppp_1 = $ivaData->iB011 + $ivaData->iB031 + $ivaData->iB051 + $ivaData->iB071;
        $this->cc_ppp_2 = $ivaData->iB012 + $ivaData->iB032 + $ivaData->iB052 + $ivaData->iB072;
        $this->cc_ppp_3 = $ivaData->iB013 + $ivaData->iB033 + $ivaData->iB053 + $ivaData->iB073;
        $this->cc_ppp_4 = $ivaData->iB014 + $ivaData->iB034 + $ivaData->iB054 + $ivaData->iB074;
        
        $this->cc_bs_1 = $ivaData->iB001 + $ivaData->iB021 + $ivaData->iB041 + $ivaData->iB061 +
                         $ivaData->iS001 + $ivaData->iS021 + $ivaData->iS041 + $ivaData->iS061;
        $this->cc_bs_2 = $ivaData->iB002 + $ivaData->iB022 + $ivaData->iB042 + $ivaData->iB062 +
                         $ivaData->iS002 + $ivaData->iS022 + $ivaData->iS042 + $ivaData->iS062;
        $this->cc_bs_3 = $ivaData->iB003 + $ivaData->iB023 + $ivaData->iB043 + $ivaData->iB063 + $iva_no_acreditables +
                         $ivaData->iS003 + $ivaData->iS023 + $ivaData->iS043 + $ivaData->iS063;
        $this->cc_bs_4 = $ivaData->iB004 + $ivaData->iB024 + $ivaData->iB044 + $ivaData->iB064 +
                         $ivaData->iS004 + $ivaData->iS024 + $ivaData->iS044 + $ivaData->iS064;
                         
        $this->cc_iva_restaurantes = $ivaData->iR001 + $ivaData->iR002 + $ivaData->iR003 + $ivaData->iR004 + $ivaData->iR005 + $ivaData->iR006;
        
        $this->cc_por_pagar = $calculos->balance_operativo;
      
      //Debe 3 
        $this->cc_iva_emitido_1 = $ivaData->iB101 + $ivaData->iB121 +
                                  $ivaData->iS101 + $ivaData->iS121 + 
                                  $ivaData->iB171;
        $this->cc_iva_emitido_2 = $ivaData->iB102 + $ivaData->iB122 +
                                  $ivaData->iS102 + $ivaData->iS122 + 
                                  $ivaData->iB172;
        $this->cc_iva_emitido_3 = $ivaData->iB103 + $ivaData->iB123 + $ivaData->iB130 + $ivaData->iS140 +
                                  $ivaData->iS103 + $ivaData->iS123 + $ivaData->iS130 + 
                                  $ivaData->iB173;
        $this->cc_iva_emitido_4 = $ivaData->iB104 + $ivaData->iB124 +
                                  $ivaData->iS104 + $ivaData->iS124 + 
                                  $ivaData->iB174;  
        
        $bases_bs1 = $ivaData->bB001 + $ivaData->bB021 +
                     $ivaData->bS001 + $ivaData->bS021;
        $bases_ppp1 = $ivaData->bB011 + $ivaData->bB031;
        $bases_bs2 = $ivaData->bB002 + $ivaData->bB022 +
                     $ivaData->bS002 + $ivaData->bS022;
        $bases_ppp2 = $ivaData->bB012 + $ivaData->bB032;
        $bases_bs3 = $ivaData->bB003 + $ivaData->bB023 +
                     $ivaData->bS003 + $ivaData->bS023;
        $bases_ppp3 = $ivaData->bB013 + $ivaData->bB033;
        $bases_bs4 = $ivaData->bB004 + $ivaData->bB024 +
                     $ivaData->bS004 + $ivaData->bS024;
        $bases_ppp4 = $ivaData->bB014 + $ivaData->bB034;
        
        $this->cc_aj_ppp_1 = $ivaData->iB011 + $ivaData->iB031;
        $this->cc_aj_ppp_2 = $ivaData->iB012 + $ivaData->iB032;
        $this->cc_aj_ppp_3 = $ivaData->iB013 + $ivaData->iB033;
        $this->cc_aj_ppp_4 = $ivaData->iB014 + $ivaData->iB034;
        
        $this->cc_aj_bs_1 = $ivaData->iB001 + $ivaData->iB021 +
                            $ivaData->iS001 + $ivaData->iS021;
        $this->cc_aj_bs_2 = $ivaData->iB002 + $ivaData->iB022 +
                            $ivaData->iS002 + $ivaData->iS022;
        $this->cc_aj_bs_3 = $ivaData->iB003 + $ivaData->iB023 +
                            $ivaData->iS003 + $ivaData->iS023;
        $this->cc_aj_bs_4 = $ivaData->iB004 + $ivaData->iB024 +
                            $ivaData->iS004 + $ivaData->iS024;
        
        $acreditable_bs1  = ( ($bases_bs1 * $ratio1_operativo * 0.01) + ($bases_bs1 * $ratio2_operativo * 0.02) + ($bases_bs1 * $ratio3_operativo * 0.13) + ($bases_bs1 * $ratio4_operativo * 0.04) ) * $prorrataOperativa;
        $acreditable_bs2  = ( ($bases_bs2 * $ratio1_operativo * 0.02) + ($bases_bs2 * $ratio2_operativo * 0.02) + ($bases_bs2 * $ratio3_operativo * 0.02) + ($bases_bs2 * $ratio4_operativo * 0.02) ) * $prorrataOperativa;
        $acreditable_bs3  = ( ($bases_bs3 * $ratio1_operativo * 0.13) + ($bases_bs3 * $ratio2_operativo * 0.02) + ($bases_bs3 * $ratio3_operativo * 0.13) + ($bases_bs3 * $ratio4_operativo * 0.04) ) * $prorrataOperativa;
        $acreditable_bs4  = ( ($bases_bs4 * $ratio1_operativo * 0.04) + ($bases_bs4 * $ratio2_operativo * 0.02) + ($bases_bs4 * $ratio3_operativo * 0.04) + ($bases_bs4 * $ratio4_operativo * 0.04) ) * $prorrataOperativa;

        $acreditable_ppp1 = ( ($bases_ppp1 * $ratio1_operativo * 0.01) + ($bases_ppp1 * $ratio2_operativo * 0.02) + ($bases_ppp1 * $ratio3_operativo * 0.13) + ($bases_ppp1 * $ratio4_operativo * 0.04) ) * $prorrataOperativa;
        $acreditable_ppp2 = ( ($bases_ppp2 * $ratio1_operativo * 0.02) + ($bases_ppp2 * $ratio2_operativo * 0.02) + ($bases_ppp2 * $ratio3_operativo * 0.02) + ($bases_ppp2 * $ratio4_operativo * 0.02) ) * $prorrataOperativa;
        $acreditable_ppp3 = ( ($bases_ppp3 * $ratio1_operativo * 0.13) + ($bases_ppp3 * $ratio2_operativo * 0.02) + ($bases_ppp3 * $ratio3_operativo * 0.13) + ($bases_ppp3 * $ratio4_operativo * 0.04) ) * $prorrataOperativa;
        $acreditable_ppp4 = ( ($bases_ppp4 * $ratio1_operativo * 0.04) + ($bases_ppp4 * $ratio2_operativo * 0.02) + ($bases_ppp4 * $ratio3_operativo * 0.04) + ($bases_ppp4 * $ratio4_operativo * 0.04) ) * $prorrataOperativa;

        $acreditable_ppp = 0 + $acreditable_ppp2 + $acreditable_ppp3 + $acreditable_ppp4;
        $acreditable_bs = 0 + $acreditable_bs2 + $acreditable_bs3 + $acreditable_bs4;
        
        $this->cc_ajuste_ppp =  - $acreditable_ppp + 0 + $this->cc_aj_ppp_2 + + $this->cc_aj_ppp_3 + + $this->cc_aj_ppp_4; 
        $this->cc_ajuste_bs =  - $acreditable_bs + 0 + $this->cc_aj_bs_2 + $this->cc_aj_bs_3 + $this->cc_aj_bs_4;
        
        $this->cc_gasto_no_acreditable = $calculos->iva_no_acreditable_identificacion_plena;
        
        
        //Sumatoria de Haber
        $this->cc_sum2 = $this->cc_ppp_1 + $this->cc_ppp_2 + $this->cc_ppp_3 + $this->cc_ppp_4 + $this->cc_iva_restaurantes
                         + $this->cc_bs_1 + $this->cc_bs_2 + $this->cc_bs_3 + $this->cc_bs_4 + $calculos->saldo_favor_anterior;
        //Sumatoria de Debe
        $this->cc_sum1 = $this->cc_iva_emitido_1 + $this->cc_iva_emitido_2 + $this->cc_iva_emitido_3 
                         + $this->cc_iva_emitido_4 + $this->cc_ajuste_ppp + $this->cc_ajuste_bs + $this->cc_gasto_no_acreditable;
            
        /*if( $this->cc_por_pagar > 0 ) {
            $this->cc_sum2 += $this->cc_por_pagar;
        }else {
            $this->cc_sum1 += abs($this->cc_por_pagar);
        }*/

    }
    
}
