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
    
    public function activity()
    {
        return $this->belongsTo(Actividades::class, 'activity_company_verification');
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
    
    //Relacion con hacienda
    public function xmlHacienda()
    {
        return $this->hasOne(XmlHacienda::class);
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
    
      $this->xml_schema = $this->commercial_activity ? 43 : 42;

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
      
      if( $request->xml_schema ){
        $this->xml_schema = $request->xml_schema;
      }
      if( $request->activity_company_verification ){
        $this->activity_company_verification = $request->activity_company_verification;
      }
      
      $this->accept_status = $request->accept_status ? 1 : 0;
      if( !$this->accept_status ) {
        $this->is_code_validated = false;
      }
      
      if( $request->accept_iva_condition ){
        $this->accept_iva_condition = $request->accept_iva_condition;
      }
      if( $request->accept_iva_acreditable ){
        $this->accept_iva_acreditable = $request->accept_iva_acreditable;
      }
      if( $request->accept_iva_gasto ){
        $this->accept_iva_gasto = $request->accept_iva_gasto;
      }
    
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
                  'porc_identificacion_plena' =>  $data['porc_identificacion_plena'] ?? 0
              ]
          );
          
          try {
            $exonerationDate = isset( $data['exoneration_date'] )  ? Carbon::createFromFormat('d/m/Y', $data['exoneration_date']) : null;
          }catch( \Exception $e ) {
            $exonerationDate = null;
          }
          if( $exonerationDate && $data['exoneration_document_type'] && $data['exoneration_document_number'] ) {
            $item->exoneration_document_type = $data['exoneration_document_type'] ?? null;
            $item->exoneration_document_number = $data['exoneration_document_number'] ?? null;
            $item->exoneration_company_name = $data['exoneration_company_name'] ?? null;
            $item->exoneration_porcent = $data['exoneration_porcent'] ?? 0;
            $item->exoneration_amount = $data['exoneration_amount'] ?? 0;
            $item->exoneration_date = $exonerationDate;
            $item->exoneration_total_amount = $data['exoneration_total_amount'] ?? 0;
            $item->impuesto_neto = isset($data['impuesto_neto']) ? $data['iva_amount'] - $data['montoExoneracion'] : $data['iva_amount'];
            $item->save();
          }
          
          return $item;
      } else {
          return false;
      }
    }
    
    
    public static function saveBillXML( $arr, $metodoGeneracion ) {
      
        $identificacionReceptor = $arr['Receptor']['Identificacion']['Numero'];
        if( $metodoGeneracion != "Email" ){
          $company = currentCompanyModel();
        }else{
          //Si es email, busca por ID del receptor para encontrar la compañia
          $company = Company::where('id_number', $identificacionReceptor)->first();
        }
        
        if( ! $company ) {
          return false;
        }
      
        $bill = Bill::firstOrNew(
              [
                  'company_id' => $company->id,
                  'document_number' => $arr['NumeroConsecutivo'],
                  'document_key' => $arr['Clave'],
              ]
        );
        
        $bill->commercial_activity = $arr['CodigoActividad'] ?? 0;
        $bill->xml_schema = $bill->commercial_activity ? 43 : 42;
        $bill->sale_condition = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        $bill->credit_time = array_key_exists('PlazoCredito', $arr) ? $arr['PlazoCredito'] : '';
        $medioPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '';
        if ( is_array($medioPago) ) {
          $medioPago = $medioPago[0];
        }
        $bill->payment_type = $medioPago;
        
        //Fechas
        $fechaEmision = Carbon::createFromFormat('Y-m-d', subS0tr($arr['FechaEmision'], 0, 10));
        $bill->generated_date = $fechaEmision;
        $bill->due_date = $fechaEmision;
        
        $month = $fechaEmision->month;
        $year = $fechaEmision->year;
        $bill->month = $month;
        $bill->year = $year;
        
        if( array_key_exists( 'CodigoTipoMoneda', $arr['ResumenFactura'] ) ) {
          $idMoneda = $arr['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'] ?? '';
          $tipoCambio = $arr['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ?? 1;
        }else {
          $idMoneda = $arr['ResumenFactura']['CodigoMoneda'] ?? '';
          $tipoCambio = array_key_exists('TipoCambio', $arr['ResumenFactura']) ? $arr['ResumenFactura']['TipoCambio'] : '1';
        }
        $bill->currency = $idMoneda;
        $bill->currency_rate = $tipoCambio;
        
        $bill->description = 'XML Importado';
        $bill->document_type = $arr['TipoDoc'] ?? '01';
        $bill->total = $arr['ResumenFactura']['TotalComprobante'];
        
        $authorize = true;
        if( $metodoGeneracion == "Email" || $metodoGeneracion == "XML-A" ) {
            $authorize = false;
        }
        
        $bill->accept_status = 0;
        $bill->hacienda_status = "03";
        $bill->payment_status = "01";
        $bill->generation_method = $metodoGeneracion;
        $bill->is_authorized = $authorize;
        $bill->is_code_validated = false;
        
        //Start DATOS PROVEEDOR
              $nombreProveedor = $arr['Emisor']['Nombre'];
              $tipoPersona = $arr['Emisor']['Identificacion']['Tipo'];
              $tipoPersona = str_pad($tipoPersona, 2, '0', STR_PAD_LEFT);
              $identificacionProveedor = $arr['Emisor']['Identificacion']['Numero'];
              $codigoProveedor = $identificacionProveedor;
              $correoProveedor = $arr['Emisor']['CorreoElectronico'];
              
              if ( isset($arr['Emisor']['Ubicacion']) ) {
                $provinciaProveedor = $arr['Emisor']['Ubicacion']['Provincia'];
                $cantonProveedor = $arr['Emisor']['Ubicacion']['Canton'];
                $distritoProveedor = $arr['Emisor']['Ubicacion']['Distrito'];
                $otrasSenas = $arr['Emisor']['Ubicacion']['OtrasSenas'] ?? null;
                
                $zipProveedor = 0;
                if( $cantonProveedor ) {
                    if( strlen( (int)$cantonProveedor ) <= 2 ) {
                        $cantonProveedor = (int)$provinciaProveedor . str_pad((int)$cantonProveedor, 2, '0', STR_PAD_LEFT);
                    }
                }
                if( $distritoProveedor ) {
                    if( strlen( $distritoProveedor ) > 4 ) {
                        $zipProveedor = $distritoProveedor;
                    }else{
                        $distritoProveedor = (int)$cantonProveedor . str_pad((int)$distritoProveedor, 2, '0', STR_PAD_LEFT);
                        $zipProveedor = $distritoProveedor;
                    }
                }
              }else{
                $provinciaProveedor = '1';
                $cantonProveedor = '101';
                $distritoProveedor = '10101';
                $zipProveedor = '10101';
                $otrasSenas = null;
              }
              
              if ( isset($arr['Emisor']['Telefono']) ) {
                $telefonoProveedor = $arr['Emisor']['Telefono']['NumTelefono'] ?? null;
              }else{
                $telefonoProveedor = null;
              }
              
              $providerCacheKey = "import-proveedors-$identificacionProveedor-".$company->id;
              if ( !Cache::has($providerCacheKey) ) {
                  $proveedorCache =  Provider::updateOrCreate(
                      [
                          'id_number' => $identificacionProveedor,
                          'company_id' => $company->id,
                      ],
                      [
                          'code' => $codigoProveedor,
                          'company_id' => $company->id,
                          'tipo_persona' => $tipoPersona,
                          'id_number' => $identificacionProveedor,
                          'first_name' => $nombreProveedor,
                          'email' => $correoProveedor,
                          'phone' => $telefonoProveedor,
                          'fullname' => "$identificacionProveedor - " . $nombreProveedor,
                          'country' => 'CR',
                          'state' => $provinciaProveedor,
                          'city' => $cantonProveedor,
                          'district' => $distritoProveedor,
                          'zip' => $zipProveedor,
                          'address' => $otrasSenas,
                      ]
                  );
                  Cache::put($providerCacheKey, $proveedorCache, 30);
              }
              $proveedor = Cache::get($providerCacheKey);
              
              $bill->provider_id = $proveedor->id;
              $bill->provider_id_number = $identificacionProveedor;
              $bill->provider_first_name = $nombreProveedor;
              $bill->provider_email = $correoProveedor;
              $bill->provider_address = $otrasSenas;
              $bill->provider_country = 'CR';
              $bill->provider_state = $provinciaProveedor;
              $bill->provider_city = $cantonProveedor;
              $bill->provider_district = $distritoProveedor;
              $bill->provider_zip = $zipProveedor;
              $bill->provider_phone = $telefonoProveedor;
              
        //End DATOS PROVEEDOR
        
        //El subtotal y iva_amount inicia en 0, lo va sumando conforme recorre las lineas.
        $bill->subtotal = 0;
        $bill->iva_amount = 0;
        
        //Revisa si es una sola linea. Si solo es una linea, lo hace un array para poder entrar en el foreach.
        $lineas = $arr['DetalleServicio']['LineaDetalle'];
        if( array_key_exists( 'NumeroLinea', $lineas ) ) {
            $lineas = [$arr['DetalleServicio']['LineaDetalle']];
        }
        
        $bill->save();
        
        $lids = array();
        $items = array();
        $numeroLinea = 0;
        foreach( $lineas as $linea ) {
            $numeroLinea++;
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
            $codigoEtax = 'B003'; //De momento asume que todo en 4.2 es al 13%.
            $montoIva = 0; //En 4.2 toma el IVA como en 0. A pesar de estar con cod. 103.
            $porcentajeIva = null;
            $impuestoNeto = $linea['ImpuestoNeto'] ?? 0;
            $totalMontoLinea = $linea['MontoTotalLinea'] ?? 0;
            
            $tipoDocumentoExoneracion = null;
            $documentoExoneracion = null;
            $companiaExoneracion = null;
            $fechaExoneracion = null;
            $porcentajeExoneracion = 0;
            $montoExoneracion = 0;
            if( array_key_exists('Impuesto', $linea) ) {
              //$codigoEtax = $linea['Impuesto']['CodigoTarifa'];
              $montoIva = $linea['Impuesto']['Monto'];
              $porcentajeIva = $linea['Impuesto']['Tarifa'];
              
              if( array_key_exists('Exoneracion', $linea['Impuesto']) ) {
                $tipoDocumentoExoneracion = $linea['Impuesto']['Exoneracion']['TipoDocumento'] ?? null;
                $documentoExoneracion = $linea['Impuesto']['Exoneracion']['NumeroDocumento']  ?? null;
                $companiaExoneracion = $linea['Impuesto']['Exoneracion']['NombreInstitucion'] ?? null;
                $fechaExoneracion = $linea['Impuesto']['Exoneracion']['FechaEmision'] ?? null;
                $porcentajeExoneracion = $linea['Impuesto']['Exoneracion']['PorcentajeExoneracion'] ?? 0;
                $montoExoneracion = $linea['Impuesto']['Exoneracion']['MontoExoneracion'] ?? 0;
              }
              
            }
            
            $bill->subtotal = $bill->subtotal + $subtotalLinea;
            $bill->iva_amount = $bill->iva_amount + $montoIva;

            $item = array(
              'id' => 0,
              'item_number' => $numeroLinea,
              'code' => $codigoProducto,
              'name' => $detalleProducto,
              'measure_unit' => $unidadMedicion,
              'item_count' => $cantidad,
              'unit_price' => $precioUnitario,
              'subtotal' => $subtotalLinea,
              'total' => $totalLinea,
              'discount_type' => 'No indica',
              'discount_reason' => 'No indica',
              'discount_percentage' => $montoDescuento,
              'discount' => $montoDescuento,
              'iva_type' => $codigoEtax,
              'iva_percentage' => $porcentajeIva,
              'iva_amount' => $montoIva,
              'impuesto_neto' => $impuestoNeto,
              'exoneration_date' => $fechaExoneracion,
              'exoneration_document_type' => $tipoDocumentoExoneracion,
              'exoneration_document_number' => $documentoExoneracion,
              'exoneration_company_name' => $companiaExoneracion,
              'exoneration_porcent' => $porcentajeExoneracion,
              'exoneration_amount' => $montoExoneracion,
              'exoneration_total_amount' => $totalMontoLinea,
              'is_exempt' => false,
              'porc_identificacion_plena' => 13,
            );
            
            $item_modificado = $bill->addEditItem($item);
            array_push( $lids, $item_modificado->id );
        }
        
        foreach ( $bill->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
        $bill->save();
        
        return $bill;
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
    
    public static function importBillRow ( $data ) {
      
      //Revisa si el método es por correo electrónico. De ser así, usa busca la compañia por cedula.
      if( $data['metodoGeneracion'] != "Email" ){
        $company = currentCompanyModel();
      }else{
        //Si es email, busca por ID del receptor para encontrar la compañia
        $company = Company::where('id_number', $data['idReceptor'])->first();
      }
      
      if( ! $company ) {
        return false;
      }
      
      $identificacionProveedor = preg_replace("/[^0-9]/", "", $data['identificacionProveedor']);
      $providerCacheKey = "import-proveedors-$identificacionProveedor-".$company->id;
      if ( !Cache::has($providerCacheKey) ) {
          $proveedorCache =  Provider::firstOrCreate(
              [
                  'id_number' => $identificacionProveedor,
                  'company_id' => $company->id,
              ],
              [
                  'code' => $data['codigoProveedor'],
                  'company_id' => $company->id,
                  'tipo_persona' => str_pad($data['tipoPersona'], 2, '0', STR_PAD_LEFT),
                  'id_number' => $identificacionProveedor,
                  'first_name' => $data['nombreProveedor'],
                  'email' => $data['correoProveedor'],
                  'phone' => $data['telefonoProveedor'],
                  'fullname' => "$identificacionProveedor - " . $data['nombreProveedor']
              ]
          );
          Cache::put($providerCacheKey, $proveedorCache, 30);
          $proveedorCache->save();
      }
      $proveedor = Cache::get($providerCacheKey);
      
      $billCacheKey = "import-factura-$identificacionProveedor-" . $company->id . "-" . $data['consecutivoComprobante'];
      if ( !Cache::has($billCacheKey) ) {
      
          $bill = Bill::firstOrNew(
              [
                  'company_id' => $company->id,
                  'provider_id' => $proveedor->id,
                  'document_number' => $data['consecutivoComprobante'],
                  'document_key' => $data['claveFactura'],
              ]
          );
          
          if( !$bill->exists ) {
              
              $bill->company_id = $company->id;
              $bill->provider_id = $proveedor->id;    
      
              //Datos generales y para Hacienda
              if( $data['tipoDocumento'] == '01' || $data['tipoDocumento'] == '02' || $data['tipoDocumento'] == '03' || $data['tipoDocumento'] == '04'
                  || $data['tipoDocumento'] == '05' || $data['tipoDocumento'] == '06' || $data['tipoDocumento'] == '07' || $data['tipoDocumento'] == '08' || $data['tipoDocumento'] == '99' ) {
                  $bill->document_type = $data['tipoDocumento'];
              } else {
                 $bill->document_type = '01'; 
              }
              
              $bill->reference_number = $company->last_bill_ref_number + 1;
              $bill->document_number =  $data['consecutivoComprobante'];
              $bill->document_key =  $data['claveFactura'];
              $bill->xml_schema =  $data['xmlSchema'] ?? 43;
              $bill->commercial_activity =  $data['codigoActividad'] ?? '0';
              
              //Datos generales
              $bill->sale_condition = $data['condicionVenta'];
              $bill->payment_type = $data['metodoPago'];
              $bill->credit_time = 0;
              $bill->description = $data['descripcion'];

              $bill->generation_method = $data['metodoGeneracion'];
              $bill->is_authorized = $data['isAuthorized'];
              $bill->is_code_validated = $data['codeValidated'];

              $bill->provider_id_number = preg_replace("/[^0-9]/", "", $data['identificacionProveedor']);
              $bill->provider_first_name = $data['nombreProveedor'] ?? null;
              $bill->provider_last_name = '';
              $bill->provider_last_name2 = '';
              $bill->provider_email = $data['correoProveedor'] ?? null;
              $bill->provider_address = $data['otrasSenas'] ?? null;
              $bill->provider_country = '';
              $bill->provider_city = $data['provinciaProveedor'] ?? null;
              $bill->provider_state = $data['cantonProveedor'] ?? null;
              $bill->provider_district = $data['distritoProveedor'] ?? null;
              $bill->provider_phone = $data['telefonoProveedor'] ?? null;
              $bill->provider_zip = $data['zipProveedor'] ?? null;
              
              if($data['metodoGeneracion'] == 'Email' || $data['metodoGeneracion'] == 'XML') {
                $bill->accept_status = 0;
                $bill->hacienda_status = "01";
              }else{
                $bill->accept_status = 1;
                $bill->hacienda_status = "03";
              }

              $bill->is_void = false;
              
              //Datos de factura
              $bill->currency = $data['idMoneda'];
              if( $bill->currency == 1 ) { $bill->currency = "CRC"; }
              if( $bill->currency == 2 ) { $bill->currency = "USD"; }
                  
              $bill->currency_rate = $data['tipoCambio'];
              //$bill->description = $row['description'] ? $row['description'] : '';
            
              $company->last_bill_ref_number = $bill->reference_number;
              
              $bill->subtotal = 0;
              $bill->iva_amount = 0;
              $bill->total = $data['totalDocumento'];
              
              $bill->save();
              $company->save();
              
          }   
          Cache::put($billCacheKey, $bill, 30);
      }
      $bill = Cache::get($billCacheKey);
      
      try{
        $bill->generated_date = Carbon::createFromFormat('d/m/Y', $data['fechaEmision']);
      }catch( \Exception $ex ){
        $dt =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['fechaEmision']);
        $bill->generated_date = Carbon::instance($dt);
      }
      
      try{
        $bill->due_date = Carbon::createFromFormat('d/m/Y', $data['fechaVencimiento']);
      }catch( \Exception $ex ){
        $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['fechaVencimiento']);
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
              'item_number' => $data['numeroLinea'],
          ]
      );
      
      $insert = false;
      
      if( !$item->exists ) {
          $bill->subtotal = $bill->subtotal + $data['subtotalLinea'];
          $bill->iva_amount = $bill->iva_amount + $data['montoIva'];
          
          $insert = [
              'bill_id' => $bill->id,
              'company_id' => $company->id,
              'year' => $year,
              'month' => $month,
              'item_number' => $data['numeroLinea'],
              'code' => $data['codigoProducto'],
              'name' => $data['detalleProducto'],
              'product_type' => 1,
              'measure_unit' => $data['unidadMedicion'],
              'item_count' => $data['cantidad'],
              'unit_price' => $data['precioUnitario'],
              'subtotal' => $data['subtotalLinea'],
              'total' => $data['totalLinea'],
              'discount_type' => '01',
              'discount' => $data['montoDescuento'],
              'iva_type' => $data['codigoEtax'],
              'iva_amount' => $data['montoIva'],
              'exoneration_document_type' => $data['tipoDocumentoExoneracion'],
              'exoneration_document_number' => $data['documentoExoneracion'],
              'exoneration_company_name' => $data['companiaExoneracion'],
              'exoneration_porcent' => $data['porcentajeExoneracion'],
              'exoneration_amount' => $data['montoExoneracion'],
              'impuesto_neto' => $data['impuestoNeto'],
              'exoneration_total_amount' => $data['totalMontoLinea']
          ];
      }
      
      clearBillCache($bill);
      
      if( $data['totalNeto'] != 0 ) {
        $bill->subtotal = $data['totalNeto'];
      }
      
      $bill->save();
      return $insert;
      
    }
    
    public function calculateAcceptFields() {
        
      if( !$this->xml_schema ){
        $this->xml_schema = $this->commercial_activity ? 43 : 42;
        $this->is_code_validated = false;
      }
      
      if( $this->is_code_validated ) {
        
        if( $this->xml_schema == 43 ) {
          $company = currentCompanyModel();
          $prorrataOperativa = $company->getProrrataOperativa( $this->year );
          $calc = new CalculatedTax();
          $calc->resetVars();
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
          
          $bienesCapital = $calc->bB011 + $calc->bB031 + $calc->bB051 + $calc->bB071 + $calc->bB015  + $calc->bB035 +
             $calc->bB012 + $calc->bB032 + $calc->bB052 + $calc->bB072 +
             $calc->bB013 + $calc->bB033 + $calc->bB053 + $calc->bB073 + $calc->bB016 + $calc->bB036 +
             $calc->bB014 + $calc->bB034 + $calc->bB054 + $calc->bB074
             +
             $calc->bS011 + $calc->bS031 + $calc->bS051 + $calc->bS071 + $calc->bS015  + $calc->bS035 +
             $calc->bS012 + $calc->bS032 + $calc->bS052 + $calc->bS072 +
             $calc->bS013 + $calc->bS033 + $calc->bS053 + $calc->bS073 + $calc->bS016 + $calc->bS036 +
             $calc->bS014 + $calc->bS034 + $calc->bS054 + $calc->bS074;
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
