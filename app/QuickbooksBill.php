<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use \Carbon\Carbon;
use App\Bill;
use App\Provider;
use App\Quickbooks;
use App\QuickbooksProvider;
use App\Company;
use QuickBooksOnline\API\Facades\Bill as qbBill;

class QuickbooksBill extends Model
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

    public function bill(){
        return $this->belongsTo(Bill::class);
    }
    
    //Devuelve la fecha de generación en formato Carbon
    public function generatedDate()
    {
        return Carbon::parse($this->qb_date);
    }
    
    public static function getMonthlyBills($dataService, $year, $month, $company) {
        $cachekey = "qb-bills-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $count = QuickbooksBill::where('company_id', $company->id)->count();
            if( $count > 0 ){
                $dateFrom = Carbon::createFromDate($year, $month, 1)->firstOfMonth()->toAtomString();
                $dateTo = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toAtomString();
                $bills = $dataService->Query("SELECT * FROM Bill WHERE MetaData.LastUpdatedTime >= '$dateFrom' AND MetaData.LastUpdatedTime <= '$dateTo'");
            }else{
                $bills = $dataService->Query("SELECT * FROM Bill");
            }
            Cache::put($cachekey, $bills, 30); //Cache por 15 segundos.
        }else{
            $bills = Cache::get($cachekey);
        }
        return $bills;
    }
    
    public static function syncMonthlyBills($dataService, $year, $month, $company){
        QuickbooksProvider::syncMonthlyProviders($dataService, $year, $month, $company);
        $qbBills = QuickbooksBill::getMonthlyBills($dataService, $year, $month, $company);
        $qbBills = $qbBills ?? [];
        $facturas = [];

        foreach($qbBills as $qbBill){
            $date = Carbon::createFromFormat("Y-m-d", $qbBill->TxnDate);
            $clientName = QuickbooksProvider::getProviderName($company, $qbBill->VendorRef);
            $qbBill = QuickBooksBill::updateOrCreate(
              [
                "qb_id" => $qbBill->Id
              ],
              [
                "qb_doc_number" => $qbBill->DocNumber,
                "qb_date" => $date,
                "qb_total" => $qbBill->TotalAmt,
                "qb_client" => $clientName,
                "qb_data" => $qbBill,
                "generated_at" => 'quickbooks',
                "month" => $month,
                "year" => $year,
                "company_id" => $company->id
              ]
            );
        }
    }
    
    public static function saveEtaxaqb($dataService, $bill, $accountRef = null){   
        try{
            $company = $bill->company;
            $lines = [];
            foreach($bill->items as $item){
                $itemRef = null;
                $itemName = null;
                $product = Product::find($item->product_id);
                if( !isset($product) ){
                    $product = Product::updateOrCreate(
                        [
                            'company_id' => $bill->company_id,
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
                $unitPrice = round($item->unit_price + ($item->iva_amount/$item->item_count), 5);
                $Amount = $qty*$unitPrice;
                $lines[] = [
                     "Amount" => $Amount,
                     "DetailType" => "SalesItemLineDetail",
                     "SalesItemLineDetail" => [
                        "UnitPrice" => $unitPrice,
                        "Qty" => $qty,
                        "ItemRef" => [
                          "value" => $itemRef,
                          "name" => $itemName
                        ]
                     ]
                ];
            }
            
            $qbVendor = QuickbooksProvider::where('company_id', $bill->company_id)
                                            ->where('client_id', $bill->client_id)->first();
            if(!isset($qbVendor)){
                $qbVendor = QuickbooksProvider::saveEtaxaqb($dataService, $bill->client);
            }
            if( !isset($qbVendor->qb_id) ){
                return back()->withError( "Error al guardar proveedor ". $bill->client_first_name ." en QuickBooks, por favor contacte a soporte o intente hacer la sincronización del proveedor. Mensaje: ".$qbVendor->getIntuitErrorMessage() );
            }
            
            $params = [
                "Line" => $lines,
                "VendorRef" => [
                      "value" => $qbVendor->qb_id,
                      "name" => $qbVendor->full_name
                ],
                "BillEmail" => [
                      "Address" => $bill->email
                ]
            ];
            
            $theResourceObj = qbBill::create($params);

            $qbBill = $dataService->Add($theResourceObj);
            $error = $dataService->getLastError();
            if ($error) {
                Log::error("The Status code is: " . $error->getHttpStatusCode() . "\n".
                            "The Helper message is: " . $error->getOAuthHelperError() . "\n".
                            "The Response message is: " . $error->getResponseBody() . "\n");
                $qbBills = $dataService->Query("SELECT * FROM Bill WHERE CompanyName = '$fullname'");
                $qbBill = $qbBills[0] ?? null;
            }
            if( isset($qbBill) ){
                $date = Carbon::createFromFormat("Y-m-d", $qbBill->TxnDate);
                $clientName = QuickbooksProvider::getProviderName($company, $qbBill->VendorRef);
                $inv = QuickBooksBill::updateOrCreate(
                  [
                    "qb_id" => $qbBill->Id
                  ],
                  [
                    "qb_doc_number" => $qbBill->DocNumber,
                    "qb_date" => $date,
                    "qb_total" => $qbBill->TotalAmt,
                    "qb_client" => $clientName,
                    "qb_data" => $qbBill,
                    "generated_at" => 'quickbooks',
                    "month" => $bill->month,
                    "year" => $bill->year,
                    "company_id" => $company->id,
                    "bill_id" => $bill->id
                  ]
                );
                return $inv;
            }
            return $error;
        }catch(\Exception $e){
            return back()->withError( "Error al sincronizar factura: $bill->document_number. Por favor contacte a soporte." );
            Log::error($e);
            return $e;
        }
    }
    
    
    
    public function saveQbaetax(){
        try{
            $company = $this->company;
            $billData = $this->qb_data;
            $items = $billData["Line"];
            
            $metodoGeneracion = "quickbooks";
            $xmlSchema = 43;
            $codigoActividad = $company->getActivities() ? $company->getActivities()[0]->codigo : '0';
            
            $vendor = QuickbooksProvider::getProviderInfo($company, $billData['VendorRef']);
            if( !$vendor ){
                return [
                    'status'  => '400',
                    'mensaje' => 'No se encontró el proveedor. Verifique que el proveedor se encuentre correctamente sincronizado e indique el tipo de persona y código postal.'
                ];
            }
            $proveedor = $vendor->client;
            
            if( isset($data['BillAddr']) ){
                  try{
                    $zip = $billData['BillAddr']['PostalCode'] ?? '10101';
                    $state = $zip[0];
                    $city = $zip[1] . $zip[2];
                    $district = $zip;
                  }catch( \Throwable $e ){ }  
                  $countryCode = $billData['BillAddr']['CountryCode'] ?? 'CR';
                  $address = $billData['BillAddr']['City'] ?? "" . " " . $billData['BillAddr']['Line1'] ?? "" . " " . $billData['BillAddr']['Line2'] ?? "";
            }
            
            if( isset($data['PrimaryPhone']) ){
                $phone = $data['PrimaryPhone']["FreeFormNumber"];
            }
            
            if( isset($data['BillEmail']) ){
                $email = $data['BillEmail']["Address"];
            }
            
            $nombreProvidere = $proveedor->fullname;
            $codigoProvidere = $proveedor->codigo;
            $tipoPersona = $proveedor->tipo_persona ?? '01'; //SOLUCIONAR - QB no incluye tipo de persona
            $identificacionProvidere = $proveedor->id_number ?? '0000'; //SOLUCIONAR - QB no incluye cédula
            $correoProvidere = $email ?? $proveedor->email;
            $telefonoProvidere = $phone ?? $proveedor->phone;
            $direccion = $address ?? $proveedor->address;
            $codProvincia = $state ?? $proveedor->state;
            $codCanton = $city ?? $proveedor->city;
            $codDistrito = $district ?? $proveedor->district;
            $zip = $zip ?? $proveedor->zip;
            
            //Define el tipo de documento
            $tipoDocumento = $tipoPersona == "F" || $tipoPersona == '1' || $tipoPersona == '01' ? '01' : '04';
            $tipoDocumento = '01';
            if( !$codDistrito || !$zip ){
                $tipoDocumento = "04";
            }
            
            $numeroReferencia = $billData['DocNumber'];
            $consecutivoComprobante = $billData['DocNumber'];
            $claveFactura = "QB-".getDocumentKey($tipoDocumento, $company, $numeroReferencia);
    
            $bill = Bill::where('document_number', $consecutivoComprobante)
                                ->where('company_id', $company->id)
                                ->with('items')
                                ->first();
            if(isset($bill)){
                $this->bill_id = $bill->id;
                $this->save();
                return [
                    'status'  => '400',
                    'mensaje' => 'Factura existente.'
                ];
            }
    
            $fechaEmision = Carbon::createFromFormat("Y-m-d", $billData['TxnDate']);
            $fechaVencimiento = Carbon::createFromFormat("Y-m-d", $billData['DueDate']);
            
            $condicionVenta = "01";
            $metodoPago = '01';
            $idMoneda = $billData['CurrencyRef'] ?? 'CRC';
            
            if( $idMoneda == 'USD' ){
                $tipoCambio = QuickbooksBill::getExchangeRate($fechaEmision);
            }else{
                $idMoneda = "CRC";
                $tipoCambio = 1;
            }
            
            
            $descripcion = isset($billData['VendorMemo']) ? $billData['VendorMemo'] : '';
            $porcentajeIVA = 13;
            if( isset($billData['TxnTaxDetail']) ){
                $taxCode = $billData['TxnTaxDetail']['TxnTaxCodeRef'];
                if( !$taxCode ){
                    $taxCode = 'default';
                }
                $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
                $codigoEtax = $qb->taxes_json['tipo_iva'][$taxCode];
                $categoriaHacienda = $qb->taxes_json['tipo_producto'][$taxCode];
                try{
                    $porcentajeIVA = $billData['TxnTaxDetail']['TaxLine']['TaxLineDetail']['TaxPercent'];
                }catch(\Exception $e){}
            }
            
            //Datos de lineas
            $i = 0;
            $totalDocumento = 0;
            $billList = array();
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
                        /****Empiezan datos proveedor***/
                        'nombreProvidere' => $nombreProvidere,
                        'descripcion' => $descripcion,
                        'codigoProvidere' => $codigoProvidere,
                        'tipoPersona' => $tipoPersona,
                        'identificacionProvidere' => $identificacionProvidere,
                        'correoProvidere' => $correoProvidere,
                        'telefonoProvidere' => $telefonoProvidere,
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
                    $billList = Bill::importBillRow($arrayInsert, $billList, $company);
                }
                
            }
            foreach($billList as $fac){
               \App\Jobs\ProcessSingleBillImport::dispatchNow($fac);
               $newBill = Bill::where('company_id', $company->id)
                                ->where('document_number', $fac['factura']['document_number'])
                                ->where('document_key', $fac['factura']['document_key'])
                                ->where('client_id_number', $fac['factura']['client_id_number'])
                                ->first();
                if($newBill){
                    $this->bill_id = $newBill->id;
                    $this->save();
                }
            }
        }catch(\Exception $e){
            dd($e);
            Log::error($e);
        }
    }
    
    
    
    
    public static function getExchangeRate($date)
    {
        
        $cacheKey = "usd_rate-".$date->format('d/m/Y');
        $lastRateKey = "last_usd_rate";
        try {
            if ( !Cache::has($cacheKey) ) {

                $client = new \GuzzleHttp\Provider();
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
