<?php

namespace App\Http\Controllers;

use App\CybersourcePaymentProcessor;
use App\PaymentMethod;
use App\PaymentProcessor;
use App\Team;
use App\User;
use App\Utils\PaymentUtils;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Mpociot\Teamwork\TeamworkTeam;

/**
 * @group Controller - Métodos de Pago
 *
 * Funciones de PaymentMethodController.
 */
class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $team = Team::where('company_id', currentCompany())->first();
        if(auth()->user()->id != $team->owner_id){
            return redirect()->back()->withErrors('Su usuario no tiene acceso a esta vista' );
        }
        $cantidad = PaymentMethod::where('user_id', auth()->user()->id)->get()->count();
        return view('payment_methods/index')->with('cantidad', $cantidad);
    }
    /**
     * indexData
     *
     *
     */
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
    /**
     * createView
     *
     *
     */
    public function createView(){
        $user = auth()->user();
        if($user->zip){
            return view('payment_methods/CreatePaymentMethod');
        }else{
            return redirect()->back()->withErrors('Debe actualizar la información de su usuario para continuar');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $user = auth()->user();
        $request->number = preg_replace('/\s+/', '',  $request->number);
        $paymentProcessor = new PaymentProcessor();
        $payment_gateway = new CybersourcePaymentProcessor();
        if(isset($request->number)){
            $ip = $paymentProcessor->getUserIpAddr();
            //$request->request->add(['IpAddress' => $ip]);
            $request->request->add(['IpAddress' => '170.81.34.78']);
            $request->request->add(['cardCity' => $request->street1]);
            $request->request->add(['cardState' => 'San Jose']);
            $request->request->add(['country' => 'CR']);
            $request->request->add(['zip' => $user->zip]);
            $request->request->add(['email' => $user->email]);
            $request->request->add(['user_id' => $user->id]);

            $newCard = $payment_gateway->createCardToken($request);
            if ($newCard->decision === 'ACCEPT') {
                $paymentMethod = PaymentMethod::create([
                    'user_id' => $user->id,
                    'name' => $request->first_name_card,
                    'last_name' => $request->last_name_card,
                    'last_4digits' => substr($request->number, -4),
                    'masked_card' => $paymentProcessor->getMaskedCard($request->number),
                    'due_date' => $request->expiry,
                    'token_bn' => $newCard->paySubscriptionCreateReply->subscriptionID,
                    'payment_gateway' => 'cybersource'
                ]);
                $cantidad = PaymentMethod::where('user_id', auth()->user()->id)->get()->count();

                return redirect('/payments-methods')->withMessage('Método de pago creado')->with('cantidad', $cantidad);
            } else {
                return redirect()->back()->withErrors('No se aprobó esta tarjeta');
            }
        }else{
            return redirect()->back()->withErrors('Debe ingresar todos los datos');
        }
    }
    /**
     *paymentMethodTokenUpdateView
     *
     *
     */
    public function paymentMethodTokenUpdateView($id){
        $paymentMethod = PaymentMethod::find($id);
        return view('payment_methods/updatePaymentMethods')->with('paymentMethod', $paymentMethod)
            ->with('Id', $id);
    }
    /**
     *tokenUpdate
     *
     *
     */
    public function tokenUpdate(Request $request){
        $paymentMethod = PaymentMethod::find($request->Id);
        $user = auth()->user();

        $request->number = preg_replace('/\s+/', '', $request->number);
        $request->request->add(['user_name' => $user->user_name]);
        $request->request->add(['user_id' => $user->id]);
        $request->request->add(['token' => $paymentMethod->token_bn]);
        $paymentProcessor = new PaymentProcessor();
        $ip = $paymentProcessor->getUserIpAddr();
        $request->request->add(['IpAddress' => $ip]);
        $paymentGateway = $paymentProcessor->selectPaymentGateway($paymentMethod->payment_gateway);
        $updatedCard = $paymentGateway->updateCardToken($request);
        $ip = $paymentProcessor->getUserIpAddr();
        $request->request->add(['IpAddress' => $ip]);
        if (gettype($updatedCard) == 'array') {
            if ($updatedCard['apiStatus'] !== 'Successful') {
                return redirect()->back()->withError('No se actualizó el método de pago');
            }
        } else if (gettype($updatedCard) == 'object'){
            if ($updatedCard->decision !== 'ACCEPT') {
                return redirect()->back()->withError('No se actualizó el método de pago');
            }
        }
        $paymentMethod->last_4digits = substr($request->number, -4);
        $paymentMethod->due_date = $request->expiry;
        $paymentMethod->updated_by = $user->id;
        $paymentMethod->masked_card = $paymentProcessor->getMaskedCard($request->number);
        $paymentMethod->token_bn =  $updatedCard->paySubscriptionUpdateReply->subscriptionID ?? $paymentMethod->token_bn;

        $paymentMethod->save();
        $cantidad = PaymentMethod::where('user_id', auth()->user()->id)->get()->count();
        return view('payment_methods/index')->with('cantidad', $cantidad);
    }
    /**
     *tokenDelete
     *
     *
     */
    public function tokenDelete($Id){
        $paymentMethod = PaymentMethod::find($Id);
        $paymentProcessor =  new PaymentProcessor($paymentMethod->payment_gateway);
        $paymetGateway = $paymentProcessor->selectPaymentGateway();
        $user = auth()->user();

        $this->authorize('update', $paymentMethod);
        $tokenDeleted = $paymetGateway->deletePaymentMethod($paymentMethod->id);

    }
    /**
     *deactivateOtherMethods
     *
     *
     */
    public function deactivateOtherMethods(){
        $user = auth()->user();
        $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        foreach($paymentMethods as $paymentMethod){
            $paymentMethod->default_card = false;
            $paymentMethod->save();
        }
    }
    /**
     * updateDefault
     *
     *
     */
    public function updateDefault($id){
        $paymentUtils = new PaymentUtils();
        $user = auth()->user();
        $paymentMethod = PaymentMethod::find($id);
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
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
                $update = $this->deactivateOtherMethods();
                $paymentMethod->default_card = true;
                $paymentMethod->save();
                return redirect()->back()->withMessage('Método de pago actualizado');
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
    public function destroy($id){
        $paymentMethod = PaymentMethod::findOrFail($id);
        $this->authorize('update', $paymentMethod);
        $paymentMethod->delete();

        $cantidad = PaymentMethod::where('user_id', auth()->user()->id)->get()->count();

        return redirect('/payments-methods')->withMessage('Método de pago eliminado')->with('cantidad', $cantidad);
    }
}
