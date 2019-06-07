<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $message = $this->markdown('emails.invoice.paid')->with(['data_invoice' => $this->content['data_invoice'],
            'company' => $this->content['data_company']]);
        $message->attachFromStorage($this->content['xml']); // attach each file
        return $message; //Send mail
    }

    private function makePdf()
    {

    }
}
