<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Invoice;
use App\Company;
use Illuminate\Http\Request;

class InvoiceController extends Controller
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
        $invoices = Invoice::where('company_id', $current_company)->get();
        return view('Invoice/index', [
          'invoices' => $invoices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("Invoice/create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $invoice = new Invoice();
        $company = auth()->user()->companies->first();
        $invoice->company_id = $company->id;

        //Datos generales y para Hacienda
        $invoice->document_type = "01";
        $invoice->document_key = "50601021900310270242900100001010000000162174804809";
        $invoice->reference_number = $company->reference_number + 1;
        $numero_doc = ((int)$company->document_number) + 1;
        $invoice->document_number = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
        $invoice->sale_condition = $request->sale_condition;
        $invoice->payment_type = $request->payment_type;
        $invoice->credit_time = $request->credit_time;
        $invoice->buy_order = $request->buy_order;
        $invoice->other_reference = $request->other_reference;
        $invoice->hacienda_status = "01";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "VOUCHER-123451234512345";
        $invoice->generation_method = "M";
      
        //Datos de cliente
        $invoice->client_name = $request->client_name;
        $invoice->client_id_type = $request->client_id_type;
        $invoice->client_id = $request->client_id;
        $invoice->client_is_exempt = $request->client_is_exempt ? true : false;
        $invoice->send_emails = $request->send_emails;
        
        //Datos de factura
        $invoice->description = $request->description;
        $invoice->subtotal = $request->subtotal;
        $invoice->currency = $request->currency;
        $invoice->currency_rate = $request->currency_rate;
        $invoice->total = $request->total;
        $invoice->iva_amount = $request->iva_amount;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->generated_date . ' ' . $request->hora);
        $invoice->generated_date = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
        $invoice->due_date = $fechaV;
      
        $invoice->save();
      
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
          
          $invoice->addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $is_exempt );
        }
      
        return redirect('/facturas-emitidas');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $invoice->generation_method != 'M' ){
          return redirect('/facturas-emitidas');
        }  
      
        return view('Invoice/edit', compact('invoice') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $invoice->generation_method != 'M' ){
          return redirect('/facturas-emitidas');
        }
      
        $company = $invoice->company; 
      
        //Datos generales y para Hacienda
        $invoice->sale_condition = $request->sale_condition;
        $invoice->payment_type = $request->payment_type;
        $invoice->credit_time = $request->credit_time;
        $invoice->buy_order = $request->buy_order;
        $invoice->other_reference = $request->other_reference;
        $invoice->hacienda_status = "01";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "VOUCHER-123451234512345";
        $invoice->generation_method = "M";
      
        //Datos de cliente
        $invoice->client_name = $request->client_name;
        $invoice->client_id_type = $request->client_id_type;
        $invoice->client_id_temp = $request->client_id_temp;
        $invoice->client_is_exempt = $request->client_is_exempt ? true : false;
        $invoice->send_emails = $request->send_emails;
        
        //Datos de factura
        $invoice->description = $request->description;
        $invoice->subtotal = $request->subtotal;
        $invoice->currency = $request->currency;
        $invoice->currency_rate = $request->currency_rate;
        $invoice->total = $request->total;
        $invoice->iva_amount = $request->iva_amount;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->generated_date . ' ' . $request->hora);
        $invoice->generated_date = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
        $invoice->due_date = $fechaV;
      
        $invoice->save();
      
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
          
          $item_modificado = $invoice->addEditItem( $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $is_exempt );

          array_push( $lids, $item_modificado->id );
        }
      
        foreach ( $invoice->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
      
        return redirect('/facturas-emitidas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        $this->authorize('update', $invoice);
        
        foreach ( $invoice-items as $item ) {
          $item->delete();
        }
        $invoice->delete();
        return redirect('/facturas-emitidas');
    }
}
