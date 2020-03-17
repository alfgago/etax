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
        $isPrueba = "";
        if ( app()->isLocal() ) {
            $isPrueba = "PRUEBAS - ";
        }
        $invoiceUtils = new InvoiceUtils();
        
        $customImg = null;
        $sendFrom = 'info@etaxcr.com';
        $cedula = $this->content['data_company']->id_number;
        if( $cedula == '3101693964' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = "facturacion@triquimas.cr";
        }
        
        if ($this->content['data_invoice']->document_type == '08') {
            $title = 'compra';
        } elseif ($this->content['data_invoice']->document_type == '09') {
            $title = 'exportacion';
        } else {
            $title = '';
        }
        $fromEmail = $this->content['data_company']->email;
        $fromName = $this->content['data_company']->business_name;
        
        $message = $this->subject($isPrueba.'Factura electrónica '.$title.' #' . $this->content['data_invoice']->document_number.
            ' De: '.$this->content['data_company']->business_name)
                    ->markdown('emails.invoice.paid')
                    ->with([
                        'data_invoice' => $this->content['data_invoice'],
                        'company' => $this->content['data_company'], 
                        'customImg' =>$customImg
                    ])
                    ->replyTo($fromEmail, $fromName)
                    ->from($sendFrom, "$fromName");
        
        $message->attachFromStorage($this->content['xml']);
        $message->attachData( $invoiceUtils->streamPdf( $this->content['data_invoice'], $this->content['data_company'] ), $this->content['data_invoice']->document_key.'.pdf', [
             'mime' => 'application/pdf',
         ]);
        return $message;
    }

}
