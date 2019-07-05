<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\BillItem;
use App\Provider;
use App\XmlHacienda;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Log;
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
    
      //$provider;
      //Datos de proveedor
      if( $request->provider_id == '-1' ){
          $tipo_persona = $request->tipo_persona;
          $identificacion_provider = preg_replace("/[^0-9]/", "", $request->id_number );
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
          $provider->foreign_address = $request->foreign_address ?? null;
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
                  'name'  => $data['name'] ?? null,
                  'product_type' => $data['product_type'] ?? '1',
                  'measure_unit' => $data['measure_unit'] ?? 'Unid',
                  'item_count'   => $data['item_count'] ?? 1,
                  'unit_price'   => $data['unit_price'] ?? 0,
                  'subtotal'     => $data['subtotal'] ?? 0,
                  'total' => $data['total'] ?? 0,
                  'discount_type' => $data['discount_type'] ?? null,
                  'discount' => $data['discount'] ?? 0,
                  'iva_type' => $data['iva_type'] ?? null,
                  'iva_percentage' => $data['iva_percentage'] ?? 0,
                  'iva_amount' => $data['iva_amount'] ?? 0,
                  'is_exempt' => $data['is_exempt'] ?? false,
                  'porc_identificacion_plena' =>  $data['porc_identificacion_plena'] ?? 0,
                  'exoneration_document_type' => $data['exoneration_document_type'] ?? null,
                  'exoneration_document_number' => $data['exoneration_document_number'] ?? null,
                  'exoneration_company_name' => $data['exoneration_company_name'] ?? null,
                  'exoneration_porcent' => $data['exoneration_porcent'] ?? 0,
                  'exoneration_amount' => $data['exoneration_amount'] ?? 0,
                  'impuesto_neto' => $data['impuesto_neto'] ?? 0,
                  'exoneration_total_amount' => $data['exoneration_total_amount'] ?? 0
              ]
          );
          return $item;
      } else {
          return false;
      }
    }
    
    public static function importBillRow ( $arrayImportBill ) {
      
      //Revisa si el método es por correo electrónico. De ser así, usa busca la compañia por cedula.
      if( $arrayImportBill['metodoGeneracion'] != "Email" ){
        $company = currentCompanyModel();
      }else{
        //Si es email, busca por ID del receptor para encontrar la compañia
        $company = Company::where('id_number', $arrayImportBill['idReceptor'])->first();
      }
      
      if( ! $company ) {
        return false;
      }
      
      $identificacionProveedor = preg_replace("/[^0-9]/", "", $arrayImportBill['identificacionProveedor']);
      $providerCacheKey = "import-proveedors-$identificacionProveedor-".$company->id;
      if ( !Cache::has($providerCacheKey) ) {
          $proveedorCache =  Provider::firstOrCreate(
              [
                  'id_number' => $identificacionProveedor,
                  'company_id' => $company->id,
              ],
              [
                  'code' => $arrayImportBill['codigoProveedor'],
                  'company_id' => $company->id,
                  'tipo_persona' => str_pad($arrayImportBill['tipoPersona'], 2, '0', STR_PAD_LEFT),
                  'id_number' => $identificacionProveedor,
                  'first_name' => $arrayImportBill['nombreProveedor'],
                  'email' => $arrayImportBill['correoProveedor'],
                  'phone' => $arrayImportBill['telefonoProveedor'],
                  'fullname' => "$identificacionProveedor - " . $arrayImportBill['nombreProveedor']
              ]
          );
          Cache::put($providerCacheKey, $proveedorCache, 30);
          $proveedorCache->save();
      }
      $proveedor = Cache::get($providerCacheKey);
      
      $billCacheKey = "import-factura-$identificacionProveedor-" . $company->id . "-" . $arrayImportBill['consecutivoComprobante'];
      if ( !Cache::has($billCacheKey) ) {
      
          $bill = Bill::firstOrNew(
              [
                  'company_id' => $company->id,
                  'provider_id' => $proveedor->id,
                  'document_number' => $arrayImportBill['consecutivoComprobante'],
                  'document_key' => $arrayImportBill['claveFactura'],
              ]
          );
          
          if( !$bill->exists ) {
              
              $bill->company_id = $company->id;
              $bill->provider_id = $proveedor->id;    
      
              //Datos generales y para Hacienda
              if( $arrayImportBill['tipoDocumento'] == '01' || $arrayImportBill['tipoDocumento'] == '02' || $arrayImportBill['tipoDocumento'] == '03' || $arrayImportBill['tipoDocumento'] == '04'
                  || $arrayImportBill['tipoDocumento'] == '05' || $arrayImportBill['tipoDocumento'] == '06' || $arrayImportBill['tipoDocumento'] == '07' || $arrayImportBill['tipoDocumento'] == '08' || $arrayImportBill['tipoDocumento'] == '99' ) {
                  $bill->document_type = $arrayImportBill['tipoDocumento'];
              } else {
                 $bill->document_type = '01'; 
              }
              
              $bill->reference_number = $company->last_bill_ref_number + 1;
              $bill->document_number =  $arrayImportBill['consecutivoComprobante'];
              $bill->document_key =  $arrayImportBill['claveFactura'];
              $bill->xml_schema =  $arrayImportBill['xmlSchema'] ?? 43;
              $bill->commercial_activity =  $arrayImportBill['codigoActividad'] ?? '0';
              
              //Datos generales
              $bill->sale_condition = $arrayImportBill['condicionVenta'];
              $bill->payment_type = $arrayImportBill['metodoPago'];
              $bill->credit_time = 0;
              $bill->description = $arrayImportBill['descripcion'];

              $bill->generation_method = $arrayImportBill['metodoGeneracion'];
              $bill->is_authorized = $arrayImportBill['isAuthorized'];
              $bill->is_code_validated = $arrayImportBill['codeValidated'];


              $bill->provider_id_number = preg_replace("/[^0-9]/", "", $arrayImportBill['identificacionProveedor']);
              $bill->provider_first_name = $arrayImportBill['nombreProveedor'];
              $bill->provider_last_name = '';
              $bill->provider_last_name2 = '';
              $bill->provider_email = $arrayImportBill['correoProveedor'];
              $bill->provider_address = '';
              $bill->provider_country = '';
              $bill->provider_city = '';
              $bill->provider_state = '';
              $bill->provider_district = '';
              $bill->provider_phone = $arrayImportBill['telefonoProveedor'];
              $bill->provider_zip = '';
              
              if($arrayImportBill['metodoGeneracion'] == 'Email' || $arrayImportBill['metodoGeneracion'] == 'XML') {
                $bill->accept_status = 0;
                $bill->hacienda_status = "01";
              }else{
                $bill->accept_status = 1;
                $bill->hacienda_status = "03";
              }

              $bill->is_void = false;
              
              //Datos de factura
              $bill->currency = $arrayImportBill['idMoneda'];
              if( $bill->currency == 1 ) { $bill->currency = "CRC"; }
              if( $bill->currency == 2 ) { $bill->currency = "USD"; }
                  
              $bill->currency_rate = $arrayImportBill['tipoCambio'];
              //$bill->description = $row['description'] ? $row['description'] : '';
            
              $company->last_bill_ref_number = $bill->reference_number;
              
              $bill->subtotal = 0;
              $bill->iva_amount = 0;
              $bill->total = $arrayImportBill['totalDocumento'];
              
              $bill->save();
              $company->save();
              
          }   
          Cache::put($billCacheKey, $bill, 30);
      }
      $bill = Cache::get($billCacheKey);
      
      try{
        $bill->generated_date = Carbon::createFromFormat('d/m/Y', $arrayImportBill['fechaEmision']);
      }catch( \Exception $ex ){
        $dt =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($arrayImportBill['fechaEmision']);
        $bill->generated_date = Carbon::instance($dt);
      }
      
      try{
        $bill->due_date = Carbon::createFromFormat('d/m/Y', $arrayImportBill['fechaVencimiento']);
      }catch( \Exception $ex ){
        $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($arrayImportBill['fechaVencimiento']);
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
              'item_number' => $arrayImportBill['numeroLinea'],
          ]
      );
      
      $insert = false;
      
      if( !$item->exists ) {
          $bill->subtotal = $bill->subtotal + $arrayImportBill['subtotalLinea'];
          $bill->iva_amount = $bill->iva_amount + $arrayImportBill['montoIva'];
          
          $insert = [
              'bill_id' => $bill->id,
              'company_id' => $company->id,
              'year' => $year,
              'month' => $month,
              'item_number' => $arrayImportBill['numeroLinea'],
              'code' => $arrayImportBill['codigoProducto'],
              'name' => $arrayImportBill['detalleProducto'],
              'product_type' => 1,
              'measure_unit' => $arrayImportBill['unidadMedicion'],
              'item_count' => $arrayImportBill['cantidad'],
              'unit_price' => $arrayImportBill['precioUnitario'],
              'subtotal' => $arrayImportBill['subtotalLinea'],
              'total' => $arrayImportBill['totalLinea'],
              'discount_type' => '01',
              'discount' => $arrayImportBill['montoDescuento'],
              'iva_type' => $arrayImportBill['codigoEtax'],
              'iva_amount' => $arrayImportBill['montoIva'],
              'exoneration_document_type' => $arrayImportBill['tipoDocumentoExoneracion'],
              'exoneration_document_number' => $arrayImportBill['documentoExoneracion'],
              'exoneration_company_name' => $arrayImportBill['companiaExoneracion'],
              'exoneration_porcent' => $arrayImportBill['porcentajeExoneracion'],
              'exoneration_amount' => $arrayImportBill['montoExoneracion'],
              'impuesto_neto' => $arrayImportBill['impuestoNeto'],
              'exoneration_total_amount' => $arrayImportBill['totalMontoLinea']
          ];
      }
      
      clearBillCache($bill);
      
      if( $arrayImportBill['totalNeto'] != 0 ) {
        $bill->subtotal = $arrayImportBill['totalNeto'];
      }
      
      $bill->save();
      return $insert;
      
    }
    
    public static function saveBillXML( $arr, $metodoGeneracion ) {
        $inserts = array();
        
        $claveFactura = $arr['Clave'];
        $codigoActividad = $arr['CodigoActividad'] ?? 0;
        $consecutivoComprobante = $arr['NumeroConsecutivo'];
        $fechaEmision = Carbon::createFromFormat('Y-m-d', substr($arr['FechaEmision'], 0, 10))->format('d/m/Y');
        $fechaVencimiento = $fechaEmision;
        $nombreProveedor = $arr['Emisor']['Nombre'];
        $tipoPersona = $arr['Emisor']['Identificacion']['Tipo'];
        $identificacionProveedor = $arr['Emisor']['Identificacion']['Numero'];
        $codigoProveedor = $identificacionProveedor;
        $correoProveedor = $arr['Emisor']['CorreoElectronico'];
        
        
        if ( isset($arr['Emisor']['Telefono']) ) {
          $telefonoProveedor = $arr['Emisor']['Telefono']['NumTelefono'] ?? null;
        }else{
          $telefonoProveedor = null;
        }
        
        $tipoIdReceptor = $arr['Receptor']['Identificacion']['Tipo'];
        $identificacionReceptor = $arr['Receptor']['Identificacion']['Numero'];
        $nombreReceptor = $arr['Receptor']['Nombre'];
        $condicionVenta = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        $plazoCredito = array_key_exists('PlazoCredito', $arr) ? $arr['PlazoCredito'] : '';
        $medioPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '';
        
        if ( is_array($medioPago) ) {
          $medioPago = $medioPago[0];
        }
        
        if( array_key_exists( 'CodigoTipoMoneda', $arr['ResumenFactura'] ) ) {
          $idMoneda = $arr['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'] ?? '';
          $tipoCambio = $arr['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ?? 1;
        }else {
          $idMoneda = $arr['ResumenFactura']['CodigoMoneda'] ?? '';
          $tipoCambio = array_key_exists('TipoCambio', $arr['ResumenFactura']) ? $arr['ResumenFactura']['TipoCambio'] : '1';
        }
        
        $totalDocumento = $arr['ResumenFactura']['TotalComprobante'];
        $totalNeto = $arr['ResumenFactura']['TotalVentaNeta'];
        
        $tipoDocumento = $arr['TipoDoc'] ?? '01';
        $descripcion = 'XML Importado';
        
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
            try {
              $codigoProducto = array_key_exists('Codigo', $linea) ? $linea['Codigo']['Codigo'] ?? '' : '';
            } catch( \Throwable $e ) {
              $codigoProducto = "No indica";
            }
            $detalleProducto = $linea['Detalle'];
            $unidadMedicion = $linea['UnidadMedida'];
            $cantidad = $linea['Cantidad'];
            $precioUnitario = (float)$linea['PrecioUnitario'];
            $subtotalLinea = (float)$linea['SubTotal'];
            $totalLinea = (float)$linea['MontoTotalLinea'];
            $montoDescuento = array_key_exists('MontoDescuento', $linea) ? $linea['MontoDescuento'] : 0;
            $codigoEtax = '003'; //De momento asume que todo en 4.2 es al 13%.
            $montoIva = 0; //En 4.2 toma el IVA como en 0. A pesar de estar con cod. 103.

            if( array_key_exists('Impuesto', $linea) ) {
              //$codigoEtax = $linea['Impuesto']['CodigoTarifa'];
              $montoIva = $linea['Impuesto']['Monto'];
            }

            $tipoDocumentoExoneracion = $linea['tipoDocumentoExoneracion'] ?? null;
            $documentoExoneracion = $linea['documentoExoneracion']  ?? null;
            $companiaExoneracion = $linea['companiaExoneracion'] ?? null;
            $porcentajeExoneracion = $linea['porcentajeExoneracion'] ?? 0;
            $montoExoneracion = $linea['montoExoneracion'] ?? 0;
            $impuestoNeto = $linea['impuestoNeto'] ?? 0;
            $totalMontoLinea = $linea['totalMontoLinea'] ?? 0;

            $arrayImportBillRow = array(
                'metodoGeneracion' => $metodoGeneracion,
                'identificacionReceptor' => $identificacionReceptor,
                'idReceptor' => $identificacionReceptor,
                'nombreProveedor' => $nombreProveedor,
                'codigoProveedor' => $codigoProveedor,
                'tipoPersona' => $tipoPersona,
                'identificacionProveedor' => $identificacionProveedor,
                'correoProveedor' => $correoProveedor,
                'telefonoProveedor' => $telefonoProveedor,
                'claveFactura' => $claveFactura,
                'consecutivoComprobante' => $consecutivoComprobante,
                'condicionVenta' => $condicionVenta,
                'metodoPago' => $medioPago,
                'numeroLinea' => $numeroLinea,
                'fechaEmision' => $fechaEmision,
                'fechaVencimiento' => $fechaVencimiento,
                'idMoneda' => $idMoneda,
                'tipoCambio' => $tipoCambio,
                'totalDocumento' => $totalDocumento,
                'totalNeto' => $totalNeto,
                'tipoDocumento' => $tipoDocumento,
                'codigoProducto' => $codigoProducto,
                'detalleProducto' => $detalleProducto,
                'unidadMedicion' => $unidadMedicion,
                'cantidad' => $cantidad,
                'precioUnitario' => $precioUnitario,
                'subtotalLinea' => $subtotalLinea,
                'totalLinea' => $totalLinea,
                'montoDescuento' => $montoDescuento,
                'codigoEtax' => $codigoEtax,
                'montoIva' => $montoIva,
                'descripcion' => $descripcion,
                'isAuthorized' => $authorize,
                'codeValidated' => false,
                'tipoDocumentoExoneracion' => $tipoDocumentoExoneracion,
                'documentoExoneracion' => $documentoExoneracion,
                'companiaExoneracion' => $companiaExoneracion,
                'porcentajeExoneracion' => $porcentajeExoneracion,
                'montoExoneracion' => $montoExoneracion,
                'impuestoNeto' => $impuestoNeto,
                'totalMontoLinea' => $totalMontoLinea,
                'xmlSchema' => $codigoActividad ? 43 : 42,
                'codigoActividad' => $codigoActividad
            );
            $insert = Bill::importBillRow( $arrayImportBillRow );
            
            if( $insert ) {
                array_push( $inserts, $insert );
            }
        }
        
        $items = BillItem::insert($inserts);
        
        return $items;
    }
    
    
    public static function storeXML($bill, $file) {
        
        $cedulaEmpresa = $bill->company->id_number;
        $cedulaProveedor = $bill->provider->id_number;
        $consecutivoComprobante = $bill->document_number;
        
        if ( Storage::exists("empresa-$cedulaEmpresa/facturas_compras/$cedulaProveedor-$consecutivoComprobante.xml")) {
            Storage::delete("empresa-$cedulaEmpresa/facturas_compras/$cedulaProveedor-$consecutivoComprobante.xml");
        }
        
        $path = \Storage::putFileAs(
            "empresa-$cedulaEmpresa/facturas_compras", $file, "$cedulaProveedor-$consecutivoComprobante.xml"
        );
        
        try{
          $xmlHacienda = new XmlHacienda();
          $xmlHacienda->xml = $path;
          $xmlHacienda->bill_id = $bill->id;
          $xmlHacienda->invoice_id = 0;
          $xmlHacienda->save();
          Log::info( 'XMLHacienda guardado: ' . $bill->id );
        }catch( \Throwable $e ){
          Log::error( 'Error al registrar en tabla XMLHacienda: ' . $e->getMessage() );
        }
        
        return $path;
        
    }
    
    public function calculateAcceptFields() {
      
      if( $this->is_code_validated ) {
        if( $this->xml_schema == 43 ) {
          $company = currentCompanyModel();
          $prorrataOperativa = $company->getProrrataOperativa( $this->year );
          $calc = new CalculatedTax();
          $lastBalance = 0;
          $query = BillItem::with('bill')->where('bill_id', $this->id);
          //$calc->setDatosEmitidos( $this->month, $this->year, $company->id );
          $calc->setDatosSoportados( $this->month, $this->year, $company->id, $query );
          $calc->setCalculosPorFactura( $prorrataOperativa, $lastBalance );
          
          $this->accept_iva_acreditable = $calc->iva_deducible_operativo;
          $this->accept_iva_gasto = $calc->iva_no_deducible;
          $this->accept_iva_total = $calc->total_bill_iva;
          $this->accept_total_factura = $calc->bills_total;
          $this->accept_id_number = $company->id_number;
          
          $this->accept_iva_condition = '02';
          if( $this->accept_iva_total == 0 ) {
            $this->accept_iva_condition = '01'; //Si no hay IVA
          }else if( $this->accept_iva_gasto == 0 ) {
            $this->accept_iva_condition = '01'; //Si todo lo pagado de IVA es acreditable
          }else if( $this->accept_iva_acreditable == 0) {
            $this->accept_iva_condition = '04'; //Si todo lo pagao de IVa va al gasto
          }
          
          $sinIdentificacion = ($calc->iva_acreditable_identificacion_plena != $calc->total_bill_iva 
                                  && $calc->iva_acreditable_identificacion_plena != 0
                                  && $calc->accept_iva_acreditable != $calc->total_bill_iva
                              );
          if( $sinIdentificacion ) {
            $this->accept_iva_condition = '05'; //Si exista minimo 1 linea sin identificación específica.
          }
          
          $bienesCapital = $calc->b011 + $calc->b031 + $calc->b051 + $calc->b071 + $calc->b015  + $calc->b035 +
             $calc->b012 + $calc->b032 + $calc->b052 + $calc->b072 +
             $calc->b013 + $calc->b033 + $calc->b053 + $calc->b073 + $calc->b016 + $calc->b036 +
             $calc->b014 + $calc->b034 + $calc->b054 + $calc->b074;
          if( $bienesCapital ) {
            $this->accept_iva_condition = '03'; // Si son propiedad, planta o equipo (Bienes de capital)
          }
          
        }
      }else {
        $this->accept_iva_acreditable = 0;
        $this->accept_iva_gasto = 0;
        $this->accept_iva_total = 0;
        $this->accept_total_factura = 0;
        $this->accept_iva_condition = '01';
      }
      
      $this->save();
      return $this;
        
    }
      
    
    
}
