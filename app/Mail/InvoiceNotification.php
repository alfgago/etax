<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;
use App\Utils\InvoiceUtils;
use Illuminate\Support\Facades\Log;

class InvoiceNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->content = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $invoiceUtils = new InvoiceUtils();
        $string = substr($this->content['xml'], -169);
        
        $customImg = null;
        $sendFrom = 'info@etaxcr.com';
        $cedula = $this->content['data_company']->id_number;
        if( $cedula == '3101693964' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = $this->content['data_company']->email;
        }
        
        $fromEmail = $this->content['data_company']->email;
        $fromName = $this->content['data_company']->business_name;
        $message = $this->subject('Confirmación Factura electrónica #' . $this->content['data_invoice']->document_number.
            ' De: '.$this->content['data_company']->business_name)->markdown('emails.invoice.confirmation')
            ->with([
                'data_invoice' => $this->content['data_invoice'], 
                'xml' => $string, 
                'customImg' =>$customImg
                ])
            ->replyTo($fromEmail, $fromName)
            ->from($sendFrom, $fromName);
        $message->attachFromStorage($this->content['xmlMH']);
        $message->attachFromStorage($this->content['xml']);
        try{
            if( $this->content['sendPdf'] ){
                $message->attachData( 
                 $invoiceUtils->streamPdf( $this->content['data_invoice'], $this->content['data_company'] ), 
                 $this->content['data_invoice']->document_key.'.pdf',
                 [ 'mime' => 'application/pdf' ]
                );
            }
        }catch(\Throwable $e){ Log::error($e->getMessage()); }
        
        return $message;
    }
}
