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

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'recurring_id');
    }
}
