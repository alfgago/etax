<?php

namespace App\Mail;

use App\Utils\InvoiceUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReceptionNotification extends Mailable
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
        $message = $this->subject('Recepcion factura electrónica #' . $this->content['data_invoice']->document_number.
            ' De: '.$this->content['data_company']->business_name)->markdown('emails.invoice.recepcion')
            ->with(['data_invoice' => $this->content['data_invoice'], 'company' => $this->content['data_company']]);
        $message->attachFromStorage($this->content['xml']);
        $message->attachFromStorage($this->content['xmlFE']);

        return $message;
    }
}
