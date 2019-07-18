<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCancellation extends Mailable
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
    
    public function build()
    {
        $message = $this->subject('Cancelación de subscripción de eTax')->markdown('emails.notifycancellation')->with('content', $this->content);

        return $message;
    }
}
