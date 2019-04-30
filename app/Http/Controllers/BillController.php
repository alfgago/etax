<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Bill;
use App\BillItem;
use App\Company;
use App\Provider;
use App\CalculatedTax;
use App\Http\Controllers\CacheController;
use App\Exports\BillExport;
use App\Imports\BillImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class BillController extends Controller
{
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['receiveEmailBills']] );
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_company = currentCompany();
        $bills = Bill::where('company_id', $current_company)->where('is_void', false)->with('provider')->sortable(['generated_date' => 'desc'])->paginate(10);
        return view('Bill/index', [
          'bills' => $bills
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("Bill/create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $bill = new Bill();
        $company = currentCompanyModel();
        $bill->company_id = $company->id;

        //Datos generales y para Hacienda
        $bill->document_type = "01";
        $bill->hacienda_status = "01";
        $bill->payment_status = "01";
        $bill->payment_receipt = "";
        $bill->generation_method = "M";
        $bill->reference_number = $company->last_bill_ref_number + 1;
        
        $bill->setBillData($request);
        
        $company->last_bill_ref_number = $bill->reference_number;
        $company->save();
        
        clearBillCache($bill);
      
        return redirect('/facturas-recibidas');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show(Bill $bill)
    {
        $this->authorize('update', $bill);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
      
        //Valida que la factura recibida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
          return redirect('/facturas-recibidas');
        }  
      
        return view('Bill/edit', compact('bill') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
          return redirect('/facturas-emitidas');
        }
      
        $bill->setBillData($request);
        
        clearBillCache($bill);
        
        return redirect('/facturas-recibidas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bill = Bill::find($id);
        $this->authorize('update', $bill);
        
        clearBillCache($bill);
        
        $bill->is_void = true;
        $bill->save();
        
        return redirect('/facturas-recibidas');
    }
    
    
    public function export() {
        return Excel::download(new BillExport(), 'documentos-recibidos.xlsx');
    }

    public function import() {
        
        request()->validate([
          'archivo' => 'required',
          'tipo_archivo' => 'required',
        ]);
      
        $time_start = $this->microtime_float();
        
        $collection = Excel::toCollection( new BillImport(), request()->file('archivo') );
        $company = currentCompany();
        
        if( $collection[0]->count() < 5001 ){
            try {
                foreach ($collection[0]->chunk(200) as $facturas) {
                    \DB::transaction(function () use ($facturas, &$company) {
                        
                        $inserts = array();    
                        foreach ($facturas[0] as $row){
                            //Datos de proveedor
                            $codigo_proveedor = $row['codigoproveedor'] ? $row['codigoproveedor'] : '';
                            $nombre_proveedor = $row['nombreproveedor'];
                            $tipo_persona = $row['tipoidentificacion'];
                            $identificacion_proveedor = $row['identificacionproveedor'];
                            $proveedor = Provider::firstOrCreate(
                                [
                                    'id_number' => $identificacion_proveedor,
                                    'company_id' => $company->id,
                                ],
                                [
                                    'code' => $codigo_proveedor ,
                                    'company_id' => $company->id,
                                    'tipo_persona' => str_pad($tipo_persona, 2, '0', STR_PAD_LEFT),
                                    'id_number' => $identificacion_proveedor,
                                    'first_name' => $nombre_proveedor
                                ]
                            );
                                
                            $bill = Bill::firstOrNew(
                                [
                                    'company_id' => $company->id,
                                    'provider_id' => $proveedor->id,
                                    'total' => $row['totaldocumento'],
                                    'document_number' => $row['consecutivocomprobante']
                                ]
                            );
                            
                            if( !$bill->exists ) {
                                
                                $bill->company_id = $company->id;
                                $bill->provider_id = $proveedor->id;    
                        
                                //Datos generales y para Hacienda
                                $bill->document_type = $row['idtipodocumento'];
                                $bill->reference_number = $company->last_bill_ref_number + 1;
                                $bill->document_number =  $row['consecutivocomprobante'];
                                
                                //Datos generales
                                $bill->sale_condition = str_pad($row['condicionventa'], 2, '0', STR_PAD_LEFT);
                                $bill->payment_type = str_pad($row['metodopago'], 2, '0', STR_PAD_LEFT);
                                $bill->credit_time = 0;
                                
                                /*$bill->buy_order = $row['ordencompra'] ? $row['ordencompra'] : '';
                                $bill->other_reference = $row['referencia'] ? $row['referencia'] : '';
                                $bill->other_document = $row['documentoanulado'] ? $row['documentoanulado'] : '';
                                $bill->hacienda_status = $row['estadohacienda'] ? $row['estadohacienda'] : '01';
                                $bill->payment_status = $row['estadopago'] ? $row['estadopago'] : '01';
                                $bill->payment_receipt = $row['comprobantepago'] ? $row['comprobantepago'] : '';*/
                                
                                $bill->generation_method = "XLSX";
                                
                                //Datos de factura
                                $bill->currency = $row['idmoneda'];
                                if( $bill->currency == 1 ) { $bill->currency = "CRC"; }
                                if( $bill->currency == 2 ) { $bill->currency = "USD"; }
                                    
                                
                                $bill->currency_rate = $row['tipocambio'];
                                //$bill->description = $row['description'] ? $row['description'] : '';
                              
                                $company->last_bill_ref_number = $bill->reference_number;
                                
                                $bill->generated_date = Carbon::createFromFormat('d/m/Y', $row['fechaemision']);
                                $bill->due_date = Carbon::createFromFormat('d/m/Y', $row['fechaemision'])->addDays(15);
                                
                                $bill->subtotal = 0;
                                $bill->iva_amount = 0;
                                $bill->total = $row['totaldocumento'];
                                
                                $bill->save();
                            }     
                            
                            $year = $invoice->generated_date->year;
                            $month = $invoice->generated_date->month;
                            
                            /**LINEA DE FACTURA**/
                            $item = BillItem::firstOrNew(
                                [
                                    'bill_id' => $bill->id,
                                    'item_number' => $row['numerolinea'] ? $row['numerolinea'] : 1,
                                ]
                            );
                            
                            if( !$item->exists ) {
                                $bill->subtotal = $bill->subtotal + (float)$row['subtotallinea'];
                                $bill->iva_amount = $bill->iva_amount + (float)$row['montoiva'];
                                
                                $inserts[] = [
                                    'bill_id' => $bill->id,
                                    'company_id' => $company->id,
                                    'year' => $year,
                                    'month' => $month,
                                    'item_number' => $row['numerolinea'] ? $row['numerolinea'] : 0,
                                    'code' => $row['codigoproducto'],
                                    'name' => $row['detalleproducto'],
                                    'product_type' => 1,
                                    'measure_unit' => $row['unidadmedicion'],
                                    'item_count' => $row['cantidad'] ? $row['cantidad'] : 1,
                                    'unit_price' => $row['preciounitario'],
                                    'subtotal' => $row['subtotallinea'],
                                    'total' => $row['totallinea'],
                                    'discount_type' => '01',
                                    'discount' => $row['montodescuento'] ? $row['montodescuento'] : 0,
                                    'discount_reason' => '',
                                    'iva_type' => $row['codigoimpuesto'],
                                    'porc_identificacion_plena' => $row['porcidentificacionplena'] ? $row['porcidentificacionplena'] : 13,
                                    'iva_amount' => $row['montoiva'] ? $row['montoiva'] : 0,
                                ];
                                
                            }
                            /**END LINEA DE FACTURA**/
                            
                            clearBillCache($bill);
                            $bill->save();
                        }
                        
                        BillItem::insert($inserts);
                    });
                    
                }
            }catch( \ErrorException $ex ){
                return back()->withError('Por favor verifique que su documento de excel contenga todas las columnas indicadas. Mensaje:' . $ex->getMessage());
            }catch( \InvalidArgumentException $ex ){
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Por favor verifique que los campos de fecha estén correctos. Formato: "dd/mm/yyyy : 01/01/2018"');
            }catch( \Exception $ex ){
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Error en la fila. Mensaje:' . $ex->getMessage());
            }
        
        $company->save();
        
        $time_end = $this->microtime_float();
        $time = $time_end - $time_start;
        
        return redirect('/facturas-recibidas')->withMessage('Facturas importados exitosamente en '.$time.'s');
        }else{
            return redirect('/facturas-emitidas')->withError('Usted tiene un límite de 5000 facturas por archivo.');
        }
        
    }
    
    public function receiveEmailBills(Request $request) {
        $file = $request->file('attachment1');
        
        $path = \Storage::putFile(
            "correos", $file
        );
        
        return response()->json([
            'path' => $path,
            'success' => 'Exito',
            'error' => 'Resource not found'
        ], 200);
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }   
    
}
