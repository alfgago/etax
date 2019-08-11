<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SwitchPlanMail extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data) {
        $this->content = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('Cambio de plan eTax ' . ucfirst($this->content['old_plan_details']->plan_type) . '-' . $this->content['old_plan_details']->plan_name . '(' . $this->content['old_plan_details']->unique_no . ')')->markdown('emails.switchplanemail')->with('content', $this->content);
    }

}
