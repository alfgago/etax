<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Actividades;
use App\OtherCharges;
use App\Provider;
use App\AvailableInvoices;
use App\CodigosPaises;
use App\UnidadMedicion;
use App\ProductCategory;
use App\CodigoIvaRepercutido;
use App\CalculatedTax;
use App\XlsInvoice;
use App\Utils\BridgeHaciendaApi;
use App\Utils\InvoiceUtils;
use \Carbon\Carbon;
use App\Invoice;
use App\InvoiceItem;
use App\Bill;
use App\BillItem;
use App\Company;
use App\Client;

class ProcessInvoicesExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $xlsInvoice = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($xlsInvoice)
    {
        $this->xlsInvoice = $xlsInvoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            //Log::debug( 'Log de xlsInvoice: ' . json_encode($this->xlsInvoice) );
            $xlsInvoice = $this->xlsInvoice;
            $company = Company::find($xlsInvoice->company_id);
            /*$xlsInvoices = XlsInvoice::select('consecutivo', 'company_id','autorizado')
                ->where('company_id',$company->id)->where('autorizado',1)->distinct('consecutivo')->get();*/
            $lineas = XlsInvoice::where('company_id', $xlsInvoice->company_id)->where('consecutivo', $xlsInvoice->consecutivo)->get();
            $factura = $lineas[0];
            Log::debug( 'Log de fac envio masivo: ' . json_encode($lineas) );
            //dd($factura);

            $apiHacienda = new BridgeHaciendaApi();
                    
                    try{
                        
                        $invoice = new Invoice();
    
                        $invoice->company_id = $company->id;
                        $invoice->document_type = $factura->tipoDocumento;
                        $invoice->hacienda_status = '01';
                        $invoice->payment_status = "01";
                        $invoice->payment_receipt = "";
                        $invoice->generation_method = "etax-masivo";
                        $invoice->xml_schema = 43;
                        if ($invoice->document_type == '01') {
                            $invoice->reference_number = $company->last_invoice_ref_number + 1;
                        }
                        if ($invoice->document_type == '08') {
                            $invoice->reference_number = $company->last_invoice_pur_ref_number + 1;
                        }
                        if ($invoice->document_type == '09') {
                            $invoice->reference_number = $company->last_invoice_exp_ref_number + 1;
                        }
                        if ($invoice->document_type== '02') {
                            $invoice->reference_number = $company->last_debit_note_ref_number + 1;
                        }
                        if ($invoice->document_type == '03') {
                            $invoice->reference_number= $company->last_note_ref_number + 1;
                        }
                        if ($invoice->document_type == '04') {
                            $invoice->reference_number = $company->last_ticket_ref_number + 1;
                        }
                        /*$invoice->document_key = $factura->document_key;
                        $invoice->document_number = $factura->document_number;*/
                        $invoice->sale_condition = $factura->condicionVenta;
                        $invoice->description = $factura->descripcion;
                        $invoice->payment_type = $factura->medioPago;
                        $invoice->credit_time = $factura->plazoCredito;
                        if ($factura->codigoActividad) {
                            $invoice->commercial_activity = $factura->codigoActividad;
                        }
                        /******************************************************************************/
                        $tipoPersona = $factura->tipoIdentificacionReceptor;
                        if($tipoPersona == "5" || $tipoPersona == "05" || $tipoPersona == 5){
                            $tipoPersona = "E";
                        }
                        if($tipoPersona == "E" || $tipoPersona == "O"){
                            $identificacionCliente = $factura->identificacionReceptor;
                        }else{
                            $identificacionCliente = preg_replace("/[^0-9]/", "", $factura->identificacionReceptor );
                        }
                        
                        $client = Client::updateOrCreate(
                            [
                                'id_number' => $identificacionCliente,
                                'company_id' => $company->id,
                            ],
                            [
                                'company_id' => $company->id,
                                'tipo_persona' => $tipoPersona,
                                'id_number' => trim($identificacionCliente),
                                'fullname' => $factura->nombreReceptor,
                                'first_name' => $factura->nombreReceptor,
                                'emisor_receptor' => 'ambos',
                                'state' => $factura->provinciaReceptor,
                                'city' => $factura->cantonReceptor,
                                'district' => $factura->distritoReceptor,
                                'address' => trim($factura->direccionReceptor),
                                'email' => trim($factura->correoReceptor),
                            ]
                        );
                        $invoice->client_id = $client->id;
                        $factura->tipoCambio = $factura->tipoCambio ? $factura->tipoCambio : 1;
                        //Datos de factura
                        $invoice->description = $factura->notas ?? null;
                        $invoice->subtotal = floatval( str_replace(",","", $factura->subtotal ));
                        $invoice->currency = $factura->codigoMoneda;
                        $invoice->currency_rate = floatval( str_replace(",","", $factura->tipoCambio ));
                        $invoice->total = floatval( str_replace(",","", $factura->totalComprobante ));
                        $invoice->iva_amount = floatval( str_replace(",","", $factura->totalImpuesto ));
    
                        $invoice->client_first_name = $client->first_name;
                        $invoice->client_last_name = $client->last_name;
                        $invoice->client_last_name2 = $client->last_name2;
                        $invoice->client_email = $client->email;
                        $invoice->client_address = $client->address;
                        $invoice->client_country = $client->country;
                        $invoice->client_state = $client->state;
                        $invoice->client_city = $client->city;
                        $invoice->client_district = $client->district;
                        $invoice->client_zip = $client->zip;
                        $invoice->client_phone = preg_replace('/[^0-9]/', '', $client->phone);
                        $invoice->client_id_number = $client->id_number;
                        $invoice->client_id_type = $client->tipo_persona;
                        
                        if( !isset($invoice->client_zip) 
                            && isset($invoice->client_state) 
                            && isset($invoice->client_city) 
                            && isset($invoice->client_district) 
                        ){
                            $invoice->client_zip = $invoice->client_state . str_pad($invoice->client_city, 2, '0', STR_PAD_LEFT) . str_pad($invoice->client_district, 2, '0', STR_PAD_LEFT);
                        }
    
                        //Fechas
                        $fecha = Carbon::createFromFormat('d/m/Y g:i A',  $factura->fechaEmision);
                        $invoice->generated_date = $fecha;
                        $fechaV = Carbon::createFromFormat('d/m/Y g:i A', $factura->fechaVencimiento);
                        $invoice->due_date = $fechaV;
                        $invoice->year = $fecha->year;
                        $invoice->month = $fecha->month;
                        $invoice->credit_time = $fechaV->format('d/m/Y');
    
                        $invoice->reference_doc_type = $factura->tipoDocumentoReferencia;
                        $invoice->reference_document_key = $factura->numeroDocumentoReferencia;
                        $invoice->reference_generated_date = $factura->fechaEmisionReferencia;
                        $invoice->code_note = $factura->codigoNota;
                        $invoice->reason = $factura->razonNota;
    
                        $invoice->document_key = getDocumentKey($invoice->document_type, $company);
                        $invoice->document_number = getDocReference($invoice->document_type,$company);
                        $start_date = Carbon::parse(now('America/Costa_Rica'));
                        $today = $start_date->year."-".$start_date->month."-".$start_date->day;
                        $fechaComparacion = $fecha->year."-".$fecha->month."-".$fecha->day;
    
                        if($today < $fechaComparacion){
                            $invoice->hacienda_status = '99';
                            $invoice->generation_method = "etax-programada";
                            $invoice->document_key = $invoice->document_key."programada";
                            $invoice->document_number = "programada";
                        }
                        $invoice->save();
                        
                        $subtotal = 0;
                        $totaliva = 0;
                        $totalComprobante = 0;
                        foreach ($lineas as $linea) {
                            if($linea->tipoLinea == 1){
                                
                                $lineaUnitario = $linea->precioUnitario ?? 0;
                                $lineaCantidad = $linea->cantidad ? trim($linea->cantidad) : 1;
                                $lineaSubtotal = (isset($linea->subTotal) && $linea->subTotal != 0) ? $linea->subTotal : $lineaUnitario*$lineaCantidad;
                                
                                $item = InvoiceItem::updateOrCreate([
                                    'item_number' => $linea->numeroLinea,
                                    'invoice_id' => $invoice->id
                                ], [
                                    'company_id' => $invoice->company_id,
                                    'year'  => $invoice->year,
                                    'month' => $invoice->month,
                                    'name'  => $linea->detalle ? trim($linea->detalle) : null,
                                    'measure_unit' => $linea->unidadMedida ?? 'Unid',
                                    'item_count'   => $lineaCantidad,
                                    'unit_price'   => $lineaUnitario,
                                    'subtotal'     => $lineaSubtotal,
                                    'total' => $linea->montoTotalLinea ?? 0,
                                    'discount_type' => $linea->naturalezaDescuento ?? null,
                                    'discount' => $linea->montoDescuento ?? 0,
                                    'iva_type' => $linea->codigoImpuesto ?? null,
                                    'product_type' => $linea->codigoTarifa ?? null,
                                    'iva_percentage' => intval($linea->tarifaImpuesto) ?? 0,
                                    'iva_amount' => $linea->montoImpuesto ?? 0,
                                    'tariff_heading' => null,
                                    'is_code_validated' => true,
                                    'is_exempt' => $linea->exento
                                    ]
                                );
    
                                $subtotal = $subtotal + $lineaSubtotal;
                                $totaliva = $totaliva + $linea->montoImpuesto;
                                //$totalComprobante = $totalComprobante + $subtotal + $linea->montoImpuesto;
                                try {
                                    $exonerationDate = isset($linea->fechaEmisionExoneracion )  ? Carbon::createFromFormat('d/m/Y', $linea->fechaEmisionExoneracion) : null;
                                }catch( \Exception $e ) {
                                    $exonerationDate = null;
                                }
                                if ($exonerationDate && $linea->tipoDocumentoExoneracion && $linea->numeroDocumentoExoneracion && $linea->porcentajeExoneracionExoneracion > 0) {
    
                                    $item->exoneration_document_type = $linea->tipoDocumentoExoneracion ?? null;
                                    $item->exoneration_document_number = $linea->numeroDocumentoExoneracion ?? null;
                                    $item->exoneration_company_name = $linea->nombreInstitucionExoneracion ?? null;
                                    $item->exoneration_porcent = $linea->porcentajeExoneracionExoneracion ?? 0;
                                    $item->exoneration_amount = $linea->montoExoneracionExoneracion ?? 0;
                                    $item->exoneration_date = $exonerationDate;
                                    $item->exoneration_total_amount = $linea->montoExoneracionExoneracion ?? 0;
                                    $item->exoneration_total_gravado = (($item->item_count * $item->unit_price) * $item->exoneration_porcent) / 100 ;
                                    $item->impuesto_neto = $linea->montoImpuesto ?? $linea->montoImpuesto - $linea->montoExoneracionExoneracion;
                                }
    
                                $item->save();
                            }else{
                                OtherCharges::updateOrCreate([
                                    'item_number' => $linea->numeroLinea,
                                    'invoice_id' => $invoice->id
                                ], 
                                [
                                    'company_id' => $invoice->company_id,
                                    'year'  => $invoice->year,
                                    'month' => $invoice->month,
                                    'document_type' => $linea->tipoCargo ?? '99',
                                    'provider_id_number' =>  $linea->identidadTercero ? trim( $linea->identidadTercero) : null,
                                    'provider_name'   => $linea->nombreTercero ? trim($linea->nombreTercero) : null,
                                    'description'   => $linea->detalleCargo ? trim($linea->detalleCargo) : null,
                                    'percentage'   =>$linea->porcentajeCargo ?? 0,
                                    'amount'   => $linea->montoCargo ?? 0,
                                ]
                                );
    
                                $totalComprobante = $totalComprobante + $linea->montoCargo;
                            }
                        }
                        $totalComprobante = $subtotal + $totaliva;
                        $invoice->subtotal = $subtotal;
                        $invoice->iva_amount = $totaliva;
                        $invoice->total = $totalComprobante;
                        
                        $invoice->save();
                        
                        if ($invoice->document_type == '08' ) {
                         
                            $bill = new Bill();
                            $bill->company_id = $company->id;
                            //Datos generales y para Hacienda
                            $bill->document_type = "01";
                            $bill->hacienda_status = "03";
                            $bill->status = "02";
                            $bill->payment_status = "01";
                            $bill->payment_receipt = "";
                            $bill->generation_method = "etax-masivo";
                            $bill->reference_number = $company->last_bill_ref_number + 1;
    
                            
                          $bill->document_key = $invoice->document_key;
                          $bill->document_number = $invoice->document_number;
                          $bill->sale_condition = $invoice->sale_condition;
                          $bill->payment_type = $invoice->payment_type;
                          $bill->credit_time = $invoice->credit_time;
                        
                          $bill->xml_schema =  43;
    
                              $identificacion_provider = preg_replace("/[^0-9]/", "", $invoice->id_number );
                              
                              $provider = Provider::firstOrCreate(
                                  [
                                      'id_number' => $identificacion_provider,
                                      'company_id' => $invoice->company_id,
                                  ],
                                  [
                                      'company_id' => $invoice->company_id,
                                      'id_number' => $identificacion_provider
                                  ]
                              );
                              $provider->first_name = $invoice->client_first_name ?? null;
                              $provider->last_name = $invoice->client_last_name ?? null;
                              $provider->last_name2 = $invoice->client_last_name2 ?? null;
                              $provider->country = $invoice->client_country ?? null;
                              $provider->state = $invoice->client_state ?? null;
                              $provider->city = $invoice->client_city ?? null;
                              $provider->district = $invoice->client_district ?? null;
                              $provider->neighborhood = $invoice->client_neighborhood ?? null;
                              $provider->zip = $invoice->client_zip ?? null;
                              $provider->address = $invoice->client_address ?? null;
                              $provider->foreign_address = $invoice->client_foreign_address ?? null;
                              $provider->phone = $invoice->client_phone ?? null;
                              $provider->es_exento = $invoice->client_es_exento ?? 0;
                              $provider->email = $invoice->client_email ?? null;
                              $provider->save();
                                  
                              $bill->provider_id = $provider->id;
                              //Datos de factura
                              $bill->description = $invoice->description;
                              $bill->subtotal = floatval( str_replace(",","", $invoice->subtotal ));
                              $bill->currency = $invoice->currency;
                              $bill->currency_rate = floatval( str_replace(",","", $invoice->currency_rate ));
                              $bill->total = floatval( str_replace(",","", $invoice->total ));
                              $bill->iva_amount = floatval( str_replace(",","", $invoice->iva_amount ));
                              
                              $bill->provider_first_name = $provider->first_name;
                              $bill->provider_last_name = $provider->last_name;
                              $bill->provider_last_name2 = $provider->last_name2;
                              $bill->provider_email = $provider->email;
                              $bill->provider_address = $provider->address;
                              $bill->provider_country = $provider->country;
                              $bill->provider_state = $provider->state;
                              $bill->provider_city = $provider->city;
                              $bill->provider_district = $provider->district;
                              $bill->provider_zip = $provider->zip;
                              $bill->provider_phone = $provider->phone;
                              $bill->provider_id_number = $provider->id_number;
                              //Fechas
                              $fecha =  $invoice->generated_date ;
                              $bill->generated_date = $fecha;
                              $fechaV =  $invoice->due_date ;
                              $bill->due_date = $fechaV;
                              
                              $bill->year = $invoice->year;
                              $bill->month = $invoice->month;
                              $bill->xml_schema = 43;
                            
                              $bill->activity_company_verification = $invoice->commercial_activity;
    
                            
                            $bill->is_code_validated = 1;
                            $bill->accept_status = 1;
                            $bill->accept_iva_condition = '01';
                            $bill->accept_iva_acreditable = $bill->iva_amount;
                            $bill->accept_iva_gasto = 0;
                            $bill->description = "FEC" . ($invoice->description ?? '');
                            if($today <= $fechaComparacion){
                                $bill->hacienda_status = '99';
                                $bill->generation_method = "etax-programada";
                                $bill->document_key = $invoice->document_key."programada";
                                $bill->document_number = "programada";
                            }
                            $bill->save();
    
                            if($invoice->hacienda_status != "99"){
                                $company->last_bill_ref_number = $bill->reference_number;
                            }
    
                        }
                        
                        if($invoice->hacienda_status != "99"){
                            $invoice->company->addSentInvoice( $invoice->year, $invoice->month );
                            $company->setLastReference($invoice->document_type, $invoice->reference_number);
                            /*if ($invoice->document_type == '01') {
                                $company->last_invoice_ref_number = $invoice->reference_number;
                                try{
                                    $company->last_document = getDocumentKey('01', $company);
                                }catch(\Exception $e){}
                            }
                            if ($invoice->document_type == '08') {
                                $company->last_invoice_pur_ref_number = $invoice->reference_number;
                            }
                            if ($invoice->document_type== '02') {
                                $company->last_debit_note_ref_number = $invoice->reference_number;
                            }
                            if ($invoice->document_type == '03') {
                               $company->last_note_ref_number = $invoice->reference_number;
                            }
                            if ($invoice->document_type == '09') {
                                $company->last_invoice_exp_ref_number = $invoice->reference_number;
                            }
                            if ($invoice->document_type == '04') {
                               $company->last_ticket_ref_number = $invoice->reference_number;
                            }*/
                        }else{
                            //En el else guarda. No guarda en el if, porque la funcion setLastReference se encarga de guardar la empresa.
                            $company->save();
                        }
                        clearInvoiceCache($invoice);
                    
                    }catch( \Exception $e ){
                        Log::error("Error en el job ENVIO MASIVO EXCEL:" . $e);
                    }
                //}
                
            XlsInvoice::where('company_id', $xlsInvoice->company_id)->where('consecutivo', $xlsInvoice->consecutivo)->delete();
        } catch ( \Exception $e) {
            Log::error("Error en el job ENVIO MASIVO EXCEL:" . $e);
        }
    
    }

}
