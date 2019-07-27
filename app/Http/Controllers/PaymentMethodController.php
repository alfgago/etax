<?php

namespace App\Http\Controllers;

use App\PaymentMethod;
use App\Utils\PaymentUtils;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $cantidad = PaymentMethod::where('user_id', $user->id)->get()->count();
        if($cantidad){
            return view('payment_methods/index')->with('cantidad', $cantidad);
        }else{
            return redirect()->back()->withErrors('No existen metodos de pago para este usuario');
        }
    }

    public function indexData(){
        $user = auth()->user();
        $query = PaymentMethod::where('user_id', $user->id);
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($paymentMethod) {
                return view('payment_methods.actions', [
                    'data' => $paymentMethod
                ])->render();
            })
            ->editColumn('name', function(PaymentMethod $paymentMethod) {
                return $paymentMethod->name . ' ' . $paymentMethod->last_name;
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function createView(){
        return view('payment_methods/CreatePaymentMethod');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $user = auth()->user();
        $request->number = preg_replace('/\s+/', '',  $request->number);
        $paymentUtils = new PaymentUtils();
        $cards = array(
            $request->number
        );
        foreach ($cards as $c) {
            $check = $paymentUtils->checkCC($c, true);
            $typeCard = $check;
        }
        if(isset($typeCard)){
            $bnStatus = $paymentUtils->statusBNAPI();
            if ($bnStatus['apiStatus'] == 'Successful') {
                switch ($typeCard) {
                    case "Visa":
                        $cardType = '001';
                        $nameCard = "Visa";
                        break;
                    case "Mastercard":
                        $cardType = '002';
                        $nameCard = "Mastercard";
                        break;
                    case "American Express":
                        $cardType = '003';
                        $nameCard = "";
                        break;
                }
                $cardYear = substr($request->expiry, -2);
                $cardMonth = substr($request->expiry, 0, 2);
                $cardBn = new Client();
                $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                    'headers' => [
                        'Content-Type' => "application/json",
                    ],
                    'json' => [
                        'applicationName' => config('etax.klap_app_name'),
                        'userName' => $user->user_name,
                        'userPassword' => 'Etax-' . $user->id . 'Klap',
                        'cardDescription' => $nameCard,
                        'primaryAccountNumber' => $request->number,
                        "expirationMonth" => $cardMonth,
                        "expirationYear" => '20' . $cardYear,
                        "verificationValue" => $request->cvc
                    ],
                    'verify' => false,
                ]);

                $card = json_decode($cardCreationResult->getBody()->getContents(), true);
                if ($card['apiStatus'] == 'Successful') {
                    $last_4digits = substr($request->number, -4);
                    $paymentMethod = PaymentMethod::create([
                        'user_id' => $user->id,
                        'name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'last_4digits' => $last_4digits,
                        'masked_card' => $card['maskedCard'],
                        'due_date' => $request->cardMonth . ' ' . $request->cardYear,
                        'token_bn' => $card['cardTokenId']
                    ]);
                    return redirect()->back()->withMessage('Metodo de pago creado');
                } else {
                    return redirect()->back()->withErrors('No se aprobó esta tarjeta');
                }
            } else {
                return redirect()->back()->withErrors('Pagos en línea está fuera de servicio en este momento. No se pudo gestionar la transacción');
            }
        }else{
            return redirect()->back()->withErrors('Solamente aceptamos Visa y Master-Card');
        }
    }

    public function paymentMethodTokenUpdateView($id){
        $subscription = getCurrentSubscription();
        $paymentMethod = PaymentMethod::find($id);
        return view('payment_methods/updatePaymentMethods')->with('paymentMethod', $paymentMethod)
            ->with('Id', $id);
    }
    public function tokenUpdate(Request $request){
        $paymentUtils = new PaymentUtils();
        $request->number = preg_replace('/\s+/', '',  $request->number);
        $cards = array(
            $request->number
        );
        foreach($cards as $c){
            $check = $paymentUtils->checkCC($c, true);
            $typeCard = $check;
        }
        switch ($typeCard){
            case "Visa":
                $cardType = '001';
                break;
            case "Mastercard":
                $cardType = '002';
                break;
            case "American Express":
                $cardType = '003';
                break;
        }
        $user = auth()->user();
        $paymentMethod = PaymentMethod::find($request->Id);
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $cardBn = new Client();
            $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserUpdateCard?applicationName=string&userName=string&userPassword=string&cardTokenId=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => [
                    'applicationName' => config('etax.klap_app_name'),
                    'userName' => $user->user_name,
                    'userPassword' => 'Etax-' . $user->id . 'Klap',
                    'cardTokenId' => $paymentMethod->token_bn,
                    "cardDescription" => $typeCard,
                    "primaryAccountNumber" => $request->number,
                    "expirationMonth" => $request->cardMonth,
                    "expirationYear" => '20' . $request->cardYear,
                    "verificationValue" => $request->cvc
                ],
                'verify' => false,
            ]);
            $card = json_decode($cardCreationResult->getBody()->getContents(), true);
            if($card['isApproved'] == true) {
                $last_4digits = substr($request->number, -4);
                $due_date = $request->cardMonth . ' ' . $request->cardYear;
                $paymentMethod->last_4digits = $last_4digits;
                $paymentMethod->due_date = $due_date;
                $paymentMethod->updated_by = $user->id;
                $paymentMethod->masked_card = $card['masked_card'];
                $paymentMethod->save();
                return redirect()->back()->withMessage('Metodo de pago actualizado');
            }else{
                return redirect()->back()->withError('No se pudo actualizar el metodo de pago');
            }
        }else{
            return redirect()->back()->withError('Transacción no disponible en este momento');
        }
    }

    public function tokenDelete($Id){
        $paymentUtils = new PaymentUtils();
        $user = auth()->user();
        $paymentMethod = PaymentMethod::find($Id);
        $this->authorize('update', $paymentMethod);
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $cardBn = new Client();
            $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserDeleteCard", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => [
                    'applicationName' => config('etax.klap_app_name'),
                    'userName' => $user->user_name,
                    'userPassword' => 'Etax-' . $user->id . 'Klap',
                    'cardTokenId' => $paymentMethod->token_bn
                ],
                'verify' => false,
            ]);
            $card = json_decode($cardCreationResult->getBody()->getContents(), true);
            if($card['apiStatus'] == 'Successful') {
                $paymentMethod->updated_by = $user->id;
                $paymentMethod->save();
                $paymentMethod->delete();
                return redirect()->back()->withMessage('Metodo de pago eliminado');
            }else{
                return redirect()->back()->withError('No se pudo eliminar el método de pago');
            }
        }else{
            return redirect()->back()->withError('Transacción no disponible en este momento');
        }
    }

    public function UpdateAllMethods(){
        $user = auth()->user();
        $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        foreach($paymentMethods as $paymentMethod){
            $paymentMethod->default_card = 1;
            $paymentMethod->save();
        }
    }

    public function updateDefault($Id){
        $paymentUtils = new PaymentUtils();
        $user = auth()->user();
        $paymentMethod = PaymentMethod::find($Id);
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $update = $this->UpdateAllMethods();
            $cardBn = new Client();
            $cardUpdateDefault = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserSetDefaultCard?applicationName=string&userName=string&cardTokenId=string", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => [
                    'applicationName' => config('etax.klap_app_name'),
                    'userName' => $user->user_name,
                    'userPassword' => 'Etax-' . $user->id . 'Klap',
                    'cardTokenId' => $paymentMethod->token_bn
                ],
                'verify' => false,
            ]);
            $card = json_decode($cardUpdateDefault->getBody()->getContents(), true);
            if($card['apiStatus'] == "Successful"){
                $paymentMethod->default_card = 0;
                $paymentMethod->save();
                return redirect()->back()->withMessage('Metodo de pago actualizado');
            }else{
                return redirect()->back()->withError('No se pudo actualizar el registro  ');
            }
        }
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
     * @param  \App\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        //
    }
}
