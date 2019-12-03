<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use App\Company;
use App\CybersourcePaymentProcessor;
use App\PaymentMethod;
use App\PaymentProcessor;
use App\Team;
use App\User;
use App\Utils\PaymentUtils;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if(auth()->user()->id != $team->owner_id  && !in_array(8, auth()->user()->permisos())){
            return redirect()->back()->withErrors('Su usuario no tiene acceso a esta vista' );
        }
        $user  = auth()->user();
        if(in_array(8, auth()->user()->permisos())){
                $email = substr($user->email, 0, -3);
                $user = User::where('email',$email)->first();
            }
        $cantidad = PaymentMethod::where('user_id', $user->id)->get()->count();
        return view('payment_methods/index')->with('cantidad', $cantidad);
    }
    /**
     * indexData
     *
     *
     */
    public function indexData(){
        $user = auth()->user();
        
        if(in_array(8, auth()->user()->permisos())){
                $email = substr($user->email, 0, -3);
                $user = User::where('email',$email)->first();
            }
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
        $company = Company::where('user_id', $user->id)->first();
        if($user->zip){
            return view('payment_methods/CreatePaymentMethod')->with('company', $company);
        }else{
            return redirect()->back()->withErrors('Debe actualizar la información de su usuario para continuar');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        try {
            $user = auth()->user();
            if(in_array(8, auth()->user()->permisos())){
                $email = substr($user->email, 0, -3);
                $user = User::where('email',$email)->first();
            }
            Log::info("Creating new paymethod user id: $user->id");
            $request->number = preg_replace('/\s+/', '',  $request->number);
            $paymentProcessor = new PaymentProcessor();
            $payment_gateway = new CybersourcePaymentProcessor();
            if(isset($request->number)){
                $ip = $paymentProcessor->getUserIpAddr();
                $request->request->add(['IpAddress' => $ip]);
                $request->request->add(['cardCity' => $request->cardCity]);
                $request->request->add(['cardState' => $request->cardState]);
                $request->request->add(['country' => 'CR']);
                $request->request->add(['zip' => $request->zip]);
                $request->request->add(['email' => $user->email]);
                $request->request->add(['user_id' => $user->id]);
                $request->request->add(['deviceFingerPrintID' => $request->deviceFingerPrintID]);

                $newCard = $payment_gateway->createCardToken($request);
                if (isset($newCard->token_bn)) {
                    $cantidad = PaymentMethod::where('user_id', auth()->user()->id)->get()->count();

                    return redirect('/payments-methods')->withMessage('Método de pago creado')->with('cantidad', $cantidad);
                } else {
                    return redirect()->back()->withErrors('No se aprobó esta tarjeta');
                }
            } else {
                return redirect()->back()->withErrors('Debe ingresar todos los datos');
            }
        } catch ( \Exception $e) {
            Log::error("Error creando tarjeta: -->>" .$e);
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
    public function tokenDelete($Id) {
        try {
            $paymentMethod = PaymentMethod::find($Id);
            $paymentProcessor =  new PaymentProcessor();
            $paymetGateway = $paymentProcessor->selectPaymentGateway($paymentMethod->payment_gateway);

            $this->authorize('update', $paymentMethod);
            $tokenDeleted = $paymetGateway->deletePaymentMethod($paymentMethod->id);
            $cantidad = PaymentMethod::where('user_id', auth()->user()->id)->get()->count();

            return redirect('/payments-methods')->withMessage('Método de pago eliminado')->with('cantidad', $cantidad);
        } catch (\Exception $e) {
            Log::error("Error al eliminar tarjeta: $Id");
        }

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
