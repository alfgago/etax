<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Bill;
use App\Company;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = Bill::all();
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
        $empresa = Company::first();
        $bill->empresa_id = $empresa->id;
      
        //Datos generales y para Hacienda
        $invoice->document_type = "01";
        $invoice->invoice_key = "50601021900310270242900100001010000000162174804809";
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
      
        //Datos de proveedor
        $bill->proveedor = $request->proveedor;
        
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
      
        return redirect('/bills');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show(Bill $bill)
    {
        //
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
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->metodo_generacion != 'M' ){
          return redirect('/bills');
        }
      
        $empresa = $bill->empresa;
      
        //Datos generales y para Hacienda
        $bill->tipo_documento = "01";
        $bill->clave_factura = "50601021900310270242900100001010000000162174804809";
        //$bill->correos_envio = $request->correos_envio;
        $invoice->sale_condition = $request->sale_condition;
        $invoice->payment_type = $request->payment_type;
        $invoice->credit_time = $request->credit_time;
        $invoice->buy_order = $request->buy_order;
        $invoice->other_reference = $request->other_reference;
        $invoice->hacienda_status = "01";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "VOUCHER-123451234512345";
        $invoice->generation_method = "M";
      
        //Datos de proveedor
        $bill->proveedor = $request->proveedor;
        
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
          
          $item_modificado = $invoice->addEditItem( $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $is_exempt );

          array_push( $lids, $item_modificado->id );
        }
      
        foreach ( $invoice->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
      
        return redirect('/bills');
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
        foreach ( $bill->lineas as $linea ) {
          $linea->delete();
        }
        $bill->delete();
        return redirect('/bills');
    }
}
