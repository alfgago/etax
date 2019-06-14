<?php

namespace App\Http\Controllers;

use App\PaymentMethod;
use App\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Payment;
use App\Utils\PaymentUtils;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Sales/index');
    }

    public function indexData(){
        $user = auth()->user();
        $query = Sales::where('user_id', $user->id);
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($sale) {
                return view('payment.actions', [
                    'data' => $sale
                ])->render();
            })
            ->editColumn('recurrency', function(Sale $sale) {
                return $sale->recurrency . ' meses';
            })
        ->rawColumns(['actions'])
        ->toJson();
    }

    public function salesNewView(){
        return view('Sales/salesNewView');
    }

    public function salesNew(Request $request){
        $bnStatus = PaymentUtils::statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $user = auth()->user();
            $current_company = currentCompany();
            $defaultCard = PaymentMethod::where('user_id', $user->user_id)->
                                            where('default_card', 1)->first();
            $date = Carbon::parse(now('America/Costa_Rica'));
            $sale = Sale::create([
                "user_id" => $user->user_id,
                "company_id" => $current_company,
                "etax_product_id" => $request->etax_product_id,
                "status" => 1,
                "recurrency" => 0
            ]);
            $payment = Payment::create([
                'sale_id' => $sale->id,
                'payment_date' => $date,
                'payment_status' => 1,
                'amount' => $request->amount
            ]);
            if(isset($defaultCard) && $defaultCard != ''){
                $data = new stdClass();
                $data->description = 'Compra productos eTax';
                $data->amount = $request->amount;
                $data->user_name = $user->user_name;
                $data->cardTokenId = $defaultCard->token_bn;
                $newSale = Payment::paymentCharge($data);

                $payment->proof = $newSale['cardTokenId'];
            }
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Sales $sales)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sales $sales)
    {
        //
    }
}
