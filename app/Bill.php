<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\BillItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;

class Bill extends Model
{
    use Sortable;
    
    protected $guarded = [];
    public $sortable = ['reference_number', 'generated_date'];
    
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }  
    
    //Relacion con el proveedor
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
  
    //Relación con facturas recibidas
    public function items()
    {
        return $this->hasMany(BillItem::class);
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
    public function setBillData($request) {
      
      $this->document_key = $request->document_key;
      $this->document_number = $request->document_number;
      $this->sale_condition = $request->sale_condition;
      $this->payment_type = $request->payment_type;
      $this->credit_time = $request->credit_time;
      $this->buy_order = $request->buy_order;
      $this->other_reference = $request->other_reference;
    
      //Datos de proveedor
      if( $request->provider_id == '-1' ){
          $tipo_persona = $request->tipo_persona;
          $identificacion_provider = $request->id_number;
          $codigo_provider = $request->code;
          
          $provider = Provider::firstOrCreate(
              [
                  'id_number' => $identificacion_provider,
                  'company_id' => $this->company_id,
              ],
              [
                  'code' => $codigo_provider ,
                  'company_id' => $this->company_id,
                  'tipo_persona' => $tipo_persona,
                  'id_number' => $identificacion_provider
              ]
          );
          $provider->first_name = $request->first_name;
          $provider->last_name = $request->last_name;
          $provider->last_name2 = $request->last_name2;
          $provider->country = $request->country;
          $provider->state = $request->state;
          $provider->city = $request->city;
          $provider->district = $request->district;
          $provider->neighborhood = $request->neighborhood;
          $provider->zip = $request->zip;
          $provider->address = $request->address;
          $provider->phone = $request->phone;
          $provider->es_exento = $request->es_exento;
          $provider->email = $request->email;
          $provider->save();
              
          $this->provider_id = $provider->id;
      }else{
          $this->provider_id = $request->provider_id;
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
        $discount_reason = '';
        $iva_type = $item['iva_type'];
        $iva_percentage = $item['iva_percentage'];
        $iva_amount = $item['iva_amount'];
        $is_exempt = false;
        $isIdentificacion = $item['is_identificacion_especifica'];
        $porc_identificacion_plena = $item['porc_identificacion_plena'];
        $item_modificado = $this->addEditItem( 
          $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, 
          $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $porc_identificacion_plena, $is_exempt );
        array_push( $lids, $item_modificado->id );
      }
      
      foreach ( $this->items as $item ) {
        if( !in_array( $item->id, $lids ) ) {
          $item->delete();
        }
      }
      
      return $this;
      
    }
  
    public function addItem( 
      $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, 
      $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $porc_identificacion_plena, $is_exempt )
    {
      return BillItem::create([
        'bill_id' => $this->id,
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
        'porc_identificacion_plena' => $porc_identificacion_plena
      ]);
      
    }
  
    public function addEditItem( 
      $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, 
      $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $porc_identificacion_plena, $is_exempt )
    {
      if( $item_id ){
        $item = BillItem::find($item_id);
        //Revisa que la linea exista y pertenece a la factura actual. Asegura que si el ID se cambia en frontend, no se actualice.
        if( $item && $item->bill_id == $this->id ) {
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
          $item->porc_identificacion_plena = $porc_identificacion_plena;
          $item->save();
        }
      }else {
        $item = $this->addItem( 
          $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, 
          $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $porc_identificacion_plena, $is_exempt );
      }
      return $item;
    }
    
    public static function importBillRow (
        $metodoGeneracion, $idReceptor, $nombreProveedor, $codigoProveedor, $tipoPersona, $identificacionProveedor, $correoProveedor, $telefonoProveedor,
        $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
        $idMoneda, $tipoCambio, $totalDocumento, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
        $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $isAuthorized, $codeValidated
    ) {
      
      //Revisa si el método es por correo electrónico. De ser así, usa busca la compañia por cedula.
      if( $metodoGeneracion != "Email" ){
        $company = currentCompanyModel();
      }else{
        //Si es email, busca por ID del receptor para encontrar la compañia
        $company = Company::where('id_number', $idReceptor)->first();
      }
      
      if( ! $company ) {
        return false;
      }
      
      $providerCacheKey = "import-proveedors-$identificacionProveedor-".$company->id;
      if ( !Cache::has($providerCacheKey) ) {
          $proveedorCache =  Provider::firstOrCreate(
              [
                  'id_number' => $identificacionProveedor,
                  'company_id' => $company->id,
              ],
              [
                  'code' => $codigoProveedor ,
                  'company_id' => $company->id,
                  'tipo_persona' => str_pad($tipoPersona, 2, '0', STR_PAD_LEFT),
                  'id_number' => $identificacionProveedor,
                  'first_name' => $nombreProveedor,
                  'fullname' => "$identificacionProveedor - $nombreProveedor"
              ]
          );
          Cache::put($providerCacheKey, $proveedorCache, 30);
      }
      $proveedor = Cache::get($providerCacheKey);
      
      $billCacheKey = "import-factura-$identificacionProveedor-" . $company->id . "-" . $consecutivoComprobante;
      if ( !Cache::has($billCacheKey) ) {
      
          $bill = Bill::firstOrNew(
              [
                  'company_id' => $company->id,
                  'provider_id' => $proveedor->id,
                  'document_number' => $consecutivoComprobante
              ]
          );
          
          if( !$bill->exists ) {
              
              $bill->company_id = $company->id;
              $bill->provider_id = $proveedor->id;    
      
              //Datos generales y para Hacienda
              if( $tipoDocumento == '01' || $tipoDocumento == '02' || $tipoDocumento == '03' || $tipoDocumento == '04' 
                  || $tipoDocumento == '05' || $tipoDocumento == '06' || $tipoDocumento == '07' || $tipoDocumento == '08' || $tipoDocumento == '99' ) {
                  $bill->document_type = $tipoDocumento;    
              } else {
                 $bill->document_type = '01'; 
              }
              
              $bill->reference_number = $company->last_bill_ref_number + 1;
              $bill->document_number =  $consecutivoComprobante;
              $bill->document_key =  $claveFactura;
              
              //Datos generales
              $bill->sale_condition = $condicionVenta;
              $bill->payment_type = $metodoPago;
              $bill->credit_time = 0;
              $bill->description = $descripcion;
              
              $bill->generation_method = $metodoGeneracion;
              $bill->is_authorized = $isAuthorized;
              $bill->is_code_validated = $codeValidated;
              
              //Datos de factura
              $bill->currency = $idMoneda;
              if( $bill->currency == 1 ) { $bill->currency = "CRC"; }
              if( $bill->currency == 2 ) { $bill->currency = "USD"; }
                  
              $bill->currency_rate = $tipoCambio;
              //$bill->description = $row['description'] ? $row['description'] : '';
            
              $company->last_bill_ref_number = $bill->reference_number;
              
              $bill->subtotal = 0;
              $bill->iva_amount = 0;
              $bill->total = $totalDocumento;
              
              $bill->save();
          }   
          Cache::put($billCacheKey, $bill, 30);
      }
      $bill = Cache::get($billCacheKey);
      
      $bill->generated_date = Carbon::createFromFormat('d/m/Y', $fechaEmision);
      $bill->due_date = Carbon::createFromFormat('d/m/Y', $fechaVencimiento);
      
      $year = $bill->generated_date->year;
      $month = $bill->generated_date->month;
      
      $bill->year = $year;
      $bill->month = $month;
    
      /**LINEA DE FACTURA**/
      $item = BillItem::firstOrNew(
          [
              'bill_id' => $bill->id,
              'item_number' => $numeroLinea,
          ]
      );
      
      $insert = false;
      
      if( !$item->exists ) {
          $bill->subtotal = $bill->subtotal + $subtotalLinea;
          $bill->iva_amount = $bill->iva_amount + $montoIva;
          
          $insert = [
              'bill_id' => $bill->id,
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
              'discount_reason' => '',
              'iva_type' => $codigoEtax,
              'iva_amount' => $montoIva,
          ];
      }
      
      clearBillCache($bill);
      $bill->save();
      return $insert;
      
    }
}
