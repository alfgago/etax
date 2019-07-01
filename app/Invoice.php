<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\InvoiceItem;
use App\Client;
use App\XmlHacienda;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    use SoftDeletes;

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
    
    public function clientName() {
      if( isset($this->client_id) ) {
        return $this->client->getFullName();
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
        return $this->hasOne(XmlHacienda::class);
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
            $this->send_emails = $request->send_email ?? null;
            if( $request->commercial_activity ){
                $this->commercial_activity = $request->commercial_activity;
            }

            //Datos de cliente. El cliente nuevo viene con ID = -1
            if( $request->client_id == '-1' ) {

                $tipo_persona = $request->tipo_persona;
                $identificacion_cliente = preg_replace("/[^0-9]/", "", $request->id_number );
                $codigo_cliente = $request->code;

                $client = Client::updateOrCreate(
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
                        'foreign_address' => $request->address,
                        'phone' => $request->phone,
                        'es_exento' => $request->es_exento,
                        'email' => $request->email,
                        'billing_emails' => $request->billing_emails ?? $request->email
                    ]
                );

                $this->client_id = $client->id;
            }else{
                $this->client_id = $request->client_id;
                $client = Client::find($this->client_id);
            }

            $request->currency_rate = $request->currency_rate ? $request->currency_rate : 1;
            //Datos de factura
            $this->description = $request->description;
            $this->subtotal = floatval( str_replace(",","", $request->subtotal ));
            $this->currency = $request->currency;
            $this->currency_rate = floatval( str_replace(",","", $request->currency_rate ));
            $this->total = floatval( str_replace(",","", $request->total ));
            $this->iva_amount = floatval( str_replace(",","", $request->iva_amount ));

            if( isset( $client ) ) {
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
            }else{
              $this->client_first_name = 'N/A';
            }
            
            //Fechas
            $fecha = Carbon::createFromFormat('d/m/Y g:i A',
                $request->generated_date . ' ' . $request->hora);
            $this->generated_date = $fecha;
            $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
            $this->due_date = $fechaV;
            $this->year = $fecha->year;
            $this->month = $fecha->month;
            
            if( !$this->id ){
              $this->company->addSentInvoice( $this->year, $this->month );
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

        } catch (\Exception $e) {
            Log::error('Error al crear factura: '.$e->getMessage());
            return back()->withError('Ha ocurrido un error al registrar la factura' . $e->getMessage());
        }
    }
  
    public function addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, 
                             $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $is_exempt, $typeDocument,
                            $numeroDocumento, $nombreInstitucion, $porcentajeExoneracion, $montoExoneracion, $impuestoNeto, $montoTotalLinea)
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
        'is_identificacion_especifica' => $isIdentificacion,
        'exoneration_document_type' => $typeDocument ?? null,
        'exoneration_document_number' => $numeroDocumento ?? null,
        'exoneration_company_name' => $nombreInstitucion ?? null,
        'exoneration_porcent' => $porcentajeExoneracion ?? 0,
        'exoneration_amount' => $montoExoneracion ?? 0,
        'impuestoNeto' => $impuestoNeto ?? 0,
        'exoneration_total_amount' => $montoTotalLinea ?? 0
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
                  'is_identificacion_especifica' => $data['is_identificacion_especifica'] ?? '',
                  'exoneration_document_type' => $data['typeDocument'] ?? null,
                  'exoneration_document_number' => $data['numeroDocumento'] ?? null,
                  'exoneration_company_name' => $data['nombreInstitucion'] ?? null,
                  'exoneration_porcent' => $data['porcentajeExoneracion'] ?? 0,
                  'exoneration_amount' => $data['montoExoneracion'] ?? 0,
                  'exoneration_date' => !empty($data['exoneration_date']) ? Carbon::createFromFormat('d/m/Y', $data['exoneration_date']) : null,
                  'exoneration_total_amount' => $data['impuestoNeto'] ?? 0,
                  'impuesto_neto' => isset($data['montoExoneracion']) ? $data['iva_amount'] - $data['montoExoneracion']
                      : $data['iva_amount']
              ]
          );
          return $item;
      } else {
          return false;
      }
    }
    
    public static function importInvoiceRow ( $data
/*        $metodoGeneracion, $idEmisor, $nombreCliente, $codigoCliente, $tipoPersona, $identificacionCliente, $correoCliente, $telefonoCliente,
        $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
        $idMoneda, $tipoCambio, $totalDocumento, $totalNeto, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
        $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $isAuthorized, $codeValidated,
        $tipoDocumentoExoneracion, $documentoExoneracion, $companiaExoneracion, $porcentajeExoneracion, $montoExoneracion, $impuestoNeto, $totalMontoLinea*/
    ) {
      
      //Revisa si el método es por correo electrónico. De ser así, usa busca la compañia por cedula.
      if( $data['metodoGeneracion'] != "Email" ){
        $company = currentCompanyModel();
      }else{
        //Si es email, busca por ID del receptor para encontrar la compañia
        $company = Company::where('id_number', $data['idEmisor'])->first();
      }
      
      if( ! $company ) {
        return false;
      }
      
      $idCliente = 0;
      $identificacionCliente = preg_replace("/[^0-9]/", "", $data['identificacionCliente'] );
      if( $identificacionCliente ) {
        $clientCacheKey = "import-clientes-$identificacionCliente-".$company->id;
        if ( !Cache::has($clientCacheKey) ) {
            $clienteCache =  Client::firstOrCreate(
                [
                    'id_number' => $identificacionCliente,
                    'company_id' => $company->id,
                ],
                [
                    'code' => $data['codigoCliente'] ,
                    'company_id' => $company->id,
                    'tipo_persona' => str_pad($data['tipoPersona'], 2, '0', STR_PAD_LEFT),
                    'id_number' => $identificacionCliente,
                    'first_name' => $data['nombreCliente'],
                    'email' => $data['correoCliente'],
                    'phone' => $data['telefonoCliente'],
                    'fullname' => "$identificacionCliente - " . $data['nombreCliente']
                ]
            );
            $clienteCache->save();
            Cache::put($clientCacheKey, $clienteCache, 30);
        }
        $cliente = Cache::get($clientCacheKey);
        $idCliente = $cliente->id;
      } else {
        $tipoDocumento = '04';
      }
      
      $idCliente = preg_replace("/[^0-9]/", "", $idCliente );
      $invoiceCacheKey = "import-factura-" . $data['nombreCliente'] . $company->id . "-" . $data['consecutivoComprobante'];
      if ( !Cache::has($invoiceCacheKey) ) {
      
          $invoice = Invoice::firstOrNew(
              [
                  'company_id' => $company->id,
                  'client_id' => $idCliente,
                  'document_number' => $data['consecutivoComprobante'],
                  'document_key' => $data['claveFactura'],
              ]
          );
          
          if( !$invoice->exists ) {
              
              $invoice->company_id = $company->id;
              $invoice->client_id = $idCliente;    
      
              //Datos generales y para Hacienda
              if( $tipoDocumento == '01' || $tipoDocumento == '02' || $tipoDocumento == '03' || $tipoDocumento == '04' 
                  || $tipoDocumento == '05' || $tipoDocumento == '06' || $tipoDocumento == '07' || $tipoDocumento == '08' || $tipoDocumento == '99' ) {
                  $invoice->document_type = $tipoDocumento;    
              } else {
                 $invoice->document_type = '01'; 
              }

              $invoice->document_number =  $data['consecutivoComprobante'];
              
              //Datos generales
              $invoice->sale_condition = $data['condicionVenta'];
              $invoice->payment_type = $data['metodoPago'];
              $invoice->credit_time = 0;
              $invoice->description = $data['descripcion'];
              
              $invoice->generation_method = $data['metodoGeneracion'];
              $invoice->is_authorized = $data['isAuthorized'];
              $invoice->is_code_validated = $data['codeValidated'];
              $invoice->hacienda_status = "03";
              
              //Datos de factura
              $invoice->currency = $data['idMoneda'];
              if( $invoice->currency == 1 ) { $invoice->currency = "CRC"; }
              if( $invoice->currency == 2 ) { $invoice->currency = "USD"; }
              
              $invoice->currency_rate = $data['tipoCambio'];
              $invoice->subtotal = 0;
              $invoice->iva_amount = 0;
              $invoice->total = $data['totalDocumento'];
              
              $invoice->save();
          }   
          Cache::put($invoiceCacheKey, $invoice, 30);
      }
      $invoice = Cache::get($invoiceCacheKey);
      
      try{
        $invoice->generated_date = Carbon::createFromFormat('d/m/Y', $data['fechaEmision']);
      }catch( \Exception $ex ){
        $dt =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['fechaEmision']);
        $invoice->generated_date = Carbon::instance($dt);
      }
      
      try{
        $invoice->due_date = Carbon::createFromFormat('d/m/Y', $data['fechaVencimiento']);
      }catch( \Exception $ex ){
        $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['fechaVencimiento']);
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
              'item_number' => $data['numeroLinea'],
          ]
      );
      
      $insert = false;
      
      if( !$item->exists ) {
          $invoice->subtotal = $invoice->subtotal + $data['subtotalLinea'];
          $invoice->iva_amount = $invoice->iva_amount + $data['montoIva'];
          
          $discount_reason = "";
          
          $insert = [
              'invoice_id' => $invoice->id,
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
              'impuestoNeto' => $data['impuestoNeto'],
              'exoneration_total_amount' => $data['totalMontoLinea']
          ];
      }
      if( $invoice->year == 2018 ) {
         clearLastTaxesCache($company->id, 2018);
      }
      
      clearInvoiceCache($invoice);
      
      if( $data['totalNeto'] != 0 ) {
        $invoice->subtotal = $data['totalNeto'];
      }
      
        $invoice->save();

        $available_invoices = AvailableInvoices::where('company_id', $company->id)->first();
        $available_invoices->current_month_sent = $available_invoices->current_month_sent + 1;
        $available_invoices->save();
      
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
        $tipoPersona = $arr['Emisor']['Identificacion']['Tipo'];
        $identificacionProveedor = $arr['Emisor']['Identificacion']['Numero'];
        $codigoCliente = $identificacionProveedor;
        
        $tipoDocumento = '01';
        if ( array_key_exists('Receptor', $arr) ){
          $correoCliente = $arr['Receptor']['CorreoElectronico'];
          $telefonoCliente = isset($arr['Receptor']['Telefono']) ? $arr['Receptor']['Telefono']['NumTelefono'] : '';
          $tipoPersona = $arr['Receptor']['Identificacion']['Tipo'];
          $identificacionCliente = $arr['Receptor']['Identificacion']['Numero'];
          $nombreCliente = $arr['Receptor']['Nombre'];
        }else{
          $correoCliente = 'N/A';
          $telefonoCliente = 'N/A';
          $tipoPersona = 'N/A';
          $identificacionCliente = 0;
          $nombreCliente = 'N/A';
          $tipoDocumento = '04';
        }
        
        $condicionVenta = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        $plazoCredito = array_key_exists('PlazoCredito', $arr) ? $arr['PlazoCredito'] : '';
        $metodoPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '';
        
        if ( is_array($metodoPago) ) {
          $metodoPago = $metodoPago[0];
        }
        
        $idMoneda = $arr['ResumenFactura']['CodigoMoneda'];
        $tipoCambio = array_key_exists('TipoCambio', $arr['ResumenFactura']) ? $arr['ResumenFactura']['TipoCambio'] : '1';
        $totalDocumento = $arr['ResumenFactura']['TotalComprobante'];
        $totalNeto = $arr['ResumenFactura']['TotalVentaNeta'];
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
            $codigoEtax = '103'; //De momento asume que todo en 4.2 es al 13%.
            $montoIva = 0; //En 4.2 toma el IVA como en 0. A pesar de estar con cod. 103.

            $tipoDocumentoExoneracion = $linea['tipoDocumentoExoneracion'];
            $documentoExoneracion = $linea['documentoExoneracion'];
            $companiaExoneracion = $linea['companiaExoneracion'];
            $porcentajeExoneracion = $linea['porcentajeExoneracion'];
            $montoExoneracion = $linea['montoExoneracion'];
            $impuestoNeto = $linea['impuestoNeto'];
            $totalMontoLinea = $linea['totalMontoLinea'];

            $arrayInsert = array(
                'metodoGeneracion' => $metodoGeneracion,
                'identificacionProveedor' => $identificacionProveedor,
                'nombreCliente' => $nombreCliente,
                'codigoCliente' => $codigoCliente,
                'tipoPersona' => $tipoPersona,
                'identificacionCliente' => $identificacionCliente,
                'correoCliente' => $correoCliente,
                'telefonoCliente' => $telefonoCliente,
                'claveFactura' => $claveFactura,
                'consecutivoComprobante' => $consecutivoComprobante,
                'condicionVenta' => $condicionVenta,
                'metodoPago' => $metodoPago,
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
                'authorize' => $authorize,
                'codeValidated' => false,
                'tipoDocumentoExoneracion' => $tipoDocumentoExoneracion,
                'documentoExoneracion' => $documentoExoneracion,
                'companiaExoneracion' => $companiaExoneracion,
                'porcentajeExoneracion' => $porcentajeExoneracion,
                'montoExoneracion' => $montoExoneracion,
                'impuestoNeto' => $impuestoNeto,
                'totalMontoLinea' => $totalMontoLinea
            );

            $insert = Invoice::importInvoiceRow( $arrayInsert );
                /*$metodoGeneracion, $identificacionProveedor, $nombreCliente, $codigoCliente, $tipoPersona, $identificacionCliente, $correoCliente, $telefonoCliente,
                $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
                $idMoneda, $tipoCambio, $totalDocumento, $totalNeto, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
                $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $authorize, false,
                $tipoDocumentoExoneracion, $documentoExoneracion, $companiaExoneracion, $porcentajeExoneracion, $montoExoneracion, $impuestoNeto, $totalMontoLinea
            );*/
            
            if( $insert ) {
                array_push( $inserts, $insert );
            }
        }
        
        $items = InvoiceItem::insert($inserts);
        
        return $items;
    }
    
    
    public static function storeXML($invoice, $file) {
        $cedulaEmpresa = $invoice->company->id_number;
        $cedulaCliente = $invoice->client->id_number;
        $consecutivoComprobante = $invoice->document_number;
        
        if ( Storage::exists("empresa-$cedulaEmpresa/facturas_ventas/$cedulaCliente-$consecutivoComprobante.xml")) {
            Storage::delete("empresa-$cedulaEmpresa/facturas_ventas/$cedulaCliente-$consecutivoComprobante.xml");
        }
        
        $path = \Storage::putFileAs(
            "empresa-$cedulaEmpresa/facturas_ventas", $file, "$cedulaCliente-$consecutivoComprobante.xml"
        );
        
        try{
          $xmlHacienda = new XmlHacienda();
          $xmlHacienda->xml = $path;
          $xmlHacienda->bill_id = 0;
          $xmlHacienda->invoice_id = $invoice->id;
          $xmlHacienda->save();
          Log::info( 'XMLHacienda guardado : ' . $invoice->id );
        }catch( \Throwable $e ){
          Log::error( 'Error al registrar en tabla XMLHacienda: ' . $e->getMessage() );
        }
        
        return $path;
        
    }

    public function setNoteData($invoiceReference) {
        try {
            $this->document_key = getDocumentKey('03', $this->reference_number, $invoiceReference->company->id_number);
            $this->document_number = getDocReference('03', $this->reference_number);;
            $this->sale_condition = $invoiceReference->sale_condition;
            $this->payment_type = $invoiceReference->payment_type;
            $this->retention_percent = $invoiceReference->retention_percent;
            $this->credit_time = $invoiceReference->credit_time;
            $this->buy_order = $invoiceReference->buy_order;
            $this->other_reference = $invoiceReference->reference_number;
            $this->reference_document_key = $invoiceReference->document_key;
            $this->reference_generated_date = $invoiceReference->generated_date;
            $this->send_emails = $invoiceReference->send_email ?? null;
            $invoiceReference->reference_document_key = $this->document_key;
            $invoiceReference->save();
            $this->save();
            $this->client_id = $invoiceReference->client_id;

            //Datos de factura
            $this->description = $invoiceReference->description;
            $this->subtotal = floatval( str_replace(",","", $invoiceReference->subtotal ));
            $this->currency = $invoiceReference->currency;
            $this->currency_rate = floatval( str_replace(",","", $invoiceReference->currency_rate ));
            $this->total = floatval( str_replace(",","", $invoiceReference->total ));
            $this->iva_amount = floatval( str_replace(",","", $invoiceReference->iva_amount ));


            $this->client_first_name = $invoiceReference->client_first_name;
            $this->client_last_name = $invoiceReference->client_last_name;
            $this->client_last_name2 = $invoiceReference->client_last_name2;
            $this->client_email = $invoiceReference->client_email;
            $this->client_address = $invoiceReference->client_address;
            $this->client_country = $invoiceReference->client_country;
            $this->client_state = $invoiceReference->client_state;
            $this->client_city = $invoiceReference->client_city;
            $this->client_district = $invoiceReference->client_district;
            $this->client_zip = $invoiceReference->client_zip;
            $this->client_phone = $invoiceReference->client_phone;
            $this->client_id_number = $invoiceReference->client_id_number;

            $fecha = Carbon::parse(now('America/Costa_Rica'));
            $this->generated_date = $fecha;
            $fechaV = $fecha;
            $this->due_date = $fechaV;
            $this->year = Carbon::now()->year;
            $this->month = Carbon::now()->month;
            $this->save();

            $lids = array();
            $dataItems = $invoiceReference->items->toArray();
            foreach($dataItems as $item) {
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

}
