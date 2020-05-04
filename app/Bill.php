<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\BillItem;
use App\Provider;
use App\XmlHacienda;
use App\HaciendaResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Orchestra\Parser\Xml\Facade as XmlParser;

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
    
    public function quickbooksBill()
    {
        return $this->hasOne(QuickbooksBill::class);
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
    //Relacion con la respuesta de Hacienda
    public function haciendaResponse()
    {
        return $this->hasOne(haciendaResponse::class);
    }
    
    public function providerName() {
      if( isset($this->provider_first_name)) {
        return "$this->provider_first_name $this->provider_last_name $this->provider_last_name2";
      }else {
        if( isset($this->provider_id) ) {
          return $this->provider->getFullName();
        }else{
          return 'N/A';
        }
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
    
    public function getCondicionVenta() {
      
      $str = "Contado";
      if( $this->sale_condition == '02' ) {
        $str = "Crédito";
      } else if( $this->sale_condition == '03' ) {
        $str = "Consignación";
      } else if( $this->sale_condition == '04' ) {
        $str = "Apartado";
      } else if( $this->sale_condition == '05' ) {
        $str = "Arrendamiento con opción de compra";
      } else if( $this->sale_condition == '06' ) {
         $str == "Arrendamiento en función financiera";
      } else if( $this->sale_condition == '99' ) {
        $str = "Otros";
      }
      return $str;
      
    }
    
    public function getMetodoPago() {
      
      $str = "Efectivo";
      if( $this->payment_type == '02' ) {
        $str = "Tarjeta";
      } else if( $this->payment_type == '03' ) {
        $str = "Cheque";
      } else if( $this->payment_type == '04' ) {
        $str = "Transferencia-Depósito Bancario";
      } else if( $this->payment_type == '05' ) {
        $str = "Recaudado por terceros";
      } else if( $this->payment_type == '99' ) {
        $str = "Otros";
      }
      return $str;
      
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
          
          if( $exonerationDate && isset($data['exoneration_document_type']) && isset($data['exoneration_document_number']) ) {
            
            $item->exoneration_document_type = $data['exoneration_document_type'] ?? null;
            $item->exoneration_document_number = $data['exoneration_document_number'] ?? null;
            $item->exoneration_company_name = $data['exoneration_company_name'] ?? null;
            $item->exoneration_porcent = $data['exoneration_porcent'] ?? 0;
            $item->exoneration_amount = $data['montoExoneracion'] ?? 0;
            $item->exoneration_date = $exonerationDate;
            $item->exoneration_total_amount = $data['exoneration_total_amount'] ?? 0;
            $item->impuesto_neto = isset($data['impuesto_neto']) ? $data['iva_amount'] - $data['montoExoneracion'] : $data['iva_amount'];

          } 
          
          $item->save();
          return $item;
      } else {
          return false;
      }
    }
    
    
    public static function saveBillXML( $arr, $metodoGeneracion, $emailRecibido = null ) {
        //Log::debug(json_encode($arr['ResumenFactura']['TotalOtrosCargos']));
        //dd($arr);
        $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0;
        if($metodoGeneracion != "Email" && $metodoGeneracion != 'GS' ){
          $company = currentCompanyModel();
          $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : $company->id_number;
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
        
        if( $bill->id ) {
          //Log::warning( "XML: No se pudo guardar la factura de compra. Ya existe para la empresa." );
          return false;
        }
        
        $bill->commercial_activity = $arr['CodigoActividad'] ?? 0;
        $bill->xml_schema = $bill->commercial_activity ? 43 : 42;
        $bill->sale_condition = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        $bill->credit_time = null;

        $medioPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '01';
        
        try{
          if( !isset($emailRecibido) ){
            $emailRecibido = array_key_exists('Receptor', $arr) ? $arr['Receptor']['CorreoElectronico'] : null;
          }
          $bill->email_reception = $emailRecibido;
          //Si es Corbana, va a poner la sucursal a la que recibe la factura. Varia dependiendo del email de recepcion
          $bill->setRegionCorbana($emailRecibido);
        }catch(\Exception $e){
          Log::warning( "Error al registrar correo receptor: " . $e );
        }

        if ( is_array($medioPago) ) {
          $medioPago = $medioPago[0];
        }
        $bill->payment_type = $medioPago;
        
        //Fechas
        $fechaEmision = Carbon::createFromFormat('Y-m-d', substr($arr['FechaEmision'], 0, 10));
        $bill->generated_date = $fechaEmision;
        $bill->due_date = $fechaEmision;
        
        $month = $fechaEmision->month;
        $year = $fechaEmision->year;
        $bill->month = $month;
        $bill->year = $year;
        
        if( array_key_exists( 'CodigoTipoMoneda', $arr['ResumenFactura'] ) ) {
          $idMoneda = $arr['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'] ?? 'CRC';
          $tipoCambio = $arr['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ?? 1;
        }else {
          $idMoneda = $arr['ResumenFactura']['CodigoMoneda'] ?? 'CRC';
          $tipoCambio = array_key_exists('TipoCambio', $arr['ResumenFactura']) ? $arr['ResumenFactura']['TipoCambio'] : '1';
        }
        if($idMoneda == 'CRC'){ $tipoCambio = 1; }
        $bill->currency = $idMoneda;
        $bill->currency_rate = $tipoCambio;
        
        $bill->description = 'XML Importado';

        if(strlen($arr['Clave']) == 50){
            $tipoDocumento = substr($arr['Clave'], 29, 2);
        }
        $bill->document_type = $tipoDocumento ?? '01';
        $bill->total = $arr['ResumenFactura']['TotalComprobante'];
        
        
        $authorize = true;
        if( ($metodoGeneracion == "Email" && !$company->auto_accept_email)|| $metodoGeneracion == "XML-A" ) {
            $authorize = false;
        }
        
        $bill->accept_status = 0; 
        $bill->hacienda_status = "03";
        $bill->payment_status = "01";
        $bill->generation_method = $metodoGeneracion;
        $bill->is_authorized = $authorize;
        $bill->is_code_validated = false;
        
        if( $metodoGeneracion == "Email" || $metodoGeneracion == "XML" ) {
            $bill->accept_status = 0; 
            $bill->hacienda_status = "01";
        }
        
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
                try{
                  if(is_array($arr['Emisor']['Ubicacion']['OtrasSenas'])){
                      $otrasSenas = null;
                  }else{
                      $otrasSenas = $arr['Emisor']['Ubicacion']['OtrasSenas'] ?? null;
                  }
                }catch(\Exception $e){ $otrasSenas = null; }
                
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
              
              try{
                if ( isset($arr['Emisor']['Telefono']) ) {
                  $telefonoProveedor = $arr['Emisor']['Telefono']['NumTelefono'] ?? null;
                }else{
                  $telefonoProveedor = null;
                }
              }catch(\Throwable $e){}
              
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
        try{
          $lineas = $arr['DetalleServicio']['LineaDetalle'];
          if( array_key_exists( 'NumeroLinea', $lineas ) ) {
              $lineas = [$arr['DetalleServicio']['LineaDetalle']];
          }
        }catch(\Exception $e){
          Log::error("Error en XML: " . $e->getMessage());
          Log::error("Error en XML, detalle: " . json_encode($arr));
          $lineas = [];
        }
        
        $bill->save();
        
        $lids = array();
        $items = array();
        $numeroLinea = 0;

        foreach( $lineas as $linea ) {
            $numeroLinea++;
            try {
              $codigoProducto = array_key_exists('Codigo', $linea) ? ($linea['Codigo']['Codigo'] ?? '') : '';
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
              try{
                $montoIva = trim($linea['Impuesto']['Monto'] );
                $porcentajeIva = trim($linea['Impuesto']['Tarifa'] );
  
                if( isset( $linea['Impuesto']['Exoneracion'] ) ) {
                  $tipoDocumentoExoneracion = $linea['Impuesto']['Exoneracion']['TipoDocumento'] ?? null;
                  $documentoExoneracion = $linea['Impuesto']['Exoneracion']['NumeroDocumento']  ?? null;
                  $companiaExoneracion = $linea['Impuesto']['Exoneracion']['NombreInstitucion'] ?? null;
                  $fechaExoneracion = $linea['Impuesto']['Exoneracion']['FechaEmision'] ?? null;
                  if($fechaExoneracion){
                    $fechaExoneracion = Carbon::createFromFormat('Y-m-d', substr($fechaExoneracion, 0, 10));
                    $fechaExoneracion = $fechaExoneracion->day."/".$fechaExoneracion->month."/".$fechaExoneracion->year;
                  }
                  $porcentajeExoneracion = $linea['Impuesto']['Exoneracion']['PorcentajeExoneracion'] ?? 0;
                  $montoExoneracion = $linea['Impuesto']['Exoneracion']['MontoExoneracion'] ?? 0;
                  if( $montoExoneracion ){
                    $montoIva = $montoIva - $montoExoneracion;
                  }
                }
              }catch(\Exception $e){
                if( is_array($linea['Impuesto'])){
                  $montoIva = 0;
                  $porcentajeIva = 0;

                  foreach ($linea['Impuesto'] as $imp){
                    if( trim($imp['Codigo']) == '01' || trim($imp['Codigo']) == 1 ){
                      $montoIva += (float)trim($imp['Monto'] );
                      $porcentajeIva += (float)trim($imp['Tarifa'] );
                    }else{
                      $subtotalLinea = $subtotalLinea + (float)trim($imp['Monto'] );
                    }
                  }
                }
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
              'montoExoneracion' => $montoExoneracion,
              'exoneration_total_amount' => $totalMontoLinea,
              'is_exempt' => false,
              'porc_identificacion_plena' => 13,
            );
            
            try{
              $item_modificado = $bill->addEditItem($item);
              array_push( $lids, $item_modificado->id );
            }catch(\Throwable $e){
              Log::error($e);
            }
        }
        
        foreach ( $bill->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
        $totalIvaDevuelto = 0;
        foreach ($bill->items as $item) {
            if ($bill->payment_type == '02' && $item->product_type == 12) {
                $totalIvaDevuelto += $item->iva_amount;
            }
        }
        
        $bill->total_iva_devuelto = $arr['ResumenFactura']['TotalIVADevuelto'] ?? 0;
        $bill->total_serv_gravados = $arr['ResumenFactura']['TotalServGravados'] ?? 0;
        $bill->total_serv_exentos = $arr['ResumenFactura']['TotalServExentos'] ?? 0;
        $bill->total_merc_gravados = $arr['ResumenFactura']['TotalMercanciasGravadas'] ?? 0;
        $bill->total_merc_exentas = $arr['ResumenFactura']['TotalMercanciasExentas'] ?? 0;
        $bill->total_exento = $arr['ResumenFactura']['TotalExento'] ?? 0;
        $bill->total_descuento = $arr['ResumenFactura']['TotalDescuentos'] ?? 0;
        $bill->total_venta_neta = $arr['ResumenFactura']['TotalVentaNeta'] ?? 0;
        $bill->total_iva = $arr['ResumenFactura']['TotalImpuesto'] ?? 0;
        $bill->total_venta = $arr['ResumenFactura']['TotalVenta'] ?? 0;
        $bill->total_comprobante = $arr['ResumenFactura']['TotalComprobante'] ?? 0;
        $bill->total_otros_cargos = $arr['ResumenFactura']['TotalOtrosCargos'] ?? 0;

        $bill->total_serv_exonerados = $arr['ResumenFactura']['TotalServExonerado'] ?? 0;
        $bill->total_merc_exonerados = $arr['ResumenFactura']['TotalMercExonerada'] ?? 0;
        $bill->total_exonerados = $arr['ResumenFactura']['TotalExonerado'] ?? 0;
        $bill->total_gravado = $arr['ResumenFactura']['TotalGravado'] ?? 0;
        $bill->save();
        
        return $bill;
    }
    
    
    public static function storeXML($bill, $file) {
        
        try{
          $cedulaEmpresa = $bill->company->id_number;
          //$cedulaProveedor = $bill->provider->id_number;
          $consecutivoComprobante = $bill->document_number;
          
          if ( Storage::exists("empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.xml")) {
              Storage::delete("empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.xml");
          }
          
          try{ //Intenta primero guardar el archivo como tipo File, si viene en content entra al try/catch y lo guarda como tipo stream de contenido
            $path = \Storage::putFileAs(
                "empresa-$cedulaEmpresa/facturas_compras", $file, "$bill->year/$bill->month/$consecutivoComprobante.xml"
            );
          }catch(\Throwable $e){
            $put = \Storage::put(
                "empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.xml", $file
            );
            if($put){
              $path = "empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.xml";
            }
          }
        
          $xmlHacienda = new XmlHacienda();
          $xmlHacienda->xml = $path;
          $xmlHacienda->bill_id = $bill->id;
          $xmlHacienda->invoice_id = 0;
          $xmlHacienda->save();
        }catch( \Throwable $e ){
          Log::error( 'Error al registrar en tabla XMLHacienda: ' . $e->getMessage() );
        }
        
        return $path;
        
    }
    
    
    public static function storePDF($bill, $file) {
        
        try{
          $cedulaEmpresa = $bill->company->id_number;
          //$cedulaProveedor = $bill->provider->id_number;
          $consecutivoComprobante = $bill->document_number;
          
          if ( Storage::exists("empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.pdf")) {
              Storage::delete("empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.pdf");
          }
          
          try{ //Intenta primero guardar el archivo como tipo File, si viene en content entra al try/catch y lo guarda como tipo stream de contenido
            $path = \Storage::putFileAs(
                "empresa-$cedulaEmpresa/facturas_compras", $file, "$bill->year/$bill->month/$consecutivoComprobante.pdf"
            );
          }catch(\Throwable $e){
            $put = \Storage::put(
                "empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.pdf", $file
            );
            if($put){
              $path = "empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/$consecutivoComprobante.pdf";
            }
          }
          
        }catch( \Throwable $e ){
          Log::error( 'Error al guardar el PDF recibido: ' . $e->getMessage() );
        }
        
        return $path;
        
    }
    
    
    public static function storeXMLMessage($bill, $file) {
        
        $path = "";
        try{
          $cedulaEmpresa = $bill->company->id_number;
          //$cedulaProveedor = $bill->provider->id_number;
          $consecutivoComprobante = $bill->document_key;
          
          if ( Storage::exists("empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/mensaje-$consecutivoComprobante.xml")) {
              Storage::delete("empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/mensaje-$consecutivoComprobante.xml");
          }
          
          
          try{ //Intenta primero guardar el archivo como tipo File, si viene en content entra al try/catch y lo guarda como tipo stream de contenido
            $path = \Storage::putFileAs(
                "empresa-$cedulaEmpresa/facturas_compras", $file, "$bill->year/$bill->month/mensaje-$consecutivoComprobante.xml"
            );
          }catch(\Throwable $e){
            $put = \Storage::put(
                "empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/mensaje-$consecutivoComprobante.xml", $file
            );
            if($put){
              $path = "empresa-$cedulaEmpresa/facturas_compras/$bill->year/$bill->month/mensaje-$consecutivoComprobante.xml";
            }
          }
          
        }catch( \Throwable $e ){
          Log::error( 'Error al guardar el MENSAJE recibido: ' . $e );
        }
        
        return $path;
        
    }
    
    public static function processMessageXML($file, $isStream = false) {
        
        try{
          if($isStream){
            $xml = simplexml_load_string( ($file) );
          }else{
            $xml = simplexml_load_string( file_get_contents($file) );
          }
          $json = json_encode( $xml ); // convert the XML string to JSON
          $xmlData = json_decode( $json, TRUE );
          
          $consecutivoComprobante = $xmlData['NumeroConsecutivo'] ?? null; //La respuesta no debe contener el cambo de numero consecutivo
          $mensaje = $xmlData['Mensaje'] ?? null; //Asegura que existe el mensaje, si no no es un XML correcto de aceptacion

          $path = "";
          if( !isset($consecutivoComprobante) && isset($mensaje) ){
            $clave = $xmlData['Clave'] ?? null;
            $nombreEmisor = $xmlData['NombreEmisor'] ?? null;
            $tipoIdentificacionEmisor = $xmlData['TipoIdentificacionEmisor'] ?? null;
            $numeroCedulaEmisor = $xmlData['NumeroCedulaEmisor'] ?? null;
            $nombreReceptor = $xmlData['NombreReceptor'] ?? null;
            $tipoIdentificacionReceptor = $xmlData['TipoIdentificacionReceptor'] ?? null;
            $numeroCedulaReceptor = $xmlData['NumeroCedulaReceptor'] ?? null;
            $detalleMensaje = $xmlData['DetalleMensaje'] ?? null;
            $montoTotalImpuesto = $xmlData['MontoTotalImpuesto'] ?? null;
            $totalFactura = $xmlData['TotalFactura'] ?? null;
            
            if(is_array($detalleMensaje)){
              $detalleMensaje = json_encode($detalleMensaje);
            }

            $bills = Bill::where('document_key', $clave)->get();
            //Guarda el haciendaResponse segun lo contenido en el XML
            foreach($bills as $bill){
              $path = Bill::storeXMLMessage($bill, $file);
              $haciendaResponse = HaciendaResponse::updateOrCreate(
                [
                  'bill_id' => $bill->id
                ],
                [
                  'clave' => $clave,
                  'nombre_emisor' => $nombreEmisor,
                  'tipo_identificacion_emisor' => $tipoIdentificacionEmisor,
                  'numero_cedula_emisor' => $numeroCedulaEmisor,
                  'nombre_receptor' => $nombreReceptor,
                  'tipo_identificacion_receptor' => $tipoIdentificacionReceptor,
                  'numero_cedula_receptor' => $numeroCedulaReceptor,
                  'mensaje' => $mensaje,
                  'detalle_mensaje' => $detalleMensaje,
                  'monto_total_impuesto' => $montoTotalImpuesto,
                  'total_factura' => $totalFactura,
                  's3url' => $path
                ]
              );
              
              $xmlHacienda = $bill->xmlHacienda;
              if( isset($xmlHacienda) ){
                $xmlHacienda->xml_message = $path;
                $xmlHacienda->save();
              }
            }
          }
          
        }catch( \Throwable $e ){
          Log::error( 'Error al procesar el MENSAJE HACIENDA recibido: ' . $e );
        }
        
        return $path;
        
    }

    public static function storeXMLError($cedulaEmpresa, $file) {
        
        try{
          
          if ( Storage::exists("empresa-$cedulaEmpresa/facturas_compras/error/email/$file->getClientOriginalName()")) {
              Storage::delete("empresa-$cedulaEmpresa/facturas_compras/error/email/$file->getClientOriginalName()");
          }
          
          $path = \Storage::putFileAs(
              "empresa-$cedulaEmpresa/facturas_compras", $file, "error/email/$file->getClientOriginalName()"
          );
        }catch( \Throwable $e ){
          Log::error( 'Error al guardar xml de error: ' . $e->getMessage() );
        }
        
        return $path;
        
    }
    
    
    public static function importBillRow ( $data, $billList, $company = false ) {
      if(!$company){
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
      
      $arrayKey = "import-factura-" . $data['claveFactura'] . $company->id . "-" . $data['consecutivoComprobante'];
      if ( !isset($billList[$arrayKey]) ) {
      
          $bill = Bill::firstOrNew(
              [
                  'company_id' => $company->id,
                  'provider_id' => $proveedor->id,
                  'document_number' => $data['consecutivoComprobante'],
                  'document_key' => $data['claveFactura'],
              ]
          );
              
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
          $bill->activity_company_verification =  $data['codigoActividad'] ?? '0';
          
          //Datos generales
          $bill->sale_condition = $data['condicionVenta'];
          $bill->payment_type = $data['metodoPago'];
          $bill->credit_time = 0;
          $bill->description = $data['descripcion'];

          $bill->generation_method = $data['metodoGeneracion'];
          $bill->is_authorized = $data['isAuthorized'];


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
            //$bill->accept_status = 1; //17/02/2020. Esto se cambia para que por defecto queden como aceptadas
            $bill->hacienda_status = "01";
          }else{
            if( $data['acceptStatus'] ){
              $bill->accept_status = 1;
              $bill->hacienda_status = "03";
              $bill->is_authorized = 1;
            }
          }

          //revisar si esta validada.
          $bill->is_void = false;     

          $bill->is_authorized = $data['isAuthorized'];
          $bill->currency_rate = $data['tipoCambio'] ?? 1;
          //Datos de factura
          $bill->currency = $data['moneda'] ?? 'CRC';
          if( $bill->currency == 1 ) { $bill->currency = "CRC"; }
          if( $bill->currency == 2 ) { $bill->currency = "USD"; }
          if($bill->currency == 'CRC'){
            $bill->currency_rate = 1;
          }
          $bill->commercial_activity =  $data['codigoActividad'] ?? '0';
          if( $data['acceptStatus'] ){
            $bill->accept_status = 1;
            $bill->hacienda_status = "03";
          }

          //$bill->description = $row['description'] ? $row['description'] : '';
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

          $company->last_bill_ref_number = $bill->reference_number;
          
          $bill->subtotal = 0;
          $bill->iva_amount = 0;

          /*if(!$bill->id){
            $bill->save();
          }
          $company->save();*/
             
          $billList[$arrayKey]['lineas'] = array();
          $billList[$arrayKey]['factura'] = $bill;
      }
      
      $bill = $billList[$arrayKey]['factura'];
      $year = $bill->generatedDate()->year;
      $month = $bill->generatedDate()->month;

      /**LINEA DE FACTURA**/
      $bill->total = $data['totalDocumento'] ?? 0;
      $subtotalLinea = $data['subtotalLinea'] ?? 0;
      $montoIvaLinea = $data['montoIva'] ?? 0;
      $totalLinea = $data['totalLinea'] ?? 0;
      $precioUnitarioLinea = $data['precioUnitario'] ?? 0;
      $montoDescuentoLinea = $data['montoDescuento'] ?? 0;
      $cantidadLinea = $data['cantidad'] ?? 0;
      $bill->subtotal = $bill->subtotal + $subtotalLinea;
      $bill->iva_amount = $bill->iva_amount + $montoIvaLinea;
      
      $item = [
          'bill_id' => $bill->id,
          'company_id' => $company->id,
          'year' => $year,
          'month' => $month,
          'item_number' => $data['numeroLinea'],
          'code' => $data['codigoProducto'] ?? 'N/A',
          'name' => $data['detalleProducto'] ?? 'No indica',
          'product_type' => $data['categoriaHacienda'] ?? null,
          'porc_identificacion_plena' => $data['identificacionEspecifica'] ?? 13,
          'measure_unit' => $data['unidadMedicion'],
          'item_count' => $cantidadLinea,
          'unit_price' => $precioUnitarioLinea,
          'subtotal' => $subtotalLinea,
          'total' => $totalLinea,
          'discount_type' => '01',
          'discount' => $montoDescuentoLinea,
          'iva_type' => $data['codigoEtax'],
          'iva_amount' => $montoIvaLinea,
          'is_code_validated' => true,
          'exoneration_document_type' => $data['tipoDocumentoExoneracion'],
          'exoneration_document_number' => $data['documentoExoneracion'],
          'exoneration_company_name' => $data['companiaExoneracion'],
          'exoneration_porcent' => $data['porcentajeExoneracion'],
          'exoneration_amount' => $data['montoExoneracion'],
          'impuesto_neto' => $data['impuestoNeto'],
          'exoneration_total_amount' => $data['totalMontoLinea']
      ];
      
      array_push($billList[$arrayKey]['lineas'], $item);
      
      if( $data['totalNeto'] != 0 ) {
        $bill->subtotal = $data['totalNeto'];
      }

      if(isset($data['codeValidated'])){
        $bill->is_code_validated = $data['codeValidated'];
      }
      
      return $billList;
      
    }
    
    public function calculateAcceptFields($company = false) {
        
      if( !$this->xml_schema ){
        $this->xml_schema = $this->commercial_activity ? 43 : 42;
        $this->is_code_validated = false;
      }
      
      $cacheKey = "cache-billaccepts-".$this->id;
      if ( Cache::has($cacheKey) ) {
        return Cache::get($cacheKey);
      }
      
      if( $this->is_code_validated ) {
        if( $this->xml_schema == 43 ) {
          if(!$company){
            $company = currentCompanyModel();
          }
          $prorrataOperativa = $company->getProrrataOperativa( $this->year );
          $calc = new CalculatedTax();
          $calc->year = $this->year;
          $calc->month = $this->month;
          $calc->resetVars();
          $lastBalance = 0;
          $query = BillItem::with('bill')->where('bill_id', $this->id);
          //$calc->setDatosEmitidos( $this->month, $this->year, $company->id );
          $calc->setDatosSoportados( $this->month, $this->year, $company->id, $query, true );
          $calc->setCalculosPorFactura( $prorrataOperativa, $lastBalance );

          $this->accept_iva_acreditable = round($calc->iva_deducible_operativo, 5);
          $this->accept_iva_gasto = round($calc->iva_no_deducible, 5);
          $this->accept_iva_total = round($calc->total_bill_iva, 5);
          $this->accept_total_factura = round($calc->bills_total, 5);
          $this->accept_id_number = $company->id_number;
          
          if( $calc->iva_acreditable_identificacion_plena > 0) {
            $this->accept_iva_condition = '02';
            if( $this->accept_iva_total == 0 ||
                $this->accept_iva_gasto == 0 ) {
              $this->accept_iva_condition = '01'; //Si no hay IVA o si todo lo pagado de IVA es acreditable
            }
          }else{
            $this->accept_iva_condition = '05'; //Prorratea
          }
          
          if( $this->accept_iva_acreditable == 0) {
            $this->accept_iva_condition = '04'; //Si todo lo pagado de IVA va al gasto
          }
          
          $ivaData = json_decode( $calc->iva_data ) ?? new \stdClass();
          $cc_propiedades1 = $ivaData->bB011 + $ivaData->bB031 + $ivaData->bB051 + $ivaData->bB071 + $ivaData->bB015  + $ivaData->bB035;
          $cc_propiedades2 = $ivaData->bB012 + $ivaData->bB032 + $ivaData->bB052 + $ivaData->bB072;
          $cc_propiedades3 = $ivaData->bB013 + $ivaData->bB033 + $ivaData->bB053 + $ivaData->bB073 + $ivaData->bB016  + $ivaData->bB036;
          $cc_propiedades4 = $ivaData->bB014 + $ivaData->bB034 + $ivaData->bB054 + $ivaData->bB074;
          $bienesCapital = $cc_propiedades1 + $cc_propiedades2 + $cc_propiedades3 + $cc_propiedades4;
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
      Cache::put($cacheKey, $this, now()->addDays(90));
      
      return $this;
        
    }
      
      
    public function setRegionCorbana($email){
      
      $email = strtolower($email);
      //Log::debug("Regiones Corbana: $email");
      $this->sucursal = null;
      if($email == "facturaelectronica@corbana.co.cr" || strpos($email, "facturaelectronica@corbana.co.cr") !== false || strpos($email, "facturaelectronica2@corbana.co.cr") !== false){
        $this->sucursal = "01";
        $this->email_reception = "facturaelectronica@corbana.co.cr" ;
      }
      if($email == "cajachica@corbana.co.cr" || strpos($email, "cajachica@corbana.co.cr") !== false || strpos($email, "cajachica2@corbana.co.cr") !== false){
        $this->sucursal = "01";  
        $this->email_reception = "cajachica@corbana.co.cr";
      }
      if($email == "corbanaguapiles@corbana.co.cr" || strpos($email, "corbanaguapiles@corbana.co.cr") !== false || strpos($email, "corbanaguapiles2@corbana.co.cr") !== false){
        $this->sucursal = "02";  
        $this->email_reception = "corbanaguapiles@corbana.co.cr";
      }
      if($email == "fincasanpablo@corbana.co.cr" || strpos($email, "fincasanpablo@corbana.co.cr") !== false || strpos($email, "fincasanpablo2@corbana.co.cr") !== false){
        $this->sucursal = "04";  
        $this->email_reception = "fincasanpablo@corbana.co.cr";
      }
      if($email == "agroforestales@corbana.co.cr" || strpos($email, "agroforestales@corbana.co.cr") !== false || strpos($email, "agroforestales2@corbana.co.cr") !== false){
        $this->sucursal = "05";  
        $this->email_reception = "agroforestales@corbana.co.cr";
      }
      
    }
    
    
    
}
