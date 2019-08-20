<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreditNoteNotificacion extends Mailable
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
        $type = $this->content['data_invoice']->document_type == '03' ? 'Crédito': 'Debito';
        $message = $this->subject('Confirmación Nota de '. $type .' #' . $this->content['data_invoice']->document_number.
            ' De: '.$this->content['data_company']->business_name)->markdown('emails.invoice.creditnote')
            ->with(['data_invoice' => $this->content['data_invoice'], 'type' => $type]);

        $message->attachFromStorage($this->content['xml']);
        return $message;
    }
}
