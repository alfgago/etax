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
        $isPrueba = "";
        if ( app()->isLocal() ) {
            $isPrueba = "PRUEBAS - ";
        }

        $invoiceUtils = new InvoiceUtils();
        $string = substr($this->content['xml'], -169);

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
        $message = $this->subject($isPrueba.$fromName.' | Confirmación Factura electrónica #' . $this->content['data_invoice']->document_number)->markdown('emails.invoice.confirmation')
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
