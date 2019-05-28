<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\InvoiceItem;
use App\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
  
    protected $guarded = [];
    
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
  
    //Relacion con el cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
  
    //Relación con facturas emitidas
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
  
    //Devuelve la fecha de generación en formato Carbon
    public function generatedDate()
    {
        return Carbon::parse($this->generated_date);
    }
  
    //Devuelve la fecha de vencimiento en formato Carbon
    public function dueDate()
    {
        return Carbon::parse($this->due_date);
    }
    
    
    /**
    * Asigna los datos de la factura segun el request recibido
    **/
    public function setInvoiceData($request) {
      
      $this->document_key = $request->document_key;
      $this->document_number = $request->document_number;
      $this->sale_condition = $request->sale_condition;
      $this->payment_type = $request->payment_type;
      $this->retention_percent = $request->retention_percent;
      $this->credit_time = $request->credit_time;
      $this->buy_order = $request->buy_order;
      $this->other_reference = $request->other_reference;
    
      //Datos de cliente. El cliente nuevo viene con ID = -1
      if( $request->client_id == '-1' ){
          $tipo_persona = $request->tipo_persona;
          $identificacion_cliente = $request->id_number;
          $codigo_cliente = $request->code;
          
          $cliente = Client::firstOrCreate(
              [
                  'id_number' => $identificacion_cliente,
                  'company_id' => $this->company_id,
              ],
              [
                  'code' => $codigo_cliente ,
                  'company_id' => $this->company_id,
                  'tipo_persona' => $tipo_persona,
                  'id_number' => $identificacion_cliente
              ]
          );
          $cliente->first_name = $request->first_name;
          $cliente->last_name = $request->last_name;
          $cliente->last_name2 = $request->last_name2;
          $cliente->emisor_receptor = 'ambos';
          $cliente->country = $request->country;
          $cliente->state = $request->state;
          $cliente->city = $request->city;
          $cliente->district = $request->district;
          $cliente->neighborhood = $request->neighborhood;
          $cliente->zip = $request->zip;
          $cliente->address = $request->address;
          $cliente->phone = $request->phone;
          $cliente->es_exento = $request->es_exento;
          $cliente->email = $request->email;
          $cliente->save();
              
          $this->client_id = $cliente->id;
      }else{
          $this->client_id = $request->client_id;
      }
      
      $request->currency_rate = $request->currency_rate ? $request->currency_rate : 1;
      
      //Datos de factura
      $this->description = $request->description;
      $this->subtotal = floatval( str_replace(",","", $request->subtotal ));
      $this->currency = $request->currency;
      $this->currency_rate = floatval( str_replace(",","", $request->currency_rate ));
      $this->total = floatval( str_replace(",","", $request->total ));
      $this->iva_amount = floatval( str_replace(",","", $request->iva_amount ));
      
      

      //Fechas
      $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->generated_date . ' ' . $request->hora);
      $this->generated_date = $fecha;
      $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
      $this->due_date = $fechaV;
      
      $this->year = $fecha->year;
      $this->month = $fecha->month;
    
      $this->save();
    
      $lids = array();
      foreach($request->items as $item) {
        $item['item_number'] = "NaN" != $item['item_number'] ? $item['item_number'] : 1;
        
        $item_id = $item['id'] ? $item['id'] : 0;
        $item_number = $item['item_number'];
        $code = $item['code'];
        $name = $item['name'];
        $product_type = $item['product_type'];
        $measure_unit = $item['measure_unit'];
        $item_count = $item['item_count'];
        $unit_price = $item['unit_price'];
        $subtotal = $item['subtotal'];
        $total = $item['total'];
        $discount_percentage = '0';
        $discount_reason = "";
        $iva_type = $item['iva_type'];
        $iva_percentage = $item['iva_percentage'];
        $iva_amount = $item['iva_amount'];
        $is_exempt = false;
        $isIdentificacion = $item['is_identificacion_especifica'];
        $item_modificado = $this->addEditItem( $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $is_exempt );
        array_push( $lids, $item_modificado->id );
      }
      
      foreach ( $this->items as $item ) {
        if( !in_array( $item->id, $lids ) ) {
          $item->delete();
        }
      }
      
      return $this;
      
    }
  
    public function addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, 
                             $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $is_exempt  )
    {
      return InvoiceItem::create([
        'invoice_id' => $this->id,
        'company_id' => $this->company_id,
        'year' => $this->year,
        'month' => $this->month,
        'item_number' => $item_number,
        'code' => $code,
        'name' => $name,
        'product_type' => $product_type,
        'measure_unit' => $measure_unit,
        'item_count' => $item_count,
        'unit_price' => $unit_price,
        'subtotal' => $subtotal,
        'total' => $total,
        'discount_type' => '01',
        'discount' => $discount_percentage,
        'iva_type' => $iva_type,
        'iva_percentage' => $iva_percentage,
        'iva_amount' => $iva_amount,
        'is_exempt' => $is_exempt,
        'is_identificacion_especifica' => $isIdentificacion
      ]);
      
    }
  
    public function addEditItem( $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, 
                                 $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $is_exempt )
    {
      if( $item_id ){
        
        $item = InvoiceItem::find($item_id);
        //Revisa que la linea exista y pertenece a la factura actual. Asegura que si el ID se cambia en frontend, no se actualice.
        if( $item && $item->invoice_id == $this->id ) {
          $item->company_id = $this->company_id;
          $item->year = $this->year;
          $item->month = $this->month;
          $item->item_number = $item_number;
          $item->code = $code;
          $item->name = $name;
          $item->product_type = $product_type;
          $item->measure_unit = $measure_unit;
          $item->item_count = $item_count;
          $item->unit_price = $unit_price;
          $item->subtotal = $subtotal;
          $item->total = $total;
          $item->discount_type = '01';
          $item->discount = $discount_percentage;
          $item->iva_type = $iva_type;
          $item->iva_percentage = $iva_percentage;
          $item->iva_amount = $iva_amount;
          $item->is_exempt = $is_exempt;
          $item->is_identificacion_especifica = $isIdentificacion;
          $item->save();
        }
      }else {
        $item = $this->addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $is_exempt );
      }
      return $item;
    }
    
    public static function importInvoiceRow (
        $metodoGeneracion, $nombreCliente, $codigoCliente, $tipoPersona, $identificacionCliente, $correoCliente, $telefonoCliente,
        $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
        $idMoneda, $tipoCambio, $totalDocumento, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
        $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $codeValidated
    ) {
      
      $company = currentCompanyModel();
      
      $clientCacheKey = "import-clientes-$identificacionCliente-".$company->id;
      if ( !Cache::has($clientCacheKey) ) {
          $clienteCache =  Client::firstOrCreate(
              [
                  'id_number' => $identificacionCliente,
                  'company_id' => $company->id,
              ],
              [
                  'code' => $codigoCliente ,
                  'company_id' => $company->id,
                  'tipo_persona' => str_pad($tipoPersona, 2, '0', STR_PAD_LEFT),
                  'id_number' => $identificacionCliente,
                  'first_name' => $nombreCliente,
                  'fullname' => "$identificacionCliente - $nombreCliente"
              ]
          );
          Cache::put($clientCacheKey, $clienteCache, 30);
      }
      $cliente = Cache::get($clientCacheKey);
      
      $invoiceCacheKey = "import-factura-$nombreCliente-" . $company->id . "-" . $consecutivoComprobante;
      if ( !Cache::has($invoiceCacheKey) ) {
      
          $invoice = Invoice::firstOrNew(
              [
                  'company_id' => $company->id,
                  'client_id' => $cliente->id,
                  'document_number' => $consecutivoComprobante
              ]
          );
          
          if( !$invoice->exists ) {
              
              $invoice->company_id = $company->id;
              $invoice->client_id = $cliente->id;    
      
              //Datos generales y para Hacienda
              $tipoDocumento = $tipoDocumento;
              if( $tipoDocumento == '01' || $tipoDocumento == '02' || $tipoDocumento == '03' || $tipoDocumento == '04' 
                  || $tipoDocumento == '05' || $tipoDocumento == '06' || $tipoDocumento == '07' || $tipoDocumento == '08' || $tipoDocumento == '99' ) {
                  $invoice->document_type = $tipoDocumento;    
              } else {
                 $invoice->document_type = '01'; 
              }
              
              $invoice->reference_number = $company->last_invoice_ref_number + 1;
              $invoice->document_number =  $consecutivoComprobante;
              
              //Datos generales
              $invoice->sale_condition = $condicionVenta;
              $invoice->payment_type = $metodoPago;
              $invoice->credit_time = 0;
              $invoice->description = $descripcion;
              
              $invoice->generation_method = $metodoGeneracion;
              $invoice->is_code_validated = $codeValidated;
              
              //Datos de factura
              $invoice->currency = $idMoneda;
              if( $invoice->currency == 1 ) { $invoice->currency = "CRC"; }
              if( $invoice->currency == 2 ) { $invoice->currency = "USD"; }
              
              $invoice->currency_rate = $tipoCambio;
            
              $company->last_invoice_ref_number = $invoice->reference_number;
              
              $invoice->subtotal = 0;
              $invoice->iva_amount = 0;
              $invoice->total = $totalDocumento;
              
              $invoice->save();
          }   
          Cache::put($invoiceCacheKey, $invoice, 30);
      }
      $invoice = Cache::get($invoiceCacheKey);
      
      $invoice->generated_date = Carbon::createFromFormat('d/m/Y', $fechaEmision);
      $invoice->due_date = Carbon::createFromFormat('d/m/Y', $fechaVencimiento);
      
      $year = $invoice->generated_date->year;
      $month = $invoice->generated_date->month;
      
      $invoice->year = $year;
      $invoice->month = $month;
    
      /**LINEA DE FACTURA**/
      $item = InvoiceItem::firstOrNew(
          [
              'invoice_id' => $invoice->id,
              'item_number' => $numeroLinea,
          ]
      );
      
      $insert = false;
      
      if( !$item->exists ) {
          $invoice->subtotal = $invoice->subtotal + $subtotalLinea;
          $invoice->iva_amount = $invoice->iva_amount + $montoIva;
          
          $discount_reason = "";
          
          $insert = [
              'invoice_id' => $invoice->id,
              'company_id' => $company->id,
              'year' => $year,
              'month' => $month,
              'item_number' => $numeroLinea,
              'code' => $codigoProducto,
              'name' => $detalleProducto,
              'product_type' => 1,
              'measure_unit' => $unidadMedicion,
              'item_count' => $cantidad,
              'unit_price' => $precioUnitario,
              'subtotal' => $subtotalLinea,
              'total' => $totalLinea,
              'discount_type' => '01',
              'discount' => $montoDescuento,
              'iva_type' => $codigoEtax,
              'iva_amount' => $montoIva,
          ];
      }
      
      if( $invoice->year == 2018 ) {
         clearLastTaxesCache($company->id, 2018);
      }
      
      clearInvoiceCache($invoice);
      $invoice->save();
      return $insert;
      
    }


    public function shortDate() {
        date_default_timezone_set("America/Costa_Rica");
        $date = date_create();
        return date_format($date,'dmy');
    }

    public function getIdFormat($id){
        $clean="000000".trim(str_replace("-","",$id));
        return substr($clean, -12);
    }

    public function getHashFromRef($ref) {
        $salesId = $ref;
        if ($salesId === null) {
            return '';
        }
        return substr('0000'.hexdec(substr(sha1($ref.'Factel'.$salesId.'Facthor'), 0, 15)) % 999999999, -8);
    }
}
