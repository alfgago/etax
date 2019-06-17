<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\BillItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Storage;

class Bill extends Model
{
    use Sortable, SoftDeletes;
    
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
    
    public function providerName() {
      if( isset($this->provider_id) ) {
        return $this->provider->getFullName();
      }else{
        return 'N/A';
      }
    }
    
    public function documentTypeName() {
      $tipo = 'Factura electrónica';
      if( $this->document_type == '03' ) {
        $tipo = "Nota de crédito";
      }else if( $this->document_type == '04' ) {
        $tipo = "Tiquete";
      }else if( $this->document_type == '02' ) {
        $tipo = "Nota de débito";
      }else if( $this->document_type == '1' ) {
         $this->document_type = '01';
         $this->save();
      }
      
      return $tipo;
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
    
      $provider;
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
          $provider = Provider::find($this->provider_id);
      }
      
      $request->currency_rate = $request->currency_rate ? $request->currency_rate : 1;
      
      //Datos de factura
      $this->description = $request->description;
      $this->subtotal = floatval( str_replace(",","", $request->subtotal ));
      $this->currency = $request->currency;
      $this->currency_rate = floatval( str_replace(",","", $request->currency_rate ));
      $this->total = floatval( str_replace(",","", $request->total ));
      $this->iva_amount = floatval( str_replace(",","", $request->iva_amount ));
      
      $this->provider_first_name = $provider->first_name;
      $this->provider_last_name = $provider->last_name;
      $this->provider_last_name2 = $provider->last_name2;
      $this->provider_email = $provider->email;
      $this->provider_address = $provider->address;
      $this->provider_country = $provider->country;
      $this->provider_state = $provider->state;
      $this->provider_city = $provider->city;
      $this->provider_district = $provider->district;
      $this->provider_zip = $provider->zip;
      $this->provider_phone = $provider->phone;
      $this->provider_id_number = $provider->id_number;

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
        $item['item_id'] = $item['id'] ? $item['id'] : 0;
        $item_modificado = $this->addEditItem($item);
                
        array_push( $lids, $item_modificado->id );
      }
      
      foreach ( $this->items as $item ) {
        if( !in_array( $item->id, $lids ) ) {
          $item->delete();
        }
      }
      
      return $this;
      
    }
  
    /*public function addItem( 
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
      
    }*/


    public function addEditItem(array $data)
    {
      if(isset($data['item_number'])) {
          $item = BillItem::updateOrCreate([
              'item_number' => $data['item_number'],
              'bill_id' => $this->id,
              'code'=> $data['code']
          ], [
                  'company_id' => $this->company_id,
                  'year'  => $this->year,
                  'month' => $this->month,
                  'name'  => $data['name'] ?? '',
                  'product_type' => $data['product_type'] ?? '',
                  'measure_unit' => $data['measure_unit'] ?? '',
                  'item_count'   => $data['item_count'] ?? '',
                  'unit_price'   => $data['unit_price'] ?? '',
                  'subtotal'     => $data['subtotal'] ?? '',
                  'total' => $data['total'] ?? '',
                  'discount_type' => $data['discount_type'] ?? null,
                  'discount' => $data['discount'] ?? 0,
                  'iva_type' => $data['iva_type'] ?? '',
                  'iva_percentage' => $data['iva_percentage'] ?? '',
                  'iva_amount' => $data['iva_amount'] ?? '',
                  'is_exempt' => $data['is_exempt'] ?? false,
                  'porc_identificacion_plena' =>  $data['porc_identificacion_plena'] ?? ''
              ]
          );
          return $item;
      } else {
          return false;
      }
    }
    
    public static function importBillRow (
        $metodoGeneracion, $idReceptor, $nombreProveedor, $codigoProveedor, $tipoPersona, $identificacionProveedor, $correoProveedor, $telefonoProveedor,
        $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
        $idMoneda, $tipoCambio, $totalDocumento, $totalNeto, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
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
                  'email' => $correoProveedor,
                  'phone' => $telefonoProveedor,
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
                  'document_number' => $consecutivoComprobante,
                  'document_key' => $claveFactura,
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
              $bill->is_void = false;
              
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
              $company->save();
              
          }   
          Cache::put($billCacheKey, $bill, 30);
      }
      $bill = Cache::get($billCacheKey);
      
      try{
        $bill->generated_date = Carbon::createFromFormat('d/m/Y', $fechaEmision);
      }catch( \Exception $ex ){
        $dt =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaEmision);
        $bill->generated_date = Carbon::instance($dt);
      }
      
      try{
        $bill->due_date = Carbon::createFromFormat('d/m/Y', $fechaVencimiento);
      }catch( \Exception $ex ){
        $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaVencimiento);
        $bill->due_date = Carbon::instance($dt);
      }
      
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
              'iva_type' => $codigoEtax,
              'iva_amount' => $montoIva,
          ];
      }
      
      clearBillCache($bill);
      
      if( $totalNeto != 0 ) {
        $bill->subtotal = $totalNeto;
      }
      
      $bill->save();
      return $insert;
      
    }
    
    public static function saveBillXML( $arr, $metodoGeneracion ) {
        $inserts = array();
        
        $claveFactura = $arr['Clave'];
        $consecutivoComprobante = $arr['NumeroConsecutivo'];
        $fechaEmision = Carbon::createFromFormat('Y-m-d', substr($arr['FechaEmision'], 0, 10))->format('d/m/Y');
        $fechaVencimiento = $fechaEmision;
        $nombreProveedor = $arr['Emisor']['Nombre'];
        $tipoPersona = $arr['Emisor']['Identificacion']['Tipo'];
        $identificacionProveedor = $arr['Emisor']['Identificacion']['Numero'];
        $codigoProveedor = $identificacionProveedor;
        $correoProveedor = $arr['Emisor']['CorreoElectronico'];
        $telefonoProveedor = isset($arr['Emisor']['Telefono']) ? $arr['Emisor']['Telefono']['NumTelefono'] : '';
        $tipoIdReceptor = $arr['Receptor']['Identificacion']['Tipo'];
        $identificacionReceptor = $arr['Receptor']['Identificacion']['Numero'];
        $nombreReceptor = $arr['Receptor']['Nombre'];
        $condicionVenta = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        $plazoCredito = array_key_exists('PlazoCredito', $arr) ? $arr['PlazoCredito'] : '';
        $medioPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '';
        
        if ( is_array($medioPago) ) {
          $medioPago = $medioPago[0];
        }
        
        $idMoneda = $arr['ResumenFactura']['CodigoMoneda'];
        $tipoCambio = array_key_exists('TipoCambio', $arr['ResumenFactura']) ? $arr['ResumenFactura']['TipoCambio'] : '1';
        $totalDocumento = $arr['ResumenFactura']['TotalComprobante'];
        $totalNeto = $arr['ResumenFactura']['TotalVentaNeta'];
        $tipoDocumento = '01';
        $descripcion = $arr['ResumenFactura']['CodigoMoneda'];
        
        $authorize = true;
        if( $metodoGeneracion == "Email" || $metodoGeneracion == "XML-A" ) {
            $authorize = false;
        }
        
        $lineas = $arr['DetalleServicio']['LineaDetalle'];
        //Revisa si es una sola linea. Si solo es una linea, lo hace un array para poder entrar en el foreach.
        if( array_key_exists( 'NumeroLinea', $lineas ) ) {
            $lineas = [$arr['DetalleServicio']['LineaDetalle']];
        }
        
        foreach( $lineas as $linea ) {
            $numeroLinea = $linea['NumeroLinea'];
            $codigoProducto = array_key_exists('Codigo', $linea) ? $linea['Codigo']['Codigo'] : '';
            $detalleProducto = $linea['Detalle'];
            $unidadMedicion = $linea['UnidadMedida'];
            $cantidad = $linea['Cantidad'];
            $precioUnitario = (float)$linea['PrecioUnitario'];
            $subtotalLinea = (float)$linea['SubTotal'];
            $totalLinea = (float)$linea['MontoTotalLinea'];
            $montoDescuento = array_key_exists('MontoDescuento', $linea) ? $linea['MontoDescuento'] : 0;
            $codigoEtax = '003'; //De momento asume que todo en 4.2 es al 13%.
            $montoIva = 0; //En 4.2 toma el IVA como en 0. A pesar de estar con cod. 103.
            
            $insert = Bill::importBillRow(
                $metodoGeneracion, $identificacionReceptor, $nombreProveedor, $codigoProveedor, $tipoPersona, $identificacionProveedor, $correoProveedor, $telefonoProveedor,
                $claveFactura, $consecutivoComprobante, $condicionVenta, $medioPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
                $idMoneda, $tipoCambio, $totalDocumento, $totalNeto, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
                $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $authorize, false
            );
            
            if( $insert ) {
                array_push( $inserts, $insert );
            }
        }
        
        BillItem::insert($inserts);
        
        return true;
    }
    
    
    public static function storeXML($file, $consecutivoComprobante, $identificacionEmisor, $identificacionReceptor) {
        
        if ( Storage::exists("empresa-$identificacionReceptor/$identificacionEmisor-$consecutivoComprobante.xml")) {
            Storage::delete("empresa-$identificacionReceptor/$identificacionEmisor-$consecutivoComprobante.xml");
        }
        
        $path = \Storage::putFileAs(
            "empresa-$identificacionReceptor", $file, "$identificacionEmisor-$consecutivoComprobante.xml"
        );
        
        return $path;
        
    }
    
}
