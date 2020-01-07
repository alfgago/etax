<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use \Carbon\Carbon;
use App\Invoice;

class RecurringInvoice extends Model
{

    use Sortable, SoftDeletes;

    public function proximo_envio($generatedDate){
    	$next_send = $generatedDate;
	    if($this->frecuency == "1"){
			$options = $this->options;
	    	if($generatedDate->dayOfWeek >= $this->options){
	    		$options = $options + 7;
	    	}
	    	$addDay = $options - $generatedDate->dayOfWeek;
    		$next_send = $generatedDate->addDays($addDay);
	    }
	    if($this->frecuency == "2"){
	    	$options = explode(",",$this->options);
	    	$new_date1 = $options[0]."/".$generatedDate->month."/".$generatedDate->year;
	    	$new_date2 = $options[1]."/".$generatedDate->month."/".$generatedDate->year;
			$new_date1 = Carbon::createFromFormat('d/m/Y', $new_date1);
			$new_date2 = Carbon::createFromFormat('d/m/Y', $new_date2);
			if($new_date1 <  $generatedDate){
				if($new_date2 <  $generatedDate){
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
	   		$new_date = $this->options."/".$generatedDate->month."/".$generatedDate->year;
			$new_date = Carbon::createFromFormat('d/m/Y', $new_date);
			if($new_date <  $generatedDate){
	    		$new_date = $new_date->addMonth(1);
			}
			$next_send = $new_date;
	       }
	    if($this->frecuency == "4"){
	    	$new_date = $this->options."/".$generatedDate->year;
			$new_date = Carbon::createFromFormat('d/m/Y', $new_date);
			if($new_date <  $generatedDate){
	    		$new_date = $new_date->addYear(1);
			}
			$next_send = $new_date;
	    }
	    if($this->frecuency == "5"){
    		$next_send = $generatedDate->addDays($this->options);
	    }
        return $next_send;
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function enviadas(){
    	$invoices = Invoice::where('recurring_id',$this->id)->get();
        return $invoices;
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

	public function valores(){
    	$opciones = ["",""];
    	if($this->frecuency == "1"){
    		$opciones[0] = $this->options;
	    }
    	if($this->frecuency == "2"){
    		$opciones = explode(",",$this->options);
	    }
    	if($this->frecuency == "3"){
    		$opciones[0] = $this->options;
	    }
    	if($this->frecuency == "4"){
    		$opciones = explode("/",$this->options);
	    }
    	if($this->frecuency == "5"){
    		$opciones[0] = $this->options;
	    }
	    return $opciones;
	}

    public function proximoVencimiento(){
        $invoice = $this->invoice;
        $date = Carbon::parse($invoice->generated_date); 
        $now = Carbon::parse($invoice->due_date);
        $diff = $date->diffInDays($now);
        $next = Carbon::parse($this->next_send);
        return $next->addDays($diff);
    }
}
