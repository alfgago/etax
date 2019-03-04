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
      
      $calculos = new CalculatedTax();
      
      $countBills = Bill::whereBetween('generated_date', [$from, $to])->count();
      $countInvoices = Invoice::whereBetween('generated_date', [$from, $to])->count();
      
      $billItems = BillItem::whereHas('bill', function ($query) use ($from, $to){
        $query->whereBetween('generated_date', [$from, $to]);
      })->get();
      
      $invoiceItems = InvoiceItem::whereHas('invoice', function ($query) use ($from, $to){
        $query->whereBetween('generated_date', [$from, $to]);
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
      
      $ratio1 = 0;
      $ratio2 = 0;
      $ratio3 = 0;
      $ratio4 = 0;
      $ratioEx = 0;
      
      $balance = 0;
      $balanceReal = 0;
      
      /**
      *
      *    Recorre todas las facturas emitidas y aumenta los montos corresponsientes.
      *
      **/
      for ($i = 0; $i < $countInvoiceItems; $i++) {
        $subtotal = $invoiceItems[$i]->subtotal;
        $ivaType = $invoiceItems[$i]->iva_type;
        $invoiceIva = $subtotal * $invoiceItems[$i]->iva_percentage / 100;
        
        $invoicesTotal += $invoiceItems[$i]->total;
        $invoicesSubtotal += $subtotal;
        $totalInvoiceIva += $invoiceIva;
        
        //Suma a las variable según el tipo de IVA que tenga.
        $bVar = "b".$ivaType;
        $iVar = "i".$ivaType;
        $calculos->$bVar += $subtotal;
        $calculos->$iVar += $invoiceIva;
        
        //Suma los del 1%
        if( $ivaType == 101 || $ivaType == 121 || $ivaType == 141 ){
          $sumaRepercutido1 += $subtotal;
        }
        
        //Suma los del 2%
        if( $ivaType == 102 || $ivaType == 122 || $ivaType == 142 ){
          $sumaRepercutido2 += $subtotal;
        }
        
        //Suma los del 13%
        if( $ivaType == 103 || $ivaType == 123 || $ivaType == 143 || $ivaType == 130 ){
          $sumaRepercutido3 += $subtotal;
        }
        
        //Suma los del 4%
        if( $ivaType == 104 || $ivaType == 124 || $ivaType == 144 ){
          $sumaRepercutido4 += $subtotal;
        }
        
        //Suma los del exentos. Estos se suman como si fueran 13 para efectos del cálculo.
        if( $ivaType == 150 || $ivaType == 160 || $ivaType == 199 ){
          $sumaRepercutido3 += $subtotal;
        }
   
        if( $ivaType > 199 ){
          $sumaRepercutidoEx += $subtotal;
        }
        
      }
      
      /**
      *
      *    Recorre todas las facturas recibidas y aumenta los montos corresponsientes.
      *
      **/
      for ($i = 0; $i < $countBillItems; $i++) {
        $subtotal = $billItems[$i]->subtotal;
        $ivaType = $billItems[$i]->iva_type;
        $billIva = $subtotal * $billItems[$i]->iva_percentage / 100;
        
        $billsTotal += $billItems[$i]->total;
        $billsSubtotal += $subtotal;
        $totalBillIva += $billIva;
        
        if( $ivaType == '61' || $ivaType == '62' || $ivaType == '63' || $ivaType == '64' )
        {
          $ivaSoportado100Deducible += $billItems[$i]->subtotal;
        }
        if( $ivaType == '70' || $ivaType == '77' )
        {
          $ivaSoportadoNoDeducible+= $billItems[$i]->subtotal;
        }
        
        $bVar = "b".$ivaType;
        $iVar = "i".$ivaType;
        
        $calculos->$bVar += $subtotal;
        $calculos->$iVar += $billIva;
        
      }
      
      //Determina numerador y denominador.
      $numeradorProrrata = $invoicesSubtotal - $sumaRepercutidoEx;
      $denumeradorProrrata = $invoicesSubtotal;
      
      if( $invoicesSubtotal > 0 ){
        //Calcula prorrata
        $prorrata = $numeradorProrrata / $denumeradorProrrata;
        
        //Define los ratios por tipo para calclo de prorrata
        $ratio1 = $sumaRepercutido1 / $numeradorProrrata;
        $ratio2 = $sumaRepercutido2 / $numeradorProrrata;
        $ratio3 = $sumaRepercutido3 / $numeradorProrrata;
        $ratio4 = $sumaRepercutido4 / $numeradorProrrata;
        
        //Calcula el total deducible y no deducible en base a los ratios y los montos de facturas recibidas.
        //$subtotalParaCFDP = $billsSubtotal - $ivaSoportado100Deducible - $ivaSoportadoNoDeducible;
        $subtotalParaCFDP = $billsSubtotal - $ivaSoportado100Deducible - $ivaSoportadoNoDeducible;
        $cfdp = $subtotalParaCFDP*$ratio1*0.01 + $subtotalParaCFDP*$ratio2*0.02 + $subtotalParaCFDP*$ratio3*0.13 + $subtotalParaCFDP*$ratio4*0.04 ;
        
        $calculos->subtotalParaCFDP = $subtotalParaCFDP;
        $calculos->CFDP = $cfdp;
      
        //Calcula el IVA deducible. Usa la prorrata del mes actual.
        $deductableIva = $cfdp * $prorrata;
        $nonDeductableIva = $totalBillIva - $deductableIva;
        
        //Calcula el balance.
        $balance = -$lastBalance + $totalInvoiceIva - $deductableIva;

        $deductableIVAReal = $cfdp * $lastProrrata;
        $balanceReal = -$lastBalance + $totalInvoiceIva - $deductableIVAReal;
        
        //Define los ratios por tipo para guardar
        $ratio1 = $sumaRepercutido1 / $invoicesSubtotal;
        $ratio2 = $sumaRepercutido2 / $invoicesSubtotal;
        $ratio3 = $sumaRepercutido3 / $invoicesSubtotal;
        $ratio4 = $sumaRepercutido4 / $invoicesSubtotal;
        $ratioEx = $sumaRepercutidoEx / $invoicesSubtotal;
      }
      
      //Guarda la instancia de calculos para no tener que volver a calcular si no hay cambios
      $calculos->count_invoices = number_format($countInvoices, 0);
      $calculos->count_bills = number_format($countBills, 0);
      $calculos->count_invoice_items = number_format($countInvoiceItems, 0);
      $calculos->count_bill_items = number_format($countBillItems, 0);
      $calculos->last_prorrata = number_format($lastProrrata, 2);
      $calculos->last_balance = number_format($lastBalance, 2);
      $calculos->prorrata = number_format($prorrata, 2);
      $calculos->non_deductable_iva = number_format($nonDeductableIva, 2);
      $calculos->deductable_iva = number_format($deductableIva, 2);
      $calculos->deductable_iva_real = number_format($deductableIVAReal, 2);
      $calculos->bills_subtotal = number_format($billsSubtotal, 2);
      $calculos->invoices_subtotal = number_format($invoicesSubtotal, 2);
      $calculos->invoices_total_exempt = number_format($sumaRepercutidoEx, 2);
      $calculos->bills_total = number_format($billsTotal, 2);
      $calculos->invoices_total = number_format($invoicesTotal, 2);
      $calculos->total_invoice_iva = number_format($totalInvoiceIva, 2);
      $calculos->total_bill_iva = number_format($totalBillIva, 2);
      $calculos->balance = number_format($balance, 2);
      $calculos->balance_real = number_format($balanceReal, 2);
      
      $calculos->ratio1 = number_format($ratio1, 2);
      $calculos->ratio2 = number_format($ratio2, 2);
      $calculos->ratio3 = number_format($ratio3, 2);
      $calculos->ratio4 = number_format($ratio4, 2);
      $calculos->ratio_ex = number_format($ratioEx, 2);

      return $calculos;
    }
  
}
