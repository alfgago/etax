<?php

namespace App;

use \Carbon\Carbon;
use App\InvoiceItem;
use App\BillItem;
use App\Invoice;
use App\Bill;
use App\Company;
use Illuminate\Database\Eloquent\Model;

class CalculatedTax extends Model
{
    protected $table = 'calculated_taxes';
    
    protected $guarded = [];
    
    public static function calcularFacturacionPorMesAno( $month, $year, $lastBalance, $lastProrrata ) {
      
      $from = new Carbon("first day of January $year");
      $to = new Carbon("last day of December $year");
      if( $month == 1 ){
        $from = new Carbon("first day of January $year");
        $to = new Carbon("last day of January $year");
      }else if( $month == 2 ){
        $from = new Carbon("first day of February $year");
        $to = new Carbon("last day of February $year");
      }else if( $month == 3 ){
        $from = new Carbon("first day of March $year");
        $to = new Carbon("last day of March $year");
      }else if( $month == 4 ){
        $from = new Carbon("first day of April $year");
        $to = new Carbon("last day of April $year");
      }else if( $month == 5 ){
        $from = new Carbon("first day of May $year");
        $to = new Carbon("last day of May $year");
      }else if( $month == 6 ){
        $from = new Carbon("first day of June $year");
        $to = new Carbon("last day of June $year");
      }else if( $month == 7 ){
        $from = new Carbon("first day of July $year");
        $to = new Carbon("last day of July $year");
      }else if( $month == 8 ){
        $from = new Carbon("first day of August $year");
        $to = new Carbon("last day of August $year");
      }else if( $month == 9 ){
        $from = new Carbon("first day of September $year");
        $to = new Carbon("last day of September $year");
      }else if( $month == 10 ){
        $from = new Carbon("first day of October $year");
        $to = new Carbon("last day of October $year");
      }else if( $month == 11 ){
        $from = new Carbon("first day of November $year");
        $to = new Carbon("last day of November $year");
      }else if( $month == 12 ){
        $from = new Carbon("first day of December $year");
        $to = new Carbon("last day of December $year");
      }else if( $month == 0 ){
        $from = new Carbon("first day of January $year");
        $to = new Carbon("last day of December $year");
      }else if( $month == -1 ){
        $from = new Carbon("first day of June $year");
        $to = new Carbon("last day of December $year");
      }
      
      return CalculatedTax::calcularFacturacion( $from, $to, $lastBalance, $lastProrrata );
      
    }
  
    //Recibe fecha de inicio y fecha de fin en base a las cuales se desea calcular la prorrata.
    public static function calcularFacturacion( $from, $to, $lastBalance, $lastProrrata ) {
      $current_company = auth()->user()->companies->first()->id;
      
      $calculos = new CalculatedTax();
      
      $countBills = Bill::where('company_id', $current_company)->whereBetween('generated_date', [$from, $to])->count();
      $countInvoices = Invoice::where('company_id', $current_company)->whereBetween('generated_date', [$from, $to])->count();
      
      $billItems = BillItem::with('bill')->whereHas('bill', function ($query) use ($from, $to, $current_company){
        $query->whereBetween('generated_date', [$from, $to]);
        $query->where('company_id', $current_company);
      })->get();
      
      $invoiceItems = InvoiceItem::with('invoice')->whereHas('invoice', function ($query) use ($from, $to, $current_company){
        $query->whereBetween('generated_date', [$from, $to]);
        $query->where('company_id', $current_company);
      })->get();
      
      $countInvoiceItems = count( $invoiceItems );
      $countBillItems = count( $billItems );
      
      $prorrata = 0;
      $importeIVA = 0;
      $nonDeductableIva = 0;
      $deductableIva = 0;
      $billsSubtotal = 0;
      $billsTotal = 0;
      $invoicesTotal = 0;
      $invoicesSubtotal = 0;
      $totalInvoiceIva = 0;
      $totalBillIva = 0;
      $ivaSoportado100Deducible = 0;
      $ivaSoportadoNoDeducible = 0;
      $deductableIVAReal = 0;
      
      $sumaRepercutido1 = 0;
      $sumaRepercutido2 = 0;
      $sumaRepercutido3 = 0;
      $sumaRepercutido4 = 0;
      $sumaRepercutidoEx = 0;
      $sumaRepercutidoExCredito = 0;
      
      $ratio1 = 0;
      $ratio2 = 0;
      $ratio3 = 0;
      $ratio4 = 0;
      $ratioEx = 0;
      
      $fakeRatio1 = 0;
      $fakeRatio2 = 0;
      $fakeRatio3 = 0;
      $fakeRatio4 = 0;
      $fakeRatioEx = 0;
      $fakeRatioExCredito = 0;
      
      $balance = 0;
      $balanceReal = 0;
      
      $total_proveedores_credito = 0;
      $total_proveedores_contado = 0;
      $total_clientes_credito = 0;
      $total_clientes_contado = 0; 
      $total_clientes_credito_exp = 0;
      $total_clientes_contado_exp = 0;
      
      /**
      *
      *    Recorre todas las facturas emitidas y aumenta los montos corresponsientes.
      *
      **/
      for ($i = 0; $i < $countInvoiceItems; $i++) {
        $subtotal = $invoiceItems[$i]->subtotal * $invoiceItems[$i]->invoice->currency_rate;
        $current_total = $invoiceItems[$i]->total * $invoiceItems[$i]->invoice->currency_rate;
        $ivaType = $invoiceItems[$i]->iva_type;
        $invoiceIva = $invoiceItems[$i]->iva_amount * $invoiceItems[$i]->invoice->currency_rate;
        
        $invoicesTotal += $current_total;
        $invoicesSubtotal += $subtotal;
        $totalInvoiceIva += $invoiceIva;
        
                $tipo_venta = $invoiceItems[$i]->invoice->sale_condition;
                if( $ivaType == '150' ){
                  if( $tipo_venta == '01' ) {
                    $total_clientes_contado_exp += $current_total;
                  }else {
                    $total_clientes_credito_exp += $current_total;
                  }
                }else{
                  if( $tipo_venta == '01' ) {
                    $total_clientes_contado += $current_total;
                  }else {
                    $total_clientes_credito += $current_total;
                  }
                }
        
        //Suma a las variable según el tipo de IVA que tenga.
        $bVar = "b".$ivaType;
        $iVar = "i".$ivaType;
        $calculos->$bVar += $subtotal;
        $calculos->$iVar += $invoiceIva;
        
        //Suma los del 1%
        if( $ivaType == '101' || $ivaType == '121' || $ivaType == '141' ){
          $sumaRepercutido1 += $subtotal;
        }
        
        //Suma los del 2%
        if( $ivaType == '102' || $ivaType == '122' || $ivaType == '142' ){
          $sumaRepercutido2 += $subtotal;
        }
        
        //Suma los del 13%
        if( $ivaType == '103' || $ivaType == '123' || $ivaType == '143' || $ivaType == '130' ){
          $sumaRepercutido3 += $subtotal;
        }
        
        //Suma los del 4%
        if( $ivaType == '104' || $ivaType == '124' || $ivaType == '144' ){
          $sumaRepercutido4 += $subtotal;
        }
        
        //Suma los del exentos. Estos se suman como si fueran 13 para efectos del cálculo.
        if( $ivaType == '150' || $ivaType == '160' || $ivaType == '199' ){
          $sumaRepercutido3 += $subtotal;
          $sumaRepercutidoExCredito += $subtotal;
        }
   
        if( $ivaType == '200' || $ivaType == '201' || $ivaType == '240' || $ivaType == '250' || $ivaType == '260' ){
          $sumaRepercutidoEx += $subtotal;
        }
        
      }
      
      /**
      *
      *    Recorre todas las facturas recibidas y aumenta los montos corresponsientes.
      *
      **/
      for ($i = 0; $i < $countBillItems; $i++) {
        $subtotal = $billItems[$i]->subtotal * $billItems[$i]->bill->currency_rate;
        $current_total = $billItems[$i]->total * $billItems[$i]->bill->currency_rate;
        $ivaType = $billItems[$i]->iva_type;
        $billIva = $billItems[$i]->iva_amount * $billItems[$i]->bill->currency_rate;
        
        $billsTotal += $current_total;
        $billsSubtotal += $subtotal;
        $totalBillIva += $billIva;
        
        if( $ivaType == '041' || $ivaType == '042' || $ivaType == '043' || $ivaType == '044' ||
            $ivaType == '051' || $ivaType == '052' || $ivaType == '053' || $ivaType == '054' || 
            $ivaType == '061' || $ivaType == '062' || $ivaType == '063' || $ivaType == '064' || 
            $ivaType == '071' || $ivaType == '072' || $ivaType == '073' || $ivaType == '074'
        )
        {
          $ivaSoportado100Deducible += $subtotal;
        }
        if( $ivaType == '080' || $ivaType == '090' || $ivaType == '097' )
        {
          $ivaSoportadoNoDeducible += $subtotal;
        }
        
        $bVar = "b".$ivaType;
        $iVar = "i".$ivaType;
        
        $calculos->$bVar += $subtotal;
        $calculos->$iVar += $billIva;
        
        //Cuenta contable de proveedor
        $tipo_venta = $billItems[$i]->bill->sale_condition;
        if( $tipo_venta == '01' ) {
          $total_proveedores_contado += $current_total;
        }else{
          $total_proveedores_credito += $current_total;
        }
      }
      
      //Determina numerador y denominador.
      $numeradorProrrata = $invoicesSubtotal - $sumaRepercutidoEx;
      $denumeradorProrrata = $invoicesSubtotal;
      
      if( $invoicesSubtotal > 0 ){
        
        //Define los ratios por tipo para calclo de prorrata
        $ratio1 = $sumaRepercutido1 / $numeradorProrrata;
        $ratio2 = $sumaRepercutido2 / $numeradorProrrata;
        $ratio3 = $sumaRepercutido3 / $numeradorProrrata;
        $ratio4 = $sumaRepercutido4 / $numeradorProrrata;
        
        //Calcula prorrata
        $prorrata = $numeradorProrrata / $denumeradorProrrata;
        
        //Calcula el total deducible y no deducible en base a los ratios y los montos de facturas recibidas.
        //$subtotalParaCFDP = $billsSubtotal - $ivaSoportado100Deducible - $ivaSoportadoNoDeducible;
        $subtotalParaCFDP = $billsSubtotal - $ivaSoportado100Deducible - $ivaSoportadoNoDeducible;
        $cfdp = $subtotalParaCFDP*$ratio1*0.01 + $subtotalParaCFDP*$ratio2*0.02 + $subtotalParaCFDP*$ratio3*0.13 + $subtotalParaCFDP*$ratio4*0.04 ;
        
        $calculos->subtotalParaCFDP = $subtotalParaCFDP;
        $calculos->CFDP = $cfdp;
      
        //Calcula el IVA deducible. Usa la prorrata del mes actual.
        $deductableIva = $cfdp * $prorrata;
        
        //Calcula el balance.
        $balance = -$lastBalance + $totalInvoiceIva - $deductableIva;

        $deductableIVAReal = $cfdp * $lastProrrata;
        $balanceReal = -$lastBalance + $totalInvoiceIva - $deductableIVAReal;
        $nonDeductableIva = $totalBillIva - $deductableIVAReal;
        
      
        //Define los ratios por tipo para guardar
        $fakeRatio1 = $sumaRepercutido1 / $invoicesSubtotal;
        $fakeRatio2 = $sumaRepercutido2 / $invoicesSubtotal;
        $fakeRatio3 = ($sumaRepercutido3-$sumaRepercutidoExCredito) / $invoicesSubtotal;
        $fakeRatio4 = $sumaRepercutido4 / $invoicesSubtotal;
        $fakeRatioEx = $sumaRepercutidoEx / $invoicesSubtotal;
        $fakeRatioExCredito = $sumaRepercutidoExCredito / $invoicesSubtotal;
      }
      
      //Guarda la instancia de calculos para no tener que volver a calcular si no hay cambios
      $calculos->count_invoices = $countInvoices;
      $calculos->count_bills = $countBills;
      $calculos->count_invoice_items = $countInvoiceItems;
      $calculos->count_bill_items = $countBillItems;
      $calculos->last_prorrata = $lastProrrata;
      $calculos->last_balance = $lastBalance;
      $calculos->prorrata = $prorrata;
      $calculos->non_deductable_iva = $nonDeductableIva;
      $calculos->deductable_iva = $deductableIva;
      $calculos->deductable_iva_real = $deductableIVAReal;
      $calculos->bills_subtotal = $billsSubtotal;
      $calculos->invoices_subtotal = $invoicesSubtotal;
      $calculos->invoices_total_exempt = $sumaRepercutidoEx;
      $calculos->bills_total = $billsTotal;
      $calculos->invoices_total = $invoicesTotal;
      $calculos->total_invoice_iva = $totalInvoiceIva;
      $calculos->total_bill_iva = $totalBillIva;
      $calculos->balance = $balance;
      $calculos->balance_real = $balanceReal;
      
      $calculos->total_proveedores_credito = $total_proveedores_credito;
      $calculos->total_proveedores_contado = $total_proveedores_contado;
      $calculos->total_clientes_credito = $total_clientes_credito;
      $calculos->total_clientes_contado = $total_clientes_contado; 
      $calculos->total_clientes_credito_exp = $total_clientes_credito_exp;
      $calculos->total_clientes_contado_exp = $total_clientes_contado_exp;
      
      $calculos->ratio1 = $ratio1;
      $calculos->ratio2 = $ratio2;
      $calculos->ratio3 = $ratio3;
      $calculos->ratio4 = $ratio4;
      $calculos->ratio_ex = $ratioEx;
      
      $calculos->fake_ratio1 = $fakeRatio1;
      $calculos->fake_ratio2 = $fakeRatio2;
      $calculos->fake_ratio3 = $fakeRatio3;
      $calculos->fake_ratio4 = $fakeRatio4;
      $calculos->fake_ratio_ex = $fakeRatioEx;
      $calculos->fake_ratio_ex_c = $fakeRatioExCredito;
      
      $calculos = CalculatedTax::asignarCuentasContables( $calculos );

      return $calculos;
    }
    
    public static function asignarCuentasContables( $calculos ){
      /**Calculos de Cuentas contables**/
      
      //Compras
            //Debe 1
              $calculos->cc_compras = $calculos->b001 + $calculos->b002 + $calculos->b003 + $calculos->b004 + 
                                      $calculos->b061 + $calculos->b062 + $calculos->b063 + $calculos->b064; 
                                      
              $calculos->cc_importaciones = $calculos->b021 + $calculos->b022 + $calculos->b023 + $calculos->b024 +
                                            $calculos->b031 + $calculos->b032 + $calculos->b033 + $calculos->b034 +
                                            $calculos->b041 + $calculos->b042 + $calculos->b043 + $calculos->b044 +
                                            $calculos->b051 + $calculos->b052 + $calculos->b053 + $calculos->b054;
              
              $calculos->cc_propiedades = $calculos->b011 + $calculos->b012 + $calculos->b013 + $calculos->b014 + 
                                          $calculos->b071 + $calculos->b072 + $calculos->b073 + $calculos->b074;
              
              $calculos->cc_iva_compras = $calculos->i001 + $calculos->i002 + $calculos->i003 + $calculos->i004 + 
                                          $calculos->i061 + $calculos->i062 + $calculos->i063 + $calculos->i064;
                                          
                                          
              $calculos->cc_iva_importaciones = $calculos->i021 + $calculos->i022 + $calculos->i023 + $calculos->i024 +
                                                $calculos->i031 + $calculos->i032 + $calculos->i033 + $calculos->i034 +
                                                $calculos->i041 + $calculos->i042 + $calculos->i043 + $calculos->i044 +
                                                $calculos->i051 + $calculos->i052 + $calculos->i053 + $calculos->i054;
                                                
              $calculos->cc_iva_propiedades = $calculos->i011 + $calculos->i012 + $calculos->i013 + $calculos->i014 + 
                                              $calculos->i071 + $calculos->i072 + $calculos->i073 + $calculos->i074;  
                                            
              $calculos->cc_compras_sin_derecho = $calculos->b080 + $calculos->b090 + $calculos->b097 + 
                                      $calculos->i080 + $calculos->i090 + $calculos->i097;                                
              
            //Haber 1
              $calculos->cc_proveedores_credito = $calculos->total_proveedores_credito;
              $calculos->cc_proveedores_contado = $calculos->total_proveedores_contado;
      
      //Ventas  
            //Haber 2 
              $calculos->cc_ventas_1 = $calculos->b101 + $calculos->b121;
              $calculos->cc_ventas_2 = $calculos->b102 + $calculos->b122;
              $calculos->cc_ventas_13 = $calculos->b103 + $calculos->b123;
              $calculos->cc_ventas_4 = $calculos->b104 + $calculos->b124;
              $calculos->cc_ventas_exp = $calculos->b150;
              $calculos->cc_ventas_estado = $calculos->b160;
              $calculos->cc_ventas_1_iva = $calculos->i101 + $calculos->i121;
              $calculos->cc_ventas_2_iva = $calculos->i102 + $calculos->i122;
              $calculos->cc_ventas_13_iva = $calculos->i103 + $calculos->i123 + $calculos->i130;
              $calculos->cc_ventas_4_iva = $calculos->i104 + $calculos->i124;
              $calculos->cc_ventas_sin_derecho = $calculos->b200 + $calculos->b201 + $calculos->b240 + $calculos->b250 + $calculos->b260;
              $calculos->cc_ventas_sum = $calculos->cc_ventas_1 + $calculos->cc_ventas_2 + $calculos->cc_ventas_13 + $calculos->cc_ventas_4 + 
                                         $calculos->cc_ventas_1_iva + $calculos->cc_ventas_2_iva + $calculos->cc_ventas_13_iva + $calculos->cc_ventas_4_iva + 
                                         $calculos->cc_ventas_exp + $calculos->cc_ventas_estado + $calculos->cc_ventas_sin_derecho;
              
            //Debe 2
              $calculos->cc_clientes_credito = $calculos->total_clientes_credito;
              $calculos->cc_clientes_contado = $calculos->total_clientes_contado;  
              $calculos->cc_clientes_credito_exp = $calculos->total_clientes_credito_exp;
              $calculos->cc_clientes_contado_exp = $calculos->total_clientes_contado_exp;  
              $calculos->cc_clientes_sum = $calculos->cc_clientes_credito + $calculos->cc_clientes_contado + $calculos->cc_clientes_credito_exp + $calculos->cc_clientes_contado_exp;
        

       //Ajuste Periodificacion 
            //Haber 3
              $calculos->cc_ppp_1 = $calculos->i011 + $calculos->i031 + $calculos->i051 + $calculos->i071;
              $calculos->cc_ppp_2 = $calculos->i012 + $calculos->i032 + $calculos->i052 + $calculos->i072;
              $calculos->cc_ppp_3 = $calculos->i013 + $calculos->i033 + $calculos->i053 + $calculos->i073;
              $calculos->cc_ppp_4 = $calculos->i014 + $calculos->i034 + $calculos->i054 + $calculos->i074;
              
              $calculos->cc_bs_1 = $calculos->i001 + $calculos->i021 + $calculos->i041 + $calculos->i061;
              $calculos->cc_bs_2 = $calculos->i002 + $calculos->i022 + $calculos->i042 + $calculos->i062;
              $calculos->cc_bs_3 = $calculos->i003 + $calculos->i023 + $calculos->i043 + $calculos->i063;
              $calculos->cc_bs_4 = $calculos->i004 + $calculos->i024 + $calculos->i044 + $calculos->i064;
              
              $calculos->cc_por_pagar = $calculos->balance_real;
            
            //Debe 3 
              $calculos->cc_iva_emitido_1 = $calculos->i101 + $calculos->i121;
              $calculos->cc_iva_emitido_2 = $calculos->i102 + $calculos->i122;
              $calculos->cc_iva_emitido_3 = $calculos->i103 + $calculos->i123;
              $calculos->cc_iva_emitido_4 = $calculos->i104 + $calculos->i124;  
              $bases_ppp = $calculos->bi011 + $calculos->b031 + $calculos->b051 + $calculos->b071
              + $calculos->b012 + $calculos->b032 + $calculos->b052 + $calculos->b072
              + $calculos->b013 + $calculos->b033 + $calculos->b053 + $calculos->b073
              + $calculos->b014 + $calculos->b034 + $calculos->b054 + $calculos->b074;
              
              $bases_bs = $calculos->b001 + $calculos->b021 + $calculos->b041 + $calculos->b061
              + $calculos->b002 + $calculos->b022 + $calculos->b042 + $calculos->b062
              + $calculos->b003 + $calculos->b023 + $calculos->b043 + $calculos->b063
              + $calculos->b004 + $calculos->b024 + $calculos->b044 + $calculos->b064;
              
              $acreditable_bs = ( ($bases_bs * $calculos->ratio1 * 0.01) + ($bases_bs * $calculos->ratio2 * 0.02) + ($bases_bs * $calculos->ratio3 * 0.13) + ($bases_bs * $calculos->ratio4 * 0.04) ) * $calculos->last_prorrata;
              $acreditable_ppp = ( ($bases_ppp * $calculos->ratio1 * 0.01) + ($bases_ppp * $calculos->ratio2 * 0.02) + ($bases_ppp * $calculos->ratio3 * 0.13) + ($bases_ppp * $calculos->ratio4 * 0.04) ) * $calculos->last_prorrata;
      
              $calculos->cc_ajuste_ppp =  - $acreditable_ppp + $calculos->cc_ppp_1 + $calculos->cc_ppp_2 + + $calculos->cc_ppp_3 + + $calculos->cc_ppp_4; 
              $calculos->cc_ajuste_bs =  - $acreditable_bs + $calculos->cc_bs_1 + $calculos->cc_bs_2 + $calculos->cc_bs_3 + $calculos->cc_bs_4;
              
              $calculos->cc_sum2 = $calculos->cc_ppp_1 + $calculos->cc_ppp_2 + $calculos->cc_ppp_3 + $calculos->cc_ppp_4 + $calculos->cc_por_pagar
                                   + $calculos->cc_bs_1 + $calculos->cc_bs_2 + $calculos->cc_bs_3 + $calculos->cc_bs_4;
              $calculos->cc_sum1 = $calculos->cc_iva_emitido_1 + $calculos->cc_iva_emitido_2 + $calculos->cc_iva_emitido_3 + $calculos->cc_iva_emitido_4 + $calculos->cc_ajuste_ppp + $calculos->cc_ajuste_bs;
      
        return $calculos;
    }
  
}
