<?php

namespace App;

use \Carbon\Carbon;
use App\InvoiceItem;
use App\BillItem;
use App\Invoice;
use App\Bill;
use App\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CalculatedTax extends Model
{
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
        return $this->belongsTo(Book::class);
    }
    
    public static function calcularFacturacionPorMesAno( $month, $year, $lastBalance, $prorrataOperativa ) {
      
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
      
      $current_company = auth()->user()->companies->first()->id;
      
      $cacheKey = "cache-taxes-$current_company-$month-$year";
      if ( !Cache::has($cacheKey) ) {
          $data = new CalculatedTax();
          $data->calcularFacturacion( $from, $to, $lastBalance, $prorrataOperativa );
          Cache::put($cacheKey, $data, now()->addDays(90));
      }
      return Cache::get($cacheKey);
      
      
    }
    
    public static function clearTaxesCache($current_company, $month, $year){
      $cacheKey = "cache-taxes-$current_company-$month-$year";
      Cache::forget($cacheKey);
    }
  
     
    /**
    *    Recorre todas las facturas emitidas y aumenta los montos correspondientes.
    **/
    public function setDatosEmitidos ( $from, $to, $company ) {
      
      $invoiceItems = InvoiceItem::with('invoice')->whereHas('invoice', function ($query) use ($from, $to, $company){
        $query->whereBetween('generated_date', [$from, $to]);
        $query->where('company_id', $company);
      })->get();
      $countInvoices = Invoice::where('company_id', $company)->whereBetween('generated_date', [$from, $to])->count();
      $countInvoiceItems = count( $invoiceItems );
      
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
      
      for ($i = 0; $i < $countInvoiceItems; $i++) {
        $subtotal = $invoiceItems[$i]->subtotal * $invoiceItems[$i]->invoice->currency_rate;
        $currentTotal = $invoiceItems[$i]->total * $invoiceItems[$i]->invoice->currency_rate;
        $ivaType = $invoiceItems[$i]->iva_type;
        $invoiceIva = $invoiceItems[$i]->iva_amount * $invoiceItems[$i]->invoice->currency_rate;
        
        $invoicesTotal += $currentTotal;
        $invoicesSubtotal += $subtotal;
        $totalInvoiceIva += $invoiceIva;
        
        $tipo_venta = $invoiceItems[$i]->invoice->sale_condition;
        if( $ivaType == '150' ){
          if( $tipo_venta == '01' ) {
            $totalClientesContadoExp += $currentTotal;
          }else {
            $totalClientesCreditoExp += $currentTotal;
          }
        }else{
          if( $tipo_venta == '01' ) {
            $totalClientesContadoLocal += $currentTotal;
          }else {
            $totalClientesCreditoLocal += $currentTotal;
          }
        }
        
        //sum a las variable según el tipo de IVA que tenga.
        $bVar = "b".$ivaType;
        $iVar = "i".$ivaType;
        $this->$bVar += $subtotal;
        $this->$iVar += $invoiceIva;
        
        //sum los del 1%
        if( $ivaType == '101' || $ivaType == '121' || $ivaType == '141' ){
          $sumRepercutido1 += $subtotal;
        }
        
        //sum los del 2%
        if( $ivaType == '102' || $ivaType == '122' || $ivaType == '142' ){
          $sumRepercutido2 += $subtotal;
        }
        
        //sum los del 13%
        if( $ivaType == '103' || $ivaType == '123' || $ivaType == '143' || $ivaType == '130' ){
          $sumRepercutido3 += $subtotal;
        }
        
        //sum los del 4%
        if( $ivaType == '104' || $ivaType == '124' || $ivaType == '144' ){
          $sumRepercutido4 += $subtotal;
        }
        
        //sum los del exentos. Estos se sumn como si fueran 13 para efectos del cálculo.
        if( $ivaType == '150' || $ivaType == '160' || $ivaType == '199' ){
          $sumRepercutido3 += $subtotal;
          $sumRepercutidoExentoConCredito += $subtotal;
        }
   
        if( $ivaType == '200' || $ivaType == '201' || $ivaType == '240' || $ivaType == '250' || $ivaType == '260' ){
          $sumRepercutidoExentoSinCredito += $subtotal;
        }
      }
      
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
      
      return $this;
    }
  
    /**
    *   Recorre todas las facturas recibidas y aumenta los montos correspondientes.
    **/
    public function setDatosSoportados ( $from, $to, $company ) {
      $billItems = BillItem::with('bill')->whereHas('bill', function ($query) use ($from, $to, $company){
        $query->whereBetween('generated_date', [$from, $to]);
        $query->where('company_id', $company);
      })->get();
      $countBills = Bill::where('company_id', $company)->whereBetween('generated_date', [$from, $to])->count();
      $countBillItems = count( $billItems );
  
      $billsTotal = 0;
      $billsSubtotal = 0;
      $totalBillIva = 0;
      $basesIdentificacionPlena = 0; //Antes ivaSoportado100Deducible
      $basesNoDeducibles = 0; //Antes $ivaSoportadoNoDeducible
      $ivaAcreditableIdentificacionPlena = 0;
      $ivaNoAcreditableIdentificacionPlena = 0;
      $totalProveedoresContado = 0;
      $totalProveedoresCredito = 0;
      
      for ($i = 0; $i < $countBillItems; $i++) {
        $subtotal = $billItems[$i]->subtotal * $billItems[$i]->bill->currency_rate;
        $currentTotal = $billItems[$i]->total * $billItems[$i]->bill->currency_rate;
        $ivaType = $billItems[$i]->iva_type;
        $billIva = $billItems[$i]->iva_amount * $billItems[$i]->bill->currency_rate;
        
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
        
        if( $ivaType == '080' || $ivaType == '090' || $ivaType == '097' )
        {
          $basesNoDeducibles += $subtotal;
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
          $menor = $porc_plena < 2 ? 1 : $porc_plena/2;
          $ivaAcreditableIdentificacionPlena += $billIva * $menor;
          $ivaNoAcreditableIdentificacionPlena += $billIva - ($billIva * $menor);
        }
        if( $ivaType == '043' || $ivaType == '053' || $ivaType == '063' || $ivaType == '073' )
        {
          $menor = $porc_plena/13;
          $ivaAcreditableIdentificacionPlena += $billIva * $menor;
          $ivaNoAcreditableIdentificacionPlena += $billIva - ($billIva * $menor);
        }
        if( $ivaType == '044' || $ivaType == '054' || $ivaType == '064' || $ivaType == '074' )
        {
          $menor = $porc_plena < 4 ? 1 : $porc_plena/4;
          $ivaAcreditableIdentificacionPlena += $billIva * $menor;
          $ivaNoAcreditableIdentificacionPlena += $billIva - ($billIva * $menor);
        }
        /***END SACA IVAS DEDUCIBLES DE IDENTIFICAIONES PLENAS**/
        
        $bVar = "b".$ivaType;
        $iVar = "i".$ivaType;
        $this->$bVar += $subtotal;
        $this->$iVar += $billIva;
        
        //Cuenta contable de proveedor
        $tipo_venta = $billItems[$i]->bill->sale_condition;
        if( $tipo_venta == '01' ) {
          $totalProveedoresContado += $currentTotal;
        }else{
          $totalProveedoresCredito += $currentTotal;
        }
      }
      
      $this->bills_total = $billsTotal;
      $this->bills_subtotal = $billsSubtotal;
      $this->total_bill_iva = $totalBillIva;
      $this->bases_identificacion_plena = $basesIdentificacionPlena;
      $this->bases_no_deducibles = $basesNoDeducibles;
      $this->iva_acreditable_identificacion_plena = $ivaAcreditableIdentificacionPlena;
      $this->iva_no_acreditable_identificacion_plena = $ivaNoAcreditableIdentificacionPlena;
      $this->total_proveedores_contado = $totalProveedoresContado;
      $this->total_proveedores_credito = $totalProveedoresCredito;
      
      return $this;
  
    }
  
    //Recibe fecha de inicio y fecha de fin en base a las cuales se desea calcular la prorrata.
    public function calcularFacturacion( $from, $to, $lastBalance, $prorrataOperativa ) {
      $current_company = auth()->user()->companies->first()->id;

      $this->setDatosEmitidos( $from, $to, $current_company );
      $this->setDatosSoportados( $from, $to, $current_company );
      
      
      //Determina numerador y denominador.
      $numeradorProrrata = $this->invoices_subtotal - $this->sum_repercutido_exento_sin_credito - $this->bases_identificacion_plena;
      $denumeradorProrrata = $this->invoices_subtotal - $this->bases_identificacion_plena;
      
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
      
      
      if( $this->invoices_subtotal > 0 ){
        
        //Define los ratios por tipo para calclo de prorrata
        $ratio1 = $this->sum_repercutido1 / $numeradorProrrata;
        $ratio2 = $this->sum_repercutido2 / $numeradorProrrata;
        $ratio3 = $this->sum_repercutido3 / $numeradorProrrata;
        $ratio4 = $this->sum_repercutido4 / $numeradorProrrata;
        
        //Calcula prorrata
        $prorrata = $numeradorProrrata / $denumeradorProrrata;
        
        //Calcula el total deducible y no deducible en base a los ratios y los montos de facturas recibidas.
        $subtotalParaCFDP = $this->bills_subtotal - $this->bases_identificacion_plena - $this->bases_no_deducibles;
        $cfdp = $subtotalParaCFDP*$ratio1*0.01 + $subtotalParaCFDP*$ratio2*0.02 + $subtotalParaCFDP*$ratio3*0.13 + $subtotalParaCFDP*$ratio4*0.04 ;
        
        $this->subtotal_para_cfdp = $subtotalParaCFDP;
        $this->cfdp = $cfdp;
      
        //Calcula el balance estimado.
        $ivaDeducibleEstimado = ($cfdp * $prorrata) + $this->iva_acreditable_identificacion_plena;
        $balanceEstimado = -$lastBalance + $this->total_invoice_iva - $ivaDeducibleEstimado;

        //Calcula el balance operativo.
        $ivaDeducibleOperativo = ($cfdp * $prorrataOperativa) + $this->iva_acreditable_identificacion_plena;
        $balanceOperativo = -$lastBalance + $this->total_invoice_iva - $ivaDeducibleOperativo;
        $ivaNoDeducible = $this->total_bill_iva - $ivaDeducibleOperativo;
        
      
        //Define los ratios por tipo para guardar
        $fakeRatio1 = $this->sum_repercutido1 / $this->invoices_subtotal;
        $fakeRatio2 = $this->sum_repercutido2 / $this->invoices_subtotal;
        $fakeRatio3 = ($this->sum_repercutido3-$this->sum_repercutido_exento_con_credito) / $this->invoices_subtotal;
        $fakeRatio4 = $this->sum_repercutido4 / $this->invoices_subtotal;
        $fakeRatioExentoSinCredito = $this->sum_repercutido_exento_sin_credito / $this->invoices_subtotal;
        $fakeRatioExentoConCredito = $this->sum_repercutido_exento_con_credito / $this->invoices_subtotal;
      }
      
      //Guarda la instancia de calculos para no tener que volver a calcular si no hay cambios
      
      $this->numerador_prorrata = $numeradorProrrata;
      $this->denumerador_prorrata = $denumeradorProrrata;
      $this->prorrata = $prorrata;
      $this->prorrata_operativa = $prorrataOperativa;
      $this->iva_deducible_estimado = $ivaDeducibleEstimado;
      $this->balance_estimado = $balanceEstimado;
      $this->iva_deducible_operativo = $ivaDeducibleOperativo;
      $this->balance_operativo = $balanceOperativo;
      $this->iva_no_deducible = $ivaNoDeducible;
      $this->iva_por_cobrar = $this->balance_operativo < 0 ? abs($this->balance_operativo) : null;
      $this->iva_por_pagar = $this->balance_operativo > 0 ? $this->balance_operativo : null;
      
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
      
      $this->setCuentaContableCompras();
      $this->setCuentaContableVentas();
      $this->setCuentaContableAjustes();

      return $this;
    }
    
    public function setCuentaContableCompras( ){
      //Debe 1
      $this->cc_compras = $this->b001 + $this->b002 + $this->b003 + $this->b004 + 
                              $this->b061 + $this->b062 + $this->b063 + $this->b064; 
                              
      $this->cc_importaciones = $this->b021 + $this->b022 + $this->b023 + $this->b024 +
                                    $this->b031 + $this->b032 + $this->b033 + $this->b034 +
                                    $this->b041 + $this->b042 + $this->b043 + $this->b044 +
                                    $this->b051 + $this->b052 + $this->b053 + $this->b054;
      
      $this->cc_propiedades = $this->b011 + $this->b012 + $this->b013 + $this->b014 + 
                                  $this->b071 + $this->b072 + $this->b073 + $this->b074;
      
      $this->cc_iva_compras = $this->i001 + $this->i002 + $this->i003 + $this->i004 + 
                                  $this->i061 + $this->i062 + $this->i063 + $this->i064;
                                  
                                  
      $this->cc_iva_importaciones = $this->i021 + $this->i022 + $this->i023 + $this->i024 +
                                        $this->i031 + $this->i032 + $this->i033 + $this->i034 +
                                        $this->i041 + $this->i042 + $this->i043 + $this->i044 +
                                        $this->i051 + $this->i052 + $this->i053 + $this->i054;
                                        
      $this->cc_iva_propiedades = $this->i011 + $this->i012 + $this->i013 + $this->i014 + 
                                      $this->i071 + $this->i072 + $this->i073 + $this->i074;  
                                    
      $this->cc_compras_sin_derecho = $this->b080 + $this->b090 + $this->b097 + 
                              $this->i080 + $this->i090 + $this->i097;                                
      
    //Haber 1
      $this->cc_proveedores_credito = $this->total_proveedores_credito;
      $this->cc_proveedores_contado = $this->total_proveedores_contado;
    }
    
    public function setCuentaContableVentas( ){
      //Haber 2 
      $this->cc_ventas_1 = $this->b101 + $this->b121;
      $this->cc_ventas_2 = $this->b102 + $this->b122;
      $this->cc_ventas_13 = $this->b103 + $this->b123;
      $this->cc_ventas_4 = $this->b104 + $this->b124;
      $this->cc_ventas_exp = $this->b150;
      $this->cc_ventas_estado = $this->b160;
      $this->cc_ventas_1_iva = $this->i101 + $this->i121;
      $this->cc_ventas_2_iva = $this->i102 + $this->i122;
      $this->cc_ventas_13_iva = $this->i103 + $this->i123 + $this->i130;
      $this->cc_ventas_4_iva = $this->i104 + $this->i124;
      $this->cc_ventas_sin_derecho = $this->b200 + $this->b201 + $this->b240 + $this->b250 + $this->b260;
      $this->cc_ventas_sum = $this->cc_ventas_1 + $this->cc_ventas_2 + $this->cc_ventas_13 + $this->cc_ventas_4 + 
                                 $this->cc_ventas_1_iva + $this->cc_ventas_2_iva + $this->cc_ventas_13_iva + $this->cc_ventas_4_iva + 
                                 $this->cc_ventas_exp + $this->cc_ventas_estado + $this->cc_ventas_sin_derecho;
      
    //Debe 2
      $this->cc_clientes_credito = $this->total_clientes_credito;
      $this->cc_clientes_contado = $this->total_clientes_contado;  
      $this->cc_clientes_credito_exp = $this->total_clientes_credito_exp;
      $this->cc_clientes_contado_exp = $this->total_clientes_contado_exp;  
      $this->cc_clientes_sum = $this->cc_clientes_credito + $this->cc_clientes_contado + $this->cc_clientes_credito_exp + $this->cc_clientes_contado_exp;
        
    }
    
    public function setCuentaContableAjustes( ){
      //Haber 3
        $this->cc_ppp_1 = $this->i011 + $this->i031 + $this->i051 + $this->i071;
        $this->cc_ppp_2 = $this->i012 + $this->i032 + $this->i052 + $this->i072;
        $this->cc_ppp_3 = $this->i013 + $this->i033 + $this->i053 + $this->i073;
        $this->cc_ppp_4 = $this->i014 + $this->i034 + $this->i054 + $this->i074;
        
        $this->cc_bs_1 = $this->i001 + $this->i021 + $this->i041 + $this->i061;
        $this->cc_bs_2 = $this->i002 + $this->i022 + $this->i042 + $this->i062;
        $this->cc_bs_3 = $this->i003 + $this->i023 + $this->i043 + $this->i063;
        $this->cc_bs_4 = $this->i004 + $this->i024 + $this->i044 + $this->i064;
        
        $this->cc_por_pagar = $this->balance_operativo;
      
      //Debe 3 
        $this->cc_iva_emitido_1 = $this->i101 + $this->i121;
        $this->cc_iva_emitido_2 = $this->i102 + $this->i122;
        $this->cc_iva_emitido_3 = $this->i103 + $this->i123;
        $this->cc_iva_emitido_4 = $this->i104 + $this->i124;  
        
        $bases_ppp = $this->b011 + $this->b031
        + $this->b012 + $this->b032 + 0 
        + $this->b013 + $this->b033 +
        + $this->b014 + $this->b034;
        
        $bases_bs = $this->b001 + $this->b021
        + $this->b002 + $this->b022
        + $this->b003 + $this->b023
        + $this->b004 + $this->b024;
        
        $this->cc_aj_ppp_1 = $this->i011 + $this->i031;
        $this->cc_aj_ppp_2 = $this->i012 + $this->i032;
        $this->cc_aj_ppp_3 = $this->i013 + $this->i033;
        $this->cc_aj_ppp_4 = $this->i014 + $this->i034;
        
        $this->cc_aj_bs_1 = $this->i001 + $this->i021 ;
        $this->cc_aj_bs_2 = $this->i002 + $this->i022 ;
        $this->cc_aj_bs_3 = $this->i003 + $this->i023 ;
        $this->cc_aj_bs_4 = $this->i004 + $this->i024 ;
        
        $acreditable_bs = ( ($bases_bs * $this->ratio1 * 0.01) + ($bases_bs * $this->ratio2 * 0.02) + ($bases_bs * $this->ratio3 * 0.13) + ($bases_bs * $this->ratio4 * 0.04) ) * $this->prorrata_operativa;
        $acreditable_ppp = ( ($bases_ppp * $this->ratio1 * 0.01) + ($bases_ppp * $this->ratio2 * 0.02) + ($bases_ppp * $this->ratio3 * 0.13) + ($bases_ppp * $this->ratio4 * 0.04) ) * $this->prorrata_operativa;

        $this->cc_ajuste_ppp =  - $acreditable_ppp + $this->cc_aj_ppp_1 + $this->cc_aj_ppp_2 + + $this->cc_aj_ppp_3 + + $this->cc_aj_ppp_4; 
        $this->cc_ajuste_bs =  - $acreditable_bs + $this->cc_aj_bs_1 + $this->cc_aj_bs_2 + $this->cc_aj_bs_3 + $this->cc_aj_bs_4;
        
        $this->cc_gasto_no_acreditable = $this->iva_no_acreditable_identificacion_plena;
        
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
