<?php

namespace App;

use \Carbon\Carbon;
use App\InvoiceItem;
use App\BillItem;
use App\Invoice;
use App\Bill;
use App\Company;
use App\CodigoIvaRepercutido;
use App\CodigoIvaSoportado;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @group Model - Cálculo de impuestos
 *
 * Funciones de CalculatedTax.
 */
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
     * parsedIvaData
     * Devuelve los datos de iva_data en formato de array
     * @return \Illuminate\Http\Response
     */
    public function parsedIvaData() {
      try{
        return json_decode($this->iva_data);
      }catch(\Throwable $e){
        return false;
      }
    }
    
 /**
     * validarMes
     * Valida si para el mes en el que se quiere asignar el dato tiene una cierre abierto 
     * @bodyParam date required Fecha en la que se va a guardar el dato ejemplo 19/09/1994
     * @return true / false
     */
    public static function validarMes($date){
      try{
        $company = currentCompanyModel();
        $generated_date = explode("/", $date);
        $existe = CalculatedTax::where([['company_id',$company->id],['month',$generated_date[1]],
              ['year',$generated_date[2]]])->count();
        if($existe == 0){
          return true;
        }else{
          $abierto = CalculatedTax::where([['company_id',$company->id],['month',$generated_date[1]],
                ['year',$generated_date[2]],['is_final',1],['is_closed',0]])->count();
          if($abierto == 0){
            return false;
          }else{
            if($abierto > 0){

                return true;
            }else{
              return false;
            }
          }
        }
      } catch( \Exception $ex ) {
        Log::error("ERROR validando si el mes esta cerrado -> ".$ex->getMessage());
        return false;
      }
    }



    /**
     * applyRatios
     * Aplica los ratios operativos y devuelve el valor con el cálculo realizado según el porcentaje indicado
     * @bodyParam porc required Campo de porcentaje (1, 2, 13, 4). En base a este campo, aplica los ratios respectivos
     * @bodyParam value required Campo de valor al cual se le aplican los ratios operativos.
     * @return \Illuminate\Http\Response
     */
    public function applyRatios( $porc, $value ) {
      
      $company = currentCompanyModel();
     
      $ratio1_operativo = $company->operative_ratio1 / 100;
      $ratio2_operativo = $company->operative_ratio2 / 100;
      $ratio3_operativo = $company->operative_ratio3 / 100;
      $ratio4_operativo = $company->operative_ratio4 / 100;
      
      //Redondea ratios a 4 decimales (Al multiplicar por 100, queda en 2)
      $ratio1_operativo = round($ratio1_operativo, 4);
      $ratio2_operativo = round($ratio2_operativo, 4);
      $ratio3_operativo = round($ratio3_operativo, 4);
      $ratio4_operativo = round($ratio4_operativo, 4);
      
      $applied = 0;
      if( $porc == 1 ){
        $applied =  $value*$ratio1_operativo*0.01 + $value*$ratio2_operativo*0.02 + $value*$ratio3_operativo*0.13 + $value*$ratio4_operativo*0.04 ;
      }
      if( $porc == 2 ){
        $applied =  $value*$ratio1_operativo*0.02 + $value*$ratio2_operativo*0.02 + $value*$ratio3_operativo*0.02 + $value*$ratio4_operativo*0.02 ;
      }
      if( $porc == 13 ){
        $applied =  $value*$ratio1_operativo*0.13 + $value*$ratio2_operativo*0.02 + $value*$ratio3_operativo*0.13 + $value*$ratio4_operativo*0.04 ; 
      }
      if( $porc == 4 ){
        $applied =  $value*$ratio1_operativo*0.04 + $value*$ratio2_operativo*0.02 + $value*$ratio3_operativo*0.04 + $value*$ratio4_operativo*0.04 ; 
      }
      
      return $applied;
    }
    
    
    /**
     * calcularFacturacionPorMesAno
     * Calcula y devuelve los datos del mes para dashboard y reportes
     *
     * @bodyParam month required
     * @bodyParam year required
     * @bodyParam lastBalance required
     * @bodyParam prorrataOperativa required
     * @return App\CalculatedTax
     */
    public static function calcularFacturacionPorMesAno( $month, $year, $lastBalance, $prorrataOperativa ) {
      
      $currentCompanyId = currentCompany();
      $cacheKey = "cache-taxes-$currentCompanyId-$month-$year";
      
      if ( !Cache::has($cacheKey) ) {
          
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
          
      }
      
      $data = Cache::get($cacheKey);
      return $data;
      
    }
    
    //
    /**
     * calcularFacturacion
     * Recibe fecha de inicio y fecha de fin en base a las cuales se desea calcular la prorrata.
     *
     * @bodyParam month required
     * @bodyParam year required
     * @bodyParam lastBalance required
     * @bodyParam prorrataOperativa required
     * @return App\CalculatedTax
     */
    public function calcularFacturacion( $month, $year, $lastBalance, $prorrataOperativa ) {

      $currentCompany = currentCompanyModel();
      //Si recibe el balance anterior en 0, intenta buscarlo.
      if( !$lastBalance ) {
        $lastBalance = $currentCompany->getLastBalance($month, $year);
      }
      
      if( $year >= 2018 ) {
        $this->setDatosEmitidos( $month, $year, $currentCompany->id );
        //dd($this);
        $query = BillItem::with('bill')->with('ivaType')
                    ->where('company_id', $currentCompany->id)
                    ->where('year', $year)
                    ->where('month', $month);
        $this->setDatosSoportados( $month, $year, $currentCompany->id, $query );
      }
      $this->setCalculosIVA( $prorrataOperativa, $lastBalance );
      
      return $this;
    }
    
        
    public function calcularFacturacionAcumulado( $year, $prorrataOperativa ) {

      $this->sumAcumulados( $year );
      $this->setCalculosIVA( $prorrataOperativa, 0 );

      return $this;
      
    }
  
    /**
    * setDatosEmitidos
    * Recorre todas las facturas emitidas y aumenta los montos correspondientes.
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
      $sumIvaSinAplicar = 0;
      
      $filterTotales = false;
      if( $month == 0 && $year == 2018 && currentCompanyModel()->first_prorrata_type == 2 ) {
        $filterTotales = true;
      }
      
      $ivaData = json_decode( $this->iva_data ) ?? new \stdClass();
      $arrayActividades = currentCompanyModel()->getActivities();
      InvoiceItem::with('invoice')
                  ->with('ivaType')
                  ->where('company_id', $company)
                  ->where('year', $year)
                  ->where('month', $month)
                  ->chunk( 2500,  function($invoiceItems) use ($year, $month, &$company, &$ivaData, $filterTotales, $arrayActividades,
       &$invoicesTotal, &$invoicesSubtotal, &$totalInvoiceIva, &$totalClientesContadoExp, &$totalClientesCreditoExp, &$totalClientesContadoLocal, &$totalClientesCreditoLocal, &$ivaRetenido, &$sumIvaSinAplicar,
       &$sumRepercutido1, &$sumRepercutido2, &$sumRepercutido3, &$sumRepercutido4, &$sumRepercutidoExentoConCredito, &$sumRepercutidoExentoSinCredito, &$basesVentasConIdentificacion, &$ivasVentasConIdentificacion
      ) {
        
        $countInvoiceItems = $invoiceItems->count();
        //Recorre las lineas de factura
        for ($i = 0; $i < $countInvoiceItems; $i++) {
          try {
            $currInvoice = $invoiceItems[$i]->invoice;
            
            if( !$currInvoice->is_void && $currInvoice->is_authorized && $currInvoice->is_code_validated 
            && $currInvoice->is_totales == $filterTotales && $currInvoice->hide_from_taxes == false ) {
            
              if( $currInvoice->currency == 'CRC' ) {
                $currInvoice->currency_rate = 1;
              }
              
              $invoiceItems[$i]->fixIvaType();
              
              $subtotal = $invoiceItems[$i]->subtotal * $currInvoice->currency_rate;
              $ivaType = $invoiceItems[$i]->iva_type;
              $prodType = $invoiceItems[$i]->product_type;
              $invoiceIva = $invoiceItems[$i]->iva_amount * $currInvoice->currency_rate;
              $currentTotal = $subtotal + $invoiceIva;
              
              $prodPorc = $invoiceItems[$i]->ivaType ? $invoiceItems[$i]->ivaType->percentage : '13';
              $prodType = $prodType ? $prodType : '17';
              $currActivity = $currInvoice->commercial_activity;
              
              if( !isset($currActivity) || !in_array($currActivity, $arrayActividades) ){
                $currActivity = $arrayActividades[0]->codigo;
                $currInvoice->commercial_activity = $currActivity;
                //$currInvoice->save();
              }
              
              //Redondea todo a 2 decimales
              $subtotal = round($subtotal, 2);
              $invoiceIva = round($invoiceIva, 2);
              $currentTotal = round($currentTotal, 2);
              
              if( $currInvoice->document_type == '03' ) {
                $subtotal = $subtotal * -1;
                $invoiceIva = $invoiceIva * -1;
                $currentTotal = $currentTotal * -1;
              }
              
              $ivaType = $ivaType ? $ivaType : 'B103';
              
              //Procesa los códigos que llevan IVA como costo dentro del subtotal de la factura.
              if( $ivaType == '200' || $ivaType == '201' || $ivaType == '240' || $ivaType == '250' || $ivaType == '260' || $ivaType == '245' || 
                  $ivaType == 'B200' || $ivaType == 'B201' || $ivaType == 'B240' || $ivaType == 'B250' || $ivaType == 'B260' || $ivaType == 'B245' ||
                  $ivaType == 'S200' || $ivaType == 'S201' || $ivaType == 'S240' || $ivaType == 'S250' || $ivaType == 'S260' || $ivaType == 'S245' ){
                $subtotal = $subtotal + $invoiceIva;
                $invoiceIva = 0;
                $sumRepercutidoExentoSinCredito += $subtotal;
              }else if( $invoiceItems[$i]->is_identificacion_especifica ) {
                $basesVentasConIdentificacion += $subtotal; //Las bases con id. específica no se incluyen en el cálculo de iva con prorrata, son 100% acreditables.
              }
              
              //sum los del 1%
              if( $ivaType == '101' || $ivaType == '121' || $ivaType == '141' ||
                  $ivaType == 'B101' || $ivaType == 'B121' || $ivaType == 'B141' ||
                  $ivaType == 'S101' || $ivaType == 'S121' || $ivaType == 'S141' ){
                $sumRepercutido1 += $subtotal;
              }
              
              //sum los del 2%
              if( $ivaType == '102' || $ivaType == '122' || $ivaType == '142' ||
                  $ivaType == 'B102' || $ivaType == 'B122' || $ivaType == 'B142' ||
                  $ivaType == 'S102' || $ivaType == 'S122' || $ivaType == 'S142' ){
                $sumRepercutido2 += $subtotal;
              }
              
              //sum los del 13%
              if( $ivaType == '103' || $ivaType == '123' || $ivaType == '143' || $ivaType == '130' || $ivaType == '140' ||
                  $ivaType == 'B103' || $ivaType == 'B123' || $ivaType == 'B143' || $ivaType == 'B130' || $ivaType == 'B140' ||
                  $ivaType == 'S103' || $ivaType == 'S123' || $ivaType == 'S143' || $ivaType == 'S130' || $ivaType == 'S140' ){
                $sumRepercutido3 += $subtotal;
              }
              
              //sum los del 4%
              if( $ivaType == '104' || $ivaType == '124' || $ivaType == '144' || $ivaType == '114' ||
                  $ivaType == 'B104' || $ivaType == 'B124' || $ivaType == 'B144' || $ivaType == 'B114' ||
                  $ivaType == 'S104' || $ivaType == 'S124' || $ivaType == 'S144' || $ivaType == 'S114' ){
                $sumRepercutido4 += $subtotal;
              }
              //sum los del exentos. Estos se suman como si fueran 13 para efectos del cálculo.
              if( $ivaType == '150' || $ivaType == '160' ||  $ivaType == '170' || $ivaType == '199' || $ivaType == '155' ||
                  $ivaType == 'B150' || $ivaType == 'B160' ||  $ivaType == 'B170' || $ivaType == 'B199' || $ivaType == 'B155' ||
                  $ivaType == 'S150' || $ivaType == 'S160' ||  $ivaType == 'S170' || $ivaType == 'S199' || $ivaType == 'S155' ){
                $subtotal = $subtotal + $invoiceIva;
                $invoiceIva = 0;
                $sumRepercutido3 += $subtotal;
                $sumRepercutidoExentoConCredito += $subtotal;
              }
              if( $ivaType == 'S181' || $ivaType == 'S182' ||  $ivaType == 'S183' || $ivaType == 'S184' ||
                  $ivaType == 'B181' || $ivaType == 'B182' ||  $ivaType == 'B183' || $ivaType == 'B184' ){
                $subtotal = $subtotal;
                $invoiceIva = 0;
                $sumRepercutido3 += $subtotal;
                $sumRepercutidoExentoConCredito += $subtotal;
              }
              //No cuenta los que no llevan IVA
              if( $ivaType == 'S300' || $ivaType == 'B300' ){
                $subtotal = $subtotal + $invoiceIva;
                $sumIvaSinAplicar += $subtotal;
              }
              
              //Suma la transitoria de canasta básica
              if( $ivaType == '165' || $ivaType == 'B165' || $ivaType == 'S165' ){
                $subtotal = $subtotal + $invoiceIva;
                $invoiceIva = 0;
                $sumRepercutido1 += $subtotal;
                $sumRepercutidoExentoConCredito += $subtotal;
              }
              
              //Ingresa retenciones al calculo
              $tipoPago = $currInvoice->payment_type;
              $retenidoLinea = 0;
              $porcRetencion = $currInvoice->retention_percent;
              if( $tipoPago == '02' ) {
                $retenidoLinea = $currentTotal * ($porcRetencion / 100);
                $ivaRetenido += $retenidoLinea;
              }
              
              //Ingresa retenciones al calculo
              $tipo_venta = $currInvoice->sale_condition;
              if( $ivaType == '150' || $ivaType == 'B150' || $ivaType == 'S150' ){
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
              
              if(!isset($ivaData->$bVar)) {
                $ivaData->$bVar = 0;
              }
              if(!isset($ivaData->$iVar)) {
                $ivaData->$iVar = 0;
              }
              $ivaData->$bVar += $subtotal;
              $ivaData->$iVar += $invoiceIva;
              
              $typeVar = "type$prodType"; //Ej. type17
              $typeVarPorc = "type$prodType-$prodPorc"; //Ej. type17-4
              $typeVarActividad = $currActivity."-".$typeVar; //Ej. 706903-type17
              $typeVarPorcActividad = $currActivity."-".$typeVarPorc; //Ej. 706903-type17-4
              
              if(!isset($ivaData->$typeVar)) {
                $ivaData->$typeVar = 0;
              }
              if(!isset($ivaData->$typeVarPorc)) {
                $ivaData->$typeVarPorc = 0;
              }
              if(!isset($ivaData->$typeVarActividad)) {
                $ivaData->$typeVarActividad = 0;
              }
              if(!isset($ivaData->$typeVarPorcActividad)) {
                $ivaData->$typeVarPorcActividad = 0;
              }
              $ivaData->$typeVar += $subtotal;
              $ivaData->$typeVarPorc += $subtotal;
              $ivaData->$typeVarActividad += $subtotal;
              $ivaData->$typeVarPorcActividad += $subtotal;

            }
            
          }catch( \Throwable $ex ){
            //Log::error('Error al leer factura para cálculo: ' . $ex->getMessage());
          }
        }
        
      });
      
      $this->iva_data = json_encode( $ivaData );
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
      $this->sum_iva_sin_aplicar = $sumIvaSinAplicar;
      $this->bases_ventas_con_identificacion = $basesVentasConIdentificacion;
      $this->iva_retenido = $ivaRetenido;
      
      return $this;
    }
  
    /**
    *   Recorre todas las facturas recibidas y aumenta los montos correspondientes.
    **/
    public function setDatosSoportados ( $month, $year, $company, $query, $singleBill = false ) {
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
      $ivaData = json_decode( $this->iva_data ) ?? new \stdClass();
      $arrayActividades = currentCompanyModel()->getActivities();

      $query->chunk( 2500,  function($billItems) use ($year, $month, &$company, &$ivaData, &$singleBill, $arrayActividades,
       &$billsTotal, &$billsSubtotal, &$totalBillIva, &$basesIdentificacionPlena, &$basesNoDeducibles, &$ivaAcreditableIdentificacionPlena, 
       &$ivaNoAcreditableIdentificacionPlena, &$totalProveedoresContado, &$totalProveedoresCredito
      ) {
        $countBillItems = count( $billItems );

        for ($i = 0; $i < $countBillItems; $i++) {
          
          try{
            
            $currBill = $billItems[$i]->bill;
            if( !$currBill->is_void && $currBill->is_authorized && $currBill->is_code_validated &&
                ( $singleBill || $currBill->accept_status == 1 ) && $currBill->hide_from_taxes == false ) {
            
              if( $currBill->currency == 'CRC' ) {
                $currBill->currency_rate = 1;
              }
              //Arrela el IVATYPE la primera vez en caso de ser codigos anteriores.
              //$billItems[$i]->fixIvaType();
              
              $subtotal = $billItems[$i]->subtotal * $currBill->currency_rate;
              $ivaType = $billItems[$i]->iva_type;
              $prodType = $billItems[$i]->product_type;
              $billIva = $billItems[$i]->iva_amount * $currBill->currency_rate;
              $currentTotal = $subtotal + $billIva;
              
              $prodPorc = $billItems[$i]->ivaType ? $billItems[$i]->ivaType->percentage : '13';
              $prodType = $prodType ? $prodType : '49';
              
              $currActivity = $currBill->activity_company_verification;
              if( !isset($currActivity) || !in_array($currActivity, $arrayActividades) ){
                $currActivity = $arrayActividades[0]->codigo;
                $currBill->commercial_activity = $currActivity;
                //$currBill->save();
              }
              
              //Redondea todo a 2 decimales
              $subtotal = $subtotal;
              $billIva = $billIva;
              $currentTotal = $currentTotal;
                
              $ivaType = $ivaType ? $ivaType : '003';
              $ivaType = str_pad($ivaType, 3, '0', STR_PAD_LEFT);
              
              $billsTotal += $currentTotal;
              $billsSubtotal += $subtotal;
              $totalBillIva += $billIva;
              
              if( $ivaType == 'B041' || $ivaType == 'B042' || $ivaType == 'B043' || $ivaType == 'B044' ||
                  $ivaType == 'B051' || $ivaType == 'B052' || $ivaType == 'B053' || $ivaType == 'B054' || 
                  $ivaType == 'B061' || $ivaType == 'B062' || $ivaType == 'B063' || $ivaType == 'B064' || 
                  $ivaType == 'B071' || $ivaType == 'B072' || $ivaType == 'B073' || $ivaType == 'B074' ||
                  $ivaType == 'S041' || $ivaType == 'S042' || $ivaType == 'S043' || $ivaType == 'S044' ||
                  $ivaType == 'S051' || $ivaType == 'S052' || $ivaType == 'S053' || $ivaType == 'S054' || 
                  $ivaType == 'S061' || $ivaType == 'S062' || $ivaType == 'S063' || $ivaType == 'S064' || 
                  $ivaType == 'S071' || $ivaType == 'S072' || $ivaType == 'S073' || $ivaType == 'S074'
              )
              {
                $basesIdentificacionPlena += $subtotal;
              }
              
              if( $ivaType == 'B080' || $ivaType == 'B090' || $ivaType == 'B097' || $ivaType == '098' || $ivaType == '099' ||
                  $ivaType == 'S080' || $ivaType == 'S090' || $ivaType == 'S097' || $ivaType == '098' || $ivaType == '099' )
              {
                $basesNoDeducibles += $subtotal;
                $ivaNoAcreditableIdentificacionPlena += $billIva;
              }
              
              if( $ivaType == 'B040' || $ivaType == 'B050' || $ivaType == 'B060' || $ivaType == 'B070' ||
                  $ivaType == 'S040' || $ivaType == 'S050' || $ivaType == 'S060' || $ivaType == 'S070' )
              {
                $basesNoDeducibles += $subtotal;
              }
              
              /***SACA IVAS DEDUCIBLES DE IDENTIFICAIONES PLENAS**/
              $porc_plena = $billItems[$i]->porc_identificacion_plena ? $billItems[$i]->porc_identificacion_plena : 0;
              
              if ( $porc_plena == 1 || $porc_plena == 5 ) {
                $porc_plena = 13;
              } 
              
              if( $ivaType == 'B041' || $ivaType == 'B051' || $ivaType == 'B061' || $ivaType == 'B071' ||
                  $ivaType == 'S041' || $ivaType == 'S051' || $ivaType == 'S061' || $ivaType == 'S071' )
              {
                //Cuando es al 1%, se puede agreditar el 100%
                $basesIdentificacionPlena += $subtotal;
                $ivaAcreditableIdentificacionPlena += $billIva;
              }
              if( $ivaType == 'B042' || $ivaType == 'B052' || $ivaType == 'B062' || $ivaType == 'B072' ||
                   $ivaType == 'S042' || $ivaType == 'S052' || $ivaType == 'S062' || $ivaType == 'S072' )
              {
                $menor = 2;
                if( $porc_plena != 2 ){
                  $menor = $porc_plena > 2 ? 2 : $porc_plena;
                }
                $menor_porc = $menor/100;
                
                $ivaAcreditableIdentificacionPlena += $subtotal * $menor_porc;
                $ivaNoAcreditableIdentificacionPlena += $billIva - ($subtotal * $menor_porc);
              }
              if( $ivaType == 'B043' || $ivaType == 'B053' || $ivaType == 'B063' || $ivaType == 'B073' ||
                  $ivaType == 'S043' || $ivaType == 'S053' || $ivaType == 'S063' || $ivaType == 'S073' )
              {
                $menor = 13;
                if( $porc_plena != 13 ){
                  $menor = $porc_plena > 13 ? 13 : $porc_plena;
                }
                $menor_porc = $menor/100;
                if( $menor != $porc_plena) { 
                  $ivaAcreditableIdentificacionPlena += $subtotal * $menor_porc;
                  $ivaNoAcreditableIdentificacionPlena += $billIva - ($subtotal * $menor_porc);
                }else{
                  $ivaAcreditableIdentificacionPlena += $billIva;
                  $ivaNoAcreditableIdentificacionPlena += 0;
                }
              }
              if( $ivaType == 'B044' || $ivaType == 'B054' || $ivaType == 'B064' || $ivaType == 'B074' ||
                  $ivaType == 'S044' || $ivaType == 'S054' || $ivaType == 'S064' || $ivaType == 'S074' )
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
              
              if(!isset($ivaData->$bVar)) {
                $ivaData->$bVar = 0;
              }
              if(!isset($ivaData->$iVar)) {
                $ivaData->$iVar = 0;
              }
              $ivaData->$bVar += $subtotal;
              $ivaData->$iVar += $billIva;
              
              //Cuenta contable de proveedor
              $tipoVenta = $currBill->sale_condition;
              if( $tipoVenta == '01' ) {
                $totalProveedoresContado += $currentTotal;
              }else{
                $totalProveedoresCredito += $currentTotal;
              }
              
              $typeVar = "type$prodType";
              $typeVarPorc = "type$prodType-$prodPorc";
              $typeVarActividad = $currActivity."-".$typeVar;
              $typeVarPorcActividad = $currActivity."-".$typeVarPorc;
              
              if(!isset($ivaData->$typeVar)) {
                $ivaData->$typeVar = 0;
                $ivaData->$typeVarPorc = 0;
                $ivaData->$typeVarActividad = 0;
                $ivaData->$typeVarPorcActividad = 0;
              }
              $ivaData->$typeVar += $subtotal;
              $ivaData->$typeVarPorc += $subtotal;
              $ivaData->$typeVarActividad += $subtotal;
              $ivaData->$typeVarPorcActividad += $subtotal;
              
            }  
            
          }catch( \Throwable $ex ){
            //Log::error('Error al leer factura para cálculo: ' . $ex->getMessage());
          }
          
        }
        
      });
              
      
      $this->iva_data = json_encode( $ivaData );
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
      $this->bills_subtotal1 = $ivaData->bB001 + $ivaData->bB011 + $ivaData->bB021 + $ivaData->bB031 + $ivaData->bB015 + $ivaData->bB035 +
                               $ivaData->bS001 + $ivaData->bS021;
      $this->bills_subtotal2 = $ivaData->bB002 + $ivaData->bB012 + $ivaData->bB022 + $ivaData->bB032 +
                               $ivaData->bS002 + $ivaData->bS022;
      $this->bills_subtotal3 = $ivaData->bB003 + $ivaData->bB013 + $ivaData->bB023 + $ivaData->bB033 + $ivaData->bB016 + $ivaData->bB036 +
                               $ivaData->bS003 + $ivaData->bS023;
      $this->bills_subtotal4 = $ivaData->bB004 + $ivaData->bB014 + $ivaData->bB024 + $ivaData->bB034 +
                               $ivaData->bS004 + $ivaData->bS024;
      
      //Canasta cuenta como acreditaccion plena. 
      $acredPorCanasta = $ivaData->iB001 + $ivaData->iB011 + $ivaData->iB021 + $ivaData->iB031 + $ivaData->iB015 + $ivaData->iB035 +
                         $ivaData->iS001 + $ivaData->iS021;
      $this->iva_acreditable_identificacion_plena = $ivaAcreditableIdentificacionPlena + $acredPorCanasta;
      $this->bills_subtotal1 = 0; //Lo deja en 0 de una vez. Todas deberian contar como base no acreditable.

      return $this;
    }
    
    public function setCalculosIVA( $prorrataOperativa, $lastBalance ) {
      
      $company = currentCompanyModel();
      $subtotalAplicado =  $this->invoices_subtotal - $this->sum_iva_sin_aplicar;
      
      //Determina numerador y denominador de la prorrata.
      $numeradorProrrata = $subtotalAplicado - $this->sum_repercutido_exento_sin_credito;
      $denumeradorProrrata = $subtotalAplicado;
      
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
        $fakeRatio1 = $this->sum_repercutido1 / $subtotalAplicado;
        $fakeRatio2 = $this->sum_repercutido2 / $subtotalAplicado;
        $fakeRatio3 = ($this->sum_repercutido3-$this->sum_repercutido_exento_con_credito) / $subtotalAplicado;
        $fakeRatio4 = $this->sum_repercutido4 / $subtotalAplicado;
        $fakeRatioExentoSinCredito = $this->sum_repercutido_exento_sin_credito / $subtotalAplicado;
        $fakeRatioExentoConCredito = $this->sum_repercutido_exento_con_credito / $subtotalAplicado;
        
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
      
      $prorrata = round($prorrata, 4);
      $prorrataOperativa = round($prorrataOperativa, 4);
      
      //Calcula el total deducible y no deducible en base a los ratios y los montos de facturas recibidas.
      $subtotalParaCFDP = $this->bills_subtotal - $this->bases_identificacion_plena - $this->bases_no_deducibles;
      
      //Usa los subtotales de cada tarifa para hacer el calculo. Los subtotales no incluyen nada 100% acreditable.
      $cfdpEstimado1 = $this->bills_subtotal1*$ratio1*0.01 + $this->bills_subtotal1*$ratio2*0.02 + $this->bills_subtotal1*$ratio3*0.13 + $this->bills_subtotal1*$ratio4*0.04 ; 
      $cfdpEstimado2 = $this->bills_subtotal2*$ratio1*0.02 + $this->bills_subtotal2*$ratio2*0.02 + $this->bills_subtotal2*$ratio3*0.02 + $this->bills_subtotal2*$ratio4*0.02 ; 
      $cfdpEstimado3 = $this->bills_subtotal3*$ratio1*0.13 + $this->bills_subtotal3*$ratio2*0.02 + $this->bills_subtotal3*$ratio3*0.13 + $this->bills_subtotal3*$ratio4*0.04 ; 
      $cfdpEstimado4 = $this->bills_subtotal4*$ratio1*0.04 + $this->bills_subtotal4*$ratio2*0.02 + $this->bills_subtotal4*$ratio3*0.04 + $this->bills_subtotal4*$ratio4*0.04 ; 
      $cfdpEstimado  = $cfdpEstimado1 + $cfdpEstimado2 + $cfdpEstimado3 + $cfdpEstimado4;
      
      //Calcula el balance estimado.
      $ivaDeducibleEstimado = ($cfdpEstimado * $prorrata) + $this->iva_acreditable_identificacion_plena;
      $balanceEstimado = -$lastBalance + $this->total_invoice_iva - $ivaDeducibleEstimado;

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
      $ivaDeducibleOperativo = ($cfdp  * $prorrataOperativa) + $this->iva_acreditable_identificacion_plena;
      $balanceOperativo = -$lastBalance + $this->total_invoice_iva - $ivaDeducibleOperativo;
      $ivaNoDeducible = $this->total_bill_iva - $ivaDeducibleOperativo;
 
      $saldoFavor = $balanceOperativo - $this->iva_retenido;
      $saldoFavor = $saldoFavor < 0 ? abs( $saldoFavor ) : 0;

      $this->numerador_prorrata = $numeradorProrrata;
      $this->denumerador_prorrata = $denumeradorProrrata;
      $this->prorrata = $prorrata;
      $this->prorrata_operativa = $prorrataOperativa;
        
      //$this->subtotal_para_cfdp = $subtotalParaCFDP;
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
    
    public function setCalculosPorFactura( $prorrataOperativa, $lastBalance ) {
      
      $company = currentCompanyModel();
      
      $ivaNoDeducible = 0;
      $ivaDeducibleOperativo = 0;
     
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
      $ivaDeducibleOperativo = ($cfdp*$prorrataOperativa) + $this->iva_acreditable_identificacion_plena;
      $ivaNoDeducible = $this->total_bill_iva - $ivaDeducibleOperativo;
      
      if( !$this->total_bill_iva ) {
        $ivaNoDeducible = 0;
        $ivaDeducibleOperativo = 0;
      }
      
      $this->iva_deducible_operativo = $ivaDeducibleOperativo;
      $this->iva_no_deducible = $ivaNoDeducible;

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
          $currentCompany->operative_prorrata = number_format( $data->prorrata*100, 2);
          $currentCompany->first_prorrata   = number_format( $data->prorrata*100, 2);
          $currentCompany->operative_ratio1 = number_format( $data->ratio1*100, 2);
          $currentCompany->operative_ratio2 = number_format( $data->ratio2*100, 2);
          $currentCompany->operative_ratio3 = number_format( $data->ratio3*100, 2);
          $currentCompany->operative_ratio4 = number_format( $data->ratio4*100, 2);
          $currentCompany->save();
        }
        Cache::put($cacheKey, $data, now()->addDays(365));
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
			$this->sum_iva_sin_aplicar = 0;

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
      
    	$ivaData = json_decode($this->iva_data);
    	$arrayActividades = currentCompanyModel()->getActivities();

      for ($i = 0; $i < $countAnteriores; $i++) {
        
        if( !( $calculosAnteriores[$i]->year == 2019 && $calculosAnteriores[$i]->month < 7 ) ){
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
    			$this->sum_iva_sin_aplicar += $calculosAnteriores[$i]->sum_iva_sin_aplicar;
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
    			$ivaDataAnterior = json_decode($calculosAnteriores[$i]->iva_data);
			
    			foreach( CodigoIvaRepercutido::all() as $codigo ) {
    			  $bVar = "b$codigo->id";
    			  $iVar = "i$codigo->id";
    			  $ivaData->$bVar += $ivaDataAnterior->$bVar;
    			  $ivaData->$iVar += $ivaDataAnterior->$iVar;
    			}
    			
    			foreach( CodigoIvaSoportado::all() as $codigo ) {
    			  $bVar = "b$codigo->id";
    			  $iVar = "i$codigo->id";
    			  $ivaData->$bVar += $ivaDataAnterior->$bVar;
    			  $ivaData->$iVar += $ivaDataAnterior->$iVar;
    			}
    			
    			foreach( ProductCategory::all() as $codigo ) {
    			  $varName = "type$codigo->id";
    			  $varName0 = "type$codigo->id-0";
    			  $varName1 = "type$codigo->id-1";
    			  $varName2 = "type$codigo->id-2";
    			  $varName4 = "type$codigo->id-4";
    			  $varName8 = "type$codigo->id-8";
    			  $varName3 = "type$codigo->id-13";
    			  
    			  $ivaData->$varName += $ivaDataAnterior->$varName;
    			  $ivaData->$varName0 += $ivaDataAnterior->$varName0;
    			  $ivaData->$varName1 += $ivaDataAnterior->$varName1;
    			  $ivaData->$varName2 += $ivaDataAnterior->$varName2;
    			  $ivaData->$varName4 += $ivaDataAnterior->$varName4;
    			  $ivaData->$varName8 += $ivaDataAnterior->$varName8;
    			  $ivaData->$varName3 += $ivaDataAnterior->$varName3;
    			  
            foreach( $arrayActividades as $act){
              $typeVarAct  = "$act->codigo-$varName";
              $typeVarAct0 = "$act->codigo-$varName0";
              $typeVarAct1 = "$act->codigo-$varName1";
              $typeVarAct2 = "$act->codigo-$varName2";
              $typeVarAct4 = "$act->codigo-$varName4";
              $typeVarAct8 = "$act->codigo-$varName8";
              $typeVarAct3 = "$act->codigo-$varName3";
              
        			try{$ivaData->$typeVarAct  += $ivaDataAnterior->$typeVarAct; }catch(\Throwable $e){}
        			try{$ivaData->$typeVarAct0 += $ivaDataAnterior->$typeVarAct0;}catch(\Throwable $e){}
        			try{$ivaData->$typeVarAct1 += $ivaDataAnterior->$typeVarAct1;}catch(\Throwable $e){}
        			try{$ivaData->$typeVarAct2 += $ivaDataAnterior->$typeVarAct2;}catch(\Throwable $e){}
        			try{$ivaData->$typeVarAct4 += $ivaDataAnterior->$typeVarAct4;}catch(\Throwable $e){}
        			try{$ivaData->$typeVarAct8 += $ivaDataAnterior->$typeVarAct8;}catch(\Throwable $e){}
        			try{$ivaData->$typeVarAct3 += $ivaDataAnterior->$typeVarAct3;}catch(\Throwable $e){}
              
            }
    			}
    			
    			$this->iva_data = json_encode($ivaData);
        }		
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
			$this->sum_iva_sin_aplicar = 0;
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
			
			$ivaData = new \stdClass();
			
			foreach( CodigoIvaRepercutido::all() as $codigo ) {
			  $bVar = "b$codigo->id";
			  $iVar = "i$codigo->id";
			  $ivaData->$bVar = 0;
			  $ivaData->$iVar = 0;
			}
			
			foreach( CodigoIvaSoportado::all() as $codigo ) {
			  $bVar = "b$codigo->id";
			  $iVar = "i$codigo->id";
			  $ivaData->$bVar = 0;
			  $ivaData->$iVar = 0;
			}
			
      $arrayActividades = currentCompanyModel()->getActivities();
			foreach( ProductCategory::all() as $codigo ) {
			  $varName  = "type$codigo->id";
			  $varName0 = "type$codigo->id-0";
			  $varName1 = "type$codigo->id-1";
			  $varName2 = "type$codigo->id-2";
			  $varName4 = "type$codigo->id-4";
			  $varName8 = "type$codigo->id-8";
			  $varName3 = "type$codigo->id-13";
			  
			  $ivaData->$varName = 0;
			  $ivaData->$varName0 = 0;
			  $ivaData->$varName1 = 0;
			  $ivaData->$varName2 = 0;
			  $ivaData->$varName4 = 0;
			  $ivaData->$varName8 = 0;
			  $ivaData->$varName3 = 0;
			  
        foreach( $arrayActividades as $act){
          $typeVarAct  = "$act->codigo-$varName";
          $typeVarAct0 = "$act->codigo-$varName0";
          $typeVarAct1 = "$act->codigo-$varName1";
          $typeVarAct2 = "$act->codigo-$varName2";
          $typeVarAct4 = "$act->codigo-$varName4";
          $typeVarAct8 = "$act->codigo-$varName8";
          $typeVarAct3 = "$act->codigo-$varName3";
          
          $ivaData->$typeVarAct = 0;
  			  $ivaData->$typeVarAct0 = 0;
  			  $ivaData->$typeVarAct1 = 0;
  			  $ivaData->$typeVarAct2 = 0;
  			  $ivaData->$typeVarAct4 = 0;
  			  $ivaData->$typeVarAct8 = 0;
  			  $ivaData->$typeVarAct3 = 0;
        }
			  
			}
			
			$this->iva_data = json_encode($ivaData);
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }  
  
}
