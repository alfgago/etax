<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Bill;
use App\Company;
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
        $bills = Bill::where('company_id', $current_company)->orderBy('generated_date', 'DESC')->paginate(10);
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
        $bill->payment_receipt = "VOUCHER-123451234512345";
        $bill->generation_method = "M";
      
        //Datos de proveedor
        $bill->provider_id = $request->provider_id;
        
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
          $is_exempt = false;
          
          $bill->addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $is_exempt );
        }
      
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
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' ){
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
        if( $bill->generation_method != 'M' ){
          return redirect('/facturas-recibidas');
        }
      
        $company = $bill->company;
      
        //Datos generales y para Hacienda
        $bill->sale_condition = $request->sale_condition;
        $bill->payment_type = $request->payment_type;
        $bill->credit_time = $request->credit_time;
        $bill->buy_order = $request->buy_order;
        $bill->other_reference = $request->other_reference;
        $bill->hacienda_status = "01";
        $bill->payment_status = "01";
        $bill->payment_receipt = "VOUCHER-123451234512345";
        $bill->generation_method = "M";
      
        //Datos de proveedor
        $bill->provider_id = $request->provider_id;
        $bill->send_emails = $request->send_emails;
        
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
          $is_exempt = false;
          
          $item_modificado = $bill->addEditItem( $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $is_exempt );

          array_push( $lids, $item_modificado->id );
        }
      
        foreach ( $bill->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
      
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
        
        foreach ( $bill->lineas as $linea ) {
          $linea->delete();
        }
        $bill->delete();
        return redirect('/facturas-recibidas');
    }
}
