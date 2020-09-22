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
        $isPrueba = "";
        if ( app()->isLocal() ) {
            $isPrueba = "PRUEBAS - ";
        }
        $customImg = null;
        $sendFrom = 'info@etaxcr.com';
        $cedula = $this->content['data_company']->id_number;
        if( $cedula == '3101693964' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = "facturacion@triquimas.cr";
        }

        if( $cedula == '3004045138' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = 'facturacontabilidad@coopealianza.fi.cr';
        }

        if( $cedula == '3101257551' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = 'facturacontabilidad@coopealianza.fi.cr';
        }

        if( $cedula == '3004481707' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = 'facturacontabilidad@coopealianza.fi.cr';
        }

        if( $cedula == '3101718553' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = 'facturacontabilidad@coopealianza.fi.cr';
        }

        if( $cedula == '3101128398' ){
            $customImg = "logo-$cedula.jpg";
            $sendFrom = 'facturacontabilidad@coopealianza.fi.cr';
        }
        
        $fromEmail = $this->content['data_company']->email;
        $fromName = $this->content['data_company']->business_name;
        
        $response = substr($this->content['response'], -169);
        $message = $this->subject($isPrueba.$fromName.' | Recepcion factura electrónica #' . $this->content['data_invoice']->document_number)->markdown('emails.invoice.recepcion')
            ->with([
                'data_invoice' => $this->content['data_invoice'], 
                'company' => $this->content['data_company'], 
                'response' => $response, 
                'customImg' =>$customImg
            ])
            ->replyTo($fromEmail, $fromName)
            ->from($sendFrom, "$fromName");
        $message->attachFromStorage($this->content['xml']);
        $message->attachFromStorage($this->content['xmlFE']);

        return $message;
    }
}
