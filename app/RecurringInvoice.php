<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;
use App\Invoice;

class RecurringInvoice extends Model
{
    public function proximo_envio($today,$generated_date){
		$today = Carbon::createFromFormat('d/m/Y', $today);
		$generated_date = Carbon::createFromFormat('d/m/Y', $generated_date);
    	$next_send = $generated_date;
    	if($today == $generated_date){
	    	if($this->frecuency == "1"){
				$options = $this->options;
	    		if($generated_date->dayOfWeek >= $this->options){
	    			$options = $options + 7;
	    		}
	    		$addDay = $options - $generated_date->dayOfWeek;
    			$next_send = $generated_date->addDays($addDay);
	        }
	    	if($this->frecuency == "2"){
	    		$options = explode(",",$this->options);
	    		$new_date1 = $options[0]."/".$generated_date->month."/".$generated_date->year;
	    		$new_date2 = $options[1]."/".$generated_date->month."/".$generated_date->year;
				$new_date1 = Carbon::createFromFormat('d/m/Y', $new_date1);
				$new_date2 = Carbon::createFromFormat('d/m/Y', $new_date2);
				if($new_date1 <  $generated_date){
					if($new_date2 <  $generated_date){
	    				$new_date1 = $new_date1->addMonth(1);
	    				$next_send = $new_date1;
					}else{
	    				$next_send = $new_date2;
					}
				}
				if($options[1] == "31"){
					if($new_date2->day == "1"){
						$next_send = $next_send->addDay(-1);
					}
				}
	        }
	    	if($this->frecuency == "3"){
	    		$new_date = $this->options."/".$generated_date->month."/".$generated_date->year;
				$new_date = Carbon::createFromFormat('d/m/Y', $new_date);
				if($new_date <  $generated_date){
	    			$new_date = $new_date->addMonth(1);
				}
				$next_send = $new_date;
	        }
	    	if($this->frecuency == "4"){
	    		$new_date = $this->options."/".$generated_date->year;
				$new_date = Carbon::createFromFormat('d/m/Y', $new_date);
				if($new_date <  $generated_date){
	    			$new_date = $new_date->addYear(1);
				}
				$next_send = $new_date;
	        }
	        
	    	if($this->frecuency == "5"){
    			$next_send = $generated_date->addDays($this->options);
	        }
    	}
        return $next_send;
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'recurring_id');
    }

    public function tipo(){
    	$tipo = "";
    	if($this->frecuency == "1"){
    		$tipo = "Semanal";
	    }
    	if($this->frecuency == "2"){
    		$tipo = "Quincenal";
	    }
    	if($this->frecuency == "3"){
    		$tipo = "Mensual";
	    }
    	if($this->frecuency == "4"){
    		$tipo = "Anual";
	    }
    	if($this->frecuency == "5"){
    		$tipo = "Cantidad de días";
	    }
	    return $tipo;
	}

    public function opciones(){
    	$opciones = "";
    	if($this->frecuency == "1"){
    		if($this->options == 0){
    			$opciones = "Domingos";
    		}
    		if($this->options == 1){
    			$opciones = "Lunes";
    		}
    		if($this->options == 2){
    			$opciones = "Martes";
    		}
    		if($this->options == 3){
    			$opciones = "Miercoles";
    		}
    		if($this->options == 4){
    			$opciones = "Jueves";
    		}
    		if($this->options == 5){
    			$opciones = "Viernes";
    		}
    		if($this->options == 6){
    			$opciones = "Sabado";
    		}
	    }
    	if($this->frecuency == "2"){
    		$options = explode(",",$this->options);
    		$opciones = $options[0]." y ".$options[1];
	    }
    	if($this->frecuency == "3"){
    		$opciones = $this->options;
	    }
    	if($this->frecuency == "4"){
    		$opciones = $this->options;
	    }
    	if($this->frecuency == "5"){
    		$opciones = $this->options;
	    }
	    return $opciones;
	}
}
