<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Utils\InvoiceUtils;

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
        $invoiceUtils = new InvoiceUtils();
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

        if( $cedula == '3004045138' ) {
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
        
        $type = $this->content['data_invoice']->document_type == '03' ? 'Crédito': 'Débito';
        $fromEmail = $this->content['data_company']->email;
        $fromName = $this->content['data_company']->business_name;
        $message = $this->subject($isPrueba.'Confirmación Nota de '. $type .' #' . $this->content['data_invoice']->document_number.
            ' De: '.$this->content['data_company']->business_name)->markdown('emails.invoice.creditnote')
            ->with([
                'data_invoice' => $this->content['data_invoice'], 
                'type' => $type, 
                'customImg' =>$customImg
            ])
            ->replyTo($fromEmail, $fromName)
            ->from($sendFrom, $fromName);

        $message->attachFromStorage($this->content['xml']);
        $message->attachFromStorage($this->content['xml_hacienda']);
        try{
            
                $message->attachData( 
                 $invoiceUtils->streamPdf( 
                    $this->content['data_invoice'], 
                    $this->content['data_company'] 
                 ), 
                 $this->content['data_invoice']->document_key.'.pdf',
                 [ 'mime' => 'application/pdf' ]
                );
            
        }catch(\Throwable $e){ Log::error($e->getMessage()); }
        
        return $message;
    }
}
