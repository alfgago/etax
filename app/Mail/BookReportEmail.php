<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use \Carbon\Carbon;

class BookReportEmail extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
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
        
        $title = $this->content['title'];
        $message = $this->content['message'];
        $file = $this->content['filePath'];
        
        $message = $this->subject( $isPrueba.$title )
                    ->markdown('emails.download-file')
                    ->with([
                        'message' => $message
                    ])
                    ->from('info@etaxcr.com', 'eTax');
        $message->attachFromStorage($file);
        return $message;
    }
}
