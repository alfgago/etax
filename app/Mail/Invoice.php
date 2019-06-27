<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;
use App\Utils\InvoiceUtils;

class Invoice extends Mailable
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
        $message = $this->subject('Factura electrónica #' . $this->content['data_invoice']->document_number.
            ' De: '.$this->content['data_company']->business_name)->markdown('emails.invoice.paid')
            ->with(['data_invoice' => $this->content['data_invoice'], 'company' => $this->content['data_company']]);
        
        $message->attachFromStorage($this->content['xml']);
        $message->attachData( $invoiceUtils->streamPdf( $this->content['data_invoice'], $this->content['data_company'] ), $this->content['data_invoice']->document_key.'.pdf', [
             'mime' => 'application/pdf',
         ]);
        return $message;
    }

}
