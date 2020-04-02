<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use \Carbon\Carbon;
use App\Invoice;
use App\Client;
use App\Quickbooks;
use App\QuickbooksCustomer;
use App\Company;
use QuickBooksOnline\API\Facades\Invoice as qbInvoice;

class QuickbooksInvoice extends Model
{

    protected $guarded = [];
    use Sortable, SoftDeletes;
    
    protected $casts = [
        'qb_data' => 'array'
    ];

	
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }  

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
    
    //Devuelve la fecha de generación en formato Carbon
    public function generatedDate()
    {
        return Carbon::parse($this->qb_date);
    }
    
    public static function getMonthlyInvoices($dataService, $year, $month, $company) {
        $cachekey = "qb-invoices-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $count = QuickbooksInvoice::where('company_id', $company->id)->count();
            if( $count > 0 ){
                $dateFrom = Carbon::createFromDate($year, $month, 1)->firstOfMonth()->toAtomString();
                $dateTo = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toAtomString();
                $invoices = $dataService->Query("SELECT * FROM Invoice WHERE MetaData.LastUpdatedTime >= '$dateFrom' AND MetaData.LastUpdatedTime <= '$dateTo'");
            }else{
                $invoices = $dataService->Query("SELECT * FROM Invoice");
            }
            Cache::put($cachekey, $invoices, 30); //Cache por 15 segundos.
        }else{
            $invoices = Cache::get($cachekey);
        }
        return $invoices;
    }
    
    public static function syncMonthlyInvoices($dataService, $year, $month, $company){
        QuickbooksCustomer::syncMonthlyClients($dataService, $year, $month, $company);
        $qbInvoices = QuickbooksInvoice::getMonthlyInvoices($dataService, $year, $month, $company);
        $qbInvoices = $qbInvoices ?? [];
        $facturas = [];

        foreach($qbInvoices as $qbInvoice){
            $date = Carbon::createFromFormat("Y-m-d", $qbInvoice->TxnDate);
            $clientName = QuickbooksCustomer::getClientName($company, $qbInvoice->CustomerRef);
            $qbInvoice = QuickBooksInvoice::updateOrCreate(
              [
                "qb_id" => $qbInvoice->Id
              ],
              [
                "qb_doc_number" => $qbInvoice->DocNumber,
                "qb_date" => $date,
                "qb_total" => $qbInvoice->TotalAmt,
                "qb_client" => $clientName,
                "qb_data" => $qbInvoice,
                "generated_at" => 'quickbooks',
                "month" => $date->month,
                "year" => $date->year,
                "company_id" => $company->id
              ]
            );
        }
    }
    
    public static function saveEtaxaqb($dataService, $invoice, $accountRef = null){   
        try{
            $company = $invoice->company;
            $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
            
            $dataService = $qb->getAuthenticatedDS();
            
            $lines = [];
            $taxCode = "S103"; //Por defecto usa este.
            foreach($invoice->items as $item){
                $itemRef = null;
                $itemName = null;
                $product = Product::find($item->product_id);
                if( !isset($product) ){
                    $product = Product::updateOrCreate(
                        [
                            'company_id' => $invoice->company_id,
                            'name' => $item->name
                        ],
                        [
                            'unit_price' => $item->unit_price,
                            'code' => $item->code,
                            'description' => $item->name ?? null,
                            'default_iva_type'  => $item->iva_type,
                            'product_category_id'  => $item->product_type,
                            'is_catalogue' => true,
                            'measure_unit' => $item->measure_unit
                        ]
                    );
                }
                $qbItem = QuickbooksProduct::where('product_id', $product->id)->first();
                if( !isset($qbItem) ){
                    $qbItem = QuickbooksProduct::saveEtaxaqb($dataService, $product, $accountRef);
                }
                $itemRef = $qbItem->qb_id;
                
                $qty = round($item->item_count, 5);
                $unitPrice = round($item->unit_price, 5);
                $Amount = $qty*$unitPrice;

                $taxCode = $item->iva_type;
                
                $lines[] = [
                     "Amount" => $Amount,
                     "DetailType" => "SalesItemLineDetail",
                     "SalesItemLineDetail" => [
                        "UnitPrice" => $unitPrice,
                        "Qty" => $qty,
                        "ItemRef" => [
                          "value" => $itemRef,
                          "name" => $itemName
                        ],
                        "TaxCodeRef" => "TAX"
                     ]
                ];
            }
            
            $qbCustomer = QuickbooksCustomer::where('company_id', $invoice->company_id)
                                            ->where('client_id', $invoice->client_id)->first();
            if(!isset($qbCustomer)){
                $qbCustomer = QuickbooksCustomer::saveEtaxaqb($dataService, $invoice->client);
            }
            if( !isset($qbCustomer->qb_id) ){
                return back()->withError( "Error al guardar cliente ". $invoice->client_first_name ." en QuickBooks, por favor contacte a soporte o intente hacer la sincronización del cliente. Mensaje: ".$qbCustomer->getIntuitErrorMessage() );
            }
            
            //Define el tax reference para enviar de la factura.
            $taxRef = null;
            foreach($qb->taxes_json['tipo_iva'] as $key => $value){
                if( $key != 'default' ){
                    if($taxCode == $value){
                        $taxRef = $key;
                    }
                }
            }
            $TxnTaxDetail = [
                "TxnTaxCodeRef" => $taxRef
            ];
            
            $params = [
                "DocNumber" => $invoice->document_number,
                "Line" => $lines,
                "CustomerRef" => [
                      "value" => $qbCustomer->qb_id,
                      "name" => $qbCustomer->full_name
                ],
                "BillEmail" => [
                      "Address" => $invoice->email
                ],
                "CustomerMemo" => $invoice->description ?? "",
                "TxnTaxDetail" => $TxnTaxDetail
            ];
            
            //Define el metodo de pago
            foreach($qb->conditions_json as $key => $value){
                if( $key != 'default' ){
                    if($invoice->sale_condition == $value){
                        $params["SalesTermRef"] = [
                            "value" => $key
                        ];
                    }
                }
            }
            
            
            //Define el metodo de pago
            foreach($qb->payment_methods_json as $key => $value){
                if( $key != 'default' ){
                    if($invoice->payment_type == $value){
                        $params["PaymentMethodRef"] = [
                            "value" => $key
                        ];
                    }
                }
            }
            
            $theResourceObj = qbInvoice::create($params);

            $qbInvoice = $dataService->Add($theResourceObj);
            $error = $dataService->getLastError();
            if ($error) {
                Log::error("The Status code is: " . $error->getHttpStatusCode() . "\n".
                            "The Helper message is: " . $error->getOAuthHelperError() . "\n".
                            "The Response message is: " . $error->getResponseBody() . "\n");
                $qbInvoices = $dataService->Query("SELECT * FROM Invoice WHERE DocNumber = '$invoice->document_number'");
                $qbInvoice = $qbInvoices[0] ?? null;
            }
            
            if( isset($qbInvoice) ){
                $date = Carbon::createFromFormat("Y-m-d", $qbInvoice->TxnDate);
                $clientName = QuickbooksCustomer::getClientName($company, $qbInvoice->CustomerRef);
                $inv = QuickBooksInvoice::updateOrCreate(
                  [
                    "qb_id" => $qbInvoice->Id
                  ],
                  [
                    "qb_doc_number" => $qbInvoice->DocNumber,
                    "qb_date" => $date,
                    "qb_total" => $qbInvoice->TotalAmt,
                    "qb_client" => $clientName,
                    "qb_data" => $qbInvoice,
                    "generated_at" => 'quickbooks',
                    "month" => $invoice->month,
                    "year" => $invoice->year,
                    "company_id" => $company->id,
                    "invoice_id" => $invoice->id
                  ]
                );
                return $inv;
            }
            return $error;
        }catch(\Exception $e){
            return back()->withError( "Error al sincronizar factura: $invoice->document_number. Por favor contacte a soporte." );
            Log::error($e);
            return $e;
        }
    }
    
    
    
    public function saveQbaetax(){
        try{
            $company = $this->company;
            $invoiceData = $this->qb_data;
            $items = $invoiceData["Line"];
            
            $metodoGeneracion = "quickbooks";
            $xmlSchema = 43;
            $codigoActividad = $company->getActivities() ? $company->getActivities()[0]->codigo : '0';
            
            try{
                $customer = QuickbooksCustomer::getClientInfo($company, $invoiceData['CustomerRef']);
                $cliente = $customer->client;
            if( !$cliente ){
                return back()->withError("No se encontró el cliente de la factura $this->qb_id. Verifique que el proveedor se encuentre correctamente sincronizado e indique el tipo de persona y código postal.");
            }
            }catch(\Exception $e){
                return back()->withError("No se encontró el cliente de la factura $this->qb_id. Verifique que el proveedor se encuentre correctamente sincronizado e indique el tipo de persona y código postal.");
            }
            
            if( isset($data['BillAddr']) ){
                  try{
                    $zip = $invoiceData['BillAddr']['PostalCode'] ?? '10101';
                    $state = $zip[0];
                    $city = $zip[1] . $zip[2];
                    $district = $zip;
                  }catch( \Throwable $e ){ }  
                  $countryCode = $invoiceData['BillAddr']['CountryCode'] ?? 'CR';
                  $address = $invoiceData['BillAddr']['City'] ?? "" . " " . $invoiceData['BillAddr']['Line1'] ?? "" . " " . $invoiceData['BillAddr']['Line2'] ?? "";
            }
            
            if( isset($data['PrimaryPhone']) ){
                $phone = $data['PrimaryPhone']["FreeFormNumber"];
            }
            
            if( isset($data['BillEmail']) ){
                $email = $data['BillEmail']["Address"];
            }
            
            $nombreCliente = $cliente->fullname;
            $codigoCliente = $cliente->codigo;
            $tipoPersona = $cliente->tipo_persona ?? '01'; //SOLUCIONAR - QB no incluye tipo de persona
            $identificacionCliente = $cliente->id_number ?? '0000'; //SOLUCIONAR - QB no incluye cédula
            $correoCliente = $email ?? $cliente->email;
            $telefonoCliente = $phone ?? $cliente->phone;
            $direccion = $address ?? $cliente->address;
            $codProvincia = $state ?? $cliente->state;
            $codCanton = $city ?? $cliente->city;
            $codDistrito = $district ?? $cliente->district;
            $zip = $zip ?? $cliente->zip;
            
            //Define el tipo de documento
            $tipoDocumento = $tipoPersona == "F" || $tipoPersona == '1' || $tipoPersona == '01' ? '01' : '04';
            $tipoDocumento = '01';
            if( !$codDistrito || !$zip ){
                $tipoDocumento = "04";
            }
            
            $numeroReferencia = $invoiceData['DocNumber'];
            $consecutivoComprobante = $invoiceData['DocNumber'];
            $claveFactura = "QB-".getDocumentKey($tipoDocumento, $company, $numeroReferencia);
    
            $invoice = Invoice::where('document_number', $consecutivoComprobante)
                                ->where('company_id', $company->id)
                                ->with('items')
                                ->first();
            if(isset($invoice)){
                $this->invoice_id = $invoice->id;
                $this->save();
                return [
                    'status'  => '400',
                    'mensaje' => 'Factura existente.'
                ];
            }
    
            $fechaEmision = Carbon::createFromFormat("Y-m-d", $invoiceData['TxnDate']);
            $fechaVencimiento = Carbon::createFromFormat("Y-m-d", $invoiceData['DueDate']);
            
            $condicionVenta = "01";
            $metodoPago = '01';
            $idMoneda = $invoiceData['CurrencyRef'] ?? 'CRC';
            
            if( $idMoneda == 'USD' ){
                $tipoCambio = QuickbooksInvoice::getExchangeRate($fechaEmision);
            }else{
                $idMoneda = "CRC";
                $tipoCambio = 1;
            }
            
            
            $descripcion = isset($invoiceData['CustomerMemo']) ? $invoiceData['CustomerMemo'] : '';
            $porcentajeIVA = 13;
            if( isset($invoiceData['TxnTaxDetail']) ){
                $taxCode = $invoiceData['TxnTaxDetail']['TxnTaxCodeRef'];
                if( !$taxCode ){
                    $taxCode = 'default';
                }
                $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
                $codigoEtax = $qb->taxes_json['tipo_iva'][$taxCode];
                $categoriaHacienda = $qb->taxes_json['tipo_producto'][$taxCode];
                try{
                    $porcentajeIVA = $invoiceData['TxnTaxDetail']['TaxLine']['TaxLineDetail']['TaxPercent'];
                }catch(\Exception $e){}
            }
            
            //Datos de lineas
            $i = 0;
            $totalDocumento = 0;
            $invoiceList = array();
            foreach($items as $item){
                $i++;
                $detail = $item['SalesItemLineDetail'];   
                $product = null;
                if( $detail ){
                    $product = QuickbooksProduct::getProductByRef($company, $detail['ItemRef']);
                }
                //Revisa que no sean las lineas de diferencial cambiario ni tarifa general
                if ( $product ) {
                    $numeroLinea = $i;
                    $codigoProducto = $product->code;
                    $detalleProducto = $product->description;
                    $unidadMedicion = $product->measure_unit;
                    
                    $cantidad = $detail['Qty'];
                    $precioUnitario = $detail['UnitPrice'];
                    $montoDescuento = $detail['DiscountAmt'];
                    
                    $subtotalLinea = $cantidad*$precioUnitario - $montoDescuento;
                    $montoIva = $subtotalLinea * ($porcentajeIVA/100);
                    $totalLinea = $subtotalLinea+$montoIva;
                    $categoriaHacienda = null;
                    $montoExoneracion = isset($documentoExoneracion) ? $montoIva : 0;
                    $totalMontoLinea = $subtotalLinea + $montoIva - $montoExoneracion - $montoDescuento;
                    
                    $cantidad = round($cantidad, 5);
                    $precioUnitario = round($precioUnitario, 5);
                    $subtotalLinea = round($subtotalLinea, 5);
                    $montoIva = round($montoIva, 5);
                    $totalLinea = round($totalLinea, 5);
                    $montoDescuento = round($montoDescuento, 5);
                    $totalMontoLinea = round($totalMontoLinea, 5);
                    $totalDocumento += $totalMontoLinea;
                    
                    $arrayInsert = array(
                        'metodoGeneracion' => $metodoGeneracion,
                        'idEmisor' => $company->id_number,
                        'haciendaStatus' => '03',
                        /****Empiezan datos cliente***/
                        'nombreCliente' => $nombreCliente,
                        'descripcion' => $descripcion,
                        'codigoCliente' => $codigoCliente,
                        'tipoPersona' => $tipoPersona,
                        'identificacionCliente' => $identificacionCliente,
                        'correoCliente' => $correoCliente,
                        'telefonoCliente' => $telefonoCliente,
                        'direccion'     => $direccion,
                        'zip'     => $zip,
                        /****Empiezan datos factura***/
                        'claveFactura' => $claveFactura,
                        'consecutivoComprobante' => $consecutivoComprobante,
                        'numeroReferencia' => $numeroReferencia,
                        'condicionVenta' => $condicionVenta,
                        'metodoPago' => $metodoPago,
                        'numeroLinea' => $numeroLinea,
                        'fechaEmision' => $fechaEmision->format('d/m/Y'),
                        'fechaVencimiento' => $fechaVencimiento->format('d/m/Y'),
                        'moneda' => $idMoneda,
                        'tipoCambio' => $tipoCambio,
                        'totalDocumento' => $totalDocumento,
                        'totalNeto' => 0,
                        'otherReference' => null,
                        /**** Empiezan datos lineas ****/
                        'cantidad' => $cantidad,
                        'precioUnitario' => $precioUnitario,
                        'porcentajeIva' => $porcentajeIVA,
                        'totalLinea' => $totalLinea,
                        'montoIva' => $montoIva,
                        'codigoEtax' => $codigoEtax,
                        'montoDescuento' => $montoDescuento,
                        'subtotalLinea' => $subtotalLinea,
                        'tipoDocumento' => $tipoDocumento,
                        'codigoProducto' => $codigoProducto,
                        'detalleProducto' => $detalleProducto,
                        'unidadMedicion' => $unidadMedicion,
                        'tipoDocumentoExoneracion' => null,
                        'documentoExoneracion' => null,
                        'companiaExoneracion' => null,
                        'porcentajeExoneracion' => 0,
                        'montoExoneracion' => 0,
                        'impuestoNeto' => 0,
                        'totalMontoLinea' => $totalMontoLinea,
                        'xmlSchema' => $xmlSchema,
                        'codigoActividad' => $codigoActividad,
                        'categoriaHacienda' => $categoriaHacienda,
                        'partidaArancelaria' => null,
                        'acceptStatus' => true,
                        'isAuthorized' => true,
                        'codeValidated' => true
                    );
                    $invoiceList = Invoice::importInvoiceRow($arrayInsert, $invoiceList, $company);
                }
                
            }
            foreach($invoiceList as $fac){
               \App\Jobs\ProcessSingleInvoiceImport::dispatchNow($fac);
               $newInvoice = Invoice::where('company_id', $company->id)
                                ->where('document_number', $fac['factura']['document_number'])
                                ->where('document_key', $fac['factura']['document_key'])
                                ->where('client_id_number', $fac['factura']['client_id_number'])
                                ->first();
                if($newInvoice){
                    $this->invoice_id = $newInvoice->id;
                    $this->save();
                }
            }
        }catch(\Exception $e){
            Log::error($e);
            return back()->withError( "Error al sincronizar factura QB a eTax. Por favor contacte a soporte. " . $e->getMessage() );
        }
    }
    
    
    
    
    public static function getExchangeRate($date)
    {
        
        $cacheKey = "usd_rate-".$date->format('d/m/Y');
        $lastRateKey = "last_usd_rate";
        try {
            if ( !Cache::has($cacheKey) ) {

                $client = new \GuzzleHttp\Client();
                $response = $client->get( config('etax.exchange_url'),
                    [
                        'query' => [
                            'Indicador' => '318',
                            'FechaInicio' => $date->format('d/m/Y'),
                            'FechaFinal' => $date->format('d/m/Y'),
                            'Nombre' => config('etax.namebccr'),
                            'SubNiveles' => 'N',
                            'CorreoElectronico' => config('etax.emailbccr'),
                            'Token' => config('etax.tokenbccr'),
                        ],
                        'timeout' => 15,
                        'connect_timeout' => 15,
                        'read_timeout' => 15,
                    ]
                );
                
                $body = $response->getBody()->getContents();
                $xml = new \SimpleXMLElement($body);
                $xml->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                $tables = $xml->xpath('//INGC011_CAT_INDICADORECONOMIC[@d:id="INGC011_CAT_INDICADORECONOMIC1"]');
                $valor =  json_decode($tables[0]->NUM_VALOR);

                Cache::put($cacheKey, $valor, now()->addHours(2));
                Cache::put($lastRateKey, $valor, now()->addDays(3));
            }

            $value = Cache::get($cacheKey);
            return $value;

        } catch( \Exception $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: ');
            $value = Cache::get($lastRateKey);
            return $value;
        } catch (RequestException $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: '.
                $e->getResponse()->getReasonPhrase());
            $value = Cache::get($lastRateKey);
            return $value;
        }

    }
    
}
