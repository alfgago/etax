<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\InvoiceItem;
use App\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    //Relacion con hacienda
    public function xmlHacienda()
    {
        return $this->belongsTo(XmlHacienda::class);
    }
    
    
    /**
    * Asigna los datos de la factura segun el request recibido
    **/
    public function setInvoiceData($request)
    {
        try {
            $this->document_key = $request->document_key;
            $this->document_number = $request->document_number;
            $this->sale_condition = $request->sale_condition;
            $this->payment_type = $request->payment_type;
            $this->retention_percent = $request->retention_percent;
            $this->credit_time = $request->credit_time;
            $this->buy_order = $request->buy_order;
            $this->other_reference = $request->other_reference;

            //Datos de cliente. El cliente nuevo viene con ID = -1
            if( $request->client_id == '-1' ) {
                $tipo_persona = $request->tipo_persona;
                $identificacion_cliente = $request->id_number;
                $codigo_cliente = $request->code;

                $cliente = Client::updateOrCreate(
                    [
                        'id_number' => $identificacion_cliente,
                        'company_id' => $this->company_id,
                    ],
                    [
                        'code' => $codigo_cliente ,
                        'company_id' => $this->company_id,
                        'tipo_persona' => $tipo_persona,
                        'id_number' => $identificacion_cliente,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'last_name2' => $request->last_name2,
                        'fullname' => $request->first_name.' '.$request->last_name.' '.$request->last_name2,
                        'emisor_receptor' => 'ambos',
                        'country' => $request->country,
                        'state' => $request->state,
                        'city' => $request->city,
                        'district' => $request->district,
                        'neighborhood' => $request->neighborhood,
                        'zip' => $request->zip,
                        'address' => $request->address,
                        'phone' => $request->phone,
                        'es_exento' => $request->es_exento,
                        'email' => $request->email,
                        'billing_emails' => $request->billing_emails ?? $request->email
                    ]
                );
                $this->client_id = $cliente->id;
            }else{
                $this->client_id = $request->client_id;
            }

            $client = Client::find($this->client_id);
            $request->currency_rate = $request->currency_rate ? $request->currency_rate : 1;
            //Datos de factura
            $this->description = $request->description;
            $this->subtotal = floatval( str_replace(",","", $request->subtotal ));
            $this->currency = $request->currency;
            $this->currency_rate = floatval( str_replace(",","", $request->currency_rate ));
            $this->total = floatval( str_replace(",","", $request->total ));
            $this->iva_amount = floatval( str_replace(",","", $request->iva_amount ));
            $this->client_first_name = $client->first_name;
            $this->client_last_name = $client->last_name;
            $this->client_last_name2 = $client->last_name2;
            $this->client_email = $client->email;
            $this->client_address = $client->address;
            $this->client_country = $client->country;
            $this->client_state = $client->state;
            $this->client_city = $client->city;
            $this->client_district = $client->district;
            $this->client_zip = $client->zip;
            $this->client_phone = $client->phone;
            $this->client_id_number = $client->id_number;
            //Fechas
            $fecha = Carbon::createFromFormat('d/m/Y g:i A',
                $request->generated_date . ' ' . $request->hora);
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

        } catch (\Exception $e) {
            Log::error('Error al crear factura: '.$e->getMessage());
            return back()->withError('Ha ocurrido un error al registrar la factura' . $e->getMessage());
        }
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
  
    public function addEditItem(array $data)
    {
      if(isset($data['item_number'])) {
          $item = InvoiceItem::updateOrCreate([
              'item_number' => $data['item_number'],
              'invoice_id' => $this->id,
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
                  'is_identificacion_especifica' =>  $data['is_identificacion_especifica'] ?? ''
              ]
          );
          return $item;
      } else {
          return false;
      }
    }
    
    public static function importInvoiceRow (
        $metodoGeneracion, $idEmisor, $nombreCliente, $codigoCliente, $tipoPersona, $identificacionCliente, $correoCliente, $telefonoCliente,
        $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
        $idMoneda, $tipoCambio, $totalDocumento, $totalNeto, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
        $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $isAuthorized, $codeValidated
    ) {
      
      //Revisa si el método es por correo electrónico. De ser así, usa busca la compañia por cedula.
      if( $metodoGeneracion != "Email" ){
        $company = currentCompanyModel();
      }else{
        //Si es email, busca por ID del receptor para encontrar la compañia
        $company = Company::where('id_number', $idEmisor)->first();
      }
      
      if( ! $company ) {
        return false;
      }
      
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
                  'document_number' => $consecutivoComprobante,
                  'document_key' => $claveFactura,
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
              $invoice->is_authorized = $isAuthorized;
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
              $company->save();
          }   
          Cache::put($invoiceCacheKey, $invoice, 30);
      }
      $invoice = Cache::get($invoiceCacheKey);
      
      try{
        $invoice->generated_date = Carbon::createFromFormat('d/m/Y', $fechaEmision);
      }catch( \Exception $ex ){
        $dt =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaEmision);
        $invoice->generated_date = Carbon::instance($dt);
      }
      
      try{
        $invoice->due_date = Carbon::createFromFormat('d/m/Y', $fechaVencimiento);
      }catch( \Exception $ex ){
        $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaVencimiento);
        $invoice->due_date = Carbon::instance($dt);
      }
      
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
      
      if( $totalNeto != 0 ) {
        $invoice->subtotal = $totalNeto;
      }
      
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
    
    
    public static function saveInvoiceXML( $arr, $metodoGeneracion ) {
        $inserts = array();
        
        $claveFactura = $arr['Clave'];
        $consecutivoComprobante = $arr['NumeroConsecutivo'];
        $fechaEmision = Carbon::createFromFormat('Y-m-d', substr($arr['FechaEmision'], 0, 10))->format('d/m/Y');
        $fechaVencimiento = $fechaEmision;
        $nombreProveedor = $arr['Emisor']['Nombre'];
        $codigoCliente = '';
        $tipoPersona = $arr['Emisor']['Identificacion']['Tipo'];
        $identificacionProveedor = $arr['Emisor']['Identificacion']['Numero'];
        $correoCliente = $arr['Receptor']['CorreoElectronico'];
        $telefonoCliente = $arr['Receptor']['Telefono']['NumTelefono'];
        $tipoPersona = $arr['Receptor']['Identificacion']['Tipo'];
        $identificacionCliente = $arr['Receptor']['Identificacion']['Numero'];
        $nombreCliente = $arr['Receptor']['Nombre'];
        $condicionVenta = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        $plazoCredito = array_key_exists('PlazoCredito', $arr) ? $arr['PlazoCredito'] : '';
        $metodoPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '';
        
        if ( is_array($metodoPago) ) {
          $metodoPago = $metodoPago[0];
        }
        
        $idMoneda = $arr['ResumenFactura']['CodigoMoneda'];
        $tipoCambio = $arr['ResumenFactura']['TipoCambio'];
        $totalDocumento = $arr['ResumenFactura']['TotalComprobante'];
        $totalNeto = $arr['ResumenFactura']['TotalVentaNeta'];
        $descripcion = $arr['ResumenFactura']['CodigoMoneda'];
        
        $authorize = true;
        if( $metodoGeneracion == "Email" || $metodoGeneracion == "XML-A" ) {
            $authorize = false;
        }
        
        $tipoDocumento = '01';
        
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
            $codigoEtax = '103'; //De momento asume que todo en 4.2 es al 13%.
            $montoIva = 0; //En 4.2 toma el IVA como en 0. A pesar de estar con cod. 103.
            
            $insert = Invoice::importInvoiceRow(
                $metodoGeneracion, $identificacionProveedor, $nombreCliente, $codigoCliente, $tipoPersona, $identificacionCliente, $correoCliente, $telefonoCliente,
                $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
                $idMoneda, $tipoCambio, $totalDocumento, $totalNeto, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
                $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $authorize, false
            );
            
            if( $insert ) {
                array_push( $inserts, $insert );
            }
        }
        
        InvoiceItem::insert($inserts);
        
        return true;
    }
    
    
    public static function storeXML($file, $consecutivoComprobante, $identificacionEmisor, $identificacionReceptor) {
        
        if ( Storage::exists("empresa-$identificacionEmisor/$identificacionReceptor-$consecutivoComprobante.xml")) {
            Storage::delete("empresa-$identificacionEmisor/$identificacionReceptor-$consecutivoComprobante.xml");
        }
        
        $path = \Storage::putFileAs(
            "empresa-$identificacionEmisor", $file, "$identificacionReceptor-$consecutivoComprobante.xml"
        );
        return $path;
    }  
}
