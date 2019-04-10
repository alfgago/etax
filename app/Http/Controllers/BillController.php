<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Bill;
use App\BillItem;
use App\Company;
use App\Provider;
use App\CalculatedTax;
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
        $this->middleware('auth');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_company = auth()->user()->companies->first()->id;
        $bills = Bill::where('company_id', $current_company)->with('provider')->sortable(['generated_date' => 'desc'])->paginate(10);
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
        $company = auth()->user()->companies->first();
        $bill->company_id = $company->id;
      
        //Datos generales y para Hacienda
        $bill->document_type = "01";
        $bill->document_key = "50601021900310270242900100001010000000162174804809";
        $bill->reference_number = $company->reference_number + 1;
        $numero_doc = ((int)$company->document_number) + 1;
        $bill->document_number = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
        $bill->sale_condition = $request->sale_condition;
        $bill->payment_type = $request->payment_type;
        $bill->credit_time = $request->credit_time;
        $bill->buy_order = $request->buy_order;
        $bill->other_reference = $request->other_reference;
        $bill->hacienda_status = "01";
        $bill->payment_status = "01";
        $bill->payment_receipt = "";
        $bill->generation_method = "M";
      
        //Datos de proveedor
        if( $request->provider_id == '-1' ){
            $tipo_persona = $request->tipo_persona;
            $identificacion_provider = $request->id_number;
            $codigo_provider = $request->code;
            
            $provider = Provider::firstOrCreate(
                [
                    'id_number' => $identificacion_provider,
                    'company_id' => $company->id,
                ],
                [
                    'code' => $codigo_provider ,
                    'company_id' => $company->id,
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
            $provider->phone = $request->phone;
            $provider->es_exento = $request->es_exento;
            $provider->email = $request->email;
            $provider->save();
                
            $bill->provider_id = $provider->id;
        }else{
            $bill->provider_id = $request->provider_id;
        }
        
        //Datos de factura
        $bill->description = $request->description;
        $bill->subtotal = $request->subtotal;
        $bill->currency = $request->currency;
        $bill->currency_rate = $request->currency_rate;
        $bill->total = $request->total;
        $bill->iva_amount = $request->iva_amount;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->generated_date . ' ' . $request->hora);
        $bill->generated_date = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
        $bill->due_date = $fechaV;
      
        $bill->save();

        foreach($request->items as $item){
          $item_number = $item['item_number'];
          $code = $item['code'];
          $name = $item['name'];
          $product_type = $item['product_type'];
          $measure_unit = $item['measure_unit'];
          $item_count = $item['item_count'];
          $unit_price = $item['unit_price'];
          $subtotal = $item['subtotal'];
          $total = $item['total'];
          $discount_percentage = '0';
          $discount_reason = '';
          $iva_type = $item['iva_type'];
          $iva_percentage = $item['iva_percentage'];
          $iva_amount = $item['iva_amount'];
          $porc_identificacion_plena = $item['porc_identificacion_plena'];
          $is_exempt = false;
          
          $bill->addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $porc_identificacion_plena, $is_exempt );
        }
        
        $this->clearBillCache($bill);
      
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
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' && $bill->generation_method != 'XML' ){
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
      
        //Valida que la factura recibida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' && $bill->generation_method != 'XML' ){
          return redirect('/facturas-recibidas');
        } 
      
        $company = $bill->company;
      
        //Datos generales y para Hacienda
        $bill->sale_condition = $request->sale_condition;
        $bill->payment_type = $request->payment_type;
        $bill->credit_time = $request->credit_time;
        $bill->buy_order = $request->buy_order;
        $bill->other_reference = $request->other_reference;
      
        //Datos de proveedor
        if( $request->provider_id == '-1' ){
            $tipo_persona = $request->tipo_persona;
            $identificacion_provider = $request->id_number;
            $codigo_provider = $request->code;
            
            $provider = Provider::firstOrCreate(
                [
                    'id_number' => $identificacion_provider,
                    'company_id' => $company->id,
                ],
                [
                    'code' => $codigo_provider ,
                    'company_id' => $company->id,
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
            $provider->phone = $request->phone;
            $provider->es_exento = $request->es_exento;
            $provider->email = $request->email;
            $provider->save();
                
            $bill->provider_id = $provider->id;
        }else{
            $bill->provider_id = $request->provider_id;
        }
        
        //Datos de factura
        $bill->description = $request->description;
        $bill->subtotal = $request->subtotal;
        $bill->currency = $request->currency;
        $bill->currency_rate = $request->currency_rate;
        $bill->total = $request->total;
        $bill->iva_amount = $request->iva_amount;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->generated_date . ' ' . $request->hora);
        $bill->generated_date = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
        $bill->due_date = $fechaV;
      
        $bill->save();
      
        //Recorre las items de factura y las guarda
        $lids = array();
        foreach($request->items as $item) {
          $item_id = $item['id'] ? $item['id'] : 0;
          $item_number = $item['item_number'];
          $code = $item['code'];
          $name = $item['name'];
          $product_type = $item['product_type'];
          $measure_unit = $item['measure_unit'];
          $item_count = $item['item_count'];
          $unit_price = $item['unit_price'];
          $subtotal = $item['subtotal'];
          $total = $item['total'];
          $discount_percentage = '0';
          $discount_reason = '';
          $iva_type = $item['iva_type'];
          $iva_percentage = $item['iva_percentage'];
          $iva_amount = $item['iva_amount'];
          $porc_identificacion_plena = $item['porc_identificacion_plena'];
          $is_exempt = false;
          
          $item_modificado = $bill->addEditItem( $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $porc_identificacion_plena, $is_exempt );

          array_push( $lids, $item_modificado->id );
        }
      
        foreach ( $bill->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
        
        $this->clearBillCache($bill);
        
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
        
        $this->clearBillCache($bill);
        
        //Valida que la factura sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' && $bill->generation_method != 'XML' ){
          return redirect('/facturas-recibidas');
        } 
        
        foreach ( $bill->items as $linea ) {
          $linea->delete();
        }
        $bill->delete();
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
        
        $facturas = Excel::toCollection( new BillImport(), request()->file('archivo') );
        $company = auth()->user()->companies->first();
        
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
                
                $bill->subtotal = 0;
                $bill->iva_amount = 0;
                $bill->total = $row['totaldocumento'];
                
                $bill->save();
            }     
            
          
            /**LINEA DE FACTURA**/
            $item = BillItem::firstOrNew(
                [
                    'bill_id' => $bill->id,
                    'item_number' => $row['numerolinea'] ? $row['numerolinea'] : 1,
                ],
                [
                'bill_id' => $bill->id,
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
                ]
            );
            
            if( !$item->exists ) {
                $bill->subtotal = $bill->subtotal + (float)$row['subtotallinea'];
                $bill->iva_amount = $bill->iva_amount + (float)$row['montoiva'];
                $item->save();
            }
            /**END LINEA DE FACTURA**/
            
            $bill->generated_date = Carbon::createFromFormat('d/m/Y', $row['fechaemision']);
            $bill->due_date = Carbon::createFromFormat('d/m/Y', $row['fechaemision'])->addDays(15);
            
            $this->clearBillCache($bill);
            
            $bill->save();
            
        }
        $company->save();
        
        $time_end = $this->microtime_float();
        $time = $time_end - $time_start;
        
        return redirect('/facturas-recibidas')->withMessage('Facturas importados exitosamente en '.$time.'s');
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }   
    
    private function clearBillCache($bill){
        $month = $bill->generatedDate()->month;
        $year = $bill->dueDate()->year;
        CalculatedTax::clearTaxesCache($bill->company_id, $month, $year);
        CalculatedTax::clearTaxesCache($bill->company_id, 0, $year);
    }
    
    
}
