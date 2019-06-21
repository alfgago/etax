<?php

namespace App;

use \Carbon\Carbon;
use App\InvoiceItem;
use App\BillItem;
use App\Invoice;
use App\Bill;
use App\Company;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CalculatedTax extends Model
{
    use SoftDeletes;

    protected $table = 'calculated_taxes';
    
    protected $guarded = [];
    
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    //Relacion con el asiento contable
    public function book()
    {
        return $this->hasOne(Book::class, 'calculated_tax_id');
    }
    
    
    /**
     * Calcula y devuelve los datos del mes para dashboard y reportes
     *
     * @param  int $month
     * @param  int $year
     * @param  int $lastBalance
     * @param  int $prorrataOperativa
     * @return App\CalculatedTax
     */
    public static function calcularFacturacionPorMesAno( $month, $year, $lastBalance, $prorrataOperativa ) {
      
      $currentCompanyId = currentCompany();
      $cacheKey = "cache-taxes-$currentCompanyId-$month-$year";
      
     // if ( !Cache::has($cacheKey) ) {
          
          //Busca el calculo del mes en Base de Datos.
          $data = CalculatedTax::firstOrNew(
              [
                  'company_id' => $currentCompanyId,
                  'month' => $month,
                  'year' => $year,
                  'is_final' => true,
              ]
          );
            
          if ( $month > 0 ) {
            
            if( !$data->is_closed ) {
              
              $data->resetVars();
              $data->calcularFacturacion( $month, $year, $lastBalance, $prorrataOperativa );
              
              if( $data->count_invoices || $data->count_bills || $data->id ) {
                $data->save();
                $book = Book::calcularAsientos( $data );
                $book->save();
                $data->book = $book;
              }
              
            }
        
          } elseif ( $month == 0 ) {
            $data->resetVars();
            $data->calcularFacturacionAcumulado( $year, $prorrataOperativa );
            
            if( $data->count_invoices || $data->count_bills || $data->id ) {
              $data->save();
              $book = Book::calcularAsientos( $data );
              $book->save();
              $data->book = $book;
            }
          }
            
          Cache::put($cacheKey, $data, now()->addDays(120));
          
     //}
      
      $data = Cache::get($cacheKey);
      return $data;
      
    }
    
    
  
    //Recibe fecha de inicio y fecha de fin en base a las cuales se desea calcular la prorrata.
    public function calcularFacturacion( $month, $year, $lastBalance, $prorrataOperativa ) {
      $currentCompanyId = currentCompany();
      //Si recibe el balance anterior en 0, intenta buscarlo.
      if( !$lastBalance ) {
        $lastBalance = $this->getLastBalance($month, $year, $currentCompanyId);
      }
      $this->setDatosEmitidos( $month, $year, $currentCompanyId );
      $this->setDatosSoportados( $month, $year, $currentCompanyId );
      $this->setCalculosIVA( $prorrataOperativa, $lastBalance );
      
      return $this;
    }
    
        
    public function calcularFacturacionAcumulado( $year, $prorrataOperativa ) {

      $this->sumAcumulados( $year );
      $this->setCalculosIVA( $prorrataOperativa, 0 );

      return $this;
      
    }
  
    /**
    *    Recorre todas las facturas emitidas y aumenta los montos correspondientes.
    **/
    public function setDatosEmitidos ( $month, $year, $company ) {
      
      $countInvoices = Invoice::where('company_id', $company)->where('year', $year)->where('month', $month)->count();
      $invoicesTotal = 0;
      $invoicesSubtotal = 0;
      $totalInvoiceIva = 0;
      $totalClientesContadoExp = 0;
      $totalClientesCreditoExp = 0;
      $totalClientesContadoLocal = 0;
      $totalClientesCreditoLocal = 0;
      $sumRepercutido1 = 0;
      $sumRepercutido2 = 0;
      $sumRepercutido3 = 0;
      $sumRepercutido4 = 0;
      $sumRepercutidoExentoConCredito = 0;
      $sumRepercutidoExentoSinCredito = 0;
      $basesVentasConIdentificacion = 0;
      $ivasVentasConIdentificacion = 0;
      $ivaRetenido = 0;
      
      InvoiceItem::with('invoice')
                  ->where('company_id', $company)
                  ->where('year', $year)
                  ->where('month', $month)
                  ->chunk( 2500,  function($invoiceItems) use ($year, $month, &$company,
       &$invoicesTotal, &$invoicesSubtotal, &$totalInvoiceIva, &$totalClientesContadoExp, &$totalClientesCreditoExp, &$totalClientesContadoLocal, &$totalClientesCreditoLocal,&$ivaRetenido,
       &$sumRepercutido1, &$sumRepercutido2, &$sumRepercutido3, &$sumRepercutido4, &$sumRepercutidoExentoConCredito, &$sumRepercutidoExentoSinCredito, &$basesVentasConIdentificacion, &$ivasVentasConIdentificacion
      ) {
        
        $countInvoiceItems = $invoiceItems->count();
        
        //Recorre las lineas de factura
        for ($i = 0; $i < $countInvoiceItems; $i++) {
          
          try {
          
            if( !$invoiceItems[$i]->invoice->is_void && $invoiceItems[$i]->invoice->is_authorized && $invoiceItems[$i]->invoice->is_code_validated ) {
            
              $subtotal = $invoiceItems[$i]->subtotal * $invoiceItems[$i]->invoice->currency_rate;
              //$currentTotal = $invoiceItems[$i]->total * $invoiceItems[$i]->invoice->currency_rate;
              $ivaType = $invoiceItems[$i]->iva_type;
              $invoiceIva = $invoiceItems[$i]->iva_amount * $invoiceItems[$i]->invoice->currency_rate;
              $currentTotal = $subtotal + $invoiceIva;
              
              //Redondea todo a 2 decimales
              $subtotal = round($subtotal, 2);
              $invoiceIva = round($invoiceIva, 2);
              $currentTotal = round($currentTotal, 2);
              
              $ivaType = $ivaType ? $ivaType : '103';
              
              if( $ivaType == '200' || $ivaType == '201' || $ivaType == '240' || $ivaType == '250' || $ivaType == '260' || $ivaType == '245' ){
                $subtotal = $subtotal + $invoiceIva;
                $invoiceIva = 0;
                $sumRepercutidoExentoSinCredito += $subtotal;
              }else if( $invoiceItems[$i]->is_identificacion_especifica ) {
                $basesVentasConIdentificacion += $subtotal;
              }
              
              //sum los del 1%
              if( $ivaType == '101' || $ivaType == '121' || $ivaType == '141' ){
                $sumRepercutido1 += $subtotal;
              }
              
              //sum los del 2%
              if( $ivaType == '102' || $ivaType == '122' || $ivaType == '142' ){
                $sumRepercutido2 += $subtotal;
              }
              
              //sum los del 13%
              if( $ivaType == '103' || $ivaType == '123' || $ivaType == '143' || $ivaType == '130' || $ivaType == '140' ){
                $sumRepercutido3 += $subtotal;
              }
              
              //sum los del 4%
              if( $ivaType == '104' || $ivaType == '124' || $ivaType == '144' || $ivaType == '114' ){
                $sumRepercutido4 += $subtotal;
              }
              //sum los del exentos. Estos se suman como si fueran 13 para efectos del cálculo.
              if( $ivaType == '150' || $ivaType == '160' ||  $ivaType == '170' || $ivaType == '199' || $ivaType == '155' ){
                $subtotal = $subtotal + $invoiceIva;
                $invoiceIva = 0;
                $sumRepercutido3 += $subtotal;
                $sumRepercutidoExentoConCredito += $subtotal;
              }
              
              //Suma la transitoria de canasta básica
              if( $ivaType == '165' ){
                $subtotal = $subtotal + $invoiceIva;
                $invoiceIva = 0;
                $sumRepercutido1 += $subtotal;
                $sumRepercutidoExentoConCredito += $subtotal;
              }
              
              //Ingresa retenciones al calculo
              $tipoPago = $invoiceItems[$i]->invoice->payment_type;
              $retenidoLinea = 0;
              $porcRetencion = $invoiceItems[$i]->invoice->retention_percent;
              if( $tipoPago == '02' ) {
                $retenidoLinea = $currentTotal * ($porcRetencion / 100);
                $ivaRetenido += $retenidoLinea;
              }
              
              //Ingresa retenciones al calculo
              $tipo_venta = $invoiceItems[$i]->invoice->sale_condition;
              if( $ivaType == '150' ){
                if( $tipo_venta == '01' ) {
                  $totalClientesContadoExp += $currentTotal-$retenidoLinea;
                }else {
                  $totalClientesCreditoExp += $currentTotal-$retenidoLinea;
                }
              }else{
                if( $tipo_venta == '01' ) {
                  $totalClientesContadoLocal += $currentTotal-$retenidoLinea;
                }else {
                  $totalClientesCreditoLocal += $currentTotal-$retenidoLinea;
                }
              }
              
              $invoicesTotal += $currentTotal; //Agrega a sumatoria de totales
              $invoicesSubtotal += $subtotal;  //Agrega a sumatoria de subtotales
              $totalInvoiceIva += $invoiceIva; //Agrega a sumatoria de ivas
              
              //sum a las variable según el tipo de IVA que tenga.
              $bVar = "b".$ivaType;
              $iVar = "i".$ivaType;
              $this->$bVar += $subtotal;
              $this->$iVar += $invoiceIva;
              
                
            }
            
          }catch( \Exception $ex ){
            Log::error('Error leer factura para cálculo' . $ex->getMessage());
          }catch( \Throwable $ex ){
            Log::error('Error leer factura para cálculo' . $ex->getMessage());
          }
        }
        
      });
      
      $this->count_invoices = $countInvoices;
      $this->invoices_total = $invoicesTotal;
      $this->invoices_subtotal = $invoicesSubtotal;
      $this->total_invoice_iva = $totalInvoiceIva;
      $this->total_clientes_contado_exp = $totalClientesContadoExp;
      $this->total_clientes_credito_exp = $totalClientesCreditoExp;
      $this->total_clientes_contado = $totalClientesContadoLocal;
      $this->total_clientes_credito = $totalClientesCreditoLocal;
      $this->sum_repercutido1 = $sumRepercutido1;
      $this->sum_repercutido2 = $sumRepercutido2;
      $this->sum_repercutido3 = $sumRepercutido3;
      $this->sum_repercutido4 = $sumRepercutido4;
      $this->sum_repercutido_exento_con_credito = $sumRepercutidoExentoConCredito;
      $this->sum_repercutido_exento_sin_credito = $sumRepercutidoExentoSinCredito;
      $this->bases_ventas_con_identificacion = $basesVentasConIdentificacion;
      $this->iva_retenido = $ivaRetenido;
      
      return $this;
    }
  
    /**
    *   Recorre todas las facturas recibidas y aumenta los montos correspondientes.
    **/
    public function setDatosSoportados ( $month, $year, $company ) {
      $countBills = Bill::where('company_id', $company)->where('year', $year)->where('month', $month)->count();
  
      $billsTotal = 0;
      $billsSubtotal = 0;
      $totalBillIva = 0;
      $basesIdentificacionPlena = 0; //Antes ivaSoportado100Deducible
      $basesNoDeducibles = 0; //Antes $ivaSoportadoNoDeducible
      $ivaAcreditableIdentificacionPlena = 0;
      $ivaNoAcreditableIdentificacionPlena = 0;
      $totalProveedoresContado = 0;
      $totalProveedoresCredito = 0;
      
      BillItem::with('bill')
                  ->where('company_id', $company)
                  ->where('year', $year)
                  ->where('month', $month)
                  ->chunk( 2500,  function($billItems) use ($year, $month, &$company,
       &$billsTotal, &$billsSubtotal, &$totalBillIva, &$basesIdentificacionPlena, &$basesNoDeducibles, &$ivaAcreditableIdentificacionPlena, 
       &$ivaNoAcreditableIdentificacionPlena, &$totalProveedoresContado, &$totalProveedoresCredito
      ) {
        $countBillItems = count( $billItems );

        for ($i = 0; $i < $countBillItems; $i++) {
          
          try {
          
            if( !$billItems[$i]->bill->is_void && $billItems[$i]->bill->is_authorized && $billItems[$i]->bill->is_code_validated ) {
            
              $subtotal = $billItems[$i]->subtotal * $billItems[$i]->bill->currency_rate;
              $ivaType = $billItems[$i]->iva_type;
              $billIva = $billItems[$i]->iva_amount * $billItems[$i]->bill->currency_rate;
              $currentTotal = $subtotal + $billIva;
              
              //Redondea todo a 2 decimales
              $subtotal = round($subtotal, 2);
              $billIva = round($billIva, 2);
              $currentTotal = round($currentTotal, 2);
              
              $ivaType = $ivaType ? $ivaType : '003';
              $ivaType = str_pad($ivaType, 3, '0', STR_PAD_LEFT);
              
              $billsTotal += $currentTotal;
              $billsSubtotal += $subtotal;
              $totalBillIva += $billIva;
              
              if( $ivaType == '041' || $ivaType == '042' || $ivaType == '043' || $ivaType == '044' ||
                  $ivaType == '051' || $ivaType == '052' || $ivaType == '053' || $ivaType == '054' || 
                  $ivaType == '061' || $ivaType == '062' || $ivaType == '063' || $ivaType == '064' || 
                  $ivaType == '071' || $ivaType == '072' || $ivaType == '073' || $ivaType == '074'
              )
              {
                $basesIdentificacionPlena += $subtotal;
              }
              
              if( $ivaType == '080' || $ivaType == '090' || $ivaType == '097' || $ivaType == '098' || $ivaType == '099' )
              {
                $basesNoDeducibles += $subtotal;
                $ivaNoAcreditableIdentificacionPlena += $billIva;
              }
              
              if( $ivaType == '040' || $ivaType == '050' || $ivaType == '060' || $ivaType == '070' )
              {
                $basesNoDeducibles += $subtotal;
              }
              
              /***SACA IVAS DEDUCIBLES DE IDENTIFICAIONES PLENAS**/
              $porc_plena = $billItems[$i]->porc_identificacion_plena ? $billItems[$i]->porc_identificacion_plena : 0;
              
              if( $ivaType == '041' || $ivaType == '051' || $ivaType == '061' || $ivaType == '071' )
              {
                $ivaAcreditableIdentificacionPlena += $billIva;
              }
              if( $ivaType == '042' || $ivaType == '052' || $ivaType == '062' || $ivaType == '072' )
              {
                $menor = 2;
                if( $porc_plena != 2 ){
                  $menor = $porc_plena > 2 ? 2 : $porc_plena;
                }
                $menor_porc = $menor/100;
                
                $ivaAcreditableIdentificacionPlena += $subtotal * $menor_porc;
                $ivaNoAcreditableIdentificacionPlena += $billIva - ($subtotal * $menor_porc);
              }
              if( $ivaType == '043' || $ivaType == '053' || $ivaType == '063' || $ivaType == '073' )
              {
                $menor = 13;
                if( $porc_plena != 13 ){
                  $menor = $porc_plena > 13 ? 13 : $porc_plena;
                }
                $menor_porc = $menor/100;
                
                $ivaAcreditableIdentificacionPlena += $subtotal * $menor_porc;
                $ivaNoAcreditableIdentificacionPlena += $billIva - ($subtotal * $menor_porc);
              }
              if( $ivaType == '044' || $ivaType == '054' || $ivaType == '064' || $ivaType == '074' )
              {
                $menor = 4;
                if( $porc_plena != 4 ){
                  $menor = $porc_plena > 4 ? 4 : $porc_plena;
                }
                $menor_porc = $menor/100;
                
                $ivaAcreditableIdentificacionPlena += $subtotal * $menor_porc;
                $ivaNoAcreditableIdentificacionPlena += $billIva - ($subtotal * $menor_porc);
              }
              /***END SACA IVAS DEDUCIBLES DE IDENTIFICAIONES PLENAS**/
              
              $bVar = "b".$ivaType;
              $iVar = "i".$ivaType;
              $this->$bVar += $subtotal;
              $this->$iVar += $billIva;
              
              //Cuenta contable de proveedor
              $tipoVenta = $billItems[$i]->bill->sale_condition;
              if( $tipoVenta == '01' ) {
                $totalProveedoresContado += $currentTotal;
              }else{
                $totalProveedoresCredito += $currentTotal;
              }
              
            }  
            
          }catch( \Exception $ex ){
            Log::error('Error leer factura para cálculo' . $ex->getMessage());
          }catch( \Throwable $ex ){
            Log::error('Error leer factura para cálculo' . $ex->getMessage());
          }
        }
        
      });
      
      $this->count_bills = $countBills;
      $this->bills_total = $billsTotal;
      $this->bills_subtotal = $billsSubtotal;
      $this->total_bill_iva = $totalBillIva;
      $this->bases_identificacion_plena = $basesIdentificacionPlena;
      $this->bases_no_deducibles = $basesNoDeducibles;
      $this->iva_acreditable_identificacion_plena = $ivaAcreditableIdentificacionPlena;
      $this->iva_no_acreditable_identificacion_plena = $ivaNoAcreditableIdentificacionPlena;
      $this->total_proveedores_contado = $totalProveedoresContado;
      $this->total_proveedores_credito = $totalProveedoresCredito;
      
      //Separa los subtotales sin identificacion por tarifa.
      $this->bills_subtotal1 = $this->b001 + $this->b011 + $this->b021 + $this->b031 + $this->b015 + $this->b035;
      $this->bills_subtotal2 = $this->b002 + $this->b012 + $this->b022 + $this->b032;
      $this->bills_subtotal3 = $this->b003 + $this->b013 + $this->b023 + $this->b033 + $this->b016 + $this->b036;
      $this->bills_subtotal4 = $this->b004 + $this->b014 + $this->b024 + $this->b034;
      
      return $this;
    }
    
    public function setCalculosIVA( $prorrataOperativa, $lastBalance ) {
      
      $company = currentCompanyModel();
      
      //Determina numerador y denominador de la prorrata.
      $numeradorProrrata = $this->invoices_subtotal - $this->sum_repercutido_exento_sin_credito;
      $denumeradorProrrata = $this->invoices_subtotal;
      
      //Otras variables relevantes
      $prorrata = 1;
      $ivaDeducibleEstimado = 0;
      $balanceEstimado = 0;
      $ivaDeducibleOperativo = 0;
      $balanceOperativo = 0;
      $ivaNoDeducible = 0;
      $ratio1 = 0;
      $ratio2 = 0;
      $ratio3 = 0;
      $ratio4 = 0;
      $fakeRatio1 = 0;
      $fakeRatio2 = 0;
      $fakeRatio3 = 0;
      $fakeRatio4 = 0;
      $fakeRatioExentoSinCredito = 0;
      $fakeRatioExentoConCredito = 0;
      $subtotalParaCFDP = 0;
      $cfdp = 0;
      
      //Primero revisa si la sumatoria de pro
      if( $numeradorProrrata > 0 ){
        
        //Define los ratios por tipo para calculo de prorrata
        $ratio1 = $this->sum_repercutido1 / $numeradorProrrata;
        $ratio2 = $this->sum_repercutido2 / $numeradorProrrata;
        $ratio3 = $this->sum_repercutido3 / $numeradorProrrata;
        $ratio4 = $this->sum_repercutido4 / $numeradorProrrata;
        
        //Redondea todo a 2 decimales
        $ratio1 = round($ratio1, 4);
        $ratio2 = round($ratio2, 4);
        $ratio3 = round($ratio3, 4);
        $ratio4 = round($ratio4, 4);
        
        //Define los ratios por tipo para guardar
        $fakeRatio1 = $this->sum_repercutido1 / $this->invoices_subtotal;
        $fakeRatio2 = $this->sum_repercutido2 / $this->invoices_subtotal;
        $fakeRatio3 = ($this->sum_repercutido3-$this->sum_repercutido_exento_con_credito) / $this->invoices_subtotal;
        $fakeRatio4 = $this->sum_repercutido4 / $this->invoices_subtotal;
        $fakeRatioExentoSinCredito = $this->sum_repercutido_exento_sin_credito / $this->invoices_subtotal;
        $fakeRatioExentoConCredito = $this->sum_repercutido_exento_con_credito / $this->invoices_subtotal;
        
        //Calcula prorrata
        $prorrata = $numeradorProrrata / $denumeradorProrrata;
      } else {
        $prorrata = 1;
        $ratio1 = 0;
        $ratio2 = 0;
        $ratio3 = 0;
        $ratio4 = 0;
      
        //Define los ratios por tipo para guardar
        $fakeRatio1 = 0;
        $fakeRatio2 = 0;
        $fakeRatio3 = 0;
        $fakeRatio4 = 0;
        $fakeRatioExentoSinCredito = 0;
        $fakeRatioExentoConCredito = 0;
        
      }
      
      //Calcula el total deducible y no deducible en base a los ratios y los montos de facturas recibidas.
      $subtotalParaCFDP = $this->bills_subtotal - $this->bases_identificacion_plena - $this->bases_no_deducibles;
      
      $cfdpEstimado1 = $this->bills_subtotal1*$ratio1*0.01 + $this->bills_subtotal1*$ratio2*0.02 + $this->bills_subtotal1*$ratio3*0.13 + $this->bills_subtotal1*$ratio4*0.04 ; 
      $cfdpEstimado2 = $this->bills_subtotal2*$ratio1*0.01 + $this->bills_subtotal2*$ratio2*0.02 + $this->bills_subtotal2*$ratio3*0.02 + $this->bills_subtotal2*$ratio4*0.02 ; 
      $cfdpEstimado3 = $this->bills_subtotal3*$ratio1*0.01 + $this->bills_subtotal3*$ratio2*0.02 + $this->bills_subtotal3*$ratio3*0.13 + $this->bills_subtotal3*$ratio4*0.04 ; 
      $cfdpEstimado4 = $this->bills_subtotal4*$ratio1*0.01 + $this->bills_subtotal4*$ratio2*0.02 + $this->bills_subtotal4*$ratio3*0.04 + $this->bills_subtotal4*$ratio4*0.04 ; 
      $cfdpEstimado  = $cfdpEstimado1 + $cfdpEstimado2 + $cfdpEstimado3 + $cfdpEstimado4;
      //Calcula el balance estimado.
      $ivaDeducibleEstimado = ($cfdpEstimado * $prorrata) + $this->iva_acreditable_identificacion_plena;
      $balanceEstimado = -$lastBalance + $this->total_invoice_iva - $ivaDeducibleEstimado;
      
      if( $this->month == 6) {
        //3dd( $this );
      }

      $ratio1_operativo = $company->operative_ratio1 / 100;
      $ratio2_operativo = $company->operative_ratio2 / 100;
      $ratio3_operativo = $company->operative_ratio3 / 100;
      $ratio4_operativo = $company->operative_ratio4 / 100;
      
      //Redondea ratios a 4 decimales (Al multiplicar por 100, queda en 2)
      $ratio1_operativo = round($ratio1_operativo, 4);
      $ratio2_operativo = round($ratio2_operativo, 4);
      $ratio3_operativo = round($ratio3_operativo, 4);
      $ratio4_operativo = round($ratio4_operativo, 4);
      
      $cfdp1 = $this->bills_subtotal1*$ratio1_operativo*0.01 + $this->bills_subtotal1*$ratio2_operativo*0.02 + $this->bills_subtotal1*$ratio3_operativo*0.13 + $this->bills_subtotal1*$ratio4_operativo*0.04 ; 
      $cfdp2 = $this->bills_subtotal2*$ratio1_operativo*0.02 + $this->bills_subtotal2*$ratio2_operativo*0.02 + $this->bills_subtotal2*$ratio3_operativo*0.02 + $this->bills_subtotal2*$ratio4_operativo*0.02 ; 
      $cfdp3 = $this->bills_subtotal3*$ratio1_operativo*0.13 + $this->bills_subtotal3*$ratio2_operativo*0.02 + $this->bills_subtotal3*$ratio3_operativo*0.13 + $this->bills_subtotal3*$ratio4_operativo*0.04 ; 
      $cfdp4 = $this->bills_subtotal4*$ratio1_operativo*0.04 + $this->bills_subtotal4*$ratio2_operativo*0.02 + $this->bills_subtotal4*$ratio3_operativo*0.04 + $this->bills_subtotal4*$ratio4_operativo*0.04 ; 
      $cfdp = $cfdp1 + $cfdp2 + $cfdp3 + $cfdp4;
      $cfdp = round($cfdp, 2); 
      
      //Calcula el balance operativo.
      $ivaDeducibleOperativo = ($cfdp * $prorrataOperativa) + $this->iva_acreditable_identificacion_plena;
      $balanceOperativo = -$lastBalance + $this->total_invoice_iva - $ivaDeducibleOperativo;
      $ivaNoDeducible = $this->total_bill_iva - $ivaDeducibleOperativo;
 
      $saldoFavor = $balanceOperativo - $this->iva_retenido;
      $saldoFavor = $saldoFavor < 0 ? abs( $saldoFavor ) : 0;

      $this->numerador_prorrata = $numeradorProrrata;
      $this->denumerador_prorrata = $denumeradorProrrata;
      $this->prorrata = $prorrata;
      $this->prorrata_operativa = $prorrataOperativa;
        
      $this->subtotal_para_cfdp = $subtotalParaCFDP;
      $this->cfdp = $cfdp;
        
      $this->iva_deducible_estimado = $ivaDeducibleEstimado;
      $this->balance_estimado = $balanceEstimado;
      $this->iva_deducible_operativo = $ivaDeducibleOperativo;
      $this->balance_operativo = $balanceOperativo;
      $this->iva_no_deducible = $ivaNoDeducible;
      $this->iva_por_cobrar = $this->balance_operativo < 0 ? abs($this->balance_operativo) : 0;
      $this->iva_por_pagar = $this->balance_operativo > 0 ? $this->balance_operativo : 0;
      
      $this->ratio1 = $ratio1;
      $this->ratio2 = $ratio2;
      $this->ratio3 = $ratio3;
      $this->ratio4 = $ratio4;
      
      $this->fake_ratio1 = $fakeRatio1;
      $this->fake_ratio2 = $fakeRatio2;
      $this->fake_ratio3 = $fakeRatio3;
      $this->fake_ratio4 = $fakeRatio4;
      $this->fake_ratio_exento_sin_credito = $fakeRatioExentoSinCredito;
      $this->fake_ratio_exento_con_credito = $fakeRatioExentoConCredito;
      
      $this->saldo_favor = $saldoFavor;
      $this->saldo_favor_anterior = $lastBalance;
      
    }
    
    public static function getProrrataPeriodoAnterior($anoAnterior) {
      
      $currentCompany = currentCompanyModel();
      $currentCompanyId = $currentCompany->id;
      
      $cacheKey = "cache-lasttaxes-$currentCompanyId-0-$anoAnterior";
      if ( !Cache::has($cacheKey) ) {
        $data = CalculatedTax::firstOrNew(
            [
                'company_id' => $currentCompanyId,
                'month' => 0,
                'year' => $anoAnterior,
                'is_final' => true,
            ]
        );
        
        if($anoAnterior == 2018 && $currentCompany->first_prorrata_type == 2 ){
          
          if( !$data->is_closed ) {
              
              $data->resetVars();
              $data->calcularFacturacion( 0, $anoAnterior, 0, 1 );
              
              if( $data->count_invoices || $data->count_bills || $data->id ) {
                $data->save();
                $book = Book::calcularAsientos( $data );
                $book->save();
                $data->book = $book;
              }
              
          }
            
        }else {
          if( !$data->is_closed ) {
            
              
              $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $anoAnterior, 0, 100 );
              $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $anoAnterior, 0, 100 );
              $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $anoAnterior, 0, 100 );
              $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $anoAnterior, 0, 100 );
              $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $anoAnterior, 0, 100 );
              $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $anoAnterior, 0, 100 );
              $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $anoAnterior, 0, 100 );
              $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $anoAnterior, 0, 100 );
              $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $anoAnterior, 0, 100 );
              $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $anoAnterior, 0, 100 );
              $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $anoAnterior, 0, 100 );
              $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $anoAnterior, 0, 100 );
            
              $data->resetVars();
              $data->calcularFacturacionAcumulado( $anoAnterior, 1 );
              
              if( $data->count_invoices || $data->count_bills ) {
                $data->save();
                $book = Book::calcularAsientos( $data );
                $book->save();
                $data->book = $book;
              }
            
          }
        }
        Cache::put($cacheKey, $data, now()->addDays(120));
      }
      
      return Cache::get($cacheKey);
      
    }
    
    function sumAcumulados( $year ) {
      
      $currentCompanyId = currentCompany();
      $calculosAnteriores = CalculatedTax::where('company_id', $currentCompanyId)->where('is_final', true)->where('year', $year)->where('month', '!=', 0)->get();
      $countAnteriores = count( $calculosAnteriores );
      
      $this->count_invoices = 0;
			$this->invoices_total = 0;
			$this->invoices_subtotal = 0;
			$this->total_invoice_iva = 0;
			$this->total_clientes_contado_exp = 0;
			$this->total_clientes_credito_exp = 0;
			$this->total_clientes_contado = 0;
			$this->total_clientes_credito = 0;
			$this->sum_repercutido1 = 0;
			$this->sum_repercutido2 = 0;
			$this->sum_repercutido3 = 0;
			$this->sum_repercutido4 = 0;
			$this->sum_repercutido_exento_con_credito = 0;
			$this->sum_repercutido_exento_sin_credito = 0;
			$this->bases_ventas_con_identificacion = 0;

			$this->count_bills = 0;
			$this->bills_total = 0;
			$this->bills_subtotal = 0;
			$this->bills_subtotal1 = 0;
			$this->bills_subtotal2 = 0;
			$this->bills_subtotal3 = 0;
			$this->bills_subtotal4 = 0;
			$this->total_bill_iva = 0;
			$this->bases_identificacion_plena = 0;
			$this->bases_no_deducibles = 0;
			$this->iva_acreditable_identificacion_plena = 0;
			$this->iva_no_acreditable_identificacion_plena = 0;
			$this->total_proveedores_contado = 0;
			$this->total_proveedores_credito = 0;
			$this->iva_retenido = 0;
      
      for ($i = 0; $i < $countAnteriores; $i++) {
          $this->count_invoices += $calculosAnteriores[$i]->count_invoices;
    			$this->invoices_total += $calculosAnteriores[$i]->invoices_total;
    			$this->invoices_subtotal += $calculosAnteriores[$i]->invoices_subtotal;
    			$this->total_invoice_iva += $calculosAnteriores[$i]->total_invoice_iva;
    			$this->total_clientes_contado_exp += $calculosAnteriores[$i]->total_clientes_contado_exp;
    			$this->total_clientes_credito_exp += $calculosAnteriores[$i]->total_clientes_credito_exp;
    			$this->total_clientes_contado += $calculosAnteriores[$i]->total_clientes_contado;
    			$this->total_clientes_credito += $calculosAnteriores[$i]->total_clientes_credito;
    			$this->sum_repercutido1 += $calculosAnteriores[$i]->sum_repercutido1;
    			$this->sum_repercutido2 += $calculosAnteriores[$i]->sum_repercutido2;
    			$this->sum_repercutido3 += $calculosAnteriores[$i]->sum_repercutido3;
    			$this->sum_repercutido4 += $calculosAnteriores[$i]->sum_repercutido4;
    			$this->sum_repercutido_exento_con_credito += $calculosAnteriores[$i]->sum_repercutido_exento_con_credito;
    			$this->sum_repercutido_exento_sin_credito += $calculosAnteriores[$i]->sum_repercutido_exento_sin_credito;
    			$this->bases_ventas_con_identificacion += $calculosAnteriores[$i]->bases_ventas_con_identificacion;
    
    			$this->count_bills += $calculosAnteriores[$i]->count_bills;
    			$this->bills_total += $calculosAnteriores[$i]->bills_total;
    			$this->bills_subtotal += $calculosAnteriores[$i]->bills_subtotal;
    			$this->bills_subtotal1 += $calculosAnteriores[$i]->bills_subtotal1;
    			$this->bills_subtotal2 += $calculosAnteriores[$i]->bills_subtotal2;
    			$this->bills_subtotal3 += $calculosAnteriores[$i]->bills_subtotal3;
    			$this->bills_subtotal4 += $calculosAnteriores[$i]->bills_subtotal4;
    			$this->total_bill_iva += $calculosAnteriores[$i]->total_bill_iva;
    			$this->bases_identificacion_plena += $calculosAnteriores[$i]->bases_identificacion_plena;
    			$this->bases_no_deducibles += $calculosAnteriores[$i]->bases_no_deducibles;
    			$this->iva_acreditable_identificacion_plena += $calculosAnteriores[$i]->iva_acreditable_identificacion_plena;
    			$this->iva_no_acreditable_identificacion_plena += $calculosAnteriores[$i]->iva_no_acreditable_identificacion_plena;
    			$this->total_proveedores_contado += $calculosAnteriores[$i]->total_proveedores_contado;
    			$this->total_proveedores_credito += $calculosAnteriores[$i]->total_proveedores_credito;
    			$this->iva_retenido += $calculosAnteriores[$i]->iva_retenido;
    			
    			$this->b001 += $calculosAnteriores[$i]->b001;
          $this->i001 += $calculosAnteriores[$i]->i001;
          $this->b002 += $calculosAnteriores[$i]->b002;
          $this->i002 += $calculosAnteriores[$i]->i002;
          $this->b003 += $calculosAnteriores[$i]->b003;
          $this->i003 += $calculosAnteriores[$i]->i003;
          $this->b004 += $calculosAnteriores[$i]->b004;
          $this->i004 += $calculosAnteriores[$i]->i004;
          
          $this->b011 += $calculosAnteriores[$i]->b011;
          $this->i011 += $calculosAnteriores[$i]->i011;
          $this->b012 += $calculosAnteriores[$i]->b012;
          $this->i012 += $calculosAnteriores[$i]->i012;
          $this->b013 += $calculosAnteriores[$i]->b013;
          $this->i013 += $calculosAnteriores[$i]->i013;
          $this->b014 += $calculosAnteriores[$i]->b014;
          $this->i014 += $calculosAnteriores[$i]->i014;
          
          $this->b021 += $calculosAnteriores[$i]->b021;
          $this->i021 += $calculosAnteriores[$i]->i021;
          $this->b022 += $calculosAnteriores[$i]->b022;
          $this->i022 += $calculosAnteriores[$i]->i022;
          $this->b023 += $calculosAnteriores[$i]->b023;
          $this->i023 += $calculosAnteriores[$i]->i023;
          $this->b024 += $calculosAnteriores[$i]->b024;
          $this->i024 += $calculosAnteriores[$i]->i024;
          
          $this->b031 += $calculosAnteriores[$i]->b031;
          $this->i031 += $calculosAnteriores[$i]->i031;
          $this->b032 += $calculosAnteriores[$i]->b032;
          $this->i032 += $calculosAnteriores[$i]->i032;
          $this->b033 += $calculosAnteriores[$i]->b033;
          $this->i033 += $calculosAnteriores[$i]->i033;
          $this->b034 += $calculosAnteriores[$i]->b034;
          $this->i034 += $calculosAnteriores[$i]->i034;

          $this->b015 += $calculosAnteriores[$i]->b015;
          $this->i015 += $calculosAnteriores[$i]->i015;
          $this->b016 += $calculosAnteriores[$i]->b016;
          $this->i016 += $calculosAnteriores[$i]->i016;
          
          $this->b035 += $calculosAnteriores[$i]->b035;
          $this->i035 += $calculosAnteriores[$i]->i035;
          $this->b036 += $calculosAnteriores[$i]->b036;
          $this->i036 += $calculosAnteriores[$i]->i036;
          
          $this->b040 += $calculosAnteriores[$i]->b040;
          $this->i040 += $calculosAnteriores[$i]->i040;
          $this->b041 += $calculosAnteriores[$i]->b041;
          $this->i041 += $calculosAnteriores[$i]->i041;
          $this->b042 += $calculosAnteriores[$i]->b042;
          $this->i042 += $calculosAnteriores[$i]->i042;
          $this->b043 += $calculosAnteriores[$i]->b043;
          $this->i043 += $calculosAnteriores[$i]->i043;
          $this->b044 += $calculosAnteriores[$i]->b044;
          $this->i044 += $calculosAnteriores[$i]->i044;
          
          $this->b050 += $calculosAnteriores[$i]->b050;
          $this->i050 += $calculosAnteriores[$i]->b050;
          $this->b051 += $calculosAnteriores[$i]->b051;
          $this->i051 += $calculosAnteriores[$i]->i051;
          $this->b052 += $calculosAnteriores[$i]->b052;
          $this->i052 += $calculosAnteriores[$i]->i052;
          $this->b053 += $calculosAnteriores[$i]->b053;
          $this->i053 += $calculosAnteriores[$i]->i053;
          $this->b054 += $calculosAnteriores[$i]->b054;
          $this->i054 += $calculosAnteriores[$i]->i054;
          
          $this->b060 += $calculosAnteriores[$i]->b060;
          $this->i060 += $calculosAnteriores[$i]->i060;
          $this->b061 += $calculosAnteriores[$i]->b061;
          $this->i061 += $calculosAnteriores[$i]->i061;
          $this->b062 += $calculosAnteriores[$i]->b062;
          $this->i062 += $calculosAnteriores[$i]->i062;
          $this->b063 += $calculosAnteriores[$i]->b063;
          $this->i063 += $calculosAnteriores[$i]->i063;
          $this->b064 += $calculosAnteriores[$i]->b064;
          $this->i064 += $calculosAnteriores[$i]->i064;
          
          $this->b070 += $calculosAnteriores[$i]->b070;
          $this->i070 += $calculosAnteriores[$i]->i070;
          $this->b071 += $calculosAnteriores[$i]->b071;
          $this->i071 += $calculosAnteriores[$i]->i071;
          $this->b072 += $calculosAnteriores[$i]->b072;
          $this->i072 += $calculosAnteriores[$i]->i072;
          $this->b073 += $calculosAnteriores[$i]->b073;
          $this->i073 += $calculosAnteriores[$i]->i073;
          $this->b074 += $calculosAnteriores[$i]->b074;
          $this->i074 += $calculosAnteriores[$i]->i074;
          
          $this->b080 += $calculosAnteriores[$i]->b080;
          $this->i080 += $calculosAnteriores[$i]->i080;
          $this->b090 += $calculosAnteriores[$i]->b090;
          $this->i090 += $calculosAnteriores[$i]->i090;
          $this->b097 += $calculosAnteriores[$i]->b097;
          $this->i097 += $calculosAnteriores[$i]->i097;
          $this->b098 += $calculosAnteriores[$i]->b098;
          $this->i098 += $calculosAnteriores[$i]->i098;
          $this->b099 += $calculosAnteriores[$i]->b099;
          $this->i099 += $calculosAnteriores[$i]->i099;
        
          $this->b101 += $calculosAnteriores[$i]->b101;
          $this->i101 += $calculosAnteriores[$i]->i101;
          $this->b102 += $calculosAnteriores[$i]->b102;
          $this->i102 += $calculosAnteriores[$i]->i102;
          $this->b103 += $calculosAnteriores[$i]->b103;
          $this->i103 += $calculosAnteriores[$i]->i103;
          $this->b104 += $calculosAnteriores[$i]->b104;
          $this->i104 += $calculosAnteriores[$i]->i104;
          
          $this->b121 += $calculosAnteriores[$i]->b121;
          $this->i121 += $calculosAnteriores[$i]->i121;
          $this->b122 += $calculosAnteriores[$i]->b122;
          $this->i122 += $calculosAnteriores[$i]->i122;
          $this->b123 += $calculosAnteriores[$i]->b123;
          $this->i123 += $calculosAnteriores[$i]->i123;
          $this->b124 += $calculosAnteriores[$i]->b124;
          $this->i124 += $calculosAnteriores[$i]->i124;
          
          $this->b130 += $calculosAnteriores[$i]->b130;
          $this->i130 += $calculosAnteriores[$i]->i130;
          $this->b140 += $calculosAnteriores[$i]->b140;
          $this->i140 += $calculosAnteriores[$i]->i140;
          
          $this->b141 += $calculosAnteriores[$i]->b141;
          $this->i141 += $calculosAnteriores[$i]->i141;
          $this->b142 += $calculosAnteriores[$i]->b142;
          $this->i142 += $calculosAnteriores[$i]->i142;
          $this->b143 += $calculosAnteriores[$i]->b143;
          $this->i143 += $calculosAnteriores[$i]->i143;
          $this->b144 += $calculosAnteriores[$i]->b144;
          $this->i144 += $calculosAnteriores[$i]->i144;
          
          $this->b150 += $calculosAnteriores[$i]->b150;
          $this->i150 += $calculosAnteriores[$i]->i150;
          
          $this->b155 += $calculosAnteriores[$i]->b155;
          $this->i155 += $calculosAnteriores[$i]->i155;
          
          $this->b160 += $calculosAnteriores[$i]->b160;
          $this->i160 += $calculosAnteriores[$i]->i160;
          
          $this->b165 += $calculosAnteriores[$i]->b165;
          $this->i165 += $calculosAnteriores[$i]->i165;
          
          $this->b170 += $calculosAnteriores[$i]->b170;
          $this->i170 += $calculosAnteriores[$i]->i170;
          
          $this->b200 += $calculosAnteriores[$i]->b200;
          $this->i200 += $calculosAnteriores[$i]->i200;
          
          $this->b201 += $calculosAnteriores[$i]->b201;
          $this->i201 += $calculosAnteriores[$i]->i201;
          
          $this->b240 += $calculosAnteriores[$i]->b240;
          $this->i240 += $calculosAnteriores[$i]->i240;
          
          $this->b250 += $calculosAnteriores[$i]->b250;
          $this->i250 += $calculosAnteriores[$i]->i250;
          
          $this->b245 += $calculosAnteriores[$i]->b245;
          $this->i245 += $calculosAnteriores[$i]->i245;
          
          $this->b260 += $calculosAnteriores[$i]->b260;
          $this->i260 += $calculosAnteriores[$i]->i260;   
      }
      
    }
    
    
    /**
    * Reinicia todas las variables de sumatoria cuando se va a volver a calcular desde el inicio
    **/
    function resetVars() {
      
      $this->count_invoices = 0;
			$this->invoices_total = 0;
			$this->invoices_subtotal = 0;
			$this->total_invoice_iva = 0;
			$this->total_clientes_contado_exp = 0;
			$this->total_clientes_credito_exp = 0;
			$this->total_clientes_contado = 0;
			$this->total_clientes_credito = 0;
			$this->sum_repercutido1 = 0;
			$this->sum_repercutido2 = 0;
			$this->sum_repercutido3 = 0;
			$this->sum_repercutido4 = 0;
			$this->sum_repercutido_exento_con_credito = 0;
			$this->sum_repercutido_exento_sin_credito = 0;
			$this->bases_ventas_con_identificacion = 0;

			$this->count_bills = 0;
			$this->bills_total = 0;
			$this->bills_subtotal = 0;
			$this->bills_subtotal1 = 0;
			$this->bills_subtotal2 = 0;
			$this->bills_subtotal3 = 0;
			$this->bills_subtotal4 = 0;
			$this->total_bill_iva = 0;
			$this->bases_identificacion_plena = 0;
			$this->bases_no_deducibles = 0;
			$this->iva_acreditable_identificacion_plena = 0;
			$this->iva_no_acreditable_identificacion_plena = 0;
			$this->total_proveedores_contado = 0;
			$this->total_proveedores_credito = 0;
			$this->iva_retenido = 0;
      
      //Debitos
            $this->b001 = 0;
            $this->i001 = 0;
            $this->b002 = 0;
            $this->i002 = 0;
            $this->b003 = 0;
            $this->i003 = 0;
            $this->b004 = 0;
            $this->i004 = 0;
            
            $this->b011 = 0;
            $this->i011 = 0;
            $this->b012 = 0;
            $this->i012 = 0;
            $this->b013 = 0;
            $this->i013 = 0;
            $this->b014 = 0;
            $this->i014 = 0;
            
            $this->b021 = 0;
            $this->i021 = 0;
            $this->b022 = 0;
            $this->i022 = 0;
            $this->b023 = 0;
            $this->i023 = 0;
            $this->b024 = 0;
            $this->i024 = 0;
            
            $this->b031 = 0;
            $this->i031 = 0;
            $this->b032 = 0;
            $this->i032 = 0;
            $this->b033 = 0;
            $this->i033 = 0;
            $this->b034 = 0;
            $this->i034 = 0;
            
            $this->b015 = 0;
            $this->i015 = 0;
            $this->b016 = 0;
            $this->i016 = 0;
            
            $this->b035 = 0;
            $this->i035 = 0;
            $this->b036 = 0;
            $this->i036 = 0;
            
            $this->b040 = 0;
            $this->i040 = 0;
            $this->b041 = 0;
            $this->i041 = 0;
            $this->b042 = 0;
            $this->i042 = 0;
            $this->b043 = 0;
            $this->i043 = 0;
            $this->b044 = 0;
            $this->i044 = 0;
            
            $this->b050 = 0;
            $this->i050 = 0;
            $this->b051 = 0;
            $this->i051 = 0;
            $this->b052 = 0;
            $this->i052 = 0;
            $this->b053 = 0;
            $this->i053 = 0;
            $this->b054 = 0;
            $this->i054 = 0;
            
            $this->b060 = 0;
            $this->i060 = 0;
            $this->b061 = 0;
            $this->i061 = 0;
            $this->b062 = 0;
            $this->i062 = 0;
            $this->b063 = 0;
            $this->i063 = 0;
            $this->b064 = 0;
            $this->i064 = 0;
            
            $this->b070 = 0;
            $this->i070 = 0;
            $this->b071 = 0;
            $this->i071 = 0;
            $this->b072 = 0;
            $this->i072 = 0;
            $this->b073 = 0;
            $this->i073 = 0;
            $this->b074 = 0;
            $this->i074 = 0;
            
            $this->b080 = 0;
            $this->i080 = 0;
            $this->b090 = 0;
            $this->i090 = 0;
            $this->b097 = 0;
            $this->i097 = 0;
            $this->b098 = 0;
            $this->i098 = 0;
            $this->b099 = 0;
            $this->i099 = 0;
          
            //Creditos
            $this->b101 = 0;
            $this->i101 = 0;
            $this->b102 = 0;
            $this->i102 = 0;
            $this->b103 = 0;
            $this->i103 = 0;
            $this->b104 = 0;
            $this->i104 = 0;
            
            $this->b121 = 0;
            $this->i121 = 0;
            $this->b122 = 0;
            $this->i122 = 0;
            $this->b123 = 0;
            $this->i123 = 0;
            $this->b124 = 0;
            $this->i124 = 0;
            
            $this->b130 = 0;
            $this->i130 = 0;
            $this->b140 = 0;
            $this->i140 = 0;
            
            $this->b141 = 0;
            $this->i141 = 0;
            $this->b142 = 0;
            $this->i142 = 0;
            $this->b143 = 0;
            $this->i143 = 0;
            $this->b144 = 0;
            $this->i144 = 0;
            
            $this->b150 = 0;
            $this->i150 = 0;
            
            $this->b155 = 0;
            $this->i155 = 0;
            
            $this->b160 = 0;
            $this->i160 = 0;
            
            $this->b165 = 0;
            $this->i165 = 0;
            
            $this->b170 = 0;
            $this->i170 = 0;
            
            $this->b200 = 0;
            $this->i200 = 0;
            
            $this->b201 = 0;
            $this->i201 = 0;
            
            $this->b240 = 0;
            $this->i240 = 0;
            
            $this->b245 = 0;
            $this->i245 = 0;
            
            $this->b250 = 0;
            $this->i250 = 0;
            
            $this->b260 = 0;
            $this->i260 = 0;
    }
    
    public function getLastBalance($month, $year, $currentCompanyId) {
      
      if( $year != 2018 ) {
        
        if( $month == 1 ) {
          $month = 11;
          $year = $year - 1;
        } else {
          $month = $month - 1;
        }
        
        //Solicita a BD el saldo_favor del periodo anterior.
        $lastBalance = CalculatedTax::where('company_id', $currentCompanyId)
                              ->where('month', $month)
                              ->where('year', $year)
                              ->where('is_final', true)
                              ->where('is_closed', true)
                              ->value('saldo_favor');
        //Si el saldo es mayor que nulo, lo pone en 0.                     
        $lastBalance = $lastBalance ? $lastBalance : 0;
        
      }else{
        $lastBalance = 0;
      }
      
      return $lastBalance;
      
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }  
  
}
