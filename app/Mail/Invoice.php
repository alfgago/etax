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

        
        $docType = $this->content['data_invoice']->document_type;
        
        $title = 'Factura electrónica';
        if( $docType == '08') {
            $title = 'Factura electrónica de compra';
        }elseif ($docType == '09') {
            $title = 'Factura electrónica de exportacion';
        }elseif ($docType == '02') {
            $title = 'Nota de débito';
        }elseif ($docType == '03') {
            $title = 'Nota de crédito';
        }elseif ($docType == '04') {
            $title = 'Tiquete electrónico';
        }
        $fromEmail = $this->content['data_company']->email;
        $fromName = $this->content['data_company']->business_name;
        
        $message = $this->subject($isPrueba.$title.' #' . $this->content['data_invoice']->document_number.
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
        
        if($this->content['xmlMH']){
            $message->attachFromStorage($this->content['xmlMH']);
        }
        
        try{
            $message->attachData( 
             $invoiceUtils->streamPdf( $this->content['data_invoice'], $this->content['data_company'] ), 
             $this->content['data_invoice']->document_key.'.pdf',
             [ 'mime' => 'application/pdf' ]
            );
        }catch(\Throwable $e){ Log::error($e->getMessage()); }
        
        return $message;
    }

}
