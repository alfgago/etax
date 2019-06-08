<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;

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
        $message = $this->subject('Factura electrónica #' . $this->content['data_invoice']->document_number.
            ' De: '.$this->content['data_company']->business_name)->markdown('emails.invoice.paid')
            ->with(['data_invoice' => $this->content['data_invoice'], 'company' => $this->content['data_company']]);
        $message->attachFromStorage($this->content['xml']);
         $message->attachData($this->makePdf(), $this->content['data_invoice']->document_key.'.pdf', [
             'mime' => 'application/pdf',
         ]);// attach each file
        return $message; //Send mail
    }

    private function makePdf()
    {
        $pdf = PDF::loadView('Pdf/invoice', ['data_invoice' => $this->content['data_invoice'],
            'company' => $this->content['data_company']]);
        return $pdf->stream('Invoice.pdf');
    }
}
