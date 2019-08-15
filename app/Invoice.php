<?php

namespace App;

use App\Company;
use App\InvoiceItem;
use \Carbon\Carbon;
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
    
    public function activity()
    {
        return $this->belongsTo(Actividades::class, 'commercial_activity');
    }
    
    public function clientName() {
      if( isset($this->client_first_name)) {
        return "$this->client_first_name $this->client_last_name $this->client_last_name2";
      }else {
        if( isset($this->client_id) ) {
          return $this->client->getFullName();
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
      }else if( $this->document_type == '08' ) {
        $tipo = "Factura de compra";
      }else if( $this->document_type == '09' ) {
        $tipo = "Factura de exportación";
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
            $this->other_reference = trim($request->other_reference);
            $this->send_emails = isset($request->send_email) ? trim($request->send_email) : null;
            if ($request->commercial_activity) {
                $this->commercial_activity = $request->commercial_activity;
            }

            //Datos de cliente. El cliente nuevo viene con ID = -1
            if ($request->client_id == '-1') {

                $tipo_persona = $request->tipo_persona;
                $identificacion_cliente = preg_replace("/[^0-9]/", "", $request->id_number );
                $codigo_cliente = $request->code;
                
                $billing_emails = isset($request->billing_emails) ? trim($request->billing_emails) : $request->email;

                $client = Client::updateOrCreate(
                    [
                        'id_number' => $identificacion_cliente,
                        'company_id' => $this->company_id,
                    ],
                    [
                        'code' => $codigo_cliente ,
                        'company_id' => $this->company_id,
                        'tipo_persona' => $tipo_persona,
                        'id_number' => trim($identificacion_cliente),
                        'first_name' => trim($request->first_name),
                        'last_name' => trim($request->last_name),
                        'last_name2' => trim($request->last_name2),
                        'fullname' => trim($request->first_name.' '.$request->last_name.' '.$request->last_name2),
                        'emisor_receptor' => 'ambos',
                        'country' => $request->country,
                        'state' => $request->state,
                        'city' => $request->city,
                        'district' => $request->district,
                        'neighborhood' => trim($request->neighborhood),
                        'zip' => $request->zip,
                        'address' => trim($request->address),
                        'foreign_address' => trim($request->address),
                        'phone' => trim($request->phone),
                        'es_exento' => $request->es_exento,
                        'email' => trim($request->email),
                        'billing_emails' => $billing_emails
                    ]
                );

                $this->client_id = $client->id;
            }else{
                $this->client_id = $request->client_id;
                $client = Client::find($this->client_id);
            }

            $request->currency_rate = $request->currency_rate ? $request->currency_rate : 1;
            //Datos de factura
            $this->description = $request->notas ?? null;
            $this->subtotal = floatval( str_replace(",","", $request->subtotal ));
            $this->currency = $request->currency;
            $this->currency_rate = floatval( str_replace(",","", $request->currency_rate ));
            $this->total = floatval( str_replace(",","", $request->total ));
            $this->iva_amount = floatval( str_replace(",","", $request->iva_amount ));

            if (isset($client)) {
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
              $this->client_phone = preg_replace('/[^0-9]/', '', $client->phone);
              $this->client_id_number = $client->id_number;
            } else {
              $this->client_first_name = 'N/A';
            }
            
            if ($this->document_type == '08' && isset($request->provider_id)) {

                $this->client_first_name = trim($this->company->name);
                $this->client_last_name = trim($this->company->last_name);
                $this->client_last_name2 = trim($this->company->last_name2);
                $this->client_email = trim($this->company->email);
                $this->client_address = trim($this->company->address);
                $this->client_country = $this->company->country;
                $this->client_state = $this->company->state;
                $this->client_city = $this->company->city;
                $this->client_district = $this->company->district;
                $this->client_zip = $this->company->zip;
                $this->client_phone = preg_replace('/[^0-9]/', '', $this->company->phone);
                $this->client_id_number = trim($this->company->id_number);

                //Datos de proveedor
                if ($request->provider_id == '-1') {
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
                    $provider->fullname = $request->last_name2;
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
                } else {
                    $this->provider_id = $request->provider_id;

                }
            }
            $this->save();
            //Fechas
            $fecha = Carbon::createFromFormat('d/m/Y g:i A',
                $request->generated_date . ' ' . $request->hora);
            $this->generated_date = $fecha;
            $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
            $this->due_date = $fechaV;
            $this->year = $fecha->year;
            $this->month = $fecha->month;
            
            if (!$this->id) {
              $this->company->addSentInvoice( $this->year, $this->month );
            }
            $this->save();

            $lids = array();
            $i = 1;
            foreach ($request->items as $item) {
                $item['item_number'] = $i;
                $item['item_id'] = $item['id'] ? $item['id'] : 0;
                $item_modificado = $this->addEditItem($item);
                array_push( $lids, $item_modificado->id );
                $i++;
            }

            foreach ($this->items as $item) {
                if (!in_array( $item->id, $lids )) {
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
        try {
            if (isset($data['item_number'])) {
                $item = InvoiceItem::updateOrCreate([
                    'item_number' => $data['item_number'],
                    'invoice_id' => $this->id,
                    'code'=> $data['code']
                ], [
                    'company_id' => $this->company_id,
                    'year'  => $this->year,
                    'month' => $this->month,
                    'name'  => $data['name'] ? trim($data['name']) : null,
                    'product_type' => $data['product_type'] ?? null,
                    'measure_unit' => $data['measure_unit'] ?? 'Unid',
                    'item_count'   => $data['item_count'] ? trim($data['item_count']) : 1,
                    'unit_price'   => $data['unit_price'] ?? 0,
                    'subtotal'     => $data['subtotal'] ?? 0,
                    'total' => $data['total'] ?? 0,
                    'discount_type' => $data['discount_type'] ?? null,
                    'discount' => $data['discount'] ?? 0,
                    'iva_type' => $data['iva_type'] ?? null,
                    'iva_percentage' => $data['iva_percentage'] ?? 0,
                    'iva_amount' => $data['iva_amount'] ?? 0,
                    'tariff_heading' => $data['tariff_heading'] ?? null,
                    'is_exempt' => $data['is_exempt'] ?? false,
                    ]
                );
                try {
                    $exonerationDate = isset( $data['exoneration_date'] )  ? Carbon::createFromFormat('d/m/Y', $data['exoneration_date']) : null;
                }catch( \Exception $e ) {
                    $exonerationDate = null;
                }

                if ($exonerationDate && isset($data['typeDocument']) && isset($data['numeroDocumento']) && $data['porcentajeExoneracion'] > 0) {
                    $item->exoneration_document_type = $data['typeDocument'] ?? null;
                    $item->exoneration_document_number = $data['numeroDocumento'] ?? null;
                    $item->exoneration_company_name = $data['nombreInstitucion'] ?? null;
                    $item->exoneration_porcent = $data['porcentajeExoneracion'] ?? 0;
                    $item->exoneration_amount = $data['montoExoneracion'] ?? 0;
                    $item->exoneration_date = $exonerationDate;
                    $item->exoneration_total_amount = $data['montoExoneracion'] ?? 0;
                    $item->exoneration_total_gravado = (($item->item_count * $item->unit_price) * $item->exoneration_porcent) / 100 ;
                    $item->impuesto_neto = $data['impuestoNeto'] ?? $data['iva_amount'] - $data['montoExoneracion'];
                    $item->save();
                }

                return $item;
            } else {
                return false;
            }
        } catch ( \Exception $e) {
            Log::error("Error en lineas de factura-->> $e");
            return false;
        }

    }
    
    /*
    * importInvoiceRow Es la función que se usa para importar por Excel.
    */
    public static function importInvoiceRow ( $data, $company = false ) {
      if(!$company){
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
                    'phone' => $data['telefonoCliente'],
                    'fullname' => "$identificacionCliente - " . $data['nombreCliente']
                ]
            );
            $correoCliente = $data['correoCliente'] ? $data['correoCliente'] : $clienteCache->email;
            $clienteCache->email = $correoCliente;
            $clienteCache->save();
            Cache::put($clientCacheKey, $clienteCache, 30);
        }
        $cliente = Cache::get($clientCacheKey);
        $idCliente = $cliente->id;
        $tipoDocumento = $data['tipoDocumento'];
      } else {
        $tipoDocumento = '04'; //Si no trae cliente, es un tiquete.
      }
      $idCliente = preg_replace("/[^0-9]/", "", $idCliente );
      
      $invoiceCacheKey = "import-factura-" . $data['claveFactura'] . $company->id . "-" . $data['consecutivoComprobante'];
      //Usa Cache por si viene la misma factura en varios lugares del Excel, las siguientes veces no reinicia el subtotal de la factura.
      if ( !Cache::has($invoiceCacheKey) ) { 
      
          $invoice = Invoice::firstOrNew(
              [
                  'company_id' => $company->id,
                  'document_number' => $data['consecutivoComprobante'],
                  'document_key' => $data['claveFactura'],
              ]
          );
          
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
          $invoice->xml_schema =  $data['xmlSchema'] ?? 43;
          $invoice->commercial_activity =  $data['codigoActividad'] ?? '0';
          
          //Datos generales
          $invoice->sale_condition = $data['condicionVenta'];
          $invoice->payment_type = $data['metodoPago'];
          $invoice->credit_time = 0;
          $invoice->description = $data['descripcion'];
          
          
          $invoice->generation_method = $data['metodoGeneracion'];
          $invoice->is_authorized = $data['isAuthorized'];
          $invoice->is_code_validated = $data['codeValidated'];
          $invoice->hacienda_status = "03";
          
          $invoice->client_id_number = preg_replace("/[^0-9]/", "", $data['identificacionCliente']);
          $invoice->client_first_name = $data['nombreCliente'] ?? null;
          $invoice->client_email = $data['correoCliente'] ?? null;
          $invoice->client_phone = $data['telefonoCliente'] ?? null;
          
          //Datos de factura
          $invoice->currency_rate = $data['tipoCambio'] ?? 1;
          //Datos de factura
          $invoice->currency = $data['moneda'] ?? 'CRC';
          if( $invoice->currency == 1 ) { $invoice->currency = "CRC"; }
          if( $invoice->currency == 2 ) { $invoice->currency = "USD"; }
          if($invoice->currency == 'CRC'){
            $invoice->currency_rate = 1;
          }
          
          try{
            $invoice->generated_date = Carbon::createFromFormat('d/m/Y', $data['fechaEmision']);
          }catch( \Exception $ex ){
            $dt =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['fechaEmision']);
            $invoice->generated_date = Carbon::instance($dt);
          }
          if( !CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/Y'), $company )){ 
            return false; 
          }
          try{
            $invoice->due_date = Carbon::createFromFormat('d/m/Y', $data['fechaVencimiento']);
          }catch( \Exception $ex ){
            $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['fechaVencimiento']);
            $invoice->due_date = Carbon::instance($dt);
          }
          
          $invoice->commercial_activity =  $data['codigoActividad'] ?? '0';
          $year = $invoice->generated_date->year;
          $month = $invoice->generated_date->month;
          $invoice->year = $year;
          $invoice->month = $month;

          $invoice->currency_rate = $data['tipoCambio'];
          $invoice->subtotal = 0;
          $invoice->iva_amount = 0;
          $invoice->total = $data['totalDocumento'] ?? 0;
          
          if($invoice->id){
            $available_invoices = AvailableInvoices::where('company_id', $company->id)
                                  ->where('year', $invoice->year)
                                  ->where('month', $invoice->month)
                                  ->first();
            if( isset($available_invoices) ) {
              $available_invoices->current_month_sent = $available_invoices->current_month_sent + 1;
              $available_invoices->save();
            }  
          }
          
          $invoice->save();
          Cache::put($invoiceCacheKey, $invoice, 30);
      }
      $invoice = Cache::get($invoiceCacheKey);
      $year = $invoice->generatedDate()->year;
      $month = $invoice->generatedDate()->month;
    
      /**LINEA DE FACTURA**/
      $subtotalLinea = $data['subtotalLinea'] ?? 0;
      $montoIvaLinea = $data['montoIva'] ?? 0;
      $totalLinea = $data['totalLinea'] ?? 0;
      $precioUnitarioLinea = $data['precioUnitario'] ?? 0;
      $montoDescuentoLinea = $data['montoDescuento'] ?? 0;
      $cantidadLinea = $data['cantidad'] ?? 0;
      $invoice->subtotal = $invoice->subtotal + $subtotalLinea;
      $invoice->iva_amount = $invoice->iva_amount + $montoIvaLinea;
      
      $discount_reason = "";
      
      $item = InvoiceItem::updateOrCreate(
      [
          'invoice_id' => $invoice->id,
          'item_number' => $data['numeroLinea'],
      ],[
          'invoice_id' => $invoice->id,
          'company_id' => $company->id,
          'year' => $year,
          'month' => $month,
          'item_number' => $data['numeroLinea'],
          'code' => $data['codigoProducto'],
          'name' => $data['detalleProducto'],
          'product_type' => $data['categoriaHacienda'] ?? 0,
          'measure_unit' => $data['unidadMedicion'],
          'item_count' => $cantidadLinea,
          'unit_price' => $precioUnitarioLinea,
          'subtotal' => $subtotalLinea,
          'total' => $totalLinea,
          'discount_type' => '01',
          'discount' => $montoDescuentoLinea,
          'iva_type' => $data['codigoEtax'],
          'iva_amount' => $montoIvaLinea,
          'exoneration_document_type' => $data['tipoDocumentoExoneracion'],
          'exoneration_document_number' => $data['documentoExoneracion'],
          'exoneration_company_name' => $data['companiaExoneracion'],
          'exoneration_porcent' => $data['porcentajeExoneracion'],
          'exoneration_amount' => $data['montoExoneracion'],
          'impuesto_neto' => $data['impuestoNeto'],
          'exoneration_total_amount' => $data['totalMontoLinea']
      ]);
      if( $invoice->year == 2018 ) {
         clearLastTaxesCache($company->id, 2018);
      }
      
      clearInvoiceCache($invoice);
      
      if( $data['totalNeto'] != 0 ) {
        $invoice->subtotal = $data['totalNeto'];
      }
      $invoice->save();
      
      return $invoice;
      
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
      
        $identificacionProveedor = $arr['Emisor']['Identificacion']['Numero'];
        if( $metodoGeneracion != "Email" ){
          $company = currentCompanyModel();
        }else{
          //Si es email, busca por ID del proveedor para encontrar la compañia
          $company = Company::where('id_number', $identificacionProveedor)->first();
        }

        if( ! $company ) {
          return false;
        }
      
        $invoice = Invoice::firstOrNew(
              [
                  'company_id' => $company->id,
                  'document_number' => $arr['NumeroConsecutivo'],
                  'document_key' => $arr['Clave'],
              ]
        );
        
        if( $invoice->id ) {
          Log::warning( "XML: No se pudo guardar la factura de venta. Ya existe para la empresa." );
          return false;
        }
        
        $invoice->hacienda_status = "03";
        $invoice->payment_status = "01";
        $invoice->generation_method = $metodoGeneracion;
        
        $invoice->commercial_activity = $arr['CodigoActividad'] ?? 0;
        $invoice->xml_schema = $invoice->commercial_activity ? 43 : 42;
        $invoice->sale_condition = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        //$invoice->credit_time = array_key_exists('PlazoCredito', $arr) ? $arr['PlazoCredito'] : '';
        $invoice->credit_time = null;
        $medioPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '';
        if ( is_array($medioPago) ) {
          $medioPago = $medioPago[0];
        }
        $invoice->payment_type = $medioPago;
        
        //Fechas
        $fechaEmision = Carbon::createFromFormat('Y-m-d', substr($arr['FechaEmision'], 0, 10));
        $invoice->generated_date = $fechaEmision;
        $invoice->due_date = $fechaEmision;
        
        $month = $fechaEmision->month;
        $year = $fechaEmision->year;
        $invoice->month = $month;
        $invoice->year = $year;
        
        if( array_key_exists( 'CodigoTipoMoneda', $arr['ResumenFactura'] ) ) {
          $idMoneda = $arr['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'] ?? '';
          $tipoCambio = $arr['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ?? 1;
        }else {
          $idMoneda = $arr['ResumenFactura']['CodigoMoneda'] ?? '';
          $tipoCambio = array_key_exists('TipoCambio', $arr['ResumenFactura']) ? $arr['ResumenFactura']['TipoCambio'] : '1';
        }
        $invoice->currency = $idMoneda;
        $invoice->currency_rate = $tipoCambio;
        
        $invoice->description = 'XML Importado';
        $invoice->total = $arr['ResumenFactura']['TotalComprobante'];
        
        $authorize = true;
        if( $metodoGeneracion == "Email" || $metodoGeneracion == "XML-A" ) {
            $authorize = false;
        }
        $invoice->is_authorized = $authorize;
        $invoice->is_code_validated = false;

        if(strlen($arr['Clave']) == 50){
            $tipoDocumento = substr($arr['Clave'], 29, 2);
        }
        $invoice->document_type = $tipoDocumento ?? '01';
        
        //Start DATOS CLIENTE
        if ( array_key_exists('Receptor', $arr) ){
            if( isset( $arr['Receptor']['CorreoElectronico'] ) ){
                $correoCliente = $arr['Receptor']['CorreoElectronico'];
            }else{
                $correoCliente = null;
            }
            if ( isset($arr['Receptor']['Telefono']) ) {
              $telefonoCliente = $arr['Receptor']['Telefono']['NumTelefono'] ?? null;
            }else {
              $telefonoCliente = null;
            }
            if( isset($arr['Receptor']['Identificacion']['Tipo']) ){
                $tipoPersona = $arr['Receptor']['Identificacion']['Tipo'];
            }else{
                $tipoPersona = null;
            }

            if($invoice->xml_schema == 42){
                if ( isset($arr['Receptor']['Identificacion']['Numero']) ) {
                    $identificacionCliente = $arr['Receptor']['Identificacion']['Numero'] ?? null;
                }else {
                    $identificacionCliente = null;
                }
            }else{
              try {
                  $identificacionCliente = $arr['Receptor']['Identificacion']['Numero'];
              }catch(\Exception $e){ $identificacionCliente = null; }
            }
            try {
                $nombreCliente = $arr['Receptor']['Nombre'];
            }catch(\Exception $e){
                $nombreCliente = 'Cliente Genérico';
            }


            if ( isset($arr['Receptor']['Ubicacion']) ) {
              $provinciaCliente = $arr['Receptor']['Ubicacion']['Provincia'];
              $cantonCliente = $arr['Receptor']['Ubicacion']['Canton'];
              $distritoCliente = $arr['Receptor']['Ubicacion']['Distrito'];
              try{
                $otrasSenas = $arr['Receptor']['Ubicacion']['OtrasSenas'] ?? null;
              }catch(\Exception $e){ $otrasSenas = null; }
              $zipCliente = 0;
              if( $cantonCliente ) {
                  if( strlen( (int)$cantonCliente ) <= 2 ) {
                      $cantonCliente = (int)$provinciaCliente . str_pad((int)$cantonCliente, 2, '0', STR_PAD_LEFT);
                  }
              }
              if( $distritoCliente ) {
                  if( strlen( $distritoCliente ) > 4 ) {
                      $zipCliente = $distritoCliente;
                  }else{
                      $distritoCliente = (int)$cantonCliente . str_pad((int)$distritoCliente, 2, '0', STR_PAD_LEFT);
                      $zipCliente = $distritoCliente;
                  }
              }
            }else{
              $provinciaCliente = '1';
              $cantonCliente = '101';
              $distritoCliente = '10101';
              $zipCliente = '10101';
              $otrasSenas = null;
            }

        if( $identificacionCliente ){
          $clientCacheKey = "import-clientes-$identificacionCliente-".$company->id;
          if ( !Cache::has($clientCacheKey) ) {
              $clienteCache =  Client::updateOrCreate(
                  [
                      'id_number' => $identificacionCliente ?? null,
                      'company_id' => $company->id,
                  ],
                  [
                      'code' => $identificacionCliente,
                      'company_id' => $company->id,
                      'tipo_persona' => $tipoPersona ?? null,
                      'id_number' => $identificacionCliente,
                      'first_name' => $nombreCliente ?? null,
                      'email' => $correoCliente ?? null,
                      'phone' => $telefonoCliente ?? null,
                      'fullname' => "$identificacionCliente - " . $nombreCliente ?? null,
                      'country' => 'CR',
                      'state' => $provinciaCliente ?? null,
                      'city' => $cantonCliente ?? null,
                      'district' => $distritoCliente ?? null,
                      'zip' => $zipCliente ?? null,
                      'address' => $otrasSenas ?? null,
                      'foreign_address' => $otrasSenas ?? null,
                  ]
              );
              Cache::put($clientCacheKey, $clienteCache, 30);
          }
          $cliente = Cache::get($clientCacheKey);

          $invoice->client_id = $cliente->id;
          $invoice->client_id_number = $identificacionCliente;
          $invoice->client_first_name = $nombreCliente;
          $invoice->client_email = $correoCliente;
          $invoice->client_address = $otrasSenas;
          $invoice->client_country = 'CR';
          $invoice->client_state = $provinciaCliente;
          $invoice->client_city = $cantonCliente;
          $invoice->client_district = $distritoCliente;
          $invoice->client_zip = $zipCliente;
          $invoice->client_phone = $telefonoCliente;
          $invoice->foreign_address = $otrasSenas;
        }else{
          $invoice->client_email = $arr['Receptor']['CorreoElectronico'] ??'N/A';
          $invoice->client_phone = $arr['Receptor']['Telefono']['NumTelefono'] ?? 'N/A';
          $invoice->client_id_number = $arr['Receptor']['Identificacion']['Numero'] ?? 0;
          $invoice->client_first_name = $arr['Receptor']['Nombre'] ?? 'N/A';
        }

        }else{
        $invoice->client_email = 'N/A';
        $invoice->client_phone = 'N/A';
        $invoice->client_id_number = 0;
        $invoice->client_first_name = 'N/A';
        $invoice->document_type = '04';
        }
              
        //End DATOS CLIENTE
        
        //El subtotal y iva_amount inicia en 0, lo va sumando conforme recorre las lineas.
        $invoice->subtotal = 0;
        $invoice->iva_amount = 0;
        
        //Revisa si es una sola linea. Si solo es una linea, lo hace un array para poder entrar en el foreach.
        $lineas = $arr['DetalleServicio']['LineaDetalle'];
        if( array_key_exists( 'NumeroLinea', $lineas ) ) {
            $lineas = [$arr['DetalleServicio']['LineaDetalle']];
        }
        
        $invoice->save();
        
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
            $codigoEtax = 'B103'; //De momento asume que todo en 4.2 es al 13%.
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
              $montoIva = trim($linea['Impuesto']['Monto']);
              $porcentajeIva = trim($linea['Impuesto']['Tarifa']);
              
              if( array_key_exists('Exoneracion', $linea['Impuesto']) ) {
                $tipoDocumentoExoneracion = $linea['Impuesto']['Exoneracion']['TipoDocumento'] ?? null;
                $documentoExoneracion = $linea['Impuesto']['Exoneracion']['NumeroDocumento']  ?? null;
                $companiaExoneracion = $linea['Impuesto']['Exoneracion']['NombreInstitucion'] ?? null;
                $fechaExoneracion = $linea['Impuesto']['Exoneracion']['FechaEmision'] ?? null;
                $porcentajeExoneracion = $linea['Impuesto']['Exoneracion']['PorcentajeExoneracion'] ?? 0;
                $montoExoneracion = $linea['Impuesto']['Exoneracion']['MontoExoneracion'] ?? 0;
              }
              
            }
            
            $invoice->subtotal = $invoice->subtotal + $subtotalLinea;
            $invoice->iva_amount = $invoice->iva_amount + $montoIva;

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
            
            $item_modificado = $invoice->addEditItem($item);
            array_push( $lids, $item_modificado->id );
        }
        
        foreach ( $invoice->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
        $invoice->save();
        
        return $invoice;
    }
    
    public static function storeXML($invoice, $file) {
        $cedulaEmpresa = $invoice->company->id_number;
        //$cedulaCliente = $invoice->client->id_number;
        $consecutivoComprobante = $invoice->document_number;
        
        if ( Storage::exists("empresa-$cedulaEmpresa/facturas_ventas/$invoice->year/$invoice->month/$consecutivoComprobante.xml")) {
            Storage::delete("empresa-$cedulaEmpresa/facturas_ventas/$invoice->year/$invoice->month/$consecutivoComprobante.xml");
        }
        
        $path = \Storage::putFileAs(
            "empresa-$cedulaEmpresa/facturas_ventas", $file, "$invoice->year/$invoice->month/$consecutivoComprobante.xml"
        );
        
        try{
          $xmlHacienda = new XmlHacienda();
          $xmlHacienda->xml = $path;
          $xmlHacienda->bill_id = 0;
          $xmlHacienda->invoice_id = $invoice->id;
          $xmlHacienda->save();
        }catch( \Throwable $e ){
          Log::error( 'Error al registrar en tabla XMLHacienda: ' . $e->getMessage() );
        }
        
        try{
          $available_invoices = AvailableInvoices::where('company_id', $invoice->company_id)
                              ->where('year', $invoice->year)
                              ->where('month', $invoice->month)
                              ->first();
          if( isset($available_invoices) ) {
            $available_invoices->current_month_sent = $available_invoices->current_month_sent + 1;
            $available_invoices->save();
          }
        }catch( \Throwable $e ){
          Log::error( 'Error al sumar en AvailableInvoices ' . $e->getMessage() );
        }
        
        return $path;
        
    }

    public function setNoteData($invoiceReference) {
        try {
            $this->document_key = getDocumentKey('03', $this->reference_number, $invoiceReference->company->id_number);
            $this->document_number = getDocReference('03', $this->reference_number);
            $this->sale_condition = $invoiceReference->sale_condition;
            $this->payment_type = $invoiceReference->payment_type;
            $this->retention_percent = $invoiceReference->retention_percent;
            $this->commercial_activity = $invoiceReference->commercial_activity;
            $this->credit_time = $invoiceReference->credit_time;
            $this->buy_order = $invoiceReference->buy_order;
            $this->other_reference = $invoiceReference->reference_number;
            $this->reference_document_key = $invoiceReference->document_key;
            $this->reference_generated_date = $invoiceReference->generated_date;
            $this->reference_doc_type = $invoiceReference->document_type;
            $this->send_emails = $invoiceReference->send_email ?? null;
            $this->xml_schema = $invoiceReference->xml_schema;
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
