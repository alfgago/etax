<?php

namespace App\Jobs;

use App\Jobs\LogActivityHandler as Activity;
use App\ApiResponse;
use App\Company;
use App\Bill;
use App\Mail\CreditNoteNotificacion;
use App\Utils\BridgeHaciendaApi;
use App\Utils\BillUtils;
use App\Jobs\ProcessReception;
use App\XmlHacienda;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessAcceptHacienda implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id = null;
    private $user = null;
    private $company = null;
    private $isAccept = null;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id = '', $user = '', $company = '', $isAccept = '')
    {
        $this->id = $id;
        $this->user = $user;
        $this->company = $company;
        $this->isAccept = $isAccept;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
	       	$bill = Bill::findOrFail($this->id);
	        $apiHacienda = new BridgeHaciendaApi();
	        $tokenApi = $apiHacienda->login(false);
	        if ($tokenApi !== false) {
	            if (!empty($bill)) {
	                $bill->accept_status = $this->isAccept;
	                $bill->save();
	                $this->company->last_rec_ref_number = $this->company->last_rec_ref_number + 1;
	                $this->company->save();
	                $this->company->last_document_rec = getDocReference('05',$this->company->last_rec_ref_number);
	                $this->company->save();
	                $apiHacienda->acceptInvoice($bill, $tokenApi);
	            }
	            $mensaje = 'Aceptación enviada.';
	            if($this->isAccept == 2){
	                $mensaje = 'Rechazo de factura enviado';
	            }
	            clearBillCache($bill);

	        /*Activity::dispatch(
	            $this->user,
	            $bill,
	            [
	                'company_id' => $bill->company_id,
	                'id' => $bill->id,
	                'document_key' => $bill->document_key
	            ],
	            $mensaje
	        )->onConnection(config('etax.queue_connections'))
	        ->onQueue('log_queue');*/
	        } else {
	            //Notification
	        }
	    }catch( Exception $e){
	    	//Notification
	    }   
        
    }

}
