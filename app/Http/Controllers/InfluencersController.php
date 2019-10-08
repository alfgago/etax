<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use Illuminate\Http\Request;
use App\Coupon;
use App\wallet_transactions;
use App\Payment;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class InfluencersController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function wallet(){
		if(auth()->user()->isInfluencers()){
	        $ingresos_dt = Coupon::join('payments', 'payments.coupon_id', 'coupons.id')
	            ->where('user_id', auth()->user()->id)
	            ->get();
	        $total_ingresos  = 0;
	        $lista_ingresos = [];
	        $ingresos = 0;
	        foreach ($ingresos_dt as $ingreso) {
	        	$total_ingresos = $total_ingresos + $ingreso->price * $ingreso->gain_percentage / 100;
	        	$ingreso_anterior = array_pull($lista_ingresos, $ingreso->code);
	        	$ingresos++;
	        	if( $ingreso_anterior == null){
					$ingreso_data = [
						"codigo"=>$ingreso->code,
						"cantidad"=>1,
	        			"total"=>$ingreso->price,
	        			"porcentaje"=>$ingreso->gain_percentage,
	        			"ganancia"=>$ingreso->price * $ingreso->gain_percentage / 100
	        		];
	        		$lista_ingresos = array_add($lista_ingresos, $ingreso->code, $ingreso_data);
	        	}else{
	        		$ingreso_anterior["cantidad"] = $ingreso_anterior["cantidad"] + 1;
	        		$ingreso_anterior["total"] = $ingreso_anterior["total"] + $ingreso->price;
	        		$ingreso_anterior["ganancia"] = $ingreso_anterior["ganancia"] + ($ingreso->price * $ingreso->gain_percentage / 100);
	        		$lista_ingresos[$ingreso->code] = $ingreso_anterior;
	        	}
	        }
	        $lista_ingresos = array_values($lista_ingresos);
	        $retiros = wallet_transactions::where('user_id', auth()->user()->id)
	            ->count('amount');

			$total_retiros = wallet_transactions::where('user_id', auth()->user()->id)
	            ->sum('amount'); 

	        $saldo = $total_ingresos - $total_retiros;

	        $lista_retiros = wallet_transactions::where('user_id', auth()->user()->id)->orderByRaw('payment_date DESC')->get();
			foreach ($lista_retiros as $retiro) {
				switch ($retiro->payment_status){
	                case 0:
	                	$retiro->payment_status = 'Denegada';
	                	break;
	                case 1:
	                    $retiro->payment_status = 'Pendiente';
	                    break;
	                case 2:
	                    $retiro->payment_status = 'Realizada';
	                    break;
	            }
	        }
	        $saldos = [
	            "ingresos" => $ingresos,
	            "retiros" => $retiros,
	            "monto_ingresos" => $total_ingresos,
	            "monto_retiros" => $total_retiros,
	            "saldo" => $saldo
	        ];
        	//return $lista_retiros;

        	return view('users/influencers')->with('lista_ingresos', $lista_ingresos)->with('lista_retiros', $lista_retiros)->with('saldos', $saldos);
        }else{
    		return redirect('usuario/perfil')->withErrors('No tienes permisos para ingresar a esta pantalla');
        }
    }

    public function retiro(Request $request){

    	try {
    		$fecha = Carbon::now('America/Costa_Rica')->format('Y-m-d H:m:s');
		  	wallet_transactions::insert(['user_id' => auth()->user()->id, 
    			'amount' => $request->monto, 
    			'payment_date' => $fecha, 
    			'payment_status'=> 1,
    			'proof' => null,
    			'account' => $request->cuenta, 
    			'dni_number' => $request->cedula]);

			$data = User::join('wallet_transactions', 'wallet_transactions.user_id', 'users.id')
				->where('users.id', auth()->user()->id)
				->where('wallet_transactions.payment_date',$fecha)
				->where('wallet_transactions.account',$request->cuenta)
				->where('wallet_transactions.dni_number',$request->cedula)
				->get();
			
			$user = auth()->user();
	          Activity::dispatch(
	              $user,
	              $data,
	              [
	                  'company_id' => $data[0]->company_id
	              ],
	              "Su retiro a sido solicitado."
	          )->onConnection(config('etax.queue_connections'))
	          ->onQueue('log_queue');
	      	Mail::to($data[0]->user_name)->send(new \App\Mail\RequestWallet($data[0]));
	      	Mail::to($data[0]->email)->send(new \App\Mail\NotifyWallet($data[0]));
		  	$retorno = 'Su retiro a sido solicitado';
		} catch (Exception $e) {
		       
		  	$retorno = 'Error en solicitar retiro';
		}
    	
    	return redirect()->back()->withMessage($retorno);
    }
}