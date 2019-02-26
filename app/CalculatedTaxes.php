<?php

namespace App;

use \Carbon\Carbon;
use App\InvoiceItem;
use App\BillItem;
use App\Invoice;
use App\Bill;
use App\Company;
use Illuminate\Database\Eloquent\Model;

class CalculatedTaxes extends Model
{
    protected $table = 'calculated_taxes';
    
    protected $guarded = [];
  
    //Recibe fecha de inicio y fecha de fin en base a las cuales se desea calcular la prorrata.
    public static function calcularFacturacion( $from, $to, $lastBalance, $lastProrrata ) {
      
      $calculos = new CalculatedTaxes();
      
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
      $invoicesSubtotal = 0;
      $invoicesTotalNotExempt = 0;
      $totalInvoiceIva = 0;
      $totalBillIva = 0;
      $iva100deducible = 0;
      
      $sumaRepercutido1 = 0;
      $sumaRepercutido2 = 0;
      $sumaRepercutido3 = 0;
      $sumaRepercutido4 = 0;
      $sumaRepercutidoEx = 0;
      
      $ratio1 = 0;
      $ratio2 = 0;
      $ratio3 = 0;
      $ratio4 = 0;
      
      $balance = 0;
      
      //Recorre todas las facturas emitidas y aumenta los montos corresponsientes.
      for ($i = 0; $i < $countInvoiceItems; $i++) {
        $subtotal = $invoiceItems[$i]->subtotal;
        
        if( $invoiceItems[$i]->iva_type > 199 ){
          $invoicesTotalNotExempt += $subtotal;
        }
        $invoicesSubtotal += $subtotal;
        $totalInvoiceIva += $subtotal * $invoiceItems[$i]->iva_percentage / 100;
        
        //Suma los del 1%
        if( $invoiceItems[$i]->iva_type == 101 || $invoiceItems[$i]->iva_type == 121 || $invoiceItems[$i]->iva_type == 141 ){
          $sumaRepercutido1 += $subtotal;
        }
        
        //Suma los del 2%
        if( $invoiceItems[$i]->iva_type == 102 || $invoiceItems[$i]->iva_type == 122 || $invoiceItems[$i]->iva_type == 142 ){
          $sumaRepercutido2 += $subtotal;
        }
        
        //Suma los del 13%
        if( $invoiceItems[$i]->iva_type == 103 || $invoiceItems[$i]->iva_type == 123 || $invoiceItems[$i]->iva_type == 143 || $invoiceItems[$i]->iva_type == 130 ){
          $sumaRepercutido3 += $subtotal;
        }
        
        //Suma los del 4%
        if( $invoiceItems[$i]->iva_type == 104 || $invoiceItems[$i]->iva_type == 124 || $invoiceItems[$i]->iva_type == 144 ){
          $sumaRepercutido4 += $subtotal;
        }
        
        //Suma los del exentos. Estos se suman como si fueran 13 para efectos del cálculo.
        if( $invoiceItems[$i]->iva_type == 150 || $invoiceItems[$i]->iva_type == 160 || $invoiceItems[$i]->iva_type == 199 ){
          $sumaRepercutido3 += $subtotal;
        }
        
      }
      
      //Recorre todas las facturas recibidas y aumenta los montos corresponsientes.
      for ($i = 0; $i < $countBillItems; $i++) {
        $billsSubtotal += $billItems[$i]->subtotal;
        $totalBillIva += $billItems[$i]->subtotal * $billItems[$i]->iva_percentage / 100;
        
        if( $billItems[$i]->iva_type == '61' || $billItems[$i]->iva_type == '62' || $billItems[$i]->iva_type == '63' || $billItems[$i]->iva_type == '64' )
        {
          $iva100deducible += $billItems[$i]->subtotal;
        }
      }
      //Resta el 100% deducible al subtotal, para que no sea usado en prorrata.
      $billsSubtotal = $billsSubtotal - $iva100deducible;
      
      //Determina numerador y denominador.
      $numeradorProrrata = $invoicesSubtotal - $invoicesTotalNotExempt;
      $denumeradorProrrata = $invoicesSubtotal;
      
      
      if( $invoicesSubtotal > 0 ){
        $prorrata = $numeradorProrrata / $denumeradorProrrata;
        //$importeIVA = $totalInvoiceIva - ( $totalBillIva * $prorrata );
        
        $ratio1 = $sumaRepercutido1 / $numeradorProrrata;
        $ratio2 = $sumaRepercutido2 / $numeradorProrrata;
        $ratio3 = $sumaRepercutido3 / $numeradorProrrata;
        $ratio4 = $sumaRepercutido4 / $numeradorProrrata;
        
        $deductableIva = ($billsSubtotal*$ratio1*0.01 + $billsSubtotal*$ratio2*0.02 + $billsSubtotal*$ratio3*0.13 + $billsSubtotal*$ratio4*0.04) * $prorrata ;
        $nonDeductableIva = $totalBillIva - $deductableIva;
        $balance = -$lastBalance + $totalInvoiceIva - $deductableIva;

        $deductableIva_anterior = ($billsSubtotal*$ratio1*0.01 + $billsSubtotal*$ratio2*0.02 + $billsSubtotal*$ratio3*0.13 + $billsSubtotal*$ratio4*0.04) * $lastProrrata ;
        $balance_anterior = -$lastBalance + $totalInvoiceIva - $deductableIva_anterior;
        
      }
      
      //Guarda la instancia de calculos para no tener que volver a calcular si no hay cambios
      $calculos->count_invoice_items = $countInvoiceItems;
      $calculos->count_bill_items = $countBillItems;
      $calculos->last_prorrata = $lastProrrata;
      $calculos->last_balance = $lastBalance;
      $calculos->prorrata = $prorrata;
      $calculos->non_deductable_iva = $nonDeductableIva;
      $calculos->deductable_iva = $deductableIva;
      $calculos->bills_subtotal = $billsSubtotal;
      $calculos->invoices_subtotal = $invoicesSubtotal;
      $calculos->invoices_total_not_exempt = $invoicesTotalNotExempt;
      $calculos->total_invoice_iva = $totalInvoiceIva;
      $calculos->total_bill_iva = $totalBillIva;
      $calculos->ratio1 = $ratio1;
      $calculos->ratio2 = $ratio2;
      $calculos->ratio3 = $ratio3;
      $calculos->ratio4 = $ratio4;
      $calculos->balance = $balance;
      
      return $calculos;
    }
  
}
