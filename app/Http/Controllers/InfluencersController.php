<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;
use App\wallet_transactions;
use App\Plan;
use App\Sales;
use App\Payment;

class InfluencersController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function wallet(){
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

        $lista_retiros = wallet_transactions::where('user_id', auth()->user()->id)->get();

        $saldos = [
            "ingresos" => $ingresos,
            "retiros" => $retiros,
            "monto_ingresos" => $total_ingresos,
            "monto_retiros" => $total_retiros,
            "saldo" => $saldo
        ];
        //return $lista_retiros;

        return view('users/influencers')->with('lista_ingresos', $lista_ingresos)->with('lista_retiros', $lista_retiros)->with('saldos', $saldos);
    }

    public function retiro(Request $request){
    	return $request;
    }
}
